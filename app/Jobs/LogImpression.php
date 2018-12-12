<?php

namespace App\Jobs;

use App\Listing;
use App\Impression;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class LogImpression implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $listings;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($listings)
    {
        $this->listings = $listings;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $today = Carbon::now()->toDateString();

        $click = Click::where('listing_id', $this->listing->id)
            ->where('date', $today)->first();

        if (count($click) > 0) {
            $click->increment('counter');
        } else {
            Click::create([
                'listing_id' => $this->listing->id,
                'date'       => $today,
                'counter'    => 1
            ]);
        }
    }
}
