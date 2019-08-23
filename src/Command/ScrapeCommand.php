<?php

namespace App\Command;

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
        /** @todo Run real web-scraper */
        $output->writeln("You choose {$input->getArgument('scraper')} scraper.");
    }
}