<?php

namespace App\Console\Commands;

use App\Http\Controllers\CronjobController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class TaskReportingSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autoReportingSystem';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'auto reporting system';

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
     * @return mixed
     */
    public function handle()
    {
        (new CronjobController())->taskReportingSystem();
    }
}
