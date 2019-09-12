<?php

namespace App\Scraper;

interface ScraperConfigurationInterface
{
    /**
     * Get configured scraper
     *
     * @return ScraperInterface
     */
    public function getScraper(): ScraperInterface;
}