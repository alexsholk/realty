<?php


namespace App\Scraper\Onliner\Rent;

use App\Scraper\ScraperInterface;

class GeneralScraper implements ScraperInterface
{
    public function getUid(): string
    {
        return 'onliner-rent-general';
    }

    public function scrape()
    {

    }
}