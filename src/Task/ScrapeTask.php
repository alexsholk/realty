<?php

namespace App\Task;

use App\Scraper\ScraperConfigurationInterface;

class ScrapeTask implements TaskInterface
{
    /** @var ScraperConfigurationInterface */
    private $scraperConfiguration;

    public function __construct(ScraperConfigurationInterface $scraperConfiguration)
    {
        $this->scraperConfiguration = $scraperConfiguration;
    }

    public function run()
    {
        $this->scraperConfiguration->getScraper()->scrape();
    }
}