<?php

namespace App\Scraper\Onliner\Rent;

use App\Scraper\ScraperInterface;

class ItemScraper implements ScraperInterface
{
    const URI = 'https://r.onliner.by/ak/apartments/';

    private $id;

    public function getUid(): string
    {
        return 'onliner-rent-item';
    }

    public function scrape()
    {

    }
}