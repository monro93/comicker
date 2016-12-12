<?php

namespace Comicker\Event;


interface ComicEventListener extends EventListener
{
    public function onComicDownloaded(ComicDownloadedEvent $event = null);
}