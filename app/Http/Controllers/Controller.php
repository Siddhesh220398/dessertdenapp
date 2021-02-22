<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /* Web Side Commons */
    public function validateForm($fields, $rules){
        $validator = Validator::make($fields, $rules)->validate();
    }

    public function DTFilters($request){
        $filters = array(
            'draw' => $request['draw'],
            'offset' => $request['start'],
            'limit' => $request['length'],
            'sort_column' => $request['columns'][$request['order'][0]['column']]['data'],
            'sort_order' => $request['order'][0]['dir'],
            'search' => $request['search']['value']

        );
        return $filters;
    }
    /* Web Side Commons */

    /* Api Side Commons */
    public $response = ['message' => '', 'data'=> null];
    public $status = 412;

    public function ApiValidator($fields, $rules) {
        $validator = Validator::make($fields, $rules);
        if ($validator->fails()) {
            $this->response['message'] = array_shift((array_values($validator->errors()->messages())[0]));
            return false;
        }
        return true;
    }

    public function return_response(){
        return response()->json(array_merge($this->response, ['statusCode' => $this->status]), 200);    
    }
    /* Api Side Commons */
}
