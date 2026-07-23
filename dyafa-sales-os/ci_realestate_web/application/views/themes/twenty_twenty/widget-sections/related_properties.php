<?php if (isset($related_properties) && $related_properties->num_rows() > 0) { ?>
    <div class="site-section site-section-sm bg-light d-print-none">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="site-section-title mb-5">
                        <h2 class="text-left"><?php echo mlx_get_lang('Related Properties'); ?></h2>
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="related-property owl-carousel col-md-12">
                    <?php foreach ($related_properties->result() as $prop_row) { ?>

                        <?php include('../../../property/template-part/single-property-grid.php'); ?>

                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>