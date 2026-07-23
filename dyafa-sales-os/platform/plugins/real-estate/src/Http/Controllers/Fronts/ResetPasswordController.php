<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use App\Http\Controllers\Controller;
use Botble\ACL\Traits\ResetsPasswords;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\Fronts\Auth\ResetPasswordForm;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    public string $redirectTo = '/';

    public function __construct()
    {
        $this->redirectTo = route('public.account.dashboard');
    }

    public function showResetForm(Request $request, $token = null)
    {
        if (! RealEstateHelper::isLoginEnabled()) {
            abort(404);
        }

        SeoHelper::setTitle(__('Reset Password'));

        return Theme::scope(
            'real-estate.account.auth.passwords.reset',
            [
                'token' => $token,
                'email' => $request->input('email'),
                'form' => ResetPasswordForm::create(),
            ],
            'plugins/real-estate::themes.auth.passwords.reset'
        )->render();
    }

    public function broker()
    {
        return Password::broker('accounts');
    }

    protected function guard()
    {
        return auth('account');
    }
}
