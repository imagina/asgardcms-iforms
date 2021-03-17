<?php

namespace Modules\Iforms\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class CreateLeadRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'g-recaptcha-response' => 'required|formCaptcha'
        ];
    }

    public function translationRules()
    {
        return [];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'g-recaptcha-response.required' => trans('iforms::common.messages.captcha_required'),
            'g-recaptcha-response.form_captcha'=> trans('iforms::common.messages.captcha_required'),
        ];
    }

    public function translationMessages()
    {
        return [];
    }
}
