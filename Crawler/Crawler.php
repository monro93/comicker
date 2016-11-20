<?php

namespace Comicker\Crawler;


interface Crawler
{
    /**
     * @param $comicChaptersUrl
     * @return array
     */
    public function crawl($comicChaptersUrl);

}