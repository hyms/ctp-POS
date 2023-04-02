<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class BackupController extends Controller
{
   //-------------------- Generate Databse -------------\\

   public function Generate_Backup(Request $request)
   {
       Artisan::call('backup:run --only-db');
       $directory = env('APP_NAME', 'ctp-backup');
       $file_path= Storage::disk('public')->files($directory);
       $file_path = collect($file_path);
       $file_path = $file_path->last();
       return Storage::disk('public')->download($file_path, 'respaldo.zip');
   }

}
