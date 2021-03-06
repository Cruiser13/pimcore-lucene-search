<?php

namespace LuceneSearch\Model;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use VDB\Uri\UriInterface;
use VDB\Spider\Event\SpiderEvents;

class Logger implements EventSubscriberInterface
{
    private $debug = false;

    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

    public static function getSubscribedEvents()
    {
        return array(
            SpiderEvents::SPIDER_CRAWL_FILTER_POSTFETCH => 'logFiltered',
            SpiderEvents::SPIDER_CRAWL_FILTER_PREFETCH => 'logFiltered',
            SpiderEvents::SPIDER_CRAWL_POST_ENQUEUE => 'logQueued',
            SpiderEvents::SPIDER_CRAWL_RESOURCE_PERSISTED => 'logPersisted',
            SpiderEvents::SPIDER_CRAWL_ERROR_REQUEST => 'logFailed'
        );
    }

    public function logQueued(GenericEvent $event)
    {
        $this->logEvent('queued', $event);
    }
    public function logPersisted(GenericEvent $event)
    {
        $this->logEvent('persisted', $event);
    }
    public function logFiltered(GenericEvent $event)
    {
        $this->logEvent('filtered', $event);
    }
    public function logFailed(GenericEvent $event)
    {
        $this->logEvent('failed', $event);
    }

    protected function logEvent($name, GenericEvent $event)
    {
        if ($this->debug === true
        ) {
            \Logger::log('LuceneSearch [' . $name . ']: ' . $event->getArgument('uri')->toString() );
        }
    }
}