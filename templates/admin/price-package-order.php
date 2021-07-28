<?php
/**
 * Created by PhpStorm.
 * User: PhuongTH
 * Date: 2/6/2020
 * Time: 2:11 PM
 */
?>
<div class="fat-sb-header">
    <img src="<?php echo esc_url(FAT_SERVICES_ASSET_URL.'/images/plugin_logo.png');?>">
    <div class="fat-sb-header-title"><?php echo esc_html__('Package order','fat-services-booking');?></div>
    <?php
    $part = 'features/booking.html';
    include plugin_dir_path(__FILE__) . 'tool-tip.php'; ?>
</div>
<div class="fat-sb-price-package-order-container fat-semantic-container fat-min-height-300 fat-pd-right-15">
    <div class="ui card full-width">
        <div class="content has-button-group">
            <div class="toolbox-action-group">
                <div class="ui transparent left icon input ui-search fat-sb-search no-border-radius fat-mg-right-10 " >
                    <input type="text" id="user_email" id="user_email" data-onKeyUp="FatSbPricePackageOrder.searchNameKeyup" autocomplete="nope"
                           placeholder="<?php echo esc_attr__('Search email ...','fat-services-booking');?>">
                    <i class="search icon"></i>
                    <a class="fat-close" data-onClick="FatSbPricePackageOrder.closeSearchOnClick">
                        <i class="times icon"></i>
                    </a>
                </div>

                <div class="ui transparent left date-input input no-border-radius">
                    <?php
                    $start_date = new DateTime();
                    $end_date = new DateTime();
                    $end_date->modify('+6 day');
                    $date_format = get_option('date_format');
                    $locale = get_locale();
                    $locale = explode('_',$locale)[0];

                    ?>
                    <input type="text"  class="date-range-picker"  name="date_of_book" id="date_of_book" data-auto-update="1" data-onChange="FatSbPricePackageOrder.searchDateOnChange"
                           data-start="<?php echo esc_attr($start_date->format('Y-m-d'));?>" data-end="<?php echo esc_attr($end_date->format('Y-m-d'));?>"
                           data-locale="<?php echo esc_attr($locale);?>"
                           data-start-init="<?php echo esc_attr($start_date->format('m/d/Y'));?>"
                           data-end-init="<?php echo esc_attr($end_date->format('m/d/Y'));?>" >
                </div>
            </div>
        </div>
        <div class="content">
            <table class="ui single line table fat-sb-list-package-order">
                <thead>
                <tr>
                  <!--  <th>
                        <div class="ui checkbox">
                            <input type="checkbox" name="example" class="table-check-all">
                            <label></label>
                        </div>
                    </th>-->
                    <th><?php echo esc_html__('Order Date','fat-services-booking');?></th>
                    <th><?php echo esc_html__('User','fat-services-booking');?></th>
                    <th><?php echo esc_html__('Package Name','fat-services-booking');?></th>
                    <th><?php echo esc_html__('Price','fat-services-booking');?></th>
                    <th><?php echo esc_html__('Price for payment','fat-services-booking');?></th>
                    <th><?php echo esc_html__('Description','fat-services-booking');?></th>
                    <th class="fat-sb-payment"><?php echo esc_html__('Gateway','fat-services-booking');?></th>
                    <th class="fat-sb-status"><?php echo esc_html__('Gateway status','fat-services-booking');?></th>
                    <th class="fat-sb-action"></th>
                </tr>
                </thead>
                <tbody>
                <tr class="fat-tr-not-found">
                    <td colspan="9">
                        <div class="ui fluid placeholder">
                            <div class="line"></div>
                            <div class="line"></div>
                            <div class="line"></div>
                            <div class="line"></div>
                            <div class="line"></div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="fat-sb-pagination" data-obj="FatSbPricePackageOrder" data-func="loadPackageOrder">

            </div>
        </div>
    </div>
</div>