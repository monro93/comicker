<?php

namespace Comicker\Event;

use Comicker\Entity\Comic;
use Comicker\Entity\ComicChapter;

class TelegramListener implements ComicEventListener
{
    const API_URL = 'https://api.telegram.org/bot';
    /**
     * @var string
     */
    private $botToken;

    /**
     * @var string
     */
    private $chatId;

    public function onComicDownloaded(ComicDownloadedEvent $event = null)
    {
        if(!isset($event))
            return;
        $this->sendComic($event->getComic(), $event->getComicChapter());
    }

    public function __construct($botToken, $chatId)
    {
        $this->botToken = $botToken;
        $this->chatId = $chatId;
    }

    private function sendComic(Comic $comic, ComicChapter $chapter)
    {
        $requestMessage = curl_init(self::API_URL.$this->botToken.'/sendMessage'.
            '?chat_id='.    $this->chatId.
            '&parse_mode='. 'Markdown'.
            '&text='.       'Chapter *'.$chapter->getName().'* of *'. $comic->getName().'* '.$chapter->getChapterUrl()
        );
        curl_exec($requestMessage);
        curl_close($requestMessage);

        $requestDocument = curl_init(self::API_URL.$this->botToken.'/sendDocument');

        curl_setopt($requestDocument, CURLOPT_POST, true);
        curl_setopt(
            $requestDocument,
            CURLOPT_POSTFIELDS,
            array(
                'document'  =>  new \CURLFile(realpath($chapter->getFilePath())),
                'chat_id'   =>  $this->chatId
            ));

        curl_exec($requestDocument);
        curl_close($requestDocument);
    }

}