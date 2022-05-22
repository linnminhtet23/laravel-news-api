<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\VisitorRequest;
use App\Http\Resources\VisitorResource;
use App\Models\Visitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitorController extends Controller
{

    public function visitorCount()
    {
        $visitors =  Visitor::first();
        return jsend_success($visitors == null ? 0 : $visitors->visitors);
    }


    public function visitorIncrement()
    {
        // $visitor = trim($request->get(self::VISITOR));
        
        $visitors =  Visitor::first();
        if($visitors ==null){
            $visitor = new Visitor();
            $visitor->visitors = 1;
            $visitor->save();
            return jsend_success($visitor->visitors);
        }else{
            $visitors->visitors+=1;
            $visitors->save();
            return jsend_success($visitors->visitors);
        }

        
    }

}
