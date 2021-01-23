# Azure Cognitive Services UI

This packages makes it easy to use Azure's cognitive service in your Laravel project.
Contents of this package is tailored to:
* Storing requests in a database (tested with MariaDB 10.4)
* Storing image data using the built-in storage driver 
* A web UI for using the service and as an example to build on

## Requirements

* Laravel 8.x
  * Database configured
    * MariaDB 10.4 tested
  * Storage configured
* PHP 7.3|8.x
* Composer package ``kvaksrud\laravel-azure-cognitive-services`` ([git](https://github.com/Kvaksrud/laravel-azure-cognitive-services)) will be installed as a prerequisite

## Installation
Open CMD or PowerShell in your project folder and run the following commands to get started
1. Install package with `composer require kvaksrud\laravel-azure-cognitive-services-ui`
2. Publish configuration with `php artisan vendor:publish --provider="--provider=Kvaksrud\AzureCognitiveServices\Ui\AzureCognitiveServicesUiServiceProvider"`
3. Install training status update as part of your scheduler. Open `app\Console\Kernel.php` and add `$schedule->command('acs:updateLpgTrainingStatus')->everyFiveMinutes();` in the `protected function schedule(Schedule $schedule)` function clause. Make sure you run the scheduler automatically using i.e conjobs. See ([Laravel Scheduler Documentation](https://laravel.com/docs/8.x/scheduling#running-the-scheduler)) for details on how to schedule.

## Commands
`acs:updateLpgTrainingStatus` updates the training status of pending learning sessions.<br />
`acs:synclpgfromazure` synchronizes changes from azure down to your application.

## Configuration
### Disabled default routes
By default, the package will publish the following routes

```
Method   | Uri                                                          | Name
----------------------------------------------------------------------------------------------------------------------
GET|HEAD | acs                                                          | acs
GET|HEAD | acs/face                                                     | acs.face
POST     | acs/face/detection                                           | acs.face.detection.store
GET|HEAD | acs/face/detection/create                                    | acs.face.detection.create
GET|HEAD | acs/face/detection/{id}                                      | acs.face.detection.view
POST     | acs/face/detection/{id}/identify                             | acs.face.detection.identify
GET|HEAD | acs/face/detection/{id}/images/{image}                       | acs.face.detection.viewImage
GET|HEAD | acs/face/detections                                          | acs.face.detection.index
POST     | acs/face/largePersonGroup                                    | acs.face.largePersonGroup.store
GET|HEAD | acs/face/largePersonGroup/create                             | acs.face.largePersonGroup.create
GET|HEAD | acs/face/largePersonGroup/{id}                               | acs.face.largePersonGroup.view
POST     | acs/face/largePersonGroup/{id}                               | acs.face.largePersonGroup.train
GET|HEAD | acs/face/largePersonGroup/{id}/delete                        | acs.face.largePersonGroup.delete
POST     | acs/face/largePersonGroup/{id}/largePersonGroupPerson        | acs.face.largePersonGroupPerson.store
GET|HEAD | acs/face/largePersonGroup/{id}/largePersonGroupPerson/create | acs.face.largePersonGroupPerson.create
POST     | acs/face/largePersonGroupPerson/addFace                      | acs.face.largePersonGroupPerson.addFace.store
POST     | acs/face/largePersonGroups                                   | acs.face.largePersonGroup.sync
GET|HEAD | acs/face/largePersonGroups                                   | acs.face.largePersonGroup.index
GET|HEAD | acs/face/persistedFaceId/{id}                                | asc.redirect.persistedFaceIdToDetection
```

To disable these routes, open the config file `config\azure-cognitive-services-ui.php` and change `enable_routes` under `general` to `false`.
