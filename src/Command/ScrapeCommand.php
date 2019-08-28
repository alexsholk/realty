<?php

namespace App\Command;

use App\Scraper\OnlinerScraper;
use App\Scraper\ScraperCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ScrapeCommand extends Command
{
    protected static $defaultName = 'realty:scrape';

    private $scraperCollection;

    public function __construct(ScraperCollection $scraperCollection, string $name = null)
    {
        parent::__construct($name);
        $this->scraperCollection = $scraperCollection;
    }

    protected function configure()
    {
        $this->setDescription('Scrape realty websites');
        $this->addArgument('scraper', InputArgument::REQUIRED, 'Scraper');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $scraper = $this->scraperCollection->get($input->getArgument('scraper'));

        $scraper->scrape();
    }
}