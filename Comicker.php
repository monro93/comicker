<?php

require_once __DIR__.'/Crawler/ReadComicsTvCrawler.php';
require_once __DIR__.'/Downloader/PageDownloader.php';
require_once __DIR__.'/FileManager/FileManager.php';

use Comicker\Crawler\ReadComicsTvCrawler;
use Comicker\Downloader\PageDownloader;
use Comicker\FileManager\FileManager;

main();

function main()
{
    echo("Reading settings.yml...");
    $settings = yaml_parse_file(__DIR__.'/Resources/settings.yml');
    echo(" Done.\n");
    echo("Reading comics.yml...");
    $comics = yaml_parse_file(__DIR__.'/Resources/comics.yml');
    echo(" Done.\n");

    if(!isset($settings['comics_folder'])||
        !isset($settings['temporal_download_folder'])){

        echo("Error: settings.yml doesn't has a proper format");
        die;

    }else {
        $readComicsTvCrawler = new ReadComicsTvCrawler();
        $downloader = new PageDownloader($settings['comics_folder'], $settings['temporal_download_folder']);
        $fileManager = new FileManager($settings['comics_folder']);
        //var_dump($comics);
        foreach ($comics['read_comics_tv'] as $comic){
            echo("Reading List of Chapters for '".$comic['comic']['name']."'... ");
            $comicsUrls = $readComicsTvCrawler->crawl($comic["comic"]["url"]);
            echo("Found ".count($comicsUrls)." Chapters\n");
            echo("Checking local files for '".$comic['comic']['name']."'... ");
            $comicsUrls = $fileManager->getNonExistingComicsUrls($comic["comic"]["name"], $comicsUrls);
            echo("Done\n");

            foreach ($comicsUrls as $chapter => $comicUrls) {
                $downloader->download($comic["comic"]["name"], $chapter, $comicUrls);
            }
        }
        echo("All the job is done!");
    }

}
