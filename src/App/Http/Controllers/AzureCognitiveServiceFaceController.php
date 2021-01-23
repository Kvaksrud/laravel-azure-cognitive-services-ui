<?php

namespace Kvaksrud\AzureCognitiveServices\Ui\App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Kvaksrud\AzureCognitiveServices\Api\Controllers\AzureFaceController;
use Kvaksrud\AzureCognitiveServices\Ui\App\Models\Detection;
use Kvaksrud\AzureCognitiveServices\Ui\App\Models\Face;
use Kvaksrud\AzureCognitiveServices\Ui\App\Models\FaceCandidate;
use Kvaksrud\AzureCognitiveServices\Ui\App\Models\FaceImage;
use Kvaksrud\AzureCognitiveServices\Ui\App\Models\LargePersonGroup;
use Kvaksrud\AzureCognitiveServices\Ui\App\Models\LargePersonGroupPerson;

class AzureCognitiveServiceFaceController extends AzureCognitiveServicesController
{
    public function index(Request $request)
    {
        return view('azure-cognitive-services-ui::face.detection.index')->with('detections',Detection::all() ?? null);
    }

    public function view(Request $request)
    {
        $detection = Detection::findOrFail($request->route()->parameter('id'));
        $previous = Detection::where('id', '<', $detection->id)->max('id');
        $next = Detection::where('id', '>', $detection->id)->min('id');

        $groups = LargePersonGroup::all();
        $people = LargePersonGroupPerson::all();

        return view('azure-cognitive-services-ui::face.detection.view')->with('detection',$detection)->with('previous',$previous)->with('next',$next)->with('people',$people)->with('groups',$groups);
    }

    public function viewImage(Request $request)
    {
        $detection = Detection::findOrFail($request->route()->parameter('id'));
        if($request->route()->parameter('image') === 'detection')
            $imagePath = $detection->image->detection_path;
        else
            $imagePath = $detection->image->original_path;

        if(Storage::exists($imagePath) === false)
            abort(404);

        $imageStorage = Storage::get($imagePath);
        $image = Image::make($imageStorage);
        if($request->exists('maxHeight') and is_int((int)$request->query('maxHeight')))
            $image->heighten((int)$request->query('maxHeight'));
        return $image->response();
    }

    public function create(Request $request)
    {
        return view('azure-cognitive-services-ui::face.detection.create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        if($request->exists(['image_url']) === false)
            return back()->with('warning','Missing image URL in the form request');

        // Input
        $imageUrl = $request->get('image_url');

        // Verify image is over https
        if(Str::startsWith(Str::lower($imageUrl),'https') === false)
            return back()->withInput()->with('warning','URL must be secure (https)');

        $detection = $this->detect($imageUrl);
        if($detection) // Success
            return redirect()->route('acs.face.detection.view',['id'=>$detection->id]);

        // Failed
        return back()->withInput()->with('error','Failed to do detection');

    }

    public function detect(string $imageUrl)
    {
        // Classes
        $azure = new AzureFaceController();


        // Detect faces
        $faces = $azure->face()->detect($imageUrl)->getBody();

        // Create a detection model to create structure to the data
        $detection = new Detection();
        try {
            $detection->saveOrFail();
        } catch (\Throwable $e) {
            Log::error('Failed to create detection',[$imageUrl,$faces]);
            return null;
        }

        // Loop faces and save them to db for later referencing
        foreach($faces as $detectedFace){
            $face = new Face();
            $face->detection_id = $detection->id;
            $face->faceId = $detectedFace->faceId;
            $face->face_rectangle = (array)$detectedFace->faceRectangle;
            try {
                $face->saveOrFail();
            } catch (\Throwable $e) {
                Log::error('Failed to save face',[$e]);
                continue;
            }
        }

        // Set stream to SSL
        $context = stream_context_create(["ssl" => [
            "verify_peer"      => true,
            "verify_peer_name" => true]
        ]);

        $faceImage = new FaceImage;
        $faceImage->detection_id = $detection->id;
        $faceImage->url = $imageUrl;

        $storageFolder = 'acs/face/detection/'.$detection->id.'/';
        $fileContent = file_get_contents($imageUrl, false, $context);
        $img = Image::make($fileContent); // Original
        if($img->height() > 500)
            $img->heighten(500);
        if($this->saveToFile($storageFolder.'original.jpg',$img->encode('jpg',72),true) !== true)
            Log::warning('Unable to save original face image', [$detection, $storageFolder, $fileContent]);
        else
            $faceImage->original_path = $storageFolder.'original.jpg';

        // Set rectangles on image
        $colors = config('azure-cognitive-services-ui.face.detection.colors',['#fff','#000']);
        $img = Image::make($fileContent); // Original
        $i = 0;
        foreach($faces as $face) {
            $dbFace = Face::where('faceId','=',$face->faceId)->first();
            $dbFace->color = $colors[$i];
            $dbFace->save();
            $img->rectangle($face->faceRectangle->left, $face->faceRectangle->top, $face->faceRectangle->left + $face->faceRectangle->width, $face->faceRectangle->top + $face->faceRectangle->height, function($draw) use ($i, $colors) {
                $draw->border(5,$colors[$i]);
            });
            $i++;
            if($i > count($colors) - 1)
                $i = 0;
        }

        // Save rectangle image
        if($img->height() > 500)
            $img->heighten(500);
        if($this->saveToFile($storageFolder.'detected.jpg',$img->encode('jpg',72),true) !== true)
            Log::warning('Unable to save detected face image', [$detection, $storageFolder, $fileContent]);
        else
            $faceImage->detection_path = $storageFolder.'detected.jpg';


        try {
            $faceImage->saveOrFail();
        } catch (\Throwable $e) {
            Log::warning('Unable to save image details', [$detection, $storageFolder, $fileContent, $faceImage,$e]);
        }

        //Storage::put($storageFolder.'detected.jpg',$img->encode('jpg',72));

        return $detection->fresh();
    }

    public function identify(Request $request)
    {
        $detection = Detection::findOrFail($request->route()->parameter('id'));
        $group = LargePersonGroup::findOrFail($request->get('group_id'));
        $faceIds = array();
        foreach($detection->faces as $face){
            $faceIds[] = $face->faceId;
        }

        $azure = new AzureFaceController();
        $result = $azure->face()->identify($group->largePersonGroupId,$faceIds);

        if($result->hasError() === false){
            foreach($result->getBody() as $faceId){
                $face = Face::where('faceId','=',$faceId->faceId)->firstOrFail();
                if($faceId->candidates) {
                    $candidate = FaceCandidate::updateOrCreate(
                        ['face_id' => $face->id, 'personId' => $faceId->candidates[0]->personId],
                        [
                            'confidence' => $faceId->candidates[0]->confidence
                        ]
                    );
                }
            }
            return back()->with('success', 'Identification process complete');
        }

        Log::debug('Identification failed',[$request->all(),$detection,$group,$faceIds,$result,$result->getBody()]);
        return back()->with('error', 'Identification process failed. See telescope logs.');
    }
}
