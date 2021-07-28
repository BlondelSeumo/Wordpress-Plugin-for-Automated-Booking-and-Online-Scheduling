<?php
/**
 * Created by PhpStorm.
 * User: RoninWP
 * Date: 6/17/2019
 * Time: 10:43 AM
 */

$setting_db = FAT_DB_Setting::instance();
$setting = $setting_db->get_setting();
$setting_currency = $setting_db->get_currency_setting();
$employee_label = isset($setting['employee_label']) && $setting['employee_label'] ? esc_html__('Select ', 'fat-services-booking').$setting['employee_label'] : esc_html__('Select Employee', 'fat-services-booking');
$service_label = isset($setting['service_label']) && $setting['service_label'] ? $setting['service_label'] : esc_html__('Service','fat-services-booking');
$extra_service_label = esc_html__('Extra ','fat-services-booking'). strtolower($service_label);
$price_label = isset($setting['price_label']) && $setting['price_label'] ? $setting['price_label'] : esc_html__('Price:', 'fat-services-booking');
$number_of_person_label = isset($setting['number_of_person_label']) && $setting['number_of_person_label'] ? $setting['number_of_person_label'] : esc_html__('Number of persons', 'fat-services-booking');
$total_cost_label = isset($setting['total_cost_label']) && $setting['total_cost_label'] ? $setting['total_cost_label'] : esc_html__('Total cost:', 'fat-services-booking');
$payment_method_label = isset($setting['payment_method_label']) && $setting['payment_method_label'] ? $setting['payment_method_label'] : esc_html__('Payment method:', 'fat-services-booking');
$s_id = isset($atts['service']) ? $atts['service'] : '';
$disable_customer_email =  isset($setting['disable_customer_email']) && $setting['disable_customer_email'] == '1' ? 1 : 0;

$booking_form = get_option('fat_sb_booking_form', '[]');
$booking_form = stripslashes($booking_form);
$container_class = 'fat-semantic-container fat-booking-container fat-sb-services-layout fat-sb-one-service  fat-sb-layout-' .$column.'-column';
$container_class .= isset($atts['hide_number_of_person']) && $atts['hide_number_of_person']=='1' ? ' hide-number-person' : '';
$container_class .= isset($atts['hide_employee']) && $atts['hide_employee'] == '1' ? ' hide-employee' : '';

if(isset($atts['hide_payment_info']) && $atts['hide_payment_info']=='1'){
    $container_class .= ' hide-payment-info';
}else{
    $container_class .= isset($setting['hide_payment']) && $setting['hide_payment']=='1' ? ' hide-payment-info' : '';
}

$current_user = wp_get_current_user();
$first_name = $last_name = $email = $phone = '';
$disable_field = '';
$email = $disable_customer_email ? uniqid().'@no_email.com' : '';
if($current_user->exists() ){
    $first_name = $current_user->first_name ? $current_user->first_name : $current_user->user_login ;
    $last_name = $current_user->last_name ? $current_user->last_name :  $current_user->user_login ;
    $email = $current_user->user_email;
    $phone = get_user_meta($current_user->ID, 'phone_number', true);
    $disable_field = 'disabled';
}
$phone_code_default = isset($setting['default_phone_code']) && $setting['default_phone_code'] ? $setting['default_phone_code'] : '+44';

$booked_message = isset($setting['booked_message']) && $setting['booked_message'] ? $setting['booked_message'] : esc_html__('Thank you! Your booking is complete. An email with detail of your booking has been send to you.','fat-services-booking');
?>

<div class="<?php echo esc_attr($container_class);?>" style="opacity: 0" data-service="<?php echo esc_attr($s_id);?>">
    <div class="fat-sb-tab-wrap">
        <ul class="fat-sb-tab">
            <li class="active employee" data-tab="services-provider" data-onClick="FatSbBookingServices_FE.tabItemOnClick">
                <?php echo esc_html($employee_label); ?>
            </li>
            <li class="fat-disabled" data-tab="time" data-onClick="FatSbBookingServices_FE.tabItemOnClick">
                <?php echo esc_html__('Time', 'fat-services-booking'); ?>
            </li>
            <li class="fat-disabled" data-tab="customer" data-onClick="FatSbBookingServices_FE.tabItemOnClick">
                <?php echo esc_html__('Details', 'fat-services-booking'); ?>
            </li>
            <li class="fat-disabled" data-tab="completed" data-onClick="FatSbBookingServices_FE.tabItemOnClick">
                <?php esc_html_e('Submit', 'fat-services-booking'); ?>
            </li>
        </ul>
    </div>
    <div class="fat-sb-tab-content-wrap">
        <div class="fat-sb-tab-content has-box-shadow services fat-hidden" data-tab="services">
            <div class="fat-sb-list-services"></div>
        </div>

        <div class="fat-sb-tab-content has-box-shadow service-provider active" data-tab="services-provider">
            <div class="fat-sb-list-employees">

            </div>
        </div>

        <?php
        $locale = get_locale();
        $locale = explode('_', $locale)[0];
        $locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.' . $locale . '.js';
        if($locale=='pl'){
            $locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.pl-PL.js';
        }
        if(!file_exists($locale_path)){
            $locale = 'en';
        }
        ?>

        <div class="fat-sb-tab-content time fat-hidden" data-tab="time">
            <div class="fat-sb-data-selected">
            </div>
            <div class="fat-sb-datetime-wrap">
                <div class="fat-sb-datetime-inner">
                    <div class="fat-sb-booking-date-wrap">
                        <input type='text' class="air-date-picker fat-sb-hidden"
                               data-locale="<?php echo esc_attr($locale); ?>" required autocomplete="off"
                               name="b_date" id="b_date"/>
                    </div>
                    <div class="fat-sb-booking-time-wrap">
                        <?php
                        $db_setting = FAT_DB_Setting::instance();
                        $setting = $db_setting->get_setting();
                        $time_step = isset($setting['time_step']) && $setting['time_step'] ? $setting['time_step'] : 15;
                        $work_hours = FAT_SB_Utils::getWorkHours($time_step); ?>
                        <?php foreach ($work_hours as $key => $value) { ?>
                            <div class="item disabled" data-value="<?php echo esc_attr($key); ?>"
                                 data-label="<?php echo esc_attr($value); ?>"
                                 data-onClick="FatSbBookingServices_FE.timeItemOnClick">
                                <div class="time-label">
                                    <?php echo esc_html($value); ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="fat-sb-multiple-days">
                    <h4><?php echo esc_html('Selected dates', 'fat-services-booking'); ?></h4>
                    <ul class="list-multiple-days">

                    </ul>
                </div>
            </div>
            <div class="fat-sb-button-groups">
                <button class="ui primary button fat-next-step fat-bt-payment fat-bt disabled" data-onclick="FatSbBookingServices_FE.nextToOrderDetail">
                    <?php echo esc_html__('Next','fat-services-booking');?>
                </button>
            </div>
        </div>

        <div class="fat-sb-tab-content customer fat-sb-hidden" data-tab="customer">
            <div class="fat-sb-data-selected">
                <div class="fat-sb-date-time-item data-item">
                    <div class="fat-sb-date-time-item-inner active">
                        <div class="fat-sb-item-avatar">
                        </div>
                        <div class="fat-sb-item-content">
                            <div class="date-title"></div>
                            <div class="meta">
                                <div class="time-title"></div>
                            </div>
                        </div>
                        <span class="fat-check"></span>
                    </div>
                </div>
            </div>
            <div class="fat-sb-customer-wrap">
                <div class="fat-sb-col-left">
                    <h3><?php esc_html_e('Your information', 'fat-services-booking'); ?></h3>
                    <div class="ui form">
                        <div class="one fields">
                            <div class="field ">
                                <label for="c_first_name"><?php echo esc_html__('First name', 'fat-services-booking'); ?><span
                                        class="required"> *</span></label>
                                <div class="ui left input ">
                                    <input type="text" name="c_first_name" id="c_first_name"
                                           data-onChange="FatSbBookingServices_FE.resetValidateField"
                                           value="<?php esc_html_e($first_name);?>"
                                        <?php echo esc_attr($disable_field);?>
                                           placeholder="<?php echo esc_attr__('First name', 'fat-services-booking'); ?>"
                                           required>
                                </div>
                                <div class="field-error-message">
                                    <?php echo esc_html__('Please enter first name', 'fat-services-booking'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="one fields">
                            <div class="field ">
                                <label for="c_last_name"><?php echo esc_html__('Last name', 'fat-services-booking'); ?><span
                                        class="required"> *</span></label>
                                <div class="ui left input ">
                                    <input type="text" name="c_last_name" id="c_last_name"
                                           data-onChange="FatSbBookingServices_FE.resetValidateField"
                                           value="<?php esc_html_e($last_name);?>"
                                        <?php echo esc_attr($disable_field);?>
                                           placeholder="<?php echo esc_attr__('Last name', 'fat-services-booking'); ?>"
                                           required>
                                </div>
                                <div class="field-error-message">
                                    <?php echo esc_html__('Please enter last name', 'fat-services-booking'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="one fields <?php echo ($disable_customer_email? 'fat-sb-hidden' : '');?>">
                            <div class="field ">
                                <label for="email"><?php echo esc_html__('Email', 'fat-services-booking'); ?> <span
                                        class="required"> *</span></label>
                                <div class="ui left input">
                                    <input type="email" name="c_email" id="c_email"
                                           data-onChange="FatSbBookingServices_FE.resetValidateField"
                                           value="<?php esc_html_e($email);?>"
                                        <?php echo esc_attr($disable_field);?>
                                           placeholder="<?php echo esc_attr__('Email', 'fat-services-booking'); ?>" required>
                                </div>
                                <div class="field-error-message">
                                    <?php echo esc_html__('Please enter email', 'fat-services-booking'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="one fields">
                            <div class="field phone-field">
                                <label for="phone"><?php echo esc_html__('Phone', 'fat-services-booking'); ?> <span
                                            class="required"> *</span></label>

                                <div class="ui fluid search selection dropdown phone-code">
                                    <input type="hidden" name="phone_code" id="phone_code" autocomplete="nope" value="<?php echo esc_attr($phone_code_default);?>">
                                    <i class="dropdown icon"></i>
                                    <div class="default text"></div>
                                    <div class="menu">
                                        <?php
                                        $phoneCode = FAT_SB_Utils::getPhoneCountry();
                                        foreach($phoneCode as $pc){
                                            $pc = explode(',',$pc);?>
                                            <div class="item"  data-value="<?php echo esc_attr($pc[1].','.$pc[2]);?>"><i class="<?php echo esc_attr($pc[2]);?> flag"></i><?php echo esc_html($pc[0]);?><span>(<?php echo esc_html($pc[1]);?>)</span></div>
                                        <?php } ?>
                                        <div class="item" data-value="other"><?php echo esc_html__('Other','fat-services-booking');?></div>
                                    </div>
                                </div>


                                <div class="ui left input phone-number">
                                    <input type="text" name="c_phone" id="c_phone" data-onChange="FatSbBookingServices_FE.resetValidateField"
                                           value="<?php esc_html_e($phone);?>"
                                           placeholder="<?php echo esc_attr__('Phone', 'fat-services-booking'); ?>" required>
                                </div>
                                <div class="field-error-message">
                                    <?php echo esc_html__('Please enter phone', 'fat-services-booking'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="one fields number-of-person-field">
                            <div class="field">
                                <label><?php echo esc_html($number_of_person_label); ?> <span
                                        class="required"> *</span></label>
                                <select class="fat-sb-number-of-person-wrap" name="number_of_person"
                                        id="number_of_person"
                                        data-onChange="FatSbBookingServices_FE.numberPersonOnChange">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                        </div>

                        <?php
                        if ($booking_form !== '' && $booking_form !== '[]'):
                            $booking_form = json_decode($booking_form);
                            $onChange = 'FatSbBookingServices_FE.resetValidateField';
                            foreach ($booking_form as $field) {
                                if(is_readable(FAT_SERVICES_DIR_PATH . '/templates/form-builder/fields/' . $field->type . '.php')){
                                    include FAT_SERVICES_DIR_PATH . '/templates/form-builder/fields/' . $field->type . '.php';
                                }
                            }
                        endif; ?>

                        <div class="one fields">
                            <div class="field ">
                                <label for="note"><?php echo esc_html__('Note', 'fat-services-booking'); ?></label>
                                <div class="ui left input">
                                    <textarea rows="3" name="note" id="note"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fat-sb-col-right">
                    <h3><?php esc_html_e('Your order', 'fat-services-booking'); ?></h3>
                    <ul class="fat-order-wrap">
                        <li class="fat-sb-order-service">
                            <span class="fat-item-label"><?php echo esc_html($service_label); ?>:</span>
                            <span class="fat-item-value"></span>
                        </li>
                        <li class="fat-sb-order-employee">
                            <span class="fat-item-label"><?php echo esc_html($employee_label); ?>:</span>
                            <span class="fat-item-value"></span>
                        </li>
                        <li class="fat-sb-order-date">
                            <span class="fat-item-label"><?php esc_html_e('Date:', 'fat-services-booking'); ?></span>
                            <span class="fat-item-value"></span>
                        </li>
                        <li class="fat-sb-order-time">
                            <span class="fat-item-label"><?php esc_html_e('Time:', 'fat-services-booking'); ?></span>
                            <span class="fat-item-value"></span>
                        </li>
                        <li class="fat-sb-order-multiple-dates">
                            <span class="fat-item-label"><?php esc_html_e('Selected dates:', 'fat-services-booking'); ?></span>
                            <span class="fat-item-value">

                            </span>
                        </li>
                        <li class="fat-sb-order-location">
                            <span class="fat-item-label"><?php esc_html_e('Location:', 'fat-services-booking'); ?></span>
                            <span class="fat-item-value"></span>
                        </li>
                        <li class="fat-sb-order-price">
                            <span class="fat-item-label"><?php echo esc_html($price_label); ?></span>
                            <span class="fat-item-value"></span>
                        </li>
                        <li class="fat-sb-order-extra-service fat-sb-hidden">
                            <span class="fat-item-label"><?php echo esc_html($extra_service_label); ?></span>
                            <span class="fat-item-value"><ul></ul></span>
                        </li>
                        <li class="fat-sb-order-tax">
                            <span class="fat-item-label"><?php esc_html_e('Tax:', 'fat-services-booking'); ?></span>
                            <span class="fat-item-value"></span>
                        </li>

                        <?php
                        $coupon_db = FAT_DB_Coupons::instance();
                        $is_exist_coupon = $coupon_db->exists_coupon();
                        if ($is_exist_coupon): ?>
                            <li class="fat-sb-order-coupon">
                                <span class="fat-item-label"><?php esc_html_e('Coupon:', 'fat-services-booking'); ?></span>
                                <span class="fat-item-value">
                                    <div class="ui left input">
                                        <input type="text" name="coupon" id="coupon"
                                               data-onChange="FatSbBookingServices_FE.couponOnChange"
                                               placeholder="<?php esc_attr_e('Coupon code', 'fat-services-booking'); ?>">
                                    </div>
                                    <button class="ui icon button" data-onClick="FatSbBookingServices_FE.initCoupon"
                                            data-content="<?php esc_attr_e('Click here to apply coupon code', 'fat-services-booking'); ?>">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                </span>
                                <div class="fat-coupon-error"></div>
                            </li>
                        <?php endif; ?>


                        <li class="fat-sb-order-payment-method">
                            <span class="fat-item-label"><?php echo esc_html($payment_method_label); ?></span>
                            <select class="fat-sb-payment-method-wrap" name="payment_method" id="payment_method"
                                    data-onChange="FatSbBookingServices_FE.paymentOnChange">
                                <?php if (!isset($setting['onsite_enable']) || $setting['onsite_enable'] == "1") : ?>
                                    <option value="onsite"><?php esc_html_e('Onsite payment', 'fat-services-booking'); ?></option>
                                <?php endif; ?>
                                <?php if (isset($setting['paypal_enable']) && $setting['paypal_enable'] == "1") : ?>
                                    <option value="paypal"><?php esc_html_e('Paypal', 'fat-services-booking'); ?></option>
                                <?php endif; ?>
                                <?php if (isset($setting['stripe_enable']) && $setting['stripe_enable'] == "1") : ?>
                                    <option value="stripe"><?php esc_html_e('Stripe', 'fat-services-booking'); ?></option>
                                <?php endif; ?>
                                <?php if (isset($setting['myPOS_enable']) && $setting['myPOS_enable']=="1") : ?>
                                    <option value="myPOS"><?php esc_html_e('myPOS', 'fat-services-booking'); ?></option>
                                <?php endif; ?>
                                <?php if (!isset($setting['price_package_enable']) || $setting['price_package_enable'] == "1") : ?>
                                    <option value="price-package"><?php esc_html_e('Price Package', 'fat-services-booking'); ?></option>
                                <?php endif; ?>
                                <?php if (!isset($setting['przelewy24_enable']) || $setting['przelewy24_enable'] == "1") : ?>
                                    <option value="przelewy24"><?php esc_html_e('Przelewy24', 'fat-services-booking'); ?></option>
                                <?php endif; ?>
                            </select>
                        </li>
                        <?php if (isset($setting['stripe_enable']) && $setting['stripe_enable']) : ?>
                            <li class="fat-sb-order-stripe">
                                <form method="post" id="stripe-payment-form"
                                      data-pk="<?php echo(isset($setting['stripe_publish_key']) ? $setting['stripe_publish_key'] : 'pk_test_9q3BpuszZDNlnc8uppYQYQH7'); ?>">
                                    <div class="form-row">
                                        <div id="card-element" class="card-element">
                                            <!-- A Stripe Element will be inserted here. -->
                                        </div>
                                        <!-- Used to display form errors. -->
                                        <div id="card-errors" role="alert"></div>
                                    </div>
                                    <button></button>
                                </form>
                            </li>
                        <?php endif; ?>

                        <?php if ($is_exist_coupon): ?>
                            <li class="fat-sb-order-discount">
                                <span class="fat-item-label"><?php esc_html_e('Discount:', 'fat-services-booking'); ?></span>
                                <span class="fat-item-value" data-value="0">0$</span>
                            </li>
                        <?php endif; ?>

                        <li class="fat-sb-order-total">
                            <span class="fat-item-label"><?php echo esc_html($total_cost_label); ?></span>
                            <span class="fat-item-value" data-value="0"></span>
                        </li>

                        <?php
                        if (isset($current_user->ID) && $current_user->ID && (!isset($setting['price_package_enable']) || $setting['price_package_enable'] == "1")):?>
                            <li class="fat-sb-price-amount-remain">
                                <span class="fat-item-label"><?php echo esc_html__('Price package remain:', 'fat-services-booking'); ?></span>
                                <span class="fat-item-value" data-value="0">
                                    <?php
                                    $user_price_amount = FAT_DB_Price_Package::get_price_amount_by_user($current_user->user_email);
                                    $remain = $user_price_amount['buy_amount'] -  $user_price_amount['has_payment'];
                                    $remain = $remain > 0 ? $remain : 0;
                                    if($setting_currency['symbol_position']=='before'){
                                        echo $setting_currency['symbol'].number_format($remain);
                                    }else{
                                        echo number_format($remain).$setting_currency['symbol'];
                                    }
                                    ?>

                                </span>
                            </li>
                        <?php endif;?>
                    </ul>
                </div>

                <div class="fat-sb-button-group">
                    <div class="fat-sb-error-message fat-sb-hidden"></div>
                    <button class="ui primary button fat-next-step fat-bt-payment fat-bt"
                            data-onClick="FatSbBookingServices_FE.submitBooking"
                            data-next-step="completed">
                        <?php esc_html_e('Next', 'fat-services-booking'); ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="fat-sb-tab-content completed fat-sb-hidden" data-tab="completed">
            <h3><?php esc_html_e('Appointment booked', 'fat-services-booking'); ?></h3>
            <div>
                <?php echo esc_html($booked_message);?>
            </div>
            <div class="fat-mg-top-15">
                <button class="ui primary button fat-bt-add-google-calendar fat-bt"
                        data-onClick="FatSbBookingServices_FE.addToGoogleCalendar">
                    <?php esc_html_e('Add to Google calendar', 'fat-services-booking'); ?>
                </button>

                <button class="ui primary button fat-bt-add-icalendar fat-bt"
                        data-onClick="FatSbBookingServices_FE.addToICalendar">
                    <?php esc_html_e('Add to iCalendar', 'fat-services-booking'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/html" id="tmpl-fat-sb-service-item-template">
    <# _.each(data, function(item){ #>
    <div class="fat-sb-service-item">
        <div class="fat-sb-service-item-inner" data-id="{{item.s_id}}" data-duration="{{item.s_duration}}"
             data-onClick="FatSbBookingServices_FE.serviceItemOnClick">
            <div class="fat-sb-item-avatar">
                <# if (item.s_image_url!=''){ #>
                <img class="fat-border-round fat-box-shadow fat-img-80" src="{{item.s_image_url}}"
                     data-image-id="{{item.s_image_id}}">
                <# }else{ #>
                <span class="fat-no-thumb fat-img-80"></span>
                <# } #>
            </div>
            <div class="fat-sb-item-content">
                <div class="service-title">{{item.s_name}}</div>
                <div class="meta">
                    <div class="duration">
                        <?php echo esc_html__('Durations:', 'fat-services-booking'); ?>
                        <span class="duration-label">
                            {{item.s_duration_label}}
                        </span>
                    </div>
                    <div class="total-employee">
                        <span class="total-label">
                        </span>
                        <?php echo esc_html__(' provider(s)', 'fat-services-booking'); ?>
                    </div>
                </div>
            </div>
            <span class="fat-check"></span>
        </div>

    </div>
    <# }) #>
</script>

<script type="text/html" id="tmpl-fat-sb-employee-item-template">
    <# _.each(data, function(item){ #>
    <div class="fat-sb-employee-item {{item.e_location_class}} {{item.e_service_class}}">
        <div class="fat-sb-employee-item-inner" data-id="{{item.e_id}}" data-loc-id="{{item.e_location_ids}}"
             data-onClick="FatSbBookingServices_FE.providerItemOnClick">
            <div class="fat-sb-item-avatar">
                <# if(item.e_avatar_url!=''){ #>
                <img class="fat-border-round fat-box-shadow fat-img-150" src="{{item.e_avatar_url}}">
                <# }else{ #>
                <span class="fat-no-thumb fat-img-150"></span>
                <# } #>
            </div>
            <div class="fat-sb-item-content">
                <div class="employee-title">{{item.e_first_name}} {{item.e_last_name}}</div>
                <div class="meta">
                    <div class="price">
                        <span class="title"><?php esc_attr_e('Price: ', 'fat-services-booking'); ?></span><span
                            class="value"></span>
                    </div>
                    <div class="capacity">
                        <span class="title"><?php esc_attr_e('Capacity: ', 'fat-services-booking'); ?></span><span
                            class="value"></span>
                    </div>
                    <div class="location" data-lab><span class="title">
                            <?php esc_attr_e('Location: ', 'fat-services-booking'); ?></span><span class="value">{{item.e_location}}</span>
                    </div>
                </div>
            </div>
            <span class="fat-check"></span>
        </div>
    </div>
    <# }) #>
</script>