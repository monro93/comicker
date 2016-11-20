<?php

require_once __DIR__.'/Crawler/Crawler.php';
require_once __DIR__.'/Crawler/ReadComicsTvCrawler.php';
require_once __DIR__.'/Crawler/TuMangaOnlineComCrawler.php';
require_once __DIR__.'/Downloader/PageDownloader.php';
require_once __DIR__.'/FileManager/FileManager.php';

use Comicker\Crawler\ReadComicsTvCrawler;
use Comicker\Crawler\TuMangaOnlineComCrawler;
use Comicker\Downloader\PageDownloader;
use Comicker\FileManager\FileManager;

main();

function main()
{
    echo("Reading settings.yml...");
    $settings = yaml_parse_file(__DIR__.'/Resources/settings.yml');
    echo(" Done.\n");
    echo("Reading comics.yml...");
    $comicsGlobal = yaml_parse_file(__DIR__.'/Resources/comics.yml');
    echo(" Done.\n");

    if(!isset($settings['comics_folder'])||
        !isset($settings['temporal_download_folder'])){

        echo("Error: settings.yml doesn't has a proper format");
        die;

    }else {
        $readComicsTvCrawler = new ReadComicsTvCrawler();
        $tuMangaOnlineComCrawler = new TuMangaOnlineComCrawler();
        $downloader = new PageDownloader($settings['comics_folder'], $settings['temporal_download_folder']);
        $fileManager = new FileManager($settings['comics_folder']);
        foreach ($comicsGlobal as $comicGlobal) {

            if (array_key_exists('read_comics_tv', $comicGlobal)) {
                $comics = $comicGlobal['read_comics_tv'];
                $crawler = $readComicsTvCrawler;
            } else if (array_key_exists('tu_manga_online_com', $comicGlobal)) {
                $comics = $comicGlobal['tu_manga_online_com'];
                $crawler = $tuMangaOnlineComCrawler;
            } else {
                echo("No comics found. Exiting.\n");
                die;
            }

            foreach ($comics as $comic) {
                echo("Reading List of Chapters for '" . $comic['comic']['name'] . "'... ");
                $comicsUrls = $crawler->crawl($comic["comic"]["url"]);
                echo("Found " . count($comicsUrls) . " Chapters\n");
                echo("Checking local files for '" . $comic['comic']['name'] . "'... ");
                $comicsUrls = $fileManager->getNonExistingComicsUrls($comic["comic"]["name"], $comicsUrls);
                echo("Done\n");

                foreach ($comicsUrls as $chapter => $comicUrls) {
                    $downloader->download($comic["comic"]["name"], $chapter, $comicUrls);
                }
            }
        }

        echo("All the job is done!\n");
    }

}
