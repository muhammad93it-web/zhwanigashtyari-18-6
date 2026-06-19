<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use PDO;

/**
 * Creates a database backup file under storage/app/backups.
 * - SQLite: a copy of the .sqlite file.
 * - MySQL: a pure-PHP .sql dump (no shell_exec / mysqldump), optionally gzipped.
 * The caller owns the returned file and must delete it after use.
 */
class BackupService
{
    /**
     * @return array{path:string, filename:string}
     */
    public function generate(bool $gzip = false): array
    {
        $dir = storage_path('app/backups');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $driver = DB::connection()->getDriverName();
        $stamp  = now()->format('Ymd_His');

        if ($driver === 'sqlite') {
            return $this->sqliteBackup($dir, $stamp);
        }

        return $this->mysqlBackup($dir, $stamp, $gzip);
    }

    private function sqliteBackup(string $dir, string $stamp): array
    {
        $dbPath = config('database.connections.sqlite.database');

        if (! $dbPath || ! file_exists($dbPath)) {
            throw new \RuntimeException('فایلی داتابەیسی SQLite نەدۆزرایەوە.');
        }

        $filename = "jwani_backup_{$stamp}.sqlite";
        $dest = $dir . DIRECTORY_SEPARATOR . $filename;

        if (! copy($dbPath, $dest)) {
            throw new \RuntimeException('نەتوانرا کۆپی باکئەپی SQLite دروست بکرێت.');
        }

        return ['path' => $dest, 'filename' => $filename];
    }

    private function mysqlBackup(string $dir, string $stamp, bool $gzip): array
    {
        $pdo      = DB::connection()->getPdo();
        $database = DB::connection()->getDatabaseName();

        $filename = "jwani_backup_{$stamp}.sql" . ($gzip ? '.gz' : '');
        $path     = $dir . DIRECTORY_SEPARATOR . $filename;

        $gz = null;
        $fh = null;
        if ($gzip) {
            $gz = gzopen($path, 'wb9');
            if ($gz === false) {
                throw new \RuntimeException('نەتوانرا فایلی باکئەپ دروست بکرێت.');
            }
        } else {
            $fh = fopen($path, 'wb');
            if ($fh === false) {
                throw new \RuntimeException('نەتوانرا فایلی باکئەپ دروست بکرێت.');
            }
        }

        $write = function (string $sql) use ($gz, $fh): void {
            if ($gz !== null) {
                gzwrite($gz, $sql);
            } else {
                fwrite($fh, $sql);
            }
        };

        $write("-- Jwani Accounting Database Backup\n");
        $write("-- Database: {$database}\n");
        $write("-- Generated: " . now()->toDateTimeString() . "\n");
        $write("-- Restore: cPanel → phpMyAdmin → Import → choose this file → Go.\n\n");
        $write("SET NAMES utf8mb4;\n");
        $write("SET FOREIGN_KEY_CHECKS = 0;\n");
        $write("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n");

        $tables = [];
        $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $row) {
            $tables[] = $row[0];
        }

        foreach ($tables as $table) {
            $this->dumpTable($pdo, $table, $write);
        }

        $write("\nSET FOREIGN_KEY_CHECKS = 1;\n");

        if ($gz !== null) {
            gzclose($gz);
        } else {
            fclose($fh);
        }

        return ['path' => $path, 'filename' => $filename];
    }

    private function dumpTable(PDO $pdo, string $table, callable $write): void
    {
        $qt = $this->quoteId($table);

        $write("\n-- ----------------------------\n");
        $write("-- Table structure for {$table}\n");
        $write("-- ----------------------------\n");
        $write("DROP TABLE IF EXISTS {$qt};\n");

        $create = $pdo->query("SHOW CREATE TABLE {$qt}")->fetch(PDO::FETCH_NUM);
        $write($create[1] . ";\n\n");

        $cols = $pdo->query("SHOW COLUMNS FROM {$qt}")->fetchAll(PDO::FETCH_ASSOC);

        $insertable = [];
        $binary     = [];
        $pk         = null;
        $pkCount    = 0;

        foreach ($cols as $c) {
            $extra = strtoupper($c['Extra'] ?? '');
            if (str_contains($extra, 'GENERATED')) {
                continue; // skip virtual/stored generated columns
            }
            $field = $c['Field'];
            $insertable[] = $field;

            $type = strtolower($c['Type'] ?? '');
            $binary[$field] = str_contains($type, 'blob')
                || str_contains($type, 'binary')
                || preg_match('/^bit\(/', $type) === 1;

            if (($c['Key'] ?? '') === 'PRI') {
                $pkCount++;
                $pk = $field;
            }
        }

        if (empty($insertable)) {
            return;
        }

        if ($pkCount !== 1) {
            $pk = null; // keyset pagination only for a single-column PK
        }

        $write("-- Data for {$table}\n");
        $this->dumpRows($pdo, $table, $insertable, $binary, $pk, $write);
        $write("\n");
    }

    private function dumpRows(PDO $pdo, string $table, array $insertable, array $binary, ?string $pk, callable $write): void
    {
        $qt      = $this->quoteId($table);
        $colList = implode(', ', array_map([$this, 'quoteId'], $insertable));

        $chunk        = 500;
        $maxStmtBytes = 800 * 1024;

        $buffer      = [];
        $bufferBytes = 0;

        $flush = function () use (&$buffer, &$bufferBytes, $write, $qt, $colList): void {
            if (empty($buffer)) {
                return;
            }
            $write("INSERT INTO {$qt} ({$colList}) VALUES\n" . implode(",\n", $buffer) . ";\n");
            $buffer = [];
            $bufferBytes = 0;
        };

        $process = function (array $rows) use (&$buffer, &$bufferBytes, $pdo, $binary, $insertable, $maxStmtBytes, $flush): void {
            foreach ($rows as $row) {
                $vals = [];
                foreach ($insertable as $col) {
                    $vals[] = $this->formatValue($pdo, $row[$col], $binary[$col] ?? false);
                }
                $tuple = '(' . implode(', ', $vals) . ')';
                $buffer[] = $tuple;
                $bufferBytes += strlen($tuple) + 2;
                if ($bufferBytes >= $maxStmtBytes) {
                    $flush();
                }
            }
        };

        if ($pk !== null) {
            $qpk = $this->quoteId($pk);
            $lastVal = null;
            while (true) {
                $sql = "SELECT {$colList} FROM {$qt}";
                if ($lastVal !== null) {
                    $sql .= " WHERE {$qpk} > " . $pdo->quote((string) $lastVal);
                }
                $sql .= " ORDER BY {$qpk} ASC LIMIT {$chunk}";

                $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                if (empty($rows)) {
                    break;
                }
                $process($rows);
                $lastVal = end($rows)[$pk];
                if (count($rows) < $chunk) {
                    break;
                }
            }
        } else {
            $offset = 0;
            while (true) {
                $sql = "SELECT {$colList} FROM {$qt} LIMIT {$chunk} OFFSET {$offset}";
                $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                if (empty($rows)) {
                    break;
                }
                $process($rows);
                if (count($rows) < $chunk) {
                    break;
                }
                $offset += $chunk;
            }
        }

        $flush();
    }

    private function formatValue(PDO $pdo, $value, bool $isBinary): string
    {
        if ($value === null) {
            return 'NULL';
        }
        if ($isBinary) {
            return $value === '' ? "''" : '0x' . bin2hex($value);
        }

        return $pdo->quote((string) $value);
    }

    private function quoteId(string $id): string
    {
        return '`' . str_replace('`', '``', $id) . '`';
    }
}
