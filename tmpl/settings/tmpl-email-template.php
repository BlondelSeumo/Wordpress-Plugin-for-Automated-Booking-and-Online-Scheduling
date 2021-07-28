<?php
/**
 * Created by PhpStorm.
 * User: RoninWP
 * Date: 5/29/2019
 * Time: 10:11 AM
 */
?>
<script type="text/html" id="tmpl-fat-sb-test-email-template">
    <div class="ui modal tiny fat-semantic-container fat-test-email-template-modal">
        <div class="header fat-sb-popup-title"><?php echo esc_html__('Send Test Email','fat-services-booking'); ?></div>
        <div class="scrolling content">
            <div class="ui form">
                <div class="one fields">
                    <div class="field ">
                        <label for="name"><?php echo esc_html__('Recipient Email','fat-services-booking'); ?><span
                                class="required"> *</span></label>
                        <div class="ui left icon input ">
                            <input type="email" name="send_to" id="send_to" autocomplete="nope"
                                   placeholder="<?php echo esc_attr__('Recipient Email','fat-services-booking'); ?>" required>
                            <i class="envelope outline icon"></i>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter recipient email','fat-services-booking'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="actions">
            <button class="ui basic button fat-close-modal">
                <i class="times circle outline icon"></i>
                <?php echo esc_html__('Cancel','fat-services-booking'); ?>
            </button>

            <button class="ui blue button fat-submit-modal"  data-invalid-message="<?php echo esc_attr__('Please input valid email','fat-services-booking'); ?>"
                    data-success-message="<?php echo esc_attr__('Email test has been send, please check mailbox','fat-services-booking'); ?>"
                    data-onClick="FatSbEmailTemplate.sendTestMailTemplate">
                <i class="paper plane outline icon"></i>
                <?php echo esc_html__('Send','fat-services-booking'); ?>
            </button>

        </div>
    </div>
</script>
