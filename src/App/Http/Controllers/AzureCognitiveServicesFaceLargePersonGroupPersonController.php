<?php

namespace Kvaksrud\AzureCognitiveServices\Ui\App\Http\Controllers;

use Illuminate\Http\Request;
use Kvaksrud\AzureCognitiveServices\Api\Controllers\AzureFaceController;
use Kvaksrud\AzureCognitiveServices\Ui\App\Models\Face;
use Kvaksrud\AzureCognitiveServices\Ui\App\Models\LargePersonGroup;

class AzureCognitiveServicesFaceLargePersonGroupPersonController extends AzureCognitiveServicesFaceLargePersonGroupController
{
    public function create(Request $request)
    {
        $group = LargePersonGroup::findOrFail($request->route()->parameter('id'));
        return view('azure-cognitive-services-ui::face.largePersonGroupPerson.create')->with('group',$group);
    }

    public function store(Request $request)
    {
        if($request->exists('name') === false)
            return back()->withInput()->with('error','Name is required');

        $group = LargePersonGroup::findOrFail($request->get('group_id'));

        $azure = new AzureFaceController();
        $person = $azure->largePersonGroup()->person()->create($group->largePersonGroupId,$request->get('name'));
        if($person->hasError() === false){
            $lpg = new AzureCognitiveServicesFaceLargePersonGroupController();
            $lpg->syncAzure();
            return redirect()->route('acs.face.largePersonGroup.index')->with('success','Successfully created person '.$request->get('name'));
        }
        return back()->withInput()->with('error',$person->getBody()->error->message);
    }

    public function persistedFaceIdRedirectToDetection(Request $request): \Illuminate\Http\RedirectResponse
    {
        $face = Face::where('persistedFaceId','=',$request->route()->parameter('id'))->firstOrFail();
        return redirect()->route('acs.face.detection.view',['id'=>$face->detection->id]);
    }
}
