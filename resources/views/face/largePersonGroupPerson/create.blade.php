@extends('azure-cognitive-services-ui::layout.kvaksrud')
@section('body')
    <h1 class="mt-5">New Large Person Group Person</h1>
    <p class="lead">Create a new person that you can attach facial recognition pointers to for the ACS Face API</p>
    <p>
        <form method="post" action="{{route('acs.face.largePersonGroupPerson.store',['id'=>$group->id])}}">
            @csrf
            <input type="hidden" name="group_id" value="{{$group->id}}">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" class="form-control" value="{{Session::get('_old_input.name')}}">
            <div id="name_help_block" class="form-text">
                The name of the person. Use a descriptive name, for instance, their full name.
            </div>

            <label for="user_data" class="form-label pt-3">User Data</label>
            <input type="text" id="user_data" name="user_data" class="form-control" value="{{Session::get('_old_input.user_data')}}">
            <div id="user_data_help_block" class="form-text">
                User data in JSON format to be stored in Azure. Can be up to 16KB.
            </div>

            <div class="pt-3">
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </p>
@endsection
