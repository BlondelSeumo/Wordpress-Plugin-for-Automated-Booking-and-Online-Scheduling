<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 11/20/2018
 * Time: 10:42 AM
 */

$booking_form  = get_option('fat_sb_booking_form','[]');
$booking_form = stripslashes($booking_form);
?>
<div class="fat-sb-header">
    <img src="<?php echo esc_url(FAT_SERVICES_ASSET_URL.'/images/plugin_logo.png');?>">
    <div class="fat-sb-header-title"><?php echo esc_html__('Form Builder','fat-services-booking');?></div>
    <?php
    $part = 'features/form-builder.html';
    include FAT_SERVICES_DIR_PATH . 'templates/admin/tool-tip.php'; ?>
</div>
<div class="fat-sb-coupons-container fat-semantic-container fat-min-height-300 fat-pd-right-15">
    <div class="ui card full-width">
        <div class="content">
            <div class="fat-form-builder" data-form="<?php echo esc_attr($booking_form);?>">

            </div>
            <div><?php echo esc_html__('Please do not use HTML code or inline style or script for field attribute. It will be breaking form', 'fat-services-booking');?></div>
        </div>
    </div>
</div>