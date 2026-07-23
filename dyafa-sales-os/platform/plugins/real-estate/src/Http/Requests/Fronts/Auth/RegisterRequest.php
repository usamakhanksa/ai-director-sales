<?php

namespace Botble\RealEstate\Http\Requests\Fronts\Auth;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Rules\EmailRule;
use Botble\Captcha\Facades\Captcha;
use Botble\Support\Http\Requests\Request;

class RegisterRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:120', 'min:2'],
            'last_name' => ['required', 'string', 'max:120', 'min:2'],
            'username' => ['required', 'string', 'max:120', 'min:2', 'unique:re_accounts,username'],
            'email' => ['required', 'max:60', 'min:6', new EmailRule(), 'unique:re_accounts'],
            'phone' => [
                'required',
                ...explode('|', BaseHelper::getPhoneValidationRule()),
                'unique:re_accounts',
            ],
            'password' => ['required', 'min:6', 'confirmed'],
        ];

        if (is_plugin_active('captcha')) {
            if (setting('member_enable_recaptcha_in_register_page', 0)) {
                $rules += Captcha::rules();
            }

            if (setting('member_enable_math_captcha_in_register_page', 0)) {
                $rules += Captcha::mathCaptchaRules();
            }
        }

        return $rules;
    }

    public function attributes(): array
    {
        return is_plugin_active('captcha') ? Captcha::attributes() : [];
    }
}
