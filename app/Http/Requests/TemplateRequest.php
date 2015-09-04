<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TemplateRequest extends Request
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
        return [
            'template_name'         => 'required',
            'template_display_name' => 'required',
            'template_description'  => 'required',
            'template_category'     => 'required',
            'section_name'          => 'required',
            'section_display_name'  => 'required',
            'section_description'   => 'required',
            'section_editable'      => 'required',
            'section_content'       => 'required'
        ];
    }
}
