<?php
/**
 * Created by PhpStorm.
 * User: PhuongTH
 * Date: 2/8/2020
 * Time: 9:02 AM
 */
$setting_db = FAT_DB_Setting::instance();
$setting = $setting_db->get_setting();
$current_user = wp_get_current_user();
$setting_currency = $setting_db->get_currency_setting();
$number_of_decimal = $setting['number_of_decimals'];
$symbol_prefix = $setting_currency['symbol_position']=='before' ?  $setting_currency['symbol'] : '';
$symbol_suffix = $setting_currency['symbol_position']=='after' ?  $setting_currency['symbol'] : '';

?>
<div class="fat-sb-price-package-order-history fat-sb-booking-history">
    <table>
        <thead>
        <tr>
            <th><?php echo esc_html__('Order Date', 'fat-services-booking'); ?></th>
            <th><?php echo esc_html__('Email', 'fat-services-booking'); ?></th>
            <th><?php echo esc_html__('Package Name', 'fat-services-booking'); ?></th>
            <th><?php echo esc_html__('Price', 'fat-services-booking'); ?></th>
            <th><?php echo esc_html__('Price for payment', 'fat-services-booking'); ?></th>
            <th><?php echo esc_html__('Gateway', 'fat-services-booking'); ?></th>
            <th class="fat-sb-status"><?php echo esc_html__('Gateway status', 'fat-services-booking'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($current_user->ID)):
            $package_db = FAT_DB_Price_Package::instance();
            $orders = isset($current_user->ID) ? $package_db->get_package_order_by_user($current_user->ID) : array();
            foreach ($orders as $od) { ?>
                <tr>
                    <td data-label="<?php echo esc_html__('Order Date', 'fat-services-booking'); ?>"><?php echo esc_html($od->pko_create_date); ?></td>
                    <td data-label="<?php echo esc_html__('Email', 'fat-services-booking'); ?>"> <?php echo esc_html($od->pko_user_email); ?></td>
                    <td data-label="<?php echo esc_html__('Package Name', 'fat-services-booking'); ?>"><?php echo esc_html($od->pk_name); ?></td>
                    <td data-label="<?php echo esc_html__('Price', 'fat-services-booking'); ?>"><?php echo  ($symbol_prefix . number_format($od->pk_price, $number_of_decimal). $symbol_suffix); ?></td>
                    <td data-label="<?php echo esc_html__('Price for payment', 'fat-services-booking'); ?>"><?php echo ($symbol_prefix . number_format($od->pk_price_for_payment, $number_of_decimal). $symbol_suffix); ?></td>
                    <td data-label="<?php echo esc_html__('Gateway', 'fat-services-booking'); ?>"><?php echo esc_html($od->pko_gateway_type); ?></td>
                    <td data-label="<?php echo esc_html__('Gateway status', 'fat-services-booking'); ?>"
                        class="fat-sb-status">
                        <?php echo ($od->pko_gateway_status == '1' || $od->pko_gateway_status == 'approved') ? esc_html__('Success', 'fat-sb-booking') : esc_html__('Cancel', 'fat-sb-booking'); ?>
                    </td>
                </tr>
            <?php } ?>
        <?php else: ?>
            <tr>
                <td colspan="7">
                    <div class="fat-sb-not-found">
                        <?php echo esc_html__('Please login to view package order history', 'fat-services-booking'); ?>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

