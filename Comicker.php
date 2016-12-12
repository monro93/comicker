<?php

require_once __DIR__.'/Crawler/Crawler.php';
require_once __DIR__.'/Crawler/ReadComicsTvCrawler.php';
require_once __DIR__.'/Crawler/TuMangaOnlineComCrawler.php';

require_once __DIR__.'/Downloader/PageDownloader.php';
require_once __DIR__.'/FileManager/FileManager.php';

require_once __DIR__.'/Entity/Comic.php';
require_once __DIR__.'/Entity/ComicChapter.php';

require_once __DIR__.'/Event/Event.php';
require_once __DIR__.'/Event/ComicDownloadedEvent.php';
require_once __DIR__.'/Event/EventDispatcher.php';
require_once __DIR__.'/Event/ComicEventDispatcher.php';
require_once __DIR__.'/Event/EventListener.php';
require_once __DIR__.'/Event/ComicEventListener.php';
require_once __DIR__.'/Event/TelegramListener.php';

use Comicker\Crawler\ReadComicsTvCrawler;
use Comicker\Crawler\TuMangaOnlineComCrawler;
use Comicker\Downloader\PageDownloader;
use Comicker\FileManager\FileManager;
use Comicker\Entity\Comic;
use Comicker\Event\TelegramListener;
use Comicker\Event\ComicEventDispatcher;

main();

function main()
{
    $settings = yaml_parse_file(__DIR__.'/Resources/settings.yml');
    $comicsGlobal = yaml_parse_file(__DIR__.'/Resources/comics.yml');

    if(!isset($settings['comics_folder'])||
        !isset($settings['temporal_download_folder'])) {
        echo("Error: settings.yml doesn't has a proper format");
        die;
    }

    $comicEventDispatcher = new ComicEventDispatcher();
    if($settings['telegram']['use_telegram'] == true){
        if(isset( $settings['telegram']['telegram_api_key']) &&
            isset($settings['telegram']['telegram_chat_id'])){
            $comicEventDispatcher->addListener(
                new TelegramListener(
                    $settings['telegram']['telegram_api_key'],
                    $settings['telegram']['telegram_chat_id']
                )
            );
        }

    }
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

        foreach ($comics as $comicRaw) {
            $comic = new Comic($comicRaw['comic']['url'], $comicRaw['comic']['name']);
            echo("Reading List of Chapters for '" . $comic->getName() . "'... ");
            $crawler->crawl($comic);
            echo("Found " . count($comic->getChapters()) . " Chapters\n");
            echo("Checking local files for '" . $comic->getName() . "'... ");
            $fileManager->setComicsPendingToDownload($comic);
            echo("Done\n");

            $downloader->download($comic);
        }
    }

    echo("All the job is done!\n");
}
