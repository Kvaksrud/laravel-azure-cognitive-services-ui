<?php

use Kvaksrud\AzureCognitiveServices\Ui\App\Http\Controllers\AzureCognitiveServiceFaceController;
use Kvaksrud\AzureCognitiveServices\Ui\App\Http\Controllers\AzureCognitiveServicesFaceLargePersonGroupController;
use Kvaksrud\AzureCognitiveServices\Ui\App\Http\Controllers\AzureCognitiveServicesFaceLargePersonGroupPersonController;

// Azure Cognitive Services
Route::get('/acs',function(){
    return redirect()->route('acs.face');
})->name('acs');
Route::get('/acs/face',function(){
    return redirect()->route('acs.face.detection.index');
})->name('acs.face');

// Detect
Route::get('/acs/face/detections',[AzureCognitiveServiceFaceController::class,'index'])->name('acs.face.detection.index');
Route::get('/acs/face/detection/{id}/images/{image}',[AzureCognitiveServiceFaceController::class,'viewImage'])->name('acs.face.detection.viewImage')->whereNumber('id')->where('image','original|detection');
Route::get('/acs/face/detection/{id}',[AzureCognitiveServiceFaceController::class,'view'])->name('acs.face.detection.view')->whereNumber('id');
Route::get('/acs/face/detection/create',[AzureCognitiveServiceFaceController::class,'create'])->name('acs.face.detection.create');
Route::post('/acs/face/detection',[AzureCognitiveServiceFaceController::class,'store'])->name('acs.face.detection.store');
Route::post('/acs/face/detection/{id}/identify',[AzureCognitiveServiceFaceController::class,'identify'])->name('acs.face.detection.identify')->whereNumber('id');


// LargePersonGroup
Route::get('/acs/face/largePersonGroups',[AzureCognitiveServicesFaceLargePersonGroupController::class,'index'])->name('acs.face.largePersonGroup.index');
Route::post('/acs/face/largePersonGroups',[AzureCognitiveServicesFaceLargePersonGroupController::class,'syncAzure'])->name('acs.face.largePersonGroup.sync');
Route::get('/acs/face/largePersonGroup/{id}/delete',[AzureCognitiveServicesFaceLargePersonGroupController::class,'delete'])->name('acs.face.largePersonGroup.delete')->whereNumber('id');
Route::get('/acs/face/largePersonGroup/{id}',[AzureCognitiveServicesFaceLargePersonGroupController::class,'view'])->name('acs.face.largePersonGroup.view')->whereNumber('id');
Route::post('/acs/face/largePersonGroup/{id}',[AzureCognitiveServicesFaceLargePersonGroupController::class,'train'])->name('acs.face.largePersonGroup.train')->whereNumber('id');
Route::get('/acs/face/largePersonGroup/create',[AzureCognitiveServicesFaceLargePersonGroupController::class,'create'])->name('acs.face.largePersonGroup.create');
Route::post('/acs/face/largePersonGroup',[AzureCognitiveServicesFaceLargePersonGroupController::class,'store'])->name('acs.face.largePersonGroup.store');


// LargePersonGroupPerson
Route::post('/acs/face/largePersonGroupPerson/addFace',[AzureCognitiveServicesFaceLargePersonGroupController::class,'addFaceStoreRequest'])->name('acs.face.largePersonGroupPerson.addFace.store'); // Needs to be moved to lpgp controller
Route::get('/acs/face/largePersonGroup/{id}/largePersonGroupPerson/create',[AzureCognitiveServicesFaceLargePersonGroupPersonController::class,'create'])->name('acs.face.largePersonGroupPerson.create')->whereNumber('id');
Route::post('/acs/face/largePersonGroup/{id}/largePersonGroupPerson',[AzureCognitiveServicesFaceLargePersonGroupPersonController::class,'store'])->name('acs.face.largePersonGroupPerson.store')->whereNumber('id');


Route::get('/acs/face/persistedFaceId/{id}',[AzureCognitiveServicesFaceLargePersonGroupPersonController::class,'persistedFaceIdRedirectToDetection'])->name('asc.redirect.persistedFaceIdToDetection');
