<?php

namespace Comicker\Downloader;

use Comicker\Entity\Comic;
use Comicker\Entity\ComicChapter;
use ZipArchive;

class PageDownloader
{
    const CBZ_TYPE = 1;
    const MAX_WIDTH = -1;
    const MAX_HEIGHT = 1000;

    private $type;
    private $comics_folder;
    private $tempFolder;

    public function __construct($comics_folder = ".", $tempFolder = "/var/tmp", $type = PageDownloader::CBZ_TYPE)
    {
        $this->type = $type;
        $this->comics_folder = $comics_folder;
        $this->tempFolder = $tempFolder;
    }

    public function download(Comic $comic)
    {
        foreach ($comic->getChapters() as $chapter) {
            if($chapter->isPendingToDonwload() == false){
                continue;
            }
            $files_downloaded = $this->downloadTempFiles($comic->getName(), $chapter);

            switch ($this->type) {
                case PageDownloader::CBZ_TYPE:
                    $this->createCbz($comic->getName(), $chapter, $files_downloaded, true);
                    break;
            }

            $this->deleteTempFiles($comic->getName(), $files_downloaded);
        }

    }

    private function downloadTempFiles($comicName, ComicChapter $chapter){
        $files_downloaded = [];
        echo("Starting the download of '$comicName' Cp. ".$chapter->getName()."...\n");
        foreach ($chapter->getPages() as $key => $page){
            if(!is_dir($this->tempFolder.'/'.md5($comicName))){
                mkdir($this->tempFolder.'/'.md5($comicName), 0770, true);
            }
            $fileName = $this->tempFolder.'/'.md5($comicName).'/'.sprintf('%03d', $key+1).'.jpg';
            file_put_contents($fileName, file_get_contents($page));
            $this->resizeImage($fileName, self::MAX_WIDTH, self::MAX_HEIGHT);
            $files_downloaded [] = $fileName;
        }
        echo("Finished download of '$comicName' Cp. ".$chapter->getName().".\n");
        return $files_downloaded;
    }

    private function deleteTempFiles($comicName, $files = array()){
        echo("Deleting temporal files...\n");
        foreach ($files as $file){
            if(file_exists($file)){
                unlink($file);
            }else{
                echo("Cannot delete '$file', doesn't exists.\n");
            }
        }
        if(is_dir($this->tempFolder.'/'.md5($comicName))){
            rmdir($this->tempFolder.'/'.md5($comicName));
        }
        echo("Deleted.\n");
    }

    private function createCbz($name, ComicChapter $chapter, $files = array(), $overwrite = false) {
        $destination = $this->comics_folder."/$name";
        if(!is_dir($destination)){
            mkdir($destination, 0770, true);
        }
        $destination.="/".$name.'_'.$chapter->getName().".cbz";

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
                $chapter->setFilePath($destination);
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

    /**
     * Resize an image and keep the proportions
     * @author Allison Beckwith <allison@planetargon.com>
     * @param string $filename
     * @param integer $max_width
     * @param integer $max_height
     */
    function resizeImage($filename, $max_width, $max_height)
    {
        list($orig_width, $orig_height) = getimagesize($filename);

        $width = $orig_width;
        $height = $orig_height;

        # taller
        if ($height > $max_height && $max_height > -1) {
            $width = ($max_height / $height) * $width;
            $height = $max_height;
        }

        # wider
        if ($width > $max_width && $max_width > -1) {
            $height = ($max_width / $width) * $height;
            $width = $max_width;
        }
        $image_p = imagecreatetruecolor($width, $height);

        $image = imagecreatefromjpeg($filename);

        imagecopyresampled($image_p, $image, 0, 0, 0, 0,
            $width, $height, $orig_width, $orig_height);
        imagejpeg($image_p, $filename, 100);
    }
}