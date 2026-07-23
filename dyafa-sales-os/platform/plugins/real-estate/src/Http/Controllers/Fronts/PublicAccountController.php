<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Language\Facades\Language;
use Botble\Media\Chunks\Exceptions\UploadMissingFileException;
use Botble\Media\Chunks\Handler\DropZoneUploadHandler;
use Botble\Media\Chunks\Receiver\FileReceiver;
use Botble\Media\Facades\RvMedia;
use Botble\Media\Models\MediaFile;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\PayPal\Services\Gateways\PayPalPaymentService;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\Fronts\ChangePasswordForm;
use Botble\RealEstate\Forms\Fronts\ProfileForm;
use Botble\RealEstate\Http\Requests\AvatarRequest;
use Botble\RealEstate\Http\Requests\SettingRequest;
use Botble\RealEstate\Http\Requests\UpdatePasswordRequest;
use Botble\RealEstate\Http\Resources\AccountResource;
use Botble\RealEstate\Http\Resources\ActivityLogResource;
use Botble\RealEstate\Http\Resources\PackageResource;
use Botble\RealEstate\Http\Resources\TransactionResource;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\AccountActivityLog;
use Botble\RealEstate\Models\Package;
use Botble\RealEstate\Models\Transaction;
use Botble\RealEstate\Services\CouponService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PublicAccountController extends BaseController
{
    public function __construct()
    {
        OptimizerHelper::disable();
    }

    public function getDashboard()
    {
        $user = auth('account')->user();

        $this->pageTitle(auth('account')->user()->name);

        Assets::usingVueJS()
            ->addScriptsDirectly('vendor/core/plugins/real-estate/js/components.js');

        return view('plugins/real-estate::themes.dashboard.index', compact('user'));
    }

    public function getSettings()
    {
        $this->pageTitle(trans('plugins/real-estate::account.account_settings'));

        $user = auth('account')->user();

        Assets::addScriptsDirectly('vendor/core/plugins/location/js/location.js');

        $profileForm = ProfileForm::createFromModel($user)
            ->renderForm();

        $changePasswordForm = ChangePasswordForm::create()
            ->renderForm();

        return view(
            'plugins/real-estate::themes.dashboard.settings.index',
            compact('user', 'profileForm', 'changePasswordForm')
        );
    }

    public function postSettings(SettingRequest $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');
        $day = $request->input('day');

        if ($year && $month && $day) {
            $request->merge(['dob' => implode('-', [$year, $month, $day])]);

            $validator = Validator::make($request->input(), [
                'dob' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return redirect()->route('public.account.settings');
            }
        }

        $account = auth('account')->user();
        $account->fill($request->except('email'));
        $account->save();

        do_action('update_account_settings', $account);

        AccountActivityLog::query()->create(['action' => 'update_setting']);

        return $this
            ->httpResponse()
            ->setNextUrl(route('public.account.settings'))
            ->setMessage(trans('plugins/real-estate::account.update_profile_success'));
    }

    public function getPackages()
    {
        if (! RealEstateHelper::isEnabledCreditsSystem()) {
            abort(404);
        }

        $this->pageTitle(trans('plugins/real-estate::account.packages'));

        Assets::addScriptsDirectly('vendor/core/plugins/real-estate/js/components.js');

        Assets::usingVueJS();

        return view('plugins/real-estate::themes.dashboard.settings.package');
    }

    public function getTransactions()
    {
        if (! RealEstateHelper::isEnabledCreditsSystem()) {
            abort(404);
        }

        $this->pageTitle(trans('plugins/real-estate::account.transactions'));

        Assets::addScriptsDirectly('vendor/core/plugins/real-estate/js/components.js');

        Assets::usingVueJS();

        return view('plugins/real-estate::themes.dashboard.settings.transactions');
    }

    public function ajaxGetPackages()
    {
        if (! RealEstateHelper::isEnabledCreditsSystem()) {
            abort(404);
        }

        if (is_plugin_active('language')) {
            Language::setCurrentAdminLocale(Language::getCurrentLocaleCode());
        }

        $account = Account::query()->with(['packages'])->findOrFail(auth('account')->id());

        $packages = Package::query()
            ->wherePublished()
            ->get();

        $packages = $packages->filter(function ($package) use ($account) {
            return $package->account_limit === null || $account->packages->where(
                'id',
                $package->id
            )->count() < $package->account_limit;
        });

        return $this
            ->httpResponse()
            ->setData([
            'packages' => PackageResource::collection($packages),
            'account' => new AccountResource($account),
        ]);
    }

    public function ajaxSubscribePackage(Request $request)
    {
        if (! RealEstateHelper::isEnabledCreditsSystem()) {
            abort(404);
        }

        $package = Package::query()->findOrFail($request->input('id'));

        $account = Account::query()->findOrFail(auth('account')->id());

        if ($package->account_limit && $account->packages()->where(
            'package_id',
            $package->id
        )->count() >= $package->account_limit) {
            abort(403);
        }

        if ((float) $package->price) {
            session(['subscribed_packaged_id' => $package->id]);

            return $this
                ->httpResponse()
                ->setData(['next_page' => route('public.account.package.subscribe', $package->id)]);
        }

        $this->savePayment($package, null, true);

        return $this
            ->httpResponse()
            ->setData(new AccountResource($account->refresh()))
            ->setMessage(trans('plugins/real-estate::package.add_credit_success'));
    }

    protected function savePayment(Package $package, ?string $chargeId, bool $force = false)
    {
        if (! RealEstateHelper::isEnabledCreditsSystem()) {
            abort(404);
        }

        $payment = Payment::query()
            ->where('charge_id', $chargeId)
            ->first();

        if (! $payment && ! $force) {
            return false;
        }

        $account = auth('account')->user();

        if (($payment && $payment->status == PaymentStatusEnum::COMPLETED) || $force) {
            $account->credits += $package->number_of_listings;
            $account->save();

            $account->packages()->attach($package);
        }

        Transaction::query()->create([
            'user_id' => 0,
            'account_id' => auth('account')->id(),
            'credits' => $package->number_of_listings,
            'payment_id' => $payment ? $payment->id : null,
        ]);

        if (! $package->price) {
            EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'account_name' => $account->name,
                    'account_email' => $account->email,
                ])
                ->sendUsingTemplate('free-credit-claimed');
        } else {
            EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'account_name' => $account->name,
                    'account_email' => $account->email,
                    'package_name' => $package->name,
                    'package_price' => $package->price,
                    'package_percent_discount' => $package->percent_save,
                    'package_number_of_listings' => $package->number_of_listings,
                    'package_price_per_credit' => $package->price ? $package->price / ($package->number_of_listings ?: 1) : 0,
                ])
                ->sendUsingTemplate('payment-received');
        }

        EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'account_name' => $account->name,
                'account_email' => $account->email,
                'package_name' => $package->name,
                'package_price' => $package->price ?: 0,
                'package_percent_discount' => $package->percent_save,
                'package_number_of_listings' => $package->number_of_listings,
                'package_price_per_credit' => $package->price ? $package->price / ($package->number_of_listings ?: 1) : 0,
            ])
            ->sendUsingTemplate('payment-receipt', auth('account')->user()->email);

        return true;
    }

    public function getSubscribePackage(int|string $id, CouponService $service)
    {
        if (! RealEstateHelper::isEnabledCreditsSystem()) {
            abort(404);
        }

        $package = Package::query()->findOrFail($id);

        Session::put('cart_total', $package->price);

        $this->pageTitle(trans('plugins/real-estate::package.subscribe_package', ['name' => $package->name]));

        add_filter(PAYMENT_FILTER_AFTER_PAYMENT_METHOD, function () use ($service, $package) {
            $totalAmount = $service->getAmountAfterDiscount(
                Session::get('coupon_discount_amount', 0),
                $package->price
            );

            return view('plugins/real-estate::coupons.partials.form', compact('package', 'totalAmount'));
        });

        return view('plugins/real-estate::account.checkout', compact('package'));
    }

    public function getPackageSubscribeCallback($packageId, Request $request)
    {
        if (! RealEstateHelper::isEnabledCreditsSystem()) {
            abort(404);
        }

        $package = Package::query()->findOrFail($packageId);

        if (is_plugin_active('paypal') && $request->input('type') == PAYPAL_PAYMENT_METHOD_NAME) {
            $validator = Validator::make($request->input(), [
                'amount' => 'required|numeric',
                'currency' => 'required',
            ]);

            if ($validator->fails()) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage($validator->getMessageBag()->first());
            }

            $payPalService = app(PayPalPaymentService::class);

            $paymentStatus = $payPalService->getPaymentStatus($request);

            if ($paymentStatus) {
                $chargeId = session('paypal_payment_id');

                $payPalService->afterMakePayment($request->input());

                $this->savePayment($package, $chargeId);

                return $this
                    ->httpResponse()
                    ->setNextUrl(route('public.account.packages'))
                    ->setMessage(trans('plugins/real-estate::package.add_credit_success'));
            }

            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl(route('public.account.packages'))
                ->setMessage($payPalService->getErrorMessage());
        }

        $this->savePayment($package, $request->input('charge_id'));

        if (! $request->has('success') || $request->input('success')) {
            return $this
                ->httpResponse()
                ->setNextUrl(route('public.account.packages'))
                ->setMessage(session()->get('success_msg') ?: trans('plugins/real-estate::package.add_credit_success'));
        }

        return $this
            ->httpResponse()
            ->setError()
            ->setNextUrl(route('public.account.packages'))
            ->setMessage(__('Payment failed!'));
    }

    public function postSecurity(UpdatePasswordRequest $request)
    {
        $request->user('account')->update([
            'password' => $request->input('password'),
        ]);

        AccountActivityLog::query()->create(['action' => 'update_security']);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/real-estate::dashboard.password_update_success'));
    }

    public function postAvatar(AvatarRequest $request)
    {
        try {
            $account = auth('account')->user();

            $result = RvMedia::uploadFromBlob($request->file('avatar_file'), folderSlug: auth('account')->user()->upload_folder);

            if ($result['error']) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage($result['message']);
            }

            $file = $result['data'];

            $avatar = MediaFile::query()->find($account->avatar_id);

            if ($avatar) {
                $avatar->forceDelete();
            }

            $account->avatar_id = $file->id;

            $account->save();

            AccountActivityLog::query()->create([
                'action' => 'changed_avatar',
            ]);

            return $this
                ->httpResponse()
                ->setMessage(trans('plugins/real-estate::dashboard.update_avatar_success'))
                ->setData(['url' => Storage::url($file->url)]);
        } catch (Exception $ex) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($ex->getMessage());
        }
    }

    public function getActivityLogs()
    {
        $activities = AccountActivityLog::query()
            ->where('account_id', auth('account')->id())
            ->latest()
            ->paginate();

        Assets::addScriptsDirectly('vendor/core/plugins/real-estate/js/components.js');

        Assets::usingVueJS();

        return $this
            ->httpResponse()
            ->setData(ActivityLogResource::collection($activities))->toApiResponse();
    }

    public function postUpload(Request $request)
    {
        if (setting('media_chunk_enabled') != '1') {
            $validator = Validator::make($request->all(), [
                'file.0' => 'required|image|mimes:jpg,jpeg,png,webp',
            ]);

            if ($validator->fails()) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage($validator->getMessageBag()->first());
            }

            $result = RvMedia::handleUpload(Arr::first($request->file('file')), 0, auth('account')->user()->upload_folder);

            if ($result['error']) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage($result['message']);
            }

            return $this
                ->httpResponse()
                ->setData($result['data']);
        }

        try {
            // Create the file receiver
            $receiver = new FileReceiver('file', $request, DropZoneUploadHandler::class);
            // Check if the upload is success, throw exception or return response you need
            if ($receiver->isUploaded() === false) {
                throw new UploadMissingFileException();
            }
            // Receive the file
            $save = $receiver->receive();
            // Check if the upload has finished (in chunk mode it will send smaller files)
            if ($save->isFinished()) {
                $result = RvMedia::handleUpload($save->getFile(), 0, auth('account')->user()->upload_folder);

                if (! $result['error']) {
                    return $this
                        ->httpResponse()
                        ->setData($result['data']);
                }

                return $this
                    ->httpResponse()
                    ->setError()->setMessage($result['message']);
            }
            // We are in chunk mode, lets send the current progress
            $handler = $save->handler();

            return response()->json([
                'done' => $handler->getPercentageDone(),
                'status' => true,
            ]);
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function postUploadFromEditor(Request $request)
    {
        return RvMedia::uploadFromEditor($request, 0, auth('account')->user()->upload_folder);
    }

    public function ajaxGetTransactions()
    {
        if (! RealEstateHelper::isEnabledCreditsSystem()) {
            abort(404);
        }

        $transactions = Transaction::query()
            ->where('account_id', auth('account')->id())
            ->latest()
            ->with(['payment', 'user'])
            ->paginate();

        return $this
            ->httpResponse()
            ->setData(TransactionResource::collection($transactions))->toApiResponse();
    }
}
