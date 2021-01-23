@extends('azure-cognitive-services-ui::layout.kvaksrud')
@section('body')
    <h1 class="mt-5">New Large Person Group</h1>
    <p class="lead">Set up a new group to collect facial data and train the AI in to identify with ACS Face API</p>
    <p>
        <form method="post" action="{{route('acs.face.largePersonGroup.store')}}">
            @csrf
            <label for="group_id" class="form-label">Group ID</label>
            <input type="text" id="group_id" name="group_id" class="form-control" value="{{Session::get('_old_input.group_id')}}">
            <div id="group_id_help_block" class="form-text">
                The valid characters include numbers, English letters in lower case, '-' and '_'. The maximum length of the largePersonGroupId is 64.
            </div>

            <label for="group_name" class="form-label">Group Name</label>
            <input type="text" id="group_id" name="group_name" class="form-control" value="{{Session::get('_old_input.group_name')}}">
            <div id="group_name_help_block" class="form-text">
                This is the display name for the group.
            </div>

            <label for="user_data" class="form-label pt-3">User Data</label>
            <input type="text" id="user_data" name="user_data" class="form-control" value="{{Session::get('_old_input.user_data')}}">
            <div id="user_data_help_block" class="form-text">
                User data in JSON format to be stored in Azure. Can be up to 16KB.
            </div>

            <label for="recognition_model" class="form-label pt-3">Recognition Model</label>
            <select class="form-select" id="recognition_model" name="recognition_model" aria-label="Default select example">
                <option value="{{\Kvaksrud\AzureCognitiveServices\Api\Controllers\AzureFaceController::RECOGNITION_03}}">Model 3 (2020 May)</option>
                <option value="{{\Kvaksrud\AzureCognitiveServices\Api\Controllers\AzureFaceController::RECOGNITION_02}}">Model 2 (2019 March)</option>
                <option value="{{\Kvaksrud\AzureCognitiveServices\Api\Controllers\AzureFaceController::RECOGNITION_01}}">Model 1 (before 2019 March)</option>
            </select>
            <div id="recognition_model_help_block" class="form-text">
                Select a recognition model to be used for identifying persons. Newer models offer better accuracy.
            </div>
            <div class="pt-3">
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </p>
@endsection
