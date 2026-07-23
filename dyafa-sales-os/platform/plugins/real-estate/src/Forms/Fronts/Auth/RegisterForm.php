<?php

namespace Botble\RealEstate\Forms\Fronts\Auth;

use Botble\Base\Forms\Fields\EmailField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\PasswordField;
use Botble\Base\Forms\Fields\PhoneNumberField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Captcha\Facades\Captcha;
use Botble\Captcha\Forms\Fields\MathCaptchaField;
use Botble\Captcha\Forms\Fields\ReCaptchaField;
use Botble\RealEstate\Forms\Fronts\Auth\FieldOptions\EmailFieldOption;
use Botble\RealEstate\Forms\Fronts\Auth\FieldOptions\TextFieldOption;
use Botble\RealEstate\Http\Requests\Fronts\Auth\RegisterRequest;
use Botble\RealEstate\Models\Account;

class RegisterForm extends AuthForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setUrl(route('public.account.register.post'))
            ->setValidatorClass(RegisterRequest::class)
            ->icon('ti ti-user-plus')
            ->heading(__('Register an account'))
            ->description(__('Your personal data will be used to support your experience throughout this website, to manage access to your account.'))
            ->when(
                theme_option('register_background'),
                fn (AuthForm $form, string $background) => $form->banner($background)
            )
            ->add(
                'first_name',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('First name'))
                    ->placeholder(__('First name'))
                    ->icon('ti ti-user')
                    ->toArray()
            )
            ->add(
                'last_name',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Last name'))
                    ->placeholder(__('Last name'))
                    ->icon('ti ti-user')
                    ->toArray()
            )
            ->add(
                'username',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Username'))
                    ->placeholder(__('Username'))
                    ->icon('ti ti-user')
                    ->toArray()
            )
            ->add(
                'email',
                EmailField::class,
                EmailFieldOption::make()
                    ->label(__('Email'))
                    ->placeholder(__('Email address'))
                    ->icon('ti ti-mail')
                    ->toArray()
            )
            ->add(
                'phone',
                PhoneNumberField::class,
                TextFieldOption::make()
                    ->label(__('Phone (optional)'))
                    ->placeholder(__('Phone number'))
                    ->icon('ti ti-phone')
                    ->toArray()
            )
            ->add(
                'password',
                PasswordField::class,
                TextFieldOption::make()
                    ->label(__('Password'))
                    ->placeholder(__('Password'))
                    ->icon('ti ti-lock')
                    ->toArray()
            )
            ->add(
                'password_confirmation',
                PasswordField::class,
                TextFieldOption::make()
                    ->label(__('Password confirmation'))
                    ->placeholder(__('Password confirmation'))
                    ->icon('ti ti-lock')
                    ->toArray()
            )
            ->when(is_plugin_active('captcha'), function (FormAbstract $form) {
                $form
                    ->when(Captcha::isEnabled() && setting('real_estate_enable_recaptcha_in_register_page', false), function (FormAbstract $form) {
                        $form->add('recaptcha', ReCaptchaField::class);
                    })
                    ->when(Captcha::mathCaptchaEnabled() && setting('real_estate_enable_math_captcha_in_register_page', false), function (FormAbstract $form) {
                        $form->add('math_captcha', MathCaptchaField::class);
                    });
            })
            ->submitButton(__('Register'), 'ti ti-arrow-narrow-right')
            ->add('login', HtmlField::class, [
                'html' => sprintf(
                    '<div class="mt-3 text-center">%s <a href="%s" class="text-decoration-underline">%s</a></div>',
                    __('Already have an account?'),
                    route('public.account.login'),
                    __('Login')
                ),
            ])
            ->add('filters', HtmlField::class, [
                'html' => apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, Account::class),
            ]);
    }
}
