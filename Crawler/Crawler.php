<?php

namespace Comicker\Crawler;

use Comicker\Entity\Comic;

interface Crawler
{
    /**
     * @param Comic $comic
     * @return Comic
     */
    public function crawl(Comic $comic);

}