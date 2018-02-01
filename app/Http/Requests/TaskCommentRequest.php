<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $inputs =\Request::all();
        if(isset($inputs['action']) && $inputs['action'] == 'action-remove-comment'){
        return [];
        } else {
            return [
                'userId' => 'required',
                'taskId' => 'required',
                'comment' => 'required',
            ];
        }

    }
}
