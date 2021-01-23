<?php

namespace Kvaksrud\AzureCognitiveServices\Ui\App\Console\Commands;

use Illuminate\Console\Command;
use Kvaksrud\AzureCognitiveServices\Ui\App\Http\Controllers\AzureCognitiveServicesFaceLargePersonGroupController;

class AcsSyncLpgFromAzure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acs:synclpgfromazure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $lpg = new AzureCognitiveServicesFaceLargePersonGroupController();
        $lpg->syncAzure();
        return 0;
    }
}
