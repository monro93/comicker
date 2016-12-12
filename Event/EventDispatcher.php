<?php

namespace Comicker\Event;

interface EventDispatcher {
    function addListener(EventListener $listener);
    function removeListener(EventListener $listener);
    function dispatch($eventName, Event $event);
}