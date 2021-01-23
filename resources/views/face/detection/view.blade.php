@extends('azure-cognitive-services-ui::layout.kvaksrud')
@section('body')
    <h1 class="mt-5">Detections [<a href="{{route('acs.face.detection.create')}}">+</a>]</h1>
    <p class="lead">This is a list of all detections run against ACS Face API</p>
    <div>
        <h4>ID: {{$detection->id}}</h4>
        <p>
            @if($previous)
                <a href="{{route('acs.face.detection.view',['id'=>$previous])}}">&laquo; Previous</a>
            @endif
            @if($previous and $next)
                |
            @endif
            @if($next)
                    <a href="{{route('acs.face.detection.view',['id'=>$next])}}">Next &raquo;</a>
            @endif
            @if($previous or $next)
                <hr class="small">
            @endif

            @if($detection->faces()->whereNull('persistedFaceId')->count() === 0 and $detection->faces()->count() > 0)
                <span class="badge bg-success small">Bound to person</span>
            @elseif($detection->faces()->count() > 1 and ($detection->faces()->count() - $detection->faces()->whereNull('persistedFaceId')->count()) > 0)
                <span class="badge bg-warning small">Some bound</span>
            @elseif($detection->faces()->whereNull('persistedFaceId')->count() > 0 and $detection->faces()->count() > 0)
                <span class="badge bg-danger small">Not bound</span>
            @else
                <span class="badge bg-info small">No faces</span>
            @endif<br />
            Time: {{$detection->created_at}}<br />
            Faces: {{$detection->faces()->count() ?? "none"}}<br />
            Image present: @if($detection->image) Yes | <a href="{{$detection->image->url}}" target="_blank">Link</a> @else No @endif
        </p>
        @if($detection->image)
        <div class="row">
            <div class="col-sm-6 text-center">
                <img src="{{route('acs.face.detection.viewImage',['id'=>$detection->id,'image'=>'detection'])}}">
            </div>
            <div class="col-sm-6 text-center">
                <img src="{{route('acs.face.detection.viewImage',['id'=>$detection->id,'image'=>'original'])}}">
            </div>
        </div>
        @endif
        @if($detection->faces)
        <form method="post" action="{{route('acs.face.detection.identify',['id' => $detection->id])}}">
            @csrf
        <div class="row pt-3">
            <div class="col-4">
                <select class="form-select" name="group_id">
                    @if($groups)
                        @foreach($groups as $group)
                            <option value="{{$group->id}}">{{$group->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-2">
                <button name="identify" value="start" type="submit" class="btn btn-primary">Identify using AI</button>
            </div>

        </div>
        </form>
        <h4 class="pt-4">Faces</h4>
        <div>
            @foreach($detection->faces()->get() as $face)
                <div class="row pb-2">
                    <div class="col-5">
                        FaceId: {{$face->faceId}}<br />
                        Color: <span style="color: {{$face->color}};">{{$face->color}}</span> ({{$face->color}})<br />
                        @if($face->candidate)AI Identified this to be: {{$face->candidate->person->name}}@endif
                        @if($face->persistedFaceId)Persisted Face ID <span title="Persisted means that this face is attached to a person">(?)</span>: {{$face->persistedFaceId}}<br />
                        Name: {{$face->persistedFace->largePersonGroupPerson->name}}<br /> @endif
                    </div>
                    <div class="col-7">
                        @if(!$face->persistedFaceId)<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFaceToPersonModal" data-bs-id="{{$face->id}}" data-bs-faceId="{{$face->faceId}}">Add face to Person</button>@endif
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>


    <div class="modal fade" id="addFaceToPersonModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{route('acs.face.largePersonGroupPerson.addFace.store')}}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">New message</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="face_id" class="col-form-label">Face id in database:</label>
                            <input type="text" class="form-control" id="face_id" name="face_id">
                        </div>
                        <div class="mb-3">
                            <label for="message-text" class="col-form-label">Person to add to:</label>
                            <select class="form-select" aria-label="select" name="person_id">
                                <option value="">Select a person from this list</option>
                                @foreach($people as $person)
                                <option value="{{$person->id}}">{{$person->name}} (Group: )</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var aFTPM = document.getElementById('addFaceToPersonModal')
        aFTPM.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget
            var id = button.getAttribute('data-bs-id')
            var faceId = button.getAttribute('data-bs-faceId')
            var modalTitle = aFTPM.querySelector('.modal-title')
            var modalBodyInput = aFTPM.querySelector('#face_id')
            modalTitle.textContent = 'Add faceId ' + faceId + ' to person'
            modalBodyInput.value = id
        })
    </script>
@endsection
