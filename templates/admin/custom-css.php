<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 3/4/2019
 * Time: 3:54 PM
 */

$fat_db_setting = FAT_DB_Setting::instance();
$custom_css = $fat_db_setting->get_custom_css();
echo sprintf('%s', $custom_css);
?>
<div class="fat-sb-header">
    <img src="<?php echo esc_url(FAT_SERVICES_ASSET_URL . '/images/plugin_logo.png'); ?>">
    <div class="fat-sb-header-title"><?php echo esc_html__('Custom CSS', 'fat-services-booking'); ?></div>
    <?php
    $part = 'getting-started.html';
    include plugin_dir_path(__FILE__) . 'tool-tip.php'; ?>
</div>
<div class="fat-sb-custom-css-container fat-semantic-container fat-min-height-300 fat-pd-right-15">
    <div class="ui card full-width">
        <div class="content">
            <div class="ui form">
                <div class="one fields">
                    <div class="field ">
                        <label for="name"><?php echo esc_html__('Custom CSS', 'fat-services-booking'); ?></label>
                        <div class="ui left input ace-container">
                            <textarea class="fat-hidden" name="custom_css"><?php echo html_entity_decode($custom_css); ?></textarea>
                            <div id="hidden_custom_css" class="fat-hidden"><?php echo html_entity_decode($custom_css); ?></div>
                            <pre data-mode="css" id="custom_css" class="fat-sb-ace-editor">
                            </pre>
                        </div>
                        <span class="fat-field-description">
                            <?php echo esc_html__('Please add custom css at here if you want customize shortcode style at frontend', 'fat-services-booking'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="actions">
            <button class="ui blue button fat-submit-modal"
                    data-onClick="FatSbCustomCSS.submitCustomCSS"
                    data-success-message="<?php echo esc_attr__('Custom CSS has been saved', 'fat-services-booking'); ?>">
                <i class="save outline icon"></i>
                <?php echo esc_html__('Save', 'fat-services-booking'); ?>
            </button>

        </div>
    </div>
</div>