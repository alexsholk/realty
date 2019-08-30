<?php

namespace App\Scraper;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractScraper
{
    protected $dispatcher;

    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    abstract public function getName();

    abstract public function doScrape();

    final public function scrape()
    {
        dump($this->getName() . ' launched');
        $this->doScrape();
    }
}