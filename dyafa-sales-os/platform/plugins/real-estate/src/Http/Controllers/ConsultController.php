<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Forms\ConsultForm;
use Botble\RealEstate\Http\Requests\ConsultRequest;
use Botble\RealEstate\Models\Consult;
use Botble\RealEstate\Repositories\Interfaces\ConsultInterface;
use Botble\RealEstate\Tables\ConsultTable;
use Exception;
use Illuminate\Http\Request;

class ConsultController extends BaseController
{
    public function __construct(protected ConsultInterface $consultRepository)
    {

        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::consult.name'), route('consult.index'));
    }

    public function index(ConsultTable $table)
    {
        $this->pageTitle(trans('plugins/real-estate::consult.name'));

        return $table->renderTable();
    }

    public function edit(int|string $id, Request $request)
    {
        $consult = Consult::query()->with(['project', 'property'])->findOrFail($id);

        event(new BeforeEditContentEvent($request, $consult));

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $consult->name]));

        return ConsultForm::createFromModel($consult)->renderForm();
    }

    public function update(int|string $id, ConsultRequest $request)
    {
        $consult = Consult::query()->findOrFail($id);

        $consult->fill($request->input());
        $consult->save();

        event(new UpdatedContentEvent(CONSULT_MODULE_SCREEN_NAME, $request, $consult));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('consult.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request)
    {
        try {
            $consult = Consult::query()->findOrFail($id);

            $consult->delete();

            event(new DeletedContentEvent(CONSULT_MODULE_SCREEN_NAME, $request, $consult));

            return $this
                ->httpResponse()
                ->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
