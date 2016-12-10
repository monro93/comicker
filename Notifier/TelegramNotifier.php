<?php

namespace Comicker\Notifier;


use Comicker\Entity\ComicChapter;
use Comicker\Entity\Comic;

class TelegramNotifier
{
    const API_URL = 'https://api.telegram.org/bot';
    private $botToken;

    /**
     * @todo It should read the ID from the /start command, but for that It has to be a service and should allow multiuser
     */
    private $chatId;

    public function __construct($botToken, $chatId)
    {
        $this->botToken = $botToken;
        $this->chatId = $chatId;
    }

    public function sendComic(Comic $comic, ComicChapter $chapter)
    {
        $requestMessage = curl_init(self::API_URL.$this->botToken.'/sendMessage'.
            '?chat_id='.    $this->chatId.
            '&parse_mode='. 'Markdown'.
            '&text='.       'Chapter *'.$chapter->getName().'* of *'. $comic->getName().'* '.$chapter->getChapterUrl()
        );
        curl_exec($requestMessage);
        curl_close($requestMessage);

        $requestDocument = curl_init(self::API_URL.$this->botToken.'/sendDocument');

        // send a file
        curl_setopt($requestDocument, CURLOPT_POST, true);
        curl_setopt(
            $requestDocument,
            CURLOPT_POSTFIELDS,
            array(
                'document'  =>  '@' . realpath($chapter->getFilePath()),
                'chat_id'   =>  $this->chatId
            ));

        curl_exec($requestDocument);
        @curl_close($requestDocument);
    }

}