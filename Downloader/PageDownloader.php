<?php

namespace Comicker\Downloader;

use ZipArchive;

class PageDownloader
{
    const CBZ_TYPE = 1;

    private $type;
    private $comics_folder;
    private $tempFolder;

    public function __construct($comics_folder = ".", $tempFolder = "/var/tmp", $type = PageDownloader::CBZ_TYPE)
    {
        $this->type = $type;
        $this->comics_folder = $comics_folder;
        $this->tempFolder = $tempFolder;
    }

    public function download($name, $chapter, $urls )
    {
        $files_downloaded = $this->downloadTempFiles($name, $chapter, $urls);

        switch ($this->type){
            case PageDownloader::CBZ_TYPE:
                $this->createCbz($name, $chapter, $files_downloaded, true);
                break;
        }

        $this->deleteTempFiles($files_downloaded);

    }

    private function downloadTempFiles($name, $chapter, $urls){
        $files_downloaded = [];
        echo("Starting the download of '$name' Cp. $chapter...\n");
        foreach ($urls as $key => $url){
            if(!is_dir($this->tempFolder.'/'.md5($urls[1]))){
                mkdir($this->tempFolder.'/'.md5($urls[1]), 0770, true);
            }
            $fileName = $this->tempFolder.'/'.md5($urls[1]).'/'.sprintf('%03d', $key+1).'.jpg';
            file_put_contents($fileName, file_get_contents($url));
            $files_downloaded [] = $fileName;
        }
        echo("Finished download of '$name' Cp. $chapter.\n");
        return $files_downloaded;
    }

    private function deleteTempFiles($files = array()){
        echo("Deleting temporal files...\n");
        foreach ($files as $file){
            if(file_exists($file)){
                unlink($file);
            }else{
                echo("Cannot delete '$file', doesn't exists.");
            }
        }
        echo("Deleted.\n");
    }

    private function createCbz($name, $chapter, $files = array(), $overwrite = false) {
        $destination = $this->comics_folder."/$name";
        if(!is_dir($destination)){
            mkdir($destination, 0770, true);
        }
        $destination.="/".sprintf('%04d', $chapter).".cbz";

        echo("Zipping comic to $destination...\n");
        if(file_exists($destination) && !$overwrite) { return false; }
        //vars
        $valid_files = array();

        if(is_array($files)) {
            foreach($files as $file) {
                if(file_exists($file)) {
                    $valid_files[] = $file;
                }
            }
        }
        //if we have good files...
        if(count($valid_files)) {
            //create the archive
            $zip = new ZipArchive();
            $zip->open($destination,ZIPARCHIVE::OVERWRITE | ZIPARCHIVE::CREATE);

            //add the files
            foreach($valid_files as $file) {
                $zip->addFile($file,basename($file));
            }
            $zip->close();
            if(file_exists($destination)){
                echo("Zipped $destination.\n");
                return true;
            }else{
                echo("Cannot Zip '$name' Cp. $chapter.\n");
                return false;
            }

        }
        else
        {
            return false;
        }
    }
}