<?php

namespace Comicker\FileManager;

use Comicker\Entity\Comic;

class FileManager
{
    private $comicPath;

    public function __construct($comicPath)
    {
        $this->comicPath = $comicPath;
    }

    public function setComicsPendingToDownload(Comic $comic)
    {
        foreach ($comic->getChapters() as $chapter){
            $file = $this->comicPath.'/'.$comic->getName().
                '/'.$comic->getName().
                '_'.$chapter->getName();

            if(is_file($file.'.cbz') || is_file($file.'.zip') || is_file($file.'.cbr')){
                $chapter->setPendingToDonwload(false);
            }
        }

    }
}