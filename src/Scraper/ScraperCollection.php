<?php

namespace App\Scraper;

class ScraperCollection
{
    private $scrapers;

    public function __construct(iterable $scrapers)
    {

        foreach ($scrapers as $key => $scraper) {

            dump($key);
            die(';---');

            $this->scrapers[$scraper->getName()] = $scraper;
        }
    }

    /**
     * @param string $name
     * @return AbstractScraper
     * @throws \Exception
     */
    public function get(string $name): AbstractScraper
    {
        if (!isset($this->scrapers[$name])) {
            throw new \Exception("Scraper not defined ($name).");
        }

        return $this->scrapers[$name];
    }
}