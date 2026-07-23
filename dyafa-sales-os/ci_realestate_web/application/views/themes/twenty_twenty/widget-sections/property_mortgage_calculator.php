<?php

$enbale_mortgage_calculator = $myHelpers->global_lib->get_option('enbale_mortgage_calculator');
if (isset($enbale_mortgage_calculator) && $enbale_mortgage_calculator == 'Y') {
?>
    <div class="bg-white widget border rounded  text-left" id="mortgage_calculator">

        <form class="form-contact-agent" name="MortgageCalculator">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mb-2">
                    <h3 class="h4 text-black widget-title mb-3"><?php echo mlx_get_lang('Mortgage Calculator'); ?></h3>
                    <small><?php echo mlx_get_lang('Use this calculator to estimate your monthly mortgage payment'); ?></small>
                </div>
            </div>
            <div class="form-group">
                <label for="house_price"><?php echo mlx_get_lang('House Price'); ?></label>
                <input type="number" id="house_price" class="form-control" value="" name="price" min="0">
            </div>

            <div class="form-group">
                <label for="interest_rate"><?php echo mlx_get_lang('Interest Rate'); ?> %</label>
                <input id="interest_rate" type="number" class="form-control" value="" name="ir" min="0">
            </div>

            <div class="form-group">
                <label for="years"><?php echo mlx_get_lang('Years'); ?></label>
                <input id="years" type="number" class="form-control" value="" name="term" min="0">
            </div>

            <div class="form-group">
                <label for="down_payment"><?php echo mlx_get_lang('Down Payment'); ?></label>
                <input id="down_payment" type="number" class="form-control" onchange="calculatePayment(this.form)" value="" name="dp" min="0">
            </div>

            <br>
            <div class="form-group">
                <label for="mortgage_principle"><?php echo mlx_get_lang('Mortgage Principle'); ?></label>
                <input id="mortgage_principle" type="text" class="form-control" readonly="" name="principle">
            </div>
            <div class="form-group">
                <label for="total_payments"><?php echo mlx_get_lang('Total Payments'); ?></label>
                <input id="total_payments" type="text" class="form-control" readonly="" name="payments">
            </div>
            <div class="form-group">
                <label for="payment_month"><?php echo mlx_get_lang('Payment/Month'); ?></label>
                <input id="payment_month" type="text" class="form-control" readonly="" name="pmt">
            </div>
            <div class="form-group">
                <input type="button" class="btn custom-btn btn-large text-white" onclick="cmdCalc_Click(this.form)" value="<?php echo mlx_get_lang('Calculate'); ?>" name="cmdCalc">
            </div>
        </form>
    </div>
<?php } ?>