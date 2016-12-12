<?php

namespace Comicker\Event;

use Comicker\Entity\Comic;
use Comicker\Entity\ComicChapter;

class ComicDownloadedEvent extends Event
{
    /**
     * @var Comic
     */
    private $comic;
    /**
     * @var ComicChapter
     */
    private $comicChapter;

    public function __construct($comic, $comicChapter)
    {
        $this->comic = $comic;
        $this->comicChapter = $comicChapter;
    }

    /**
     * @return ComicChapter
     */
    public function getComicChapter()
    {
        return $this->comicChapter;
    }

    /**
     * @return Comic
     */
    public function getComic()
    {
        return $this->comic;
    }


}