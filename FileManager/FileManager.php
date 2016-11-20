<?php


namespace Comicker\FileManager;


class FileManager
{
    private $comicPath;

    public function __construct($comicPath)
    {
        $this->comicPath = $comicPath;
    }

    public function getNonExistingComicsUrls($name, $comicsURLs)
    {
        $comicsURLsToDownload = $comicsURLs;

        foreach ($comicsURLs as $chapter => $comicsURL){
            $file = $this->comicPath.'/'.$name.'/'.$name.'_'.sprintf('%04d', $chapter);
            if(is_file($file.'.cbz') || is_file($file.'.zip') || is_file($file.'.cbr')){
                unset($comicsURLsToDownload[$chapter]);
            }
        }
        return $comicsURLsToDownload;
    }
}