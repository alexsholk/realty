<?php

namespace App\Scraper;

class ScraperCollection
{
    private $scrapers = [];

    public function __construct(iterable $scrapers)
    {
        $this->scrapers = $scrapers;
    }

    public function get($name)
    {
        foreach ($this->scrapers as $scraper) {
            if ($scraper->getName() === $name) {
                return $scraper;
            }
        }

        throw new \Exception("Scraper not defined ($name).");
    }
}