<?php

namespace Comicker\Crawler;

class ReadComicsTvCrawler
{
    const DOMAIN_URL = 'http://www.readcomics.tv/';
    const COMIC_LIST_URL = 'comic/';
    const COMIC_URL_TRANSFORMATION = '/full';

    public function crawl($comicName)
    {
        $comicsUrl = $this->getComicsURL($comicName);
        $comicPages = $this->getComicPagesURL($comicsUrl);

        return $comicPages;
    }

    private function getComicsURL($comicName){
        $html = file_get_contents(self::DOMAIN_URL.self::COMIC_LIST_URL.$comicName);

        if(preg_match('|<ul class="basic-list">(.*?)</ul>|s', $html, $comicList)){
            if(preg_match_all('|<a[^>]*href="([^"]*chapter-([^"]*))"[^>]*>|s', $comicList[1], $comicUrl)){
                $comics = [];
                for($i = 0; $i< count($comicUrl[1]); $i++){
                    $comics[$comicUrl[2][$i]] = $comicUrl[1][$i].self::COMIC_URL_TRANSFORMATION;
                }
                return $comics;
            }
        }
        return -1;
    }

    private function getComicPagesURL($comicsUrl){
        $pagesUrl = [];
        if(is_array($comicsUrl)) {
            foreach ($comicsUrl as $key => $comicUrl) {
                $html = file_get_contents($comicUrl);
                if (preg_match_all('|<img class="chapter_img"[^>]*src="([^"]*)"|s', $html, $pageUrl)) {
                    $pagesUrl[$key] = $pageUrl[1];
                }
            }
            return $pagesUrl;
        }
        return -1;
    }

}