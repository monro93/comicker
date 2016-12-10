<?php

namespace Comicker\Crawler;

use Comicker\Entity\Comic;
use Comicker\Entity\ComicChapter;

class TuMangaOnlineComCrawler implements Crawler
{
    const API_URL = 'http://www.tumangaonline.com/api/v1/mangas/';
    const API_URL_END = '/capitulos?page=';
    const COMIC_URL_INFO = 'http://www.tumangaonline.com/api/v1/mangas/';
    const CHAPTER_URL = 'http://www.tumangaonline.com/lector/%s/%s/%s/%s';
    const IMAGE_URL = 'http://img1.tumangaonline.com/subidas/%s/%s/%s/%s';
    const MAX_NUMBER_RETIRES = 3;
    const WAIT_TIME_AVOID_BAN = 121;

    public function crawl(Comic $comic)
    {
        $this->getComicsURL($comic);

        return $comic;
    }

    private function getUrlComicName(Comic $comic){
        $html = file_get_contents(self::COMIC_URL_INFO.$comic->getUrl());

        return json_decode($html, true)["nombreUrl"];
    }

    private function getComicsUrl(Comic $comic)
    {
        $UrlComicName = $this->getUrlComicName($comic);

        $totalPages = 1;

        for($page = 1; $page<=$totalPages; $page++){
            $html = file_get_contents(self::API_URL.$comic->getUrl().self::API_URL_END.$page);

            $attemptsRemaining = self::MAX_NUMBER_RETIRES;
            while($html == false && $attemptsRemaining > 0 ){
                echo("TuMangaOnline.com is baning us. Waiting ".self::WAIT_TIME_AVOID_BAN." seconds to try again...\n");
                sleep(self::WAIT_TIME_AVOID_BAN);
                echo("Trying again...\n");
                $html = file_get_contents(self::API_URL.$comic->getUrl().self::API_URL_END.$page);
                $attemptsRemaining--;
            }

            if($totalPages == 1){
                if(preg_match('|"last_page":(\d+)|', $html, $m)){
                    $totalPages = $m[1];
                }
            }
            $json = json_decode($html, true);

            foreach($json['data'] as $chapterInfo){
                $comic->addChapter(
                    $this->processChapterInfo(
                        $chapterInfo,
                        $comic,
                        $UrlComicName
                    )
                );
            }
        }
    }

    private function processChapterInfo($chapterInfo, Comic $comic, $UrlComicName){
        $chapNumberRaw = $chapterInfo['numCapitulo'];
        $chapterNumber = str_replace('.00', '', $chapNumberRaw);
        $idScan = $chapterInfo['subidas'][0]['idScan'];
        $imagesRaw = $chapterInfo['subidas'][0]['imagenes'];
        $chapter = new ComicChapter(
            $chapterNumber,
            sprintf(self::CHAPTER_URL,
                $UrlComicName,
                $comic->getUrl(),
                $chapNumberRaw,
                $idScan
            )
        );
        if(preg_match_all('|"([^"]*\.[^"]{3,5})\\"|', $imagesRaw, $images)){
            foreach ($images[1] as $image){
                $chapter->addPage(
                    sprintf(self::IMAGE_URL,
                        $comic->getUrl(),
                        $chapNumberRaw,
                        $idScan,
                        $image)
                );
            }
        }

        return $chapter;
    }

}