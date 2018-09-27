<?php
namespace App;

use PHRETS\Session;
use App\Contracts\RETS;
use PHRETS\Configuration;

class Navica extends Association implements RETS {
    const CLASSES = ['COMM', 'Land', 'Rental', 'RESI'];
    const QUERY_OPTIONS = [
        'QueryType' => 'DMQL2',
        'Count' => 1, // count and records
        'Format' => 'COMPACT-DECODED',
        'Limit' => 9999,
        'StandardNames' => 0, // give system names
    ];
    const LOOKUP = [
        'Int' => 'integer',
        'Character' => 'string',
        'Boolean' => 'boolean',
        'Decimal' => 'decimal',
        'Date' => 'date',
        'DateTime' => 'dateTime'
    ];

    public function __construct()
    {
        $this->url      = config('navica.url');
        $this->username = config('navica.username');
        $this->password = config('navica.password');
    }

    public function connect()
    {
        $config = new Configuration();
        $config->setLoginUrl($this->url)
            ->setUsername($this->username)
            ->setPassword($this->password)
            ->setRetsVersion('1.7.2')
            ->setOption("compression_enabled", true)
            ->setOption("offset_support", true);
        $this->rets = new Session($config);
        $this->rets->Login();
        return $this;
    }

    public function getTableMetadata($class)
    {
        return $this->rets->GetTableMetadata('Property', $class);
    }

    public function buildListings($resource, $class)
    {
        $offset = 0;
        $maxRowsReached = false;
        while (!$maxRowsReached) {
            $options = self::QUERY_OPTIONS;
            $options['Offset'] = $offset;
            $results = $this->rets->Search('Property', $class, 'sys_Last_Modified=2010-01-01+', $options);
            echo '---------------------------------------------------------' . PHP_EOL;
            echo 'Class: ' . $class . PHP_EOL;
            echo 'Returned Results: ' . $results->getReturnedResultsCount() . PHP_EOL;
            echo 'Total Results: ' . $results->getTotalResultsCount() . PHP_EOL;
            echo 'Offset before this batch: ' . $offset . PHP_EOL;
            foreach ($results as $result) {
                $resource::updateOrCreate(['MST_MLS_NUMBER' => $result['MST_MLS_NUMBER']], $result->toArray());
            }
            $offset += $results->getReturnedResultsCount();
            echo 'Offset after this batch: ' . $offset . PHP_EOL;
            if ($offset >= $results->getTotalResultsCount()) {
                echo 'Final Offset: ' . $offset . PHP_EOL;
                $maxRowsReached = true;
            }
        }
    }

    public function buildPhotos()
    {
        $mlsNumbers = Listing::pluck('mls_acct');
        foreach ($mlsNumbers as $mlsNumber)  {
            $photos = $this->rets->GetObject('Property', 'Photo', '25532', '*', 1);
            foreach ($photos as $photo) {
                dd(base64_decode($photo->getContent()));
            }

        }

        dd($photos->first());
    }

    public function getMLSList()
    {
        return;
    }
}