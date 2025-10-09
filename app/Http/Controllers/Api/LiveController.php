<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarketerSocial;
use App\Models\Marketer;
use App\Models\Social;
use App\Http\Requests\Api\TokenSaveRequest;
use Illuminate\Support\Facades\Validator;
class LiveController extends Controller
{
    public function savefaceaccesstoken(Request $request)
    {
        $formdata = $request->all();
        $storrequest = new TokenSaveRequest();

        $validator = Validator::make(
            $formdata,
            $storrequest->rules(),
            $storrequest->messages()
        );
        if ($validator->fails()) {
            return response()->json(
                ["success" => 0, "message" => $validator->errors()?->first(), "data" => $validator->errors()]
                ,
                422
            );
        } else {
            $id = $formdata['id'];
            $access = $formdata['access_token'];
            $social = Social::where('code', 'facebook')->first();
            if ($social) {
                $record = MarketerSocial::firstOrNew([
                    'marketer_id' => $id,
                    'social_id' => $social->id,
                ]);
                $record->access_token = $access;
                $record->save();

            }
            return response()->json(
                ["success" => 1, "message" => __('api_messages.form.success save'), "data" => []]
            );
        }
    }
}
