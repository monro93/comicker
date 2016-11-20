<?php

namespace Comicker\Crawler;


class TuMangaOnlineComCrawler implements Crawler
{
    const API_URL = 'http://www.tumangaonline.com/api/v1/mangas/';
    const API_URL_END = '/capitulos?page=';
    const IMAGE_URL = 'http://img1.tumangaonline.com/subidas/%s/%s/%s/%s';
    const MAX_NUMBER_RETIRES = 3;
    const WAIT_TIME_AVOID_BAN = 40;
    public function __construct()
    {

    }

    public function crawl($comicChaptersUrl)
    {
        $comicsUrl = $this->getComicsURL($comicChaptersUrl);

        return $comicsUrl;
    }

    private function getComicsUrl($comicChaptersUrl)
    {
        $chapters = [];
        $totalPages = 1;

        for($page = 1; $page<=$totalPages; $page++){
            echo(self::API_URL.$comicChaptersUrl.self::API_URL_END.$page);
            $html = file_get_contents(self::API_URL.$comicChaptersUrl.self::API_URL_END.$page);

            $attemptsRemaining = self::MAX_NUMBER_RETIRES;
            while($html == false && $attemptsRemaining > 0 ){
                echo("TuMangaOnline.com is baning us. Waiting 120 seconds to try again...\n");
                sleep(121);
                echo("Trying again...\n");
                $html = file_get_contents(self::API_URL.$comicChaptersUrl.self::API_URL_END.$page);
                $attemptsRemaining--;
            }

            if($totalPages == 1){
                if(preg_match('|"last_page":(\d+)|', $html, $m)){
                    $totalPages = $m[1];
                }
            }
            $json = json_decode($html, true);
            foreach($json['data'] as $chapterInfo){
                $chapNumberRaw = $chapterInfo['numCapitulo'];
                $chapterNumber = str_replace('.00', '', $chapNumberRaw);
                $idScan = $chapterInfo['subidas'][0]['idScan'];
                $imagesRaw = $chapterInfo['subidas'][0]['imagenes'];
                if(preg_match_all('|"([^"]*\.[^"]{3,5})\\"|', $imagesRaw, $images)){
                    foreach ($images[1] as $image){
                        $chapters[$chapterNumber][] =
                            sprintf(self::IMAGE_URL,
                                $comicChaptersUrl,
                                $chapNumberRaw,
                                $idScan,
                                $image);
                    }
                }

            }

        }

        return $chapters;
    }

}