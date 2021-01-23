<?php

namespace Kvaksrud\AzureCognitiveServices\Ui\App\Console\Commands;

use Kvaksrud\AzureCognitiveServices\Ui\App\Http\Controllers\AzureCognitiveServicesFaceLargePersonGroupController;
use Kvaksrud\AzureCognitiveServices\Ui\App\Models\LargePersonGroupTrainingStatus;
use Illuminate\Console\Command;

class AcsLpgTrainingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acs:updateLpgTrainingStatus {force?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command updates training status on all LPG\'s pending an update or not in a successful state';

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

        $lpgTs = LargePersonGroupTrainingStatus::with('LargePersonGroup');

        if($this->argument('force') === null) // Force parameter
            $lpgTs = $lpgTs->where('status','NOT LIKE','succeeded');

        if($lpgTs->count() === 0)
            return 0;
        foreach($lpgTs->get() as $lpgTsObject){
            $lpgController = new AzureCognitiveServicesFaceLargePersonGroupController();
            $lpgController->updateTrainingStatus($lpgTsObject->largePersonGroup->id);
        }
        return 0;
    }
}
