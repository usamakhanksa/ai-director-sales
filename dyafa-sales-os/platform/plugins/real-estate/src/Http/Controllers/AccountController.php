<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Media\Models\MediaFile;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\RealEstate\Forms\AccountForm;
use Botble\RealEstate\Http\Requests\AccountCreateRequest;
use Botble\RealEstate\Http\Requests\AccountEditRequest;
use Botble\RealEstate\Http\Resources\AccountResource;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Tables\AccountTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends BaseController
{
    public function __construct()
    {
        OptimizerHelper::disable();

        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::account.name'), route('account.index'));
    }

    public function index(AccountTable $dataTable)
    {
        $this->pageTitle(trans('plugins/real-estate::account.name'));

        return $dataTable->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/real-estate::account.create'));

        return AccountForm::create()
            ->remove('is_change_password')
            ->renderForm();
    }

    public function store(AccountCreateRequest $request)
    {
        $account = new Account();
        $account->fill($request->input());
        $account->is_featured = $request->input('is_featured');
        $account->is_public_profile = $request->input('is_public_profile');
        $account->confirmed_at = Carbon::now();

        $account->password = Hash::make($request->input('password'));
        $account->dob = Carbon::parse($request->input('dob'))->toDateString();

        if ($request->input('avatar_image')) {
            $account->avatar_id = MediaFile::query()
                ->where('url', $request->input('avatar_image'))
                ->value('id');
        } else {
            $account->avatar_id = null;
        }

        $account->save();

        event(new CreatedContentEvent(ACCOUNT_MODULE_SCREEN_NAME, $request, $account));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('account.index'))
            ->setNextUrl(route('account.edit', $account->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Account $account)
    {
        $this->pageTitle(trans('plugins/real-estate::account.edit', ['name' => $account->name]));

        $account->password = null;

        return AccountForm::createFromModel($account)
            ->renderForm();
    }

    public function update(Account $account, AccountEditRequest $request)
    {
        $account->fill($request->except('password'));

        if ($request->input('is_change_password') == 1) {
            $account->password = Hash::make($request->input('password'));
        }

        $account->dob = Carbon::parse($request->input('dob'))->toDateString();

        if ($request->input('avatar_image')) {
            $account->avatar_id = MediaFile::query()
                ->where('url', $request->input('avatar_image'))
                ->value('id');
        } else {
            $account->avatar_id = null;
        }

        $account->is_featured = $request->input('is_featured');
        $account->is_public_profile = $request->input('is_public_profile');
        $account->save();

        event(new UpdatedContentEvent(ACCOUNT_MODULE_SCREEN_NAME, $request, $account));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('account.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Account $account)
    {
        return DeleteResourceAction::make($account);
    }

    public function getList(Request $request)
    {
        $keyword = BaseHelper::stringify($request->input('q'));

        if (! $keyword) {
            return $this
                ->httpResponse()
                ->setData([]);
        }

        $data = Account::query()
            ->where('first_name', 'LIKE', '%' . $keyword . '%')
            ->orWhere('last_name', 'LIKE', '%' . $keyword . '%')
            ->select(['id', 'first_name', 'last_name'])
            ->take(10)
            ->get();

        return $this
            ->httpResponse()
            ->setData(AccountResource::collection($data));
    }
}
