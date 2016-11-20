<?php


require_once 'Crawler\ReadComicsTvCrawler.php';
require_once 'Downloader\PageDownloader.php';

use Comicker\Crawler\ReadComicsTvCrawler;
use Comicker\Downloader\PageDownloader;

main();

function main()
{
    $settings = yaml_parse_file('Resources/settings.yml');
    $comics = yaml_parse_file('Resources/comics.yml');
    if(!isset($settings['comics_folder'])||
        !isset($settings['temporal_download_folder'])){
        echo("Error: settings.yml doesn't has a proper format");
        die;
    }else {
        $readComicsTvCrawler = new ReadComicsTvCrawler();
        $downloader = new PageDownloader($settings['comics_folder'], $settings['temporal_download_folder']);
        var_dump($comics);
        foreach ($comics['read_comics_tv'] as $comicName){
            $readComicsTvCrawler->crawl($comicName);
            $comicsUrls = $readComicsTvCrawler->crawl($readComicsTvCrawler);
            foreach ($comicsUrls as $key => $comicUrls) {
                $downloader->download($comicName, $key + 1, $comicUrls);
            }
        }
        /*$comicName = 'the-clone-conspiracy-2016';
        $comicsUrls = $crawler->crawl($comicName);
        foreach ($comicsUrls as $key => $comicUrls){
            $downloader->download($comicName, $key+1, $comicUrls);
        }*/

        echo("All the job is done!");
    }



}
