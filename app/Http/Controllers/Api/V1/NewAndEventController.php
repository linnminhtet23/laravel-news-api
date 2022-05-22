<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewAndEventRequest;
use App\Http\Resources\NewAndEventResource;
use App\Models\NewAndEvent;
use App\Models\NewImage;
use App\Models\Recruitment;
use App\Services\FileUploadService;
use App\Utils\ErrorType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewAndEventController extends Controller
{
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const IMAGE ='image';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $newsandevent = NewAndEvent::orderBy('updated_at', 'DESC')->get();
        return jsend_success(NewAndEventResource::collection($newsandevent));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NewAndEventRequest $request)
    {

        // return "hello";
        // DB::beginTransaction();
        try {

            $title = trim($request->get(self::TITLE));
            $description = trim($request->get(self::DESCRIPTION));
            $image = $request->file(self::IMAGE);

            $image_name =FileUploadService::save($image,'news');


            $newsandevent = new NewAndEvent();
            $newsandevent->title = $title;
            $newsandevent->description = $description;
            $newsandevent->image = $image_name;

            $newsandevent->save();

            // if ($request->hasFile('images')) {
            //     foreach ($request->file("images") as $img) {
            //         $image_name = FileUploadService::save($img, "news_images");
            //         $new_images = new NewImage();
            //         $new_images->news_id = $newsandevent->id;
            //         $new_images->image = $image_name;

            //         $new_images->save();
            //     }
            // }

            // DB::commit();
            return jsend_success(new NewAndEventResource($newsandevent), JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {

            // DB::rollBack();
            Log::error(__('api.saved-failed', ['model' => class_basename(NewAndEvent::class)]), [
                'code' => $e->getCode(),
                'trace' => $e->getTrace(),
            ]);

            return jsend_error(__('api.saved-failed', ['model' => class_basename(NewAndEvent::class)]), [
                $e->getCode(),
                ErrorType::SAVE_ERROR,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(NewAndEvent $newsandevents)
    {
        return jsend_success(new NewAndEventResource($newsandevents));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NewAndEventRequest $request, NewAndEvent $newsandevents)
    {
        try {
            $title = trim($request->get(self::TITLE));
            $description = trim($request->get(self::DESCRIPTION));
            $image = $request->file(self::IMAGE);

            if ($request->has(self::IMAGE)) {
                $image_name = FileUploadService::save($image, "news");
                FileUploadService::remove($newsandevents->image, "news");
                $newsandevents->image = $image_name;
            }

            $newsandevents->title = $title;
            $newsandevents->description = $description;

            $newsandevents->save();

            // if ($request->has('images')) {
            //     if ($request->hasFile('images')) {
            //         $new_images_model = NewImage::where('news_id', $newsandevents->id)->get();
            //         foreach ($new_images_model as $new_image_model) {
            //             FileUploadService::remove($new_image_model->image, 'news_images');
            //             $new_image_model->delete();
            //         }

            //         foreach ($request->file("images") as $img) {

            //             $image_name = FileUploadService::save($img, "news_images");

            //             $new_images = new NewImage();
            //             $new_images->news_id = $newsandevents->id;
            //             $new_images->image = $image_name;


            //             $new_images->save();
            //         }
            //     }
            // }
            // return 'hi';
            return jsend_success(new NewAndEventResource($newsandevents), JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error(__('api.saved-failed', ['model' => class_basename(Recruitment::class)]), [
                'code' => $e->getCode(),
                'trace' => $e->getTrace(),
            ]);

            return jsend_error(__('api.saved-failed', ['model' => class_basename(Recruitment::class)]), [
                $e->getCode(),
                ErrorType::UPDATE_ERROR,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(NewAndEvent $newsandevents)
    {
        try {
           
            
            // $new_images_model = NewImage::where('news_id', $newsandevents->id)->get();
            // foreach ($new_images_model as $new_image_model) {
            //     FileUploadService::remove($new_image_model->image, 'news_images');   
            //     // return "hi";
            //     $new_image_model->delete();
            // }
            
            $newsandevents->delete();
            FileUploadService::remove($newsandevents->image, 'news');

            return jsend_success(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            Log::error(__('api.saved-failed', ['model' => class_basename(Recruitment::class)]), [
                'code' => $e->getCode(),
                'trace' => $e->getTrace(),
            ]);

            return jsend_error(__('api.saved-failed', ['model' => class_basename(Recruitment::class)]), [
                $e->getCode(),
                ErrorType::DELETE_ERROR,
            ]);
        }
    }
}
