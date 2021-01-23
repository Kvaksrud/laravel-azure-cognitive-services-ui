<?php

namespace Kvaksrud\AzureCognitiveServices\Ui\App\Http\Controllers;

use Kvaksrud\AzureCognitiveServices\Ui\App\Models\Face;
use Kvaksrud\AzureCognitiveServices\Ui\App\Models\LargePersonGroup;
use Kvaksrud\AzureCognitiveServices\Ui\App\Models\LargePersonGroupPerson;
use Kvaksrud\AzureCognitiveServices\Ui\App\Models\LargePersonGroupTrainingStatus;
use Kvaksrud\AzureCognitiveServices\Ui\App\Models\PersistedFaceId;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Kvaksrud\AzureCognitiveServices\Api\Controllers\AzureFaceController;

class AzureCognitiveServicesFaceLargePersonGroupController extends AzureCognitiveServicesController
{
    public function index(Request $request)
    {
        return view('azure-cognitive-services-ui::face.largePersonGroups.index')->with('largePersonGroups',LargePersonGroup::all() ?? null);
    }

    public function view(Request $request)
    {

    }

    public function create(Request $request)
    {
        return view('azure-cognitive-services-ui::face.largePersonGroups.create');
    }

    public function store(Request $request)
    {
        if($request->exists(['group_id','group_name']) === false)
            return back()->with('error','Group ID and name are required')->withInput();

        $groupId = $request->get('group_id');
        $groupName = $request->get('group_name');
        $userData = $request->get('user_data');
        $recognitionModel = $request->get('recognition_model');

        $azure = new AzureFaceController();
        $lpg = $azure->largePersonGroup()->create($groupId,$groupName);
        if($lpg->hasError() === false)
            return redirect()->route('acs.face.largePersonGroup.index')->with('success','The Large Person Group '.$groupName.' was created successfully');
        return back()->withInput()->with('error',$lpg->getBody()->error->message);
    }

    public function train(Request $request)
    {
        $group = LargePersonGroup::findOrFail($request->route()->parameter('id'));
        if($request->get('training') !== 'start')
            return back()->with('error','Invalid form submission');

        $azure = new AzureFaceController();
        $training = $azure->largePersonGroup()->train($group->largePersonGroupId);
        Log::debug('Training response',[$training->result()]);
        if($training->hasError() === false){
            if($this->updateTrainingStatus($group->id) === null)
                return back()->with('warning','Started training for Large Person group '.$group->name.', but failed to update training status.');
            return back()->with('success','Started training for Large Person group '.$group->name.'.');
        }
        return back()->with('error',$training->getBody()->error->message);
    }

    public function updateTrainingStatus(int $groupId): ?bool
    {
        $group = LargePersonGroup::findOrFail($groupId);
        $azure = new AzureFaceController();
        $trainingStatus = $azure->largePersonGroup()->getTrainingStatus($group->largePersonGroupId);
        if($trainingStatus->hasError() === false){
            $statusBody = $trainingStatus->getBody();
            $status = LargePersonGroupTrainingStatus::updateOrCreate(
                ['large_person_group_id' => $group->id],
                [
                    'status' => $statusBody->status,
                    'createdDateTime' => Carbon::create($statusBody->createdDateTime),
                    'lastActionDateTime' => Carbon::create($statusBody->lastActionDateTime) ?? null,
                    'lastSuccessfulTrainingDateTime' => Carbon::create($statusBody->lastSuccessfulTrainingDateTime) ?? null,
                ]
            );
            if($status){
                if(($status->wasRecentlyCreated or $status->wasChanged()) and $statusBody->status === 'succeeded'){
                    foreach($group->people()->with('persistedFaceIds')->get() as $person){
                        if($person->persistedFaceIds()->where('trained','=',false)->count() === 0)
                            continue;

                        foreach($person->persistedFaceIds()->get() as $pFace){
                            $pFace->trained = true;
                            $pFace->save();
                        }
                    }
                }
                return true;
            }
        } else {
            if($trainingStatus->getBody()->error->code === 'LargePersonGroupNotTrained'){
                LargePersonGroupTrainingStatus::updateOrCreate(
                    ['large_person_group_id' => $group->id],
                    [
                        'status' => 'not trained',
                        'createdDateTime' => Carbon::now(),
                        'lastActionDateTime' => null,
                        'lastSuccessfulTrainingDateTime' => null,
                    ]
                );
                return true;
            }
        }
        return null;
    }

    public function addFaceStoreRequest(Request $request)
    {
        if($request->exists(['face_id','person_id']) === false or $request->get('person_id') === null or $request->get('face_id') === null)
            return back()->with('error','Could not addMissing face id or person id');

        $face = Face::find($request->get('face_id')) ?? null;
        $person = LargePersonGroupPerson::find($request->get('person_id')) ?? null;

        if($face === null)
            return back()->with('error','Face ID is invalid');
        if($person === null)
            return back()->with('error','Person ID is invalid');

        $result = $this->addFaceStore($face,$person);
        if($result->status === 'success') {
            $lpg = new AzureCognitiveServicesFaceLargePersonGroupController();
            $lpg->syncAzure();
            Log::info('Bound face to person',[$result,$face,$person]);
            return back()->with('success', 'Successfully bound face to person');
        }
        Log::info('Failed to bind face to person',[$result,$face,$person]);
        return back()->with('error', 'Failed to bind the face to person. Error: '.$result->data->error->message);
    }

    public function addFaceStore(Face $face, LargePersonGroupPerson $person)
    {
        $azure = new AzureFaceController();

        $imageUrl = $face->detection->image->url;
        $lpgId = $person->largePersonGroup->largePersonGroupId;
        $personId = $person->personId;

        $addFace = $azure->largePersonGroup()->person()->addFace($imageUrl,$lpgId,$personId,$face->asFaceObject());

        if($addFace->hasError() === false){
            $face->persistedFaceId = $addFace->getBody()->persistedFaceId;
            try {
                $face->saveOrFail();
            } catch (\Throwable $e) {
                Log::warning('Failed to sace persistedFaceId to face',[$e]);
            }
            return (object)['status'=>'success','data'=>$addFace];
        }
        return (object)['status'=>'error','data'=>$addFace->getBody()];
    }

    public function delete(Request $request)
    {
        $lpg = LargePersonGroup::findOrFail($request->route()->parameter('id'));
        if($this->destroy($lpg->id))
            return redirect()->route('acs.face.largePersonGroup.index')->with('success','Large Person Group '.$lpg->name.' was deleted successfully');
        return back()->with('error','Unable to delete Large Person Group in Azure');
    }

    public function destroy(string $id)
    {
        $lpg = LargePersonGroup::findOrFail($id);
        $azure = new AzureFaceController();
        $delete = $azure->largePersonGroup()->delete($lpg->largePersonGroupId);
        if($delete->hasApiError() === false and $delete->hasError() === false) {
            $lpg->delete();
            return true;
        }
        return false;
    }

    public function syncAzure(): \Illuminate\Http\RedirectResponse
    {
        $azure = new AzureFaceController();
        $largePersonGroups = $azure->largePersonGroup()->list()->getBody();
        foreach($largePersonGroups as $group){
            Log::debug('sync group '.$group->largePersonGroupId,[$group]);
            $LPG = LargePersonGroup::updateOrCreate(
                ['largePersonGroupId' => $group->largePersonGroupId],
                [
                    'name' => $group->name,
                    'userData' => $group->userData ?? null,
                ]);

            if(!$LPG)
                return back()->with('error','Failed to update or create '.$group->largePersonGroupId);

            if($LPG->traningStatus === null){
                if($this->updateTrainingStatus($LPG->id))
                    Log::debug('updated training status on sync',[$LPG,$LPG->trainingStatus()->get()]);
            }

            $people = $azure->largePersonGroup()->person()->list($group->largePersonGroupId)->getBody();
            if(!$people)
                continue;

            foreach($people as $person){
                $LPGP = LargePersonGroupPerson::updateOrCreate(
                    ['personId' => $person->personId, 'large_person_group_id' => $LPG->id],
                    [
                        //'persistedFaceIds' => $person->persistedFaceIds, // Replaced with own table for this relationship to be compatible for relationship without having extra addons to laravel for json relationships
                        'name' => $person->name,
                        'userData' => $person->userData ?? null,
                    ]
                );
                foreach($person->persistedFaceIds as $pFID){
                    PersistedFaceId::updateOrCreate(
                        ['large_person_group_person_id' => $LPGP->id, 'persistedFaceId' => $pFID]
                    );
                }
            }

        }
        return back()->with('success','Synchronization completed');
    }
}
