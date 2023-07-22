<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\DbDumper\Databases\MySql;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupController extends Controller
{
    //DB BACKUP 
    public function createBackup()
    {
        $dbfilename = 'backup.sql';
        $dbpath = storage_path($dbfilename);

        MySql::create()
            ->setDbName(config('database.connections.mysql.database'))
            ->setUserName(config('database.connections.mysql.username'))
            ->setPassword(config('database.connections.mysql.password'))
            ->dumpToFile($dbpath);

        $zipfile = 'backup.zip';
        $zippath = storage_path($zipfile);
        $zip = new ZipArchive();
        $zip->open($zippath, ZipArchive::CREATE);
        $zip->addFile($dbpath, $dbfilename);
        $zip->close();

       //Returning Response
        return response()->download($zippath, $zipfile);  
    } 
}





    
  
