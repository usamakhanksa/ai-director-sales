<?php

namespace Botble\RealEstate\Http\Controllers\Settings;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\RealEstate\Http\Requests\Settings\InvoiceTemplateSettingRequest;
use Botble\RealEstate\Supports\InvoiceHelper;
use Botble\Setting\Http\Controllers\SettingController;
use Illuminate\Support\Facades\File;

class InvoiceTemplateSettingController extends SettingController
{
    public function edit(InvoiceHelper $invoiceHelper)
    {
        Assets::addScriptsDirectly('vendor/core/core/setting/js/setting.js');

        $content = $invoiceHelper->getInvoiceTemplate();
        $variables = $invoiceHelper->getVariables();

        return view('plugins/real-estate::invoices.template', compact('content', 'variables'));
    }

    public function update(InvoiceTemplateSettingRequest $request)
    {
        BaseHelper::saveFileData(storage_path('app/templates/invoice.tpl'), $request->input('content'), false);

        return $this
            ->httpResponse()
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function reset()
    {
        File::delete(storage_path('app/templates/invoice.tpl'));

        return $this
            ->httpResponse()
            ->setMessage(trans('core/setting::setting.email.reset_success'));
    }

    public function preview(InvoiceHelper $invoiceHelper)
    {
        $invoice = $invoiceHelper->getDataForPreview();

        return $invoiceHelper->streamInvoice($invoice);
    }
}
