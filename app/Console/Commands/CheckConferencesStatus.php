<?php

namespace App\Console\Commands;

use App\Models\Conference;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

class CheckConferencesStatus extends Command
{
    protected $signature = 'conferences:deactivate';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        Conference::query()
            ->where('scheduled_date', '<', Carbon::today())
            ->update(['active' => false]);
    }
}
