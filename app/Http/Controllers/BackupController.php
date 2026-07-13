<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;

class BackupController extends Controller
{
    public function index()
    {
        $storagePath = storage_path('backups');

        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true, true);
        }

        $backupFiles = collect(File::files($storagePath))
            ->filter(fn($item) => in_array($item->getExtension(), ['sql', 'zip']))
            ->sortByDesc(fn($item) => $item->getMTime())
            ->values()
            ->map(function ($fileInstance, $key) {
                $fileExtension = $fileInstance->getExtension();
                $isSql = ($fileExtension === 'sql');

                return [
                    'no'         => $key + 1,
                    'filename'   => $fileInstance->getFilename(),
                    'size'       => $this->humanReadableSize($fileInstance->getSize()),
                    'type'       => $isSql ? 'Database' : 'Source Code',
                    'type_class' => $isSql 
                        ? 'bg-blue-50 text-blue-700 ring-blue-600/20' 
                        : 'bg-purple-50 text-purple-700 ring-purple-600/20',
                    'icon_color' => $isSql ? 'text-blue-500' : 'text-purple-500',
                    'created_at' => date('d-m-Y H:i:s', $fileInstance->getMTime()),
                ];
            });

        $dbStatus = DB::select('SHOW TABLE STATUS');
        $totalTables = count($dbStatus);
        $totalDbSize = array_sum(array_map(fn($table) => ($table->Data_length ?? 0) + ($table->Index_length ?? 0), $dbStatus));

        $lastDbBackup   = $backupFiles->firstWhere('type', 'Database');
        $lastCodeBackup = $backupFiles->firstWhere('type', 'Source Code');

        return view('admin.backup.index', [
            'files'           => $backupFiles,
            'tableCount'      => $totalTables,
            'dbSize'          => $totalDbSize,
            'lastDbBackup'    => $lastDbBackup,
            'lastCodeBackup'  => $lastCodeBackup
        ]);
    }

    public function backupDatabase()
    {
        $storagePath = storage_path('backups');
        
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true, true);
        }

        $generatedName = 'backup_database' . date('Y-m-d_His') . '.sql';
        $fullPath = $storagePath . DIRECTORY_SEPARATOR . $generatedName;

        File::put($fullPath, $this->compileSqlStructureAndData());

        return response()->download($fullPath, $generatedName)->deleteFileAfterSend(false);
    }

    public function backupSourceCode()
    {
        if (!class_exists('ZipArchive')) {
            return back()->with('error', 'Fitur kompresi ZipArchive dinonaktifkan pada server ini.');
        }

        $storagePath = storage_path('backups');
        
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true, true);
        }

        $generatedName = 'backup_sourcecode' . date('Y-m-d_His') . '.zip';
        $fullPath = $storagePath . DIRECTORY_SEPARATOR . $generatedName;

        $archive = new ZipArchive();
        if ($archive->open($fullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Sistem gagal menginisialisasi pembuatan berkas arsip ZIP.');
        }

        $blacklistDirs = ['vendor', 'node_modules', '.git', 'storage/backups', 'storage/logs', 'storage/framework'];

        $this->packFolderToZip($archive, base_path(), '', $blacklistDirs);
        $archive->close();

        if (!File::exists($fullPath) || File::size($fullPath) === 0) {
            return back()->with('error', 'Proses pembentukan berkas backup gagal atau menghasilkan file kosong.');
        }

        return response()->download($fullPath, $generatedName)->deleteFileAfterSend(false);
    }

    public function downloadBackup($filename)
    {
        if (!preg_match('/^[\w\-. ]+$/', $filename)) {
            abort(400, 'Format nama berkas terdeteksi tidak aman.');
        }

        $fullPath = storage_path('backups/' . $filename);
        
        if (!File::exists($fullPath)) {
            abort(404, 'Berkas cadangan yang Anda cari tidak tersedia.');
        }

        return response()->download($fullPath);
    }

    public function deleteBackup(Request $request, $filename)
    {
        if (!preg_match('/^[\w\-. ]+$/', $filename)) {
            return back()->with('error', 'Karakter nama file tidak valid.');
        }

        $fullPath = storage_path('backups/' . $filename);
        
        if (!File::exists($fullPath)) {
            return back()->with('error', 'Berkas backup gagal ditemukan untuk dihapus.');
        }

        File::delete($fullPath);

        return back()->with('success', "Arsip backup \"{$filename}\" telah berhasil dibersihkan.");
    }

    private function compileSqlStructureAndData()
    {
        $currentDb = env('DB_DATABASE');
        $buffer = [];

        $buffer[] = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";";
        $buffer[] = "SET AUTOCOMMIT = 0;";
        $buffer[] = "START TRANSACTION;";
        $buffer[] = "SET time_zone = \"+00:00\";";
        $buffer[] = "SET FOREIGN_KEY_CHECKS = 0;\n";

        $databaseTables = DB::select("SHOW TABLES");
        if (empty($databaseTables)) {
            return implode("\n", $buffer);
        }
        
        $objectKey = array_keys((array) $databaseTables[0])[0];

        foreach ($databaseTables as $tableObj) {
            $currentTable = $tableObj->$objectKey;

            $buffer[] = "DROP TABLE IF EXISTS `$currentTable`;";

            $ddlQuery = DB::select("SHOW CREATE TABLE `{$currentTable}`");
            $createStatement = '';
            foreach ($ddlQuery[0] as $prop => $sqlString) {
                if ($prop !== 'Table' && $prop !== 'View') {
                    $createStatement = $sqlString;
                    break;
                }
            }
            $buffer[] = $createStatement . ";\n";

            $records = DB::select("SELECT * FROM `{$currentTable}`");

            if (!empty($records)) {
                $fields = array_keys((array) $records[0]);

                foreach ($records as $row) {
                    $sanitizedValues = [];
                    foreach (array_values((array) $row) as $cell) {
                        if (is_null($cell)) {
                            $sanitizedValues[] = 'NULL';
                        } elseif (is_numeric($cell) && !str_contains($cell, '-')) {
                            $sanitizedValues[] = $cell;
                        } else {
                            $escapedData = str_replace(
                                ['\\', "'", "\0", "\n", "\r", "\x1a"],
                                ['\\\\', "\\'", '\\0', '\\n', '\\r', '\\Z'],
                                $cell
                            );
                            $sanitizedValues[] = "'{$escapedData}'";
                        }
                    }
                    $buffer[] = "INSERT INTO `{$currentTable}` (`" . implode('`, `', $fields) . "`) VALUES (" . implode(', ', $sanitizedValues) . ");";
                }
                $buffer[] = "";
            }
        }

        $buffer[] = "SET FOREIGN_KEY_CHECKS = 1;";
        $buffer[] = "COMMIT;";

        return implode("\n", $buffer);
    }

    private function packFolderToZip(ZipArchive $zipEngine, $originPath, $insideZipPath, array $ignoredFolders)
    {
        $directoryContent = @scandir($originPath);
        if ($directoryContent === false) return;

        foreach ($directoryContent as $node) {
            if ($node === '.' || $node === '..') continue;

            $absoluteNodePath = $originPath . DIRECTORY_SEPARATOR . $node;
            $relativeZipPath  = $insideZipPath !== '' ? $insideZipPath . '/' . $node : $node;

            if (is_dir($absoluteNodePath)) {
                if (in_array($node, $ignoredFolders)) continue;
                $zipEngine->addEmptyDir($relativeZipPath);
                $this->packFolderToZip($zipEngine, $absoluteNodePath, $relativeZipPath, $ignoredFolders);
            } else {
                $zipEngine->addFile($absoluteNodePath, $relativeZipPath);
            }
        }
    }

    private function humanReadableSize($sizeInBytes)
    {
        if ($sizeInBytes === 0) return '0 B';
        $labels = ['B', 'KB', 'MB', 'GB'];
        $factor = floor(log($sizeInBytes, 1024));
        return round($sizeInBytes / pow(1024, $factor), 2) . ' ' . $labels[$factor];
    }
}