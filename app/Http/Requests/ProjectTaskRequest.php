<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;

class ProjectTaskRequest extends FormRequest
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
        $userRole= Session::get('user_role');
      if($userRole==\Config::get('constants.ROLE.USER')){
          if (!empty($requestUrl['taskId']) && $requestUrl['taskId']) {
              return [
                  'subject' => 'required|max:255',
              ];
          } else {

              return [
                  'subject' => 'required|max:255',
                  'project_id' => 'required',
                  'project_board_id' => 'required',
                  'project_board_column_id' => 'required',

              ];
          }
      }else {

          if (!empty($requestUrl['taskId']) && $requestUrl['taskId']) {
              return [
                  'subject' => 'required|max:255',
                  'users' => 'required',
              ];
          } else {

              return [
                  'subject' => 'required|max:255',
                  'project_id' => 'required',
                  'project_board_id' => 'required',
                  'project_board_column_id' => 'required',
                  'users' => 'required',
                  'priority' => 'required',
              ];
          }
      }
    }
    public function messages() {
        return [
            'subject.required'=>'Title field is required',
            'subject.max' => 'Title name may not be greater than 255 characters.',
        ];
    }
}
