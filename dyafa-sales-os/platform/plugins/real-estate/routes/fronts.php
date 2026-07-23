<?php

use Botble\Location\Models\City;
use Botble\Location\Models\State;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Http\Controllers\CustomFieldController;
use Botble\RealEstate\Http\Controllers\Fronts\ConsultController;
use Botble\RealEstate\Http\Controllers\Fronts\CouponController;
use Botble\RealEstate\Http\Controllers\Fronts\InvoiceController;
use Botble\RealEstate\Http\Controllers\Fronts\ReviewController;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;

if (defined('THEME_MODULE_SCREEN_NAME')) {
    Route::group(['namespace' => 'Botble\RealEstate\Http\Controllers\Fronts'], function () {
        Theme::registerRoutes(function () {
            $projectsPrefix = SlugHelper::getPrefix(Project::class, 'projects') ?: 'projects';

            $propertiesPrefix = SlugHelper::getPrefix(Property::class, 'properties') ?: 'properties';

            Route::match(theme_option('projects_list_page_id') ? ['POST'] : ['POST', 'GET'], $projectsPrefix, 'PublicController@getProjects')
                ->name('public.projects');

            Route::match(theme_option('properties_list_page_id') ? ['POST'] : ['POST', 'GET'], $propertiesPrefix, 'PublicController@getProperties')
                ->name('public.properties');

            $cityPrefix = SlugHelper::getPrefix(City::class, 'city') ?: 'city';

            $statePrefix = SlugHelper::getPrefix(State::class, 'state') ?: 'state';

            Route::match(['POST', 'GET'], $projectsPrefix . '/' . $cityPrefix . '/{slug}', 'PublicController@getProjectsByCity')
                ->name('public.projects-by-city');

            Route::match(['POST', 'GET'], $propertiesPrefix . '/' . $cityPrefix . '/{slug}', 'PublicController@getPropertiesByCity')
                ->name('public.properties-by-city');

            Route::match(['POST', 'GET'], $projectsPrefix . '/' . $statePrefix . '/{slug}', 'PublicController@getProjectsByState')
                ->name('public.projects-by-state');

            Route::match(['POST', 'GET'], $propertiesPrefix . '/' . $statePrefix . '/{slug}', 'PublicController@getPropertiesByState')
                ->name('public.properties-by-state');

            if (! RealEstateHelper::isDisabledPublicProfile()) {
                Route::get(SlugHelper::getPrefix(Account::class, 'agents') ?: 'agents', 'PublicController@getAgents')
                    ->name('public.agents');
            }

            Route::post('send-consult', 'PublicController@postSendConsult')
                ->name('public.send.consult');

            Route::get('currency/switch/{code?}', [
                'as' => 'public.change-currency',
                'uses' => 'PublicController@changeCurrency',
            ]);

            Route::group(['as' => 'public.account.'], function () {
                Route::group(['middleware' => ['account.guest']], function () {
                    Route::get('login', 'LoginController@showLoginForm')
                        ->name('login');
                    Route::post('login', 'LoginController@login')
                        ->name('login.post');

                    Route::get('register', 'RegisterController@showRegistrationForm')
                        ->name('register');
                    Route::post('register', 'RegisterController@register')
                        ->name('register.post');

                    Route::get('verify', 'RegisterController@getVerify')
                        ->name('verify');

                    Route::get(
                        'password/request',
                        'ForgotPasswordController@showLinkRequestForm'
                    )
                        ->name('password.request');
                    Route::post(
                        'password/email',
                        'ForgotPasswordController@sendResetLinkEmail'
                    )
                        ->name('password.email');
                    Route::post('password/reset', 'ResetPasswordController@reset')
                        ->name('password.update');
                    Route::get(
                        'password/reset/{token}',
                        'ResetPasswordController@showResetForm'
                    )
                        ->name('password.reset');
                });

                Route::group([
                    'middleware' => [
                        setting(
                            'verify_account_email',
                            false
                        ) ? 'account.guest' : 'account',
                    ],
                ], function () {
                    Route::get(
                        'register/confirm/resend',
                        'RegisterController@resendConfirmation'
                    )
                        ->name('resend_confirmation');
                    Route::get('register/confirm/{user}', 'RegisterController@confirm')
                        ->name('confirm');
                });
            });

            Route::get('feed/properties', [
                'as' => 'feeds.properties',
                'uses' => 'PublicController@getPropertyFeeds',
            ]);

            Route::get('feed/projects', [
                'as' => 'feeds.projects',
                'uses' => 'PublicController@getProjectFeeds',
            ]);

            Route::group(['middleware' => ['account'], 'as' => 'public.account.'], function () {
                Route::group(['prefix' => 'account'], function () {
                    Route::post('logout', 'LoginController@logout')
                        ->name('logout');

                    Route::get('dashboard', [
                        'as' => 'dashboard',
                        'uses' => 'PublicAccountController@getDashboard',
                    ]);

                    Route::get('settings', [
                        'as' => 'settings',
                        'uses' => 'PublicAccountController@getSettings',
                    ]);

                    Route::post('settings', [
                        'as' => 'post.settings',
                        'uses' => 'PublicAccountController@postSettings',
                    ]);

                    Route::get('security', [
                        'as' => 'security',
                        'uses' => 'PublicAccountController@getSecurity',
                    ]);

                    Route::put('security', [
                        'as' => 'post.security',
                        'uses' => 'PublicAccountController@postSecurity',
                    ]);

                    Route::post('avatar', [
                        'as' => 'avatar',
                        'uses' => 'PublicAccountController@postAvatar',
                    ]);

                    Route::get('packages', [
                        'as' => 'packages',
                        'uses' => 'PublicAccountController@getPackages',
                    ]);

                    Route::get('transactions', [
                        'as' => 'transactions',
                        'uses' => 'PublicAccountController@getTransactions',
                    ]);

                    Route::prefix('coupon')->name('coupon.')->group(function () {
                        Route::post('apply', [CouponController::class, 'apply'])->name('apply');
                        Route::post('remove', [CouponController::class, 'remove'])->name('remove');
                        Route::get('refresh/{id}', [CouponController::class, 'refresh'])->name('refresh');
                    });

                    Route::match(['GET', 'POST'], 'consults', [ConsultController::class, 'index'])
                        ->name('consults.index');

                    Route::get('consults/{id}', [ConsultController::class, 'show'])
                        ->name('consults.show')
                        ->wherePrimaryKey();
                });

                Route::group(['prefix' => 'account/ajax'], function () {
                    Route::get('activity-logs', [
                        'as' => 'activity-logs',
                        'uses' => 'PublicAccountController@getActivityLogs',
                    ]);

                    Route::get('transactions', [
                        'as' => 'ajax.transactions',
                        'uses' => 'PublicAccountController@ajaxGetTransactions',
                    ]);

                    Route::post('upload', [
                        'as' => 'upload',
                        'uses' => 'PublicAccountController@postUpload',
                    ]);

                    Route::post('upload-from-editor', [
                        'as' => 'upload-from-editor',
                        'uses' => 'PublicAccountController@postUploadFromEditor',
                    ]);

                    Route::get('packages', 'PublicAccountController@ajaxGetPackages')
                        ->name('ajax.packages');
                    Route::put('packages', 'PublicAccountController@ajaxSubscribePackage')
                        ->name('ajax.package.subscribe');
                });

                Route::group(['prefix' => 'account/properties', 'as' => 'properties.'], function () {
                    Route::resource('', 'AccountPropertyController')
                        ->parameters(['' => 'property']);

                    Route::post('renew/{id}', [
                        'as' => 'renew',
                        'uses' => 'AccountPropertyController@renew',
                    ])->wherePrimaryKey();
                });

                Route::group(['prefix' => 'account'], function () {
                    Route::get('packages/{id}/subscribe', 'PublicAccountController@getSubscribePackage')
                        ->name('package.subscribe')
                        ->wherePrimaryKey();

                    Route::get('packages/{id}/subscribe/callback', 'PublicAccountController@getPackageSubscribeCallback')
                        ->name('package.subscribe.callback')
                        ->wherePrimaryKey();
                });

                Route::group(['prefix' => 'account/invoices', 'as' => 'invoices.', 'controller' => 'InvoiceController'], function () {
                    Route::match(['GET', 'POST'], '/', 'index')->name('index');
                    Route::get('{id}', 'show')->name('show')
                        ->wherePrimaryKey();
                    Route::get('{id}/generate', [InvoiceController::class, 'generate'])->name('generate')
                        ->wherePrimaryKey();
                });

                Route::prefix('account/custom-fields')->name('custom-fields.')->group(function () {
                    Route::get('info', [CustomFieldController::class, 'getInfo'])->name('get-info');
                });
            });

            Route::middleware('account')->group(function () {
                Route::post('ajax/review/{slug}', [ReviewController::class, 'store'])->name('public.ajax.review.store');
            });

            Route::get('ajax/review/{slug}', [ReviewController::class, 'index'])->name('public.ajax.review.index');
        });

        Route::group(['prefix' => 'payments', 'middleware' => ['web', 'core']], function () {
            Route::post('checkout', 'CheckoutController@postCheckout')->name('payments.checkout');
        });
    });
}
