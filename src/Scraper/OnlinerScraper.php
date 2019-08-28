<?php

namespace App\Scraper;

use GuzzleHttp\Client;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OnlinerScraper extends AbstractScraper
{
    protected $dispatcher;

    protected static $counter = 0;

    protected $scrapeDirectory;
    protected $client;

    public static function getDefaultIndexName(): string
    {
        return 'onliner';
    }

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;

//        $scrapeDirectory =
//        $this->scrapeDirectory = $scrapeDirectory;
        $this->client = new Client([
            'base_uri' => 'https://ak.api.onliner.by/',
            'headers' => [
                'Accept' => 'application/json, text/plain, */*',
                'Referrer' => 'https://r.onliner.by/pk/',
                'Sec-Fetch-Mode' => 'cors',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36',
            ]
        ]);
    }

    public function scrape()
    {
        die('scrape launched!');

        return;
        $scrapeDate = date('Ymd_His');
        $path = $this->scrapeDirectory . '/onliner_a/' . $scrapeDate;
        mkdir($path, 0755, true);

        $query = [
            'bounds' => [
                // Minsk
//                'lb' => ['lat' => 53.70158461260564, 'long' => 27.235107421875004],
//                'rt' => ['lat' => 54.093630810050485, 'long' => 27.888793945312504],
                // Centre
//                'lb' => ['lat' => 53.882841598548, 'long' => 27.52055614142163],
//                'rt' => ['lat' => 53.92549729493005, 'long' => 27.591689537821342],
                // No limits
                'lb' => ['lat' => -90, 'long' => -180],
                'rt' => ['lat' => 90, 'long' => 180],
            ],
            'page' => 1,
        ];

        file_put_contents($path . '/query.json', \GuzzleHttp\json_encode($query, JSON_PRETTY_PRINT));
        do {
            $response = $this->client->request('GET', 'search/apartments', [
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

    public function scrapeTessellate()
    {
        $scrapeDate = date('Ymd_His');
        $path = $this->scrapeDirectory . '/onliner_tess/' . $scrapeDate;
        mkdir($path, 0755, true);


        // All world
        $bounds_array = $this->getBoundsTessellationUniform();

        $apartments = [];
        foreach ($bounds_array as $bounds) {
            $apartments = array_merge($apartments, $this->scrapeArea($bounds['lb'], $bounds['rt'], $path));
        }

        $data = [
            'total' => count($apartments),
            'apartments' => $apartments,
        ];
        file_put_contents($path . '/apartments.json', \GuzzleHttp\json_encode($data, JSON_PRETTY_PRINT));
    }

    protected function splitBounds($lb, $rt, int $horiz = 2, int $vert = 2)
    {
        $height = abs($rt['lat'] - $lb['lat']) / $vert;
        $width = abs($rt['long'] - $lb['long']) / $horiz;

        $bounds = [];
        for ($i = 0; $i < $vert; $i++) {
            for ($j = 0; $j < $horiz; $j++) {
                $bounds[] = [
                    'lb' => [
                        'lat' => $lb['lat'] + $height * $i,
                        'long' => $lb['long'] + $width * $j
                    ],
                    'rt' => [
                        'lat' => $lb['lat'] + $height * ($i + 1),
                        'long' => $lb['long'] + $width * ($j + 1),
                    ],
                ];
            }
        }
//
//        dump([$lb]);
//        dump([$rt]);
//        dump($bounds);
//        echo "-----------------------\n";
        return $bounds;
    }

    protected function scrapeArea($lb, $rt, $path)
    {
        $apartments = [];

        $query = [
            'bounds' => ['lb' => $lb, 'rt' => $rt],
            'page' => 1,
        ];

        $data = $this->request($query);

        $datac = ['lb' => $lb, 'rt' => $rt] + $data;

        $mt = self::$counter++;
        file_put_contents($path . "/datalog_$mt.json", \GuzzleHttp\json_encode($datac, JSON_PRETTY_PRINT));
        // return []; use for test mode!

        $can_parse_all = $data['total'] <= $data['page']['limit'] * $data['page']['last'];
        if (!$can_parse_all) {
            foreach ($this->splitBounds($lb, $rt) as $bound) {
                $apartments = array_merge($apartments, $this->scrapeArea($bound['lb'], $bound['rt'], $path));
            };
            return $apartments;
        }

        $apartments = $data['apartments'];
        for ($i = 2; $i <= $data['page']['last']; $i++) {
            $query['page']++;
            $data = $this->request($query);
            $apartments = array_merge($apartments, $data['apartments']);
        }

        return $apartments;
    }

    protected function request($query)
    {
        $query['v'] = $this->JsMathRandomEquivalent();

        try {
            $response = $this->client->request('GET', 'search/apartments', [
                'query' => $query,
            ]);
        } catch (\Exception $exception) {
            dump($query);
            dump($response ?? null);
            throw $exception;
        }

        if ($response->getStatusCode() != 200) {
            dump($response->getHeaders());
            dump($response->getReasonPhrase());
            die;
        }

        return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
    }

    protected function getBoundsAllWorld()
    {
        return [
            [
                'lb' => ['lat' => -90, 'long' => -180],
                'rt' => ['lat' => 90, 'long' => 180],
            ]
        ];
    }

    protected function getBoundsTessellation1()
    {
        $bounds_array[] = [
            'lb' => ['lat' => -90.0, 'long' => -180.0],
            'rt' => ['lat' => 54.093630810050485, 'long' => 27.235107421875004],
        ];
        $bounds_array[] = [
            'lb' => ['lat' => 54.093630810050485, 'long' => -180.0],
            'rt' => ['lat' => 90.0, 'long' => 27.888793945312504],
        ];
        $bounds_array[] = [
            'lb' => ['lat' => 53.70158461260564, 'long' => 27.888793945312504],
            'rt' => ['lat' => 90.0, 'long' => 180.0],
        ];
        $bounds_array[] = [
            'lb' => ['lat' => -90, 'long' => 27.235107421875004],
            'rt' => ['lat' => 53.70158461260564, 'long' => 180.0],
        ];
        $bounds_array[] = [
            // Minsk
            'lb' => ['lat' => 53.70158461260564, 'long' => 27.235107421875004],
            'rt' => ['lat' => 54.093630810050485, 'long' => 27.888793945312504],
        ];
        return $bounds_array;
    }

    protected function getBoundsTessellation2()
    {
        $center = [
            'lat' => 53.897607711328064,
            'long' => 27.561950683593754,
        ];

        // Split world in 4 pieces by (approximately) center of Minsk
        $bounds_array = [];
        $bounds_array[] = [
            'lb' => ['lat' => -90.0, 'long' => -180.0],
            'rt' => ['lat' => $center['lat'], 'long' => $center['long']],
        ];
        $bounds_array[] = [
            'lb' => ['lat' => $center['lat'], 'long' => -180.0],
            'rt' => ['lat' => 90.0, 'long' => $center['long']],
        ];
        $bounds_array[] = [
            'lb' => ['lat' => $center['lat'], 'long' => $center['long']],
            'rt' => ['lat' => 90.0, 'long' => 180.0],
        ];
        $bounds_array[] = [
            'lb' => ['lat' => -90, 'long' => $center['long']],
            'rt' => ['lat' => $center['lat'], 'long' => 180.0],
        ];
        return $bounds_array;
    }

    protected function getBoundsTessellation3()
    {
        $point_lat = 53.9104; // value that bottom and top pieces of world have near equal count of flats

        // Split world in 2 pieces by horizontal
        $bounds_array = [];
        $bounds_array[] = [
            'lb' => ['lat' => -90.0, 'long' => -180.0],
            'rt' => ['lat' => $point_lat, 'long' => 180.0],
        ];
        $bounds_array[] = [
            'lb' => ['lat' => $point_lat, 'long' => -180.0],
            'rt' => ['lat' => 90.0, 'long' => 180.0],
        ];
        return $bounds_array;
    }

    protected function getBoundsTessellation4()
    {
        // Tessellate top piece of the world
        // Смолячкова 7 (Краснозвездная 9)
        $point_lat = 53.9104; // --- must be equal \/
        $point_long = 27.5845;

        // Split world in 2 pieces by horizontal
        $bounds_array = [];
        $bounds_array[] = [
            'lb' => ['lat' => $point_lat, 'long' => -180.0],
            'rt' => ['lat' => 90.0, 'long' => $point_long],
        ];
        $bounds_array[] = [
            'lb' => ['lat' => $point_lat, 'long' => $point_long],
            'rt' => ['lat' => 90.0, 'long' => 180.0],
        ];
        return $bounds_array;
    }

    protected function getBoundsTessellation5()
    {
        // Tessellate bottom piece of the world
        // Аллея Праведников мира
        $point_lat = 53.9104; // --- must be equal ^^^
        $point_long = 27.5420;

        // Split world in 2 pieces by horizontal
        $bounds_array = [];
        $bounds_array[] = [
            'lb' => ['lat' => -90.0, 'long' => -180.0],
            'rt' => ['lat' => $point_lat, 'long' => $point_long],
        ];
        $bounds_array[] = [
            'lb' => ['lat' => -90.0, 'long' => $point_long],
            'rt' => ['lat' => $point_lat, 'long' => 180.0],
        ];
        return $bounds_array;
    }

    protected function getBoundsTessellationUniform()
    {
        // Split world in 4 piece with approximately equal count of flats
        $bounds_array = array_merge($this->getBoundsTessellation4(), $this->getBoundsTessellation5());
        return $bounds_array;
    }

    protected function JsMathRandomEquivalent()
    {
        return (float)rand() / (float)getrandmax();
    }
}