<html>
<head>
<style>
    /*
     * Branded invoice document (BRD Section 10 "Download Invoices") - the one
     * artifact a corporate client actually downloads and keeps. Rendered by
     * dompdf outside the normal header.php layout, so brand colors are
     * plain hex here (dompdf/most PDF renderers don't reliably support
     * CSS custom properties or color-mix()) rather than the CSS variables
     * used everywhere else - the values below must stay in sync with the
     * 5-hex palette in application/views/dyafa/layout/header.php.
     */
    body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #2a273c; margin: 0; }
    .dso-pdf-header { background: #2a273c; color: #ffffff; padding: 22px 30px; }
    .dso-pdf-header table { width: 100%; border: none; margin: 0; }
    .dso-pdf-header td { border: none; padding: 0; vertical-align: middle; }
    .dso-pdf-brand-mark {
        display: inline-block; width: 36px; height: 36px; background: #e95a54;
        color: #ffffff; font-weight: bold; font-size: 15px; text-align: center;
        line-height: 36px; border-radius: 8px;
    }
    .dso-pdf-brand-name { font-size: 16px; font-weight: bold; padding-left: 10px; }
    .dso-pdf-brand-tagline { font-size: 10px; color: #fbcdab; padding-left: 10px; }
    .dso-pdf-invoice-title { text-align: right; font-size: 22px; font-weight: bold; color: #ffffff; }
    .dso-pdf-invoice-no { text-align: right; font-size: 11px; color: #fbcdab; }

    .dso-pdf-body { padding: 24px 30px; }
    .meta { margin-top: 4px; margin-bottom: 20px; }
    .meta table { width: 100%; border: none; }
    .meta td { border: none; padding: 3px 0; font-size: 12.5px; }
    .meta .label { color: #8f9793; width: 140px; }

    table.items { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table.items th, table.items td { border: 1px solid #e3e1db; padding: 8px 12px; text-align: left; }
    table.items th { background: #f2f0eb; color: #2a273c; font-size: 11px; text-transform: uppercase; letter-spacing: 0.03em; }
    table.items td.amount, table.items th.amount { text-align: right; }
    .total-row td { font-weight: bold; background: #f2f0eb; }

    .dso-pdf-badge {
        display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 11px;
        font-weight: bold; color: #ffffff; background: #8f9793;
    }
    .dso-pdf-badge.paid { background: #8f9793; }
    .dso-pdf-badge.overdue,.dso-pdf-badge.pending { background: #e95a54; }

    .dso-pdf-footer {
        margin-top: 34px; padding-top: 12px; border-top: 1px solid #e3e1db;
        font-size: 10.5px; color: #8f9793; text-align: center;
    }
</style>
</head>
<body>
    <div class="dso-pdf-header">
        <table>
            <tr>
                <td style="width: 60%;">
                    <table style="width: auto;">
                        <tr>
                            <td style="width: 36px;"><span class="dso-pdf-brand-mark">DS</span></td>
                            <td>
                                <div class="dso-pdf-brand-name">Dyafa Sales OS</div>
                                <div class="dso-pdf-brand-tagline">Dyafa Hospitality Services Company</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 40%;">
                    <div class="dso-pdf-invoice-title">INVOICE</div>
                    <div class="dso-pdf-invoice-no">#<?php echo htmlspecialchars($collection->invoice_no); ?></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="dso-pdf-body">
        <div class="meta">
            <table>
                <tr><td class="label">Bill To</td><td><b><?php echo htmlspecialchars($account->company_name); ?></b></td></tr>
                <tr><td class="label">Due Date</td><td><?php echo $collection->due_date; ?></td></tr>
                <tr><td class="label">Status</td><td>
                    <span class="dso-pdf-badge <?php echo strtolower($collection->status); ?>"><?php echo $collection->status; ?></span>
                </td></tr>
                <?php if (!empty($collection->payment_reference)): ?>
                <tr><td class="label">Payment Reference</td><td><?php echo htmlspecialchars($collection->payment_reference); ?></td></tr>
                <?php endif; ?>
                <?php if (!empty($collection->finance_reference)): ?>
                <tr><td class="label">Finance/ERP Reference</td><td><?php echo htmlspecialchars($collection->finance_reference); ?></td></tr>
                <?php endif; ?>
            </table>
        </div>

        <table class="items">
            <tr><th>Description</th><th class="amount">Amount</th></tr>
            <tr><td>Invoice <?php echo htmlspecialchars($collection->invoice_no); ?></td><td class="amount"><?php echo number_format($collection->amount, 2); ?></td></tr>
            <tr><td>Paid</td><td class="amount"><?php echo number_format($collection->paid_amount, 2); ?></td></tr>
            <tr class="total-row"><td>Balance Due</td><td class="amount"><?php echo number_format($collection->amount - $collection->paid_amount, 2); ?></td></tr>
        </table>

        <div class="dso-pdf-footer">
            Dyafa Hospitality Services Company &middot; Generated via the Corporate Self-Service Portal &middot; Dyafa Sales OS
        </div>
    </div>
</body>
</html>
