<?php

namespace App\Jobs;

use App\Navica;
use App\Listing;
use App\Photo;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdatePhotos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mlsNumbers;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mlsNumbers)
    {
        $this->mlsNumbers = $mlsNumbers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mlsNumbers = [];

        Listing::whereIn('mls_acct',$this->mlsNumbers)->chunk(1500, function ($listings) use (&$mlsNumbers) {
            foreach ($listings as $listing) {
                $mlsNumbers[$listing->id] = $listing->mls_acct;
            }
        });

        if(count($mlsNumbers) > 0) {
            (new Photo)->fullUpdate($mlsNumbers);
        }

    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['updaters', 'photos'];
    }
}
