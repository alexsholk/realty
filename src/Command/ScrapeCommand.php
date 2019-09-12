<?php

namespace App\Command;

use App\Scraper\Onliner\Rent\ItemScraper;
use App\Scraper\OnlinerScraper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScrapeCommand extends Command
{
    protected static $defaultName = 'realty:scrape';

    protected function configure()
    {
        $this->setDescription('Scrape realty websites');
        $this->addArgument('scraper', InputArgument::REQUIRED, 'Scraper');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $is = new ItemScraper();

        dump($is->getUid());
        die;

        $scraperName = $input->getArgument('scraper');
        switch ($scraperName) {
            case 'onliner':
                $scraper = new OnlinerScraper();
                break;
            default:
                throw new \Exception('Unknown scraper ' . $scraperName);
        }

        $result = $scraper->scrape();

        $path = __DIR__ . '/../../data/onliner/';
        $filename = date('Ymd_His') . '.json';

        mkdir($path, 0755, true);
        file_put_contents($path . $filename, json_encode($result, JSON_PRETTY_PRINT));
    }
}