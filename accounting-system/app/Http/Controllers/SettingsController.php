<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Services\BackupService;
use App\Services\ResetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $dbPath     = config('database.connections.sqlite.database');
            $dbExists   = $dbPath && file_exists($dbPath);
            $dbSizeMb   = $dbExists ? round(filesize($dbPath) / 1024 / 1024, 2) : 0;
            $dbModified = $dbExists ? date('Y-m-d H:i', filemtime($dbPath)) : null;
        } else {
            $dbExists = true;
            $name = DB::connection()->getDatabaseName();
            $row  = DB::selectOne(
                'SELECT COALESCE(SUM(DATA_LENGTH + INDEX_LENGTH), 0) AS bytes FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?',
                [$name]
            );
            $dbSizeMb   = round((float) ($row->bytes ?? 0) / 1024 / 1024, 2);
            $dbModified = now()->format('Y-m-d H:i');
        }

        $driverLabel = $driver === 'sqlite' ? 'SQLite' : 'MySQL';

        $resetPinSet = (string) AppSetting::get('reset_pin', '') !== '';
        $resetSections = ResetService::SECTIONS;

        return view('settings.index', compact(
            'dbExists', 'dbSizeMb', 'dbModified', 'driver', 'driverLabel',
            'resetPinSet', 'resetSections'
        ));
    }

    /** دانان/گۆڕینی PIN ـی زیرۆکردنەوە. بە شێوەی hash هەڵدەگیرێت. */
    public function saveResetPin(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'string', 'regex:/^\d{4,8}$/'],
        ], [
            'pin.required' => 'تکایە PIN بنووسە.',
            'pin.regex'    => 'PIN دەبێت لە ٤ بۆ ٨ ژمارە بێت.',
        ]);

        AppSetting::set('reset_pin', Hash::make($request->input('pin')));

        return back()->with('success', 'PIN ـی زیرۆکردنەوە پاشەکەوت کرا.');
    }

    /** زیرۆکردنەوەی یەک بەش. */
    public function resetSection(Request $request, ResetService $reset)
    {
        $request->validate([
            'section' => ['required', 'string'],
            'pin'     => ['required', 'string'],
        ]);

        $section = $request->input('section');

        if (! ResetService::isValidSection($section)) {
            return back()->with('error', 'بەشی هەڵبژێردراو نادروستە.');
        }

        if (! $this->verifyResetPin($request->input('pin'))) {
            return back()->with('error', 'PIN هەڵەیە. زیرۆکردنەوە ئەنجام نەدرا.');
        }

        $reset->resetSection($section);

        return back()->with('success', 'بەشی «' . ResetService::SECTIONS[$section] . '» بە سەرکەوتوویی زیرۆ کرایەوە.');
    }

    /** زیرۆکردنەوەی گشتی. */
    public function resetMaster(Request $request, ResetService $reset)
    {
        $request->validate([
            'pin' => ['required', 'string'],
        ]);

        if (! $this->verifyResetPin($request->input('pin'))) {
            return back()->with('error', 'PIN هەڵەیە. زیرۆکردنەوە ئەنجام نەدرا.');
        }

        $reset->resetMaster();

        return back()->with('success', 'هەموو بەشە دارایییەکان بە سەرکەوتوویی زیرۆ کرانەوە. ناوە بنەڕەتییەکان ماونەتەوە.');
    }

    /** پشکنینی PIN لە لای سێرڤەر. */
    private function verifyResetPin(string $pin): bool
    {
        $stored = (string) AppSetting::get('reset_pin', '');

        return $stored !== '' && Hash::check($pin, $stored);
    }

    public function downloadBackup(BackupService $backup)
    {
        try {
            $result = $backup->generate();
        } catch (\Throwable $e) {
            return back()->with('error', 'نەتوانرا باکئەپ دروست بکرێت: ' . $e->getMessage());
        }

        return response()
            ->download($result['path'], $result['filename'])
            ->deleteFileAfterSend(true);
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

        if (! $dbPath || DB::connection()->getDriverName() !== 'sqlite') {
            return back()->with('error', 'هێنانی باکئەپ لە ناو سیستەمەوە تەنها لە دۆخی SQLite کار دەکات. لەسەر هۆستی MySQL، باکئەپ بە phpMyAdmin بهێنە.');
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
