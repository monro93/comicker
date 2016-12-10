<?php
namespace Comicker\Entity;
class Comic
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int|null
     */
    private $numberChaptersToSave;

    /**
     * @var ComicChapter[]
     */
    private $chapters;

    public function __construct($url, $name = null, $numberChaptersToSave = null)
    {
        $this->url = $url;
        $this->name = (isset($name))?$name:$url;
        $this->numberChaptersToSave = $numberChaptersToSave;
    }

    public function addChapter(ComicChapter $chapter)
    {
        $this->chapters[$chapter->getName()] = $chapter;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int|null
     */
    public function getNumberChaptersToSave()
    {
        return $this->numberChaptersToSave;
    }

    /**
     * @return ComicChapter[]
     */
    public function getChapters()
    {
        return $this->chapters;
    }


}