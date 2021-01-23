@extends('azure-cognitive-services-ui::layout.kvaksrud')
@section('body')
    <h1 class="mt-5">Detections [<a href="{{route('acs.face.detection.create')}}">+</a>]</h1>
    <p class="lead">This is a list of all detections run against ACS Face API</p>
    @if ($detections !== null)
        @foreach($detections as $detection)
            <div class="row">
                <div class="col-sm-3">
                    <h4>ID: <a href="{{route('acs.face.detection.view',['id' => $detection->id])}}">{{$detection->id}}</a></h4>
                    <p>
                        @if($detection->faces()->whereNull('persistedFaceId')->count() === 0)<span class="badge bg-success small">Bound to person</span>@endif<br />
                        Time: {{$detection->created_at}}<br />
                        Faces: {{$detection->faces()->count() ?? "none"}}<br />
                        Image present: @if($detection->image()) Yes @else No @endif
                    </p>
                </div>
                <div class="col-sm-9">
                    <a href="{{route('acs.face.detection.view',['id' => $detection->id])}}"><img border="0" src="{{route('acs.face.detection.viewImage',['id'=>$detection->id,'image'=>'original','maxHeight'=>100])}}"></a>
                </div>
            </div>
        @endforeach
    @else
        <p><em>You have not run any detection yet</em></p>
    @endif
@endsection
