@extends('azure-cognitive-services-ui::layout.kvaksrud')
@section('body')
    <h1 class="mt-5">New face detection</h1>
    <p class="lead">Specify an image to run facial recognition against ACS Face API</p>
    <p>
        <form method="post" action="{{route('acs.face.detection.store')}}">
            @csrf
            <label for="image_url" class="form-label">Image URL</label>
            <input type="text" id="image_url" name="image_url" class="form-control" value="{{Session::get('_old_input.image_url')}}">
            <div id="image_url_help_block" class="form-text">
                The image must be an image hosted by a secure (https) hosting provider with a valid public certificate.
            </div>
            <div class="pt-3">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </p>
@endsection
