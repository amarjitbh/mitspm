<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class addBoards extends FormRequest
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
        $requestUrl = \Request::all();

        if (isset($requestUrl['columnName']) && $requestUrl['columnId']) {
            return [
                'columnName' => 'required|string|max:255',
                'columnId' => 'required|int'
            ];
        }else {

            return [
                'project_board_name' => 'required|string|max:255',
                'project_id' => 'required|int',
            ];
        }
    }
}