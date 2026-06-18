<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        $dbPath = config('database.connections.sqlite.database');
        $dbExists = $dbPath && file_exists($dbPath);
        $dbSizeMb = $dbExists ? round(filesize($dbPath) / 1024 / 1024, 2) : 0;
        $dbModified = $dbExists ? date('Y-m-d H:i', filemtime($dbPath)) : null;

        return view('settings.index', compact('dbExists', 'dbSizeMb', 'dbModified'));
    }

    public function downloadBackup()
    {
        $dbPath = config('database.connections.sqlite.database');

        if (! $dbPath || ! file_exists($dbPath)) {
            return back()->with('error', 'فایلی داتابەیس نەدۆزرایەوە.');
        }

        $filename = 'jwani_backup_' . now()->format('Ymd_His') . '.sqlite';

        return response()->download($dbPath, $filename, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function importBackup(Request $request)
    {
        $request->validate([
            'backup_file' => ['required', 'file', 'mimes:sqlite,db', 'max:102400'],
        ], [
            'backup_file.required' => 'تکایە فایلێک هەڵبژێرە.',
            'backup_file.mimes'    => 'فایلەکە دەبێت .sqlite یان .db بێت.',
            'backup_file.max'      => 'فایلەکە زۆر گەورەیە (زیادتر لە 100MB).',
        ]);

        $dbPath = config('database.connections.sqlite.database');

        if (! $dbPath) {
            return back()->with('error', 'ئەم تایبەتمەندییە تەنها لە دۆخی SQLite کار دەکات.');
        }

        $file = $request->file('backup_file');

        // Validate it's a real SQLite file
        $handle = fopen($file->getRealPath(), 'rb');
        $header = fread($handle, 16);
        fclose($handle);

        if (strpos($header, 'SQLite format') === false) {
            return back()->with('error', 'فایلەکە فایلی SQLite ی ڕاستەقینە نییە.');
        }

        // Close DB connection before replacing
        DB::disconnect('sqlite');

        // Backup current file first
        $backupDir = dirname($dbPath);
        $currentBackup = $backupDir . '/database_before_import_' . now()->format('Ymd_His') . '.sqlite';
        if (file_exists($dbPath)) {
            copy($dbPath, $currentBackup);
        }

        // Replace with uploaded file
        $file->move(dirname($dbPath), basename($dbPath));

        return back()->with('success', 'باکئەپ بە سەرکەوتوویی هاتە ناو سیستەم. تکایە دووبارە چوونەژوورەوە بکە.');
    }
}
