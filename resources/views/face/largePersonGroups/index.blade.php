@extends('azure-cognitive-services-ui::layout.kvaksrud')
@section('body')
    <h1 class="mt-5">Large Person Groups [<a href="{{route('acs.face.largePersonGroup.create')}}">+</a>]</h1>
    <p class="lead">This is a list of all large person groups in Azure</p>
    <form method="post" action="{{route('acs.face.largePersonGroup.sync')}}">@csrf<button class="btn btn-primary" name="syncAzure" type="submit" value="start">Sync with Azure</button></form>

    @if($largePersonGroups !== null)
    <div class="accordion pt-4" id="accordionLGP">
    @foreach($largePersonGroups as $group)
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading{{$group->id}}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$group->id}}" aria-expanded="false" aria-controls="collapse{{$group->id}}">
                    {{$group->name}}
                </button>
            </h2>
            <div id="collapse{{$group->id}}" class="accordion-collapse collapse" aria-labelledby="heading{{$group->id}}" data-bs-parent="#accordionLGP">
                <div class="accordion-body">
                    <div>
                        @if($group->isTeachable() and ($group->trainingStatus->status === 'succeeded' or $group->trainingStatus->status === 'failed' or $group->trainingStatus->status === null or $group->trainingStatus->status === 'not trained'))<form method="post" action="{{route('acs.face.largePersonGroup.train',['id'=>$group->id])}}">@csrf<button type="submit" name="training" value="start" class="btn btn-success" style="float: right">Train AI</button></form>@else <span class="badge @if($group->trainingStatus->status === 'succeeded') bg-success @else bg-info @endif " style="float: right">Training status: {{$group->trainingStatus->status}}</span> @endif
                        Azure ID: {{$group->largePersonGroupId}} <span class="small">[<a href="{{route('acs.face.largePersonGroup.delete',[$group->id])}}">delete</a>]</span><br />
                        Database ID: {{$group->id}}
                    </div>
                    @if($group->people()->count() > 0)
                    <div class="pt-2">
                        <h5>People <a href="{{route('acs.face.largePersonGroupPerson.create',['id'=>$group->id])}}">[+]</a></h5>
                        @foreach($group->people()->get() as $person)
                        <div class="row pb-2">
                            <div class="col-sm-6">
                                <h6 class="pb-0 mb-0">Name: {{$person->name}}</h6>
                                Azure Person ID: {{$person->personId}}<br />
                                Persisted faces: {{$person->persistedFaceIds()->count()}}@if($person->persistedFaceIds()->count() > 0)<br />

                                @foreach($person->persistedFaceIds()->get() as $faceId)
                                    <a href="{{route('asc.redirect.persistedFaceIdToDetection',['id'=>$faceId->persistedFaceId])}}">{{$faceId->persistedFaceId}}</a> <span class="badge @if($faceId->trained === true) bg-success @else bg-warning @endif ">@if($faceId->trained === true) Trained @else Not trained @endif</span><br />
                                @endforeach @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                        <h5 class="text-muted small pt-1">This group has no people associated with it. <a href="{{route('acs.face.largePersonGroupPerson.create',['id'=>$group->id])}}">Create one!</a></h5>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
    </div>
    @else
    <div class="alert alert-primary mt-5" role="alert">
        <h4 class="alert-heading">You have no Large Person Groups</h4>
        <p>It does not seem you have any Large Person Groups. If this is unexpected, try syncronizing from Azure with the button above, or create a new Large Person Group</p>
    </div>
    @endif


@endsection
