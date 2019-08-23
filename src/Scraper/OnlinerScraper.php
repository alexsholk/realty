<?php

namespace App\Scraper;

use GuzzleHttp\Client;

class OnlinerScraper
{
    protected $scrapeDirectory;

    public function __construct($scrapeDirectory)
    {
        $this->scrapeDirectory = $scrapeDirectory;
    }

    public function scrape()
    {
        $scrapeDate = date('Ymd_His');
        $path = $this->scrapeDirectory . '/onliner_a/' . $scrapeDate;
        mkdir($path, 0755, true);

        $client = new Client([
            'base_uri' => 'https://ak.api.onliner.by/',
            'headers' => [
                'Accept' => 'application/json, text/plain, */*',
                'Referrer' => 'https://r.onliner.by/pk/',
                'Sec-Fetch-Mode' => 'cors',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36',
            ]
        ]);

        $query = [
            // Minsk
//            'bounds' => [
//                'lb' => ['lat' => 53.70158461260564, 'long' => 27.235107421875004],
//                'rt' => ['lat' => 54.093630810050485, 'long' => 27.888793945312504],
//            ],
            // Centre
            'bounds' => [
                'lb' => ['lat' => 53.882841598548, 'long' => 27.52055614142163],
                'rt' => ['lat' => 53.92549729493005, 'long' => 27.591689537821342],
            ],
            'page' => 1,
        ];

        file_put_contents($path . '/query.json', \GuzzleHttp\json_encode($query, JSON_PRETTY_PRINT));
        do {
            $response = $client->request('GET', 'search/apartments', [
                'query' => $query,
            ]);

            if ($response->getStatusCode() != 200) {
                dump($response->getHeaders());
                dump($response->getReasonPhrase());
                return;
            }

            $content = $response->getBody()->getContents();
            $data = \GuzzleHttp\json_decode($content, true);

            file_put_contents($path . '/page_' . $query['page'] . '.json', \GuzzleHttp\json_encode($data, JSON_PRETTY_PRINT));

            $query['page']++;
        } while (
            $response->getStatusCode() == 200
            && $query['page'] <= $data['page']['last'] ?? null
        );
    }
}