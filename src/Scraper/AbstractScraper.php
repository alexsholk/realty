<?php

namespace App\Scraper;

abstract class AbstractScraper
{
    abstract public static function getDefaultIndexName(): string;

    abstract public function scrape();
}