<?php

namespace Comicker\Event;


class ComicEventDispatcher implements EventDispatcher
{
    private $listeners;

    public function __construct()
    {
        $this->listeners = [];
    }
    public function addListener(EventListener $listener) {
        $this->listeners[] = $listener;
    }
    public function removeListener(EventListener $listener) {
        foreach($this->listeners as $key => $value) {
            if ($value == $listener) {
                unset($this->listeners[$key]);
            }
        }
    }
    public function dispatch($eventName, Event $event) {
        switch ($eventName){
            case 'comic_downloaded':
                $this->dispatchComicDownloaded($event);
                break;
            default:break;
        }
    }

    private function dispatchComicDownloaded($event){
        foreach ($this->listeners as $listener){
            if($listener instanceof ComicEventListener){
                $listener->onComicDownloaded($event);
            }
        }
    }
}