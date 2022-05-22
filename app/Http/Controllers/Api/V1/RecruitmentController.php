<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecruitmentRequest;
use App\Http\Resources\RecruitmentResource;
use App\Models\Recruitment;
use App\Services\FileUploadService;
use App\Utils\ErrorType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class RecruitmentController extends Controller
{
    const JOBTITLE = 'jobtitle';
    const JOBTYPE = 'jobtype';
    const POSITIONS = 'positions';
    const JOBDESC = 'jobdescription';
    const IMAGE = 'image';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $recruitment = Recruitment::all();
        $recruitment = Recruitment::orderBy('updated_at', 'DESC')->get();
        return jsend_success(RecruitmentResource::collection($recruitment));
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
    public function store(RecruitmentRequest $request)
    {
        try {
            $jobtitle = trim($request->get(self::JOBTITLE));
            $jobtype = trim($request->get(self::JOBTYPE));
            $positions = trim($request->get(self::POSITIONS));
            $jobdescription = trim($request->get(self::JOBDESC));
            $image = $request->file(self::IMAGE);

            $image_name = FileUploadService::save($image, "recruitments");

            $recruitment = new Recruitment();
            $recruitment->jobtitle = $jobtitle;
            $recruitment->jobtype = $jobtype;
            $recruitment->positions = $positions;
            $recruitment->jobdescription = $jobdescription;
            $recruitment->image = $image_name;

            // return $recruitment;
            $recruitment->save();
            return jsend_success(new RecruitmentResource($recruitment), JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error(__('api.saved-failed', ['model' => class_basename(Recruitment::class)]), [
                'code' => $e->getCode(),
                'trace' => $e->getTrace(),
            ]);

            return jsend_error(__('api.saved-failed', ['model' => class_basename(Recruitment::class)]), [
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
    public function show(Recruitment $recruitment)
    {
        return jsend_success(new RecruitmentResource($recruitment));
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
    public function update(RecruitmentRequest $request, Recruitment $recruitment)
    {
        try {
            $jobtitle = trim($request->get(self::JOBTITLE));
            $jobtype = trim($request->get(self::JOBTYPE));
            $positions = trim($request->get(self::POSITIONS));
            $jobdescription = trim($request->get(self::JOBDESC));
            $image = $request->file(self::IMAGE);

            if ($request->has(self::IMAGE)) {
                $image_name = FileUploadService::save($image, "recruitments");

                FileUploadService::remove($recruitment->image, "recruitments");
                $recruitment->image = $image_name;
            }

            $recruitment->jobtitle = $jobtitle;
            $recruitment->jobtype = $jobtype;
            $recruitment->positions = $positions;
            $recruitment->jobdescription = $jobdescription;

            $recruitment->save();
            return jsend_success(new RecruitmentResource($recruitment), JsonResponse::HTTP_CREATED);
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
    public function destroy(Recruitment $recruitment)
    {
        try {
            $recruitment->delete();
            FileUploadService::remove($recruitment->image, "recruitments");

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
