<?php

namespace Comicker\Entity;


class ComicChapter
{
    private $name;

    private $pages;

    private $filePath;

    private $coverImage;

    private $chapterUrl;

    private $pendingToDonwload;

    public function __construct($name, $url)
    {
        $this->name = $name;
        $this->chapterUrl = $url;
        $this->pendingToDonwload = true;
        $this->pages = [];
    }

    public function addPage($page, $number = null)
    {
        if(isset($number)){
            $this->pages[$number] = $page;
        }else{
            $this->pages[] = $page;
        }
    }

    public function addPages($pages = array())
    {
        foreach ($pages as $page){
            $this->addPage($page);
        }
    }

    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCoverImage()
    {
        return $this->coverImage;
    }

    /**
     * @return mixed
     */
    public function getChapterUrl()
    {
        return $this->chapterUrl;
    }

    /**
     * @return boolean
     */
    public function isPendingToDonwload()
    {
        return $this->pendingToDonwload;
    }

    /**
     * @param boolean $pendingToDonwload
     */
    public function setPendingToDonwload($pendingToDonwload)
    {
        $this->pendingToDonwload = $pendingToDonwload;
    }

    /**
     * @return mixed
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param mixed $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }



}