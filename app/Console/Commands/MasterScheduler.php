<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Trip;
use App\Models\Schedule;
use App\Jobs\ScheduleQueue;
use App\Services\InsertTrip;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Demeter\Crawler\Request\Request;

class MasterScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:master-scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Master Scheduler';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Running master scheduler');
        $trips[] = Schedule::pendingRowsToProcessed();
        $this->info(json_encode($trips));
        $this->info('Pending: '.count($trips));
        $this->running($trips);
        InsertTrip::insert($trips);
        $this->done($trips);
        $this->info('Success');
        return self::SUCCESS;
    }

    /**
     * Execute the console command.
     *
     * @return JsonResponse
     */
 

    /**
     * Execute the console command.
     *
     */
    public function worker()
    {
        Artisan::call('run:master-scheduler');

        return redirect()->back()->with(['success' => 'Scheduler ran successfully']);
    }

    

    /**
     * Execute the console command.
     *
     */
    public function runner()
    {
        return Artisan::call('queue:work', ['--queue' => 'low,default']);
    }

    private function running($trips): void
    {
        Trip::whereIn('id', $trips->pluck('id'))->update(['in_process' => 1]);
    }

    private function done($trips): void
    {
        Trip::whereIn('id', $trips->pluck('id'))->update(['done' => Carbon::now()]);

    }
}
