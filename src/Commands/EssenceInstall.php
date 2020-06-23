<?php namespace Atxy2k\Essence\Commands;

use Illuminate\Console\Command;

class EssenceInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'essence:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate developer role and default user';

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
        //
    }
}
