<?php

namespace App\Command;

use App\Scraper\OnlinerScraper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScrapeCommand extends Command
{
    protected static $defaultName = 'realty:scrape';

    protected $projectDir;

    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Scrape realty websites');
        $this->addArgument('scraper', InputArgument::REQUIRED, 'Scraper');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $scraperName = $input->getArgument('scraper');
        switch ($scraperName) {
            case 'onliner':
                (new OnlinerScraper($this->projectDir . '/data'))->scrape();
                break;
            default:
                throw new \Exception('Unknown scraper ' . $scraperName);
        }
    }
}