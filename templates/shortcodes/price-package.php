<?php
/**
 * Created by PhpStorm.
 * User: PhuongTH
 * Date: 2/6/2020
 * Time: 5:17 PM
 */
$fat_db_setting = FAT_DB_Setting::instance();
$currency = $fat_db_setting->get_currency_setting();
$setting = $fat_db_setting->get_setting();
$number_of_decimal = isset($setting['number_of_decimals']) && $setting['number_of_decimals'] != '' ? $setting['number_of_decimals'] : 2;

$column = isset($atts['column']) && $atts['column'] ? $atts['column'] : 3;
$css_class = 'fat-sb-price-package column-' . $column;
$db_package = FAT_DB_Price_Package::instance();
$package = $db_package->get_package();

$payment_setting = $fat_db_setting->get_setting();
?>
<div class="<?php echo esc_attr($css_class); ?>" data-paypal="<?php echo esc_attr($payment_setting['paypal_enable']);?>"
data-stripe="<?php echo esc_attr($payment_setting['stripe_enable']);?>" data-myPOS="<?php echo esc_attr($payment_setting['myPOS_enable']);?>"
     data-przelewy24="<?php echo esc_attr($payment_setting['przelewy24_enable']);?>">
    <?php
    $pk_image_url = '';
    $price = 0;
    $price_for_payment = 0;
    foreach ($package as $pk) {
        $pk_image_url = isset($pk->pk_image_id) ? wp_get_attachment_image_src($pk->pk_image_id, 'medium') : '';
        $pk_image_url = isset($pk_image_url[0]) ? $pk_image_url[0] : '';
        $price = number_format($pk->pk_price, $number_of_decimal);
        $price_for_payment = number_format($pk->pk_price_for_payment, $number_of_decimal);
        ?>
        <div class="price-package-item">
            <div class="pk-thumbnail">
                <?php if ($pk_image_url): ?>
                    <img src="<?php echo esc_url($pk_image_url); ?>" title="<?php echo esc_html($pk->pk_name); ?>"
                         alt="<?php echo esc_html($pk->pk_name); ?>">
                <?php endif; ?>
            </div>
            <h3 class="pk-name">
                <?php echo esc_html($pk->pk_name); ?>
            </h3>
            <div class="pk-price-wrap">
                <span class="pk-price"><?php echo esc_html($currency['symbol'] . $price); ?> </span>
                <?php if ($pk->pk_price != $pk->pk_price_for_payment): ?>
                    / <span class="pk-price-for-payment"><?php echo esc_html($price_for_payment); ?></span>
                <?php endif; ?>
            </div>
            <div class="pk-description">
                <?php echo esc_html($pk->pk_description); ?>
            </div>
            <div class="pk-payment-method">
            </div>
            <div class="pk-button-purchase">
                <a href="#" class="fat-sb-select-package fat-bt" data-onClick="FatSbPricePackageOrderFE.selectPackage" data-pk-id="<?php echo esc_attr($pk->pk_id);?>">
                    <?php echo esc_html__('Select','fat-services-booking');?>
                </a>
                <a href="#" class="fat-sb-payment-submit fat-bt" data-onClick="FatSbPricePackageOrderFE.submitPayment" data-pk-id="<?php echo esc_attr($pk->pk_id);?>">
                    <?php echo esc_html__('Purchase','fat-services-booking');?>
                </a>
            </div>
        </div>
    <?php } ?>
</div>
