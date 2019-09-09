<?php

namespace App\Scraper;

interface ScraperInterface
{
    public function getUid(): string;

    public function scrape();
}