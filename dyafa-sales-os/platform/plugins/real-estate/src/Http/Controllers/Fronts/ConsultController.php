<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Models\Consult;
use Botble\RealEstate\Tables\Fronts\ConsultTable;

class ConsultController extends BaseController
{
    public function index(ConsultTable $table)
    {
        $this->pageTitle(trans('plugins/real-estate::consult.name'));

        return $table->renderTable();
    }

    public function show(int|string $id)
    {
        $consult = Consult::query()
            ->whereAccount(auth('account')->user())
            ->with(['project', 'property'])
            ->findOrFail($id);

        $this->pageTitle(trans('plugins/real-estate::account.viewing_consult', ['name' => $consult->getKey()]));

        return view('plugins/real-estate::account.consults.show', compact('consult'));
    }
}
