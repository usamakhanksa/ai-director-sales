<?php

$enbale_agent_contact_form = $myHelpers->global_lib->get_option('enbale_agent_contact_form');



if (isset($enbale_agent_contact_form) && $enbale_agent_contact_form == 'Y') {
?>
    <div class="bg-white widget border rounded text-left" id="agent_contact_form">
        <?php
        $recaptcha_site_key = $myHelpers->global_lib->get_option('recaptcha_site_key');
        $recaptcha_secret_key = $myHelpers->global_lib->get_option('recaptcha_secret_key');

        $is_recaptcha_enable = false;
        $isBlogAct = $myHelpers->isPluginActive('google_recaptcha');
        if ($isBlogAct == true) {
            $is_recaptcha_enable = true;
        }

        ?>
        <?php if ($is_recaptcha_enable && !empty($recaptcha_site_key) && !empty($recaptcha_secret_key)) { ?>
            <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit&hl=<?php echo $this->default_language; ?>" async defer>
            </script>
            <script type="text/javascript">
                var onloadCallback = function() {
                    grecaptcha.render('recaptcha_element', {
                        'sitekey': '<?php echo $recaptcha_site_key; ?>'
                    });
                };
            </script>


        <?php } ?>

        <h3 class="h4 text-black widget-title mb-3"><?php echo mlx_get_lang('Contact Agent'); ?></h3>
        <?php
        $args = array('class' => 'form-contact-agent', 'id' => 'contact_agent_form');
        echo form_open_multipart('', $args); ?>
        <input type="hidden" name="p_id" value="<?php echo $myHelpers->global_lib->EncryptClientID($single_property->p_id); ?>">
        <div class="form-group">
            <label for="name"><?php echo mlx_get_lang('Name'); ?> <span class="required text-danger">*</span></label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email"><?php echo mlx_get_lang('Email'); ?> <span class="required text-danger">*</span></label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="message"><?php echo mlx_get_lang('Message'); ?> <span class="required text-danger">*</span></label>
            <textarea id="message" name="message" class="form-control" required style="height:auto;"></textarea>
        </div>
        <?php if ($is_recaptcha_enable && !empty($recaptcha_site_key) && !empty($recaptcha_secret_key)) { ?>
            <div class="row form-group">
                <div class="col-md-12">
                    <div id="recaptcha_element"></div>
                </div>
            </div>
        <?php } ?>
        <div class="form-group">
            <button type="submit" name="submit" class="btn custom-btn text-white submit-contact-agent-form-btn"><?php echo mlx_get_lang('Send Message'); ?></button>
        </div>
        </form>
    </div>
<?php } ?>