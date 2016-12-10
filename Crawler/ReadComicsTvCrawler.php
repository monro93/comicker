<?php


namespace Comicker\Crawler;

use Comicker\Entity\Comic;
use Comicker\Entity\ComicChapter;

class ReadComicsTvCrawler implements Crawler
{
    const DOMAIN_URL = 'http://www.readcomics.tv/';
    const COMIC_LIST_URL = 'comic/';
    const COMIC_URL_TRANSFORMATION = '/full';

    public function crawl(Comic $comic)
    {
        $this->getChapters($comic);
        $this->getComicPagesURL($comic);

        return $comic;
    }

    private function getChapters(Comic $comic){
        $html = file_get_contents(self::DOMAIN_URL.self::COMIC_LIST_URL.$comic->getUrl());

        if(preg_match('|<ul class="basic-list">(.*?)</ul>|s', $html, $comicList)){
            if(preg_match_all('|<a[^>]*href="([^"]*chapter-([^"]*))"[^>]*>|s', $comicList[1], $chapterRaw)){

                for($i = 0; $i< count($chapterRaw[1]); $i++){
                    $chapter = new ComicChapter($chapterRaw[2][$i], $chapterRaw[1][$i].self::COMIC_URL_TRANSFORMATION);
                    $comic->addChapter($chapter);
                }
            }
        }
    }

    private function getComicPagesURL(Comic $comic){
        foreach ($comic->getChapters() as $chapter) {
            $html = file_get_contents($chapter->getChapterUrl());
            if (preg_match_all('|<img class="chapter_img"[^>]*src="([^"]*)"|s', $html, $pageUrl)) {
                $chapter->addPages($pageUrl[1]);
            }
        }
    }

}