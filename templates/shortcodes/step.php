<?php
/**
 * Created by PhpStorm.
 * User: RoninWP
 * Date: 6/8/2019
 * Time: 8:18 AM
 */

$setting_db = FAT_DB_Setting::instance();
$setting = $setting_db->get_setting();
$setting_currency = $setting_db->get_currency_setting();
$employee_label = isset($setting['employee_label']) && $setting['employee_label'] ? $setting['employee_label'] : esc_html__('Employee','fat-services-booking');
$service_label = isset($setting['service_label']) && $setting['service_label'] ? $setting['service_label'] : esc_html__('Service','fat-services-booking');
$extra_service_label = esc_html__('Extra ','fat-services-booking'). strtolower($service_label);
$extra_service_duration_label = esc_html__('Extra durations:','fat-services-booking');
$price_label = isset($setting['price_label']) && $setting['price_label'] ? $setting['price_label'] : esc_html__('Price:', 'fat-services-booking');
$number_of_person_label = isset($setting['number_of_person_label']) && $setting['number_of_person_label'] ? $setting['number_of_person_label'] : esc_html__('Number of persons', 'fat-services-booking');
$total_cost_label = isset($setting['total_cost_label']) && $setting['total_cost_label'] ? $setting['total_cost_label'] : esc_html__('Total cost:', 'fat-services-booking');
$payment_method_label = isset($setting['payment_method_label']) && $setting['payment_method_label'] ? $setting['payment_method_label'] : esc_html__('Payment method:', 'fat-services-booking');
$disable_customer_email =  isset($setting['disable_customer_email']) && $setting['disable_customer_email'] == '1' ? 1 : 0;

$booking_form = get_option('fat_sb_booking_form', '[]');
$booking_form = stripslashes($booking_form);

$location = isset($atts['location']) && $atts['location'] ? $atts['location'] : '';
$category = isset($atts['category']) && $atts['category'] ? $atts['category'] : '';

$container_class = 'fat-booking-container fat-sb-step-layout';
$container_class .= isset($atts['hide_number_of_person']) && $atts['hide_number_of_person']=='1' ? ' hide-number-person' : '';
$container_class .= isset($atts['hide_employee']) && $atts['hide_employee']=='1' ? ' hide-employee' : '';
$container_class .= $location ? ' has-location-default' : '';
$container_class .= $category ? ' has-category-default' : '';

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
<div class="fat-semantic-container">
    <div class="<?php echo esc_attr($container_class);?>" style="opacity: 0;"
         data-location="<?php echo esc_attr($location);?>" data-category="<?php echo esc_attr($category);?>">
        <div class="ui ordered steps">
            <div class="step active" data-step="services">
                <div class="content">
                    <div class="title"><?php echo ucfirst($service_label); ?></div>
                    <div class="description"><?php echo sprintf(esc_html__('Select %s', 'fat-services-booking'), strtolower($service_label)); ?></div>
                </div>
            </div>
            <div class="step disabled" data-step="customer">
                <div class="content">
                    <div class="title"><?php esc_html_e('Details', 'fat-services-booking'); ?></div>
                    <div class="description"><?php esc_html_e('Enter Information', 'fat-services-booking'); ?></div>
                </div>
            </div>
            <div class="step disabled" data-step="completed">
                <div class="content">
                    <div class="title"><?php esc_html_e('Submit', 'fat-services-booking'); ?></div>
                    <div class="description"><?php esc_html_e('Review and Submit Request', 'fat-services-booking'); ?></div>
                </div>
            </div>
        </div>
        <div class="ui step-tab-content">
            <div class="ui step-tab active" data-step="services">
                <div class="ui form">
                    <div class="one fields location-dic">
                        <div class="field">
                            <label for="location"><?php echo esc_html__('Location', 'fat-services-booking'); ?> <span
                                        class="required"> *</span></label>
                            <div class="ui selection dropdown fat-sb-location-dic"
                                 data-onChange="FatSbBooking_FE.locationOnChange">
                                <input type="hidden" name="location" id="location" required>
                                <i class="dropdown icon"></i>
                                <div class="default text"><?php echo esc_html__('Select location', 'fat-services-booking'); ?></div>
                                <div class="menu">
                                </div>
                            </div>
                            <div class="field-error-message">
                                <?php echo esc_html__('Please select location', 'fat-services-booking'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="one fields category-dic">
                        <div class="field">
                            <label for="services_cat"><?php echo esc_html__('Service category', 'fat-services-booking'); ?>
                                <span
                                        class="required"> *</span></label>
                            <div class="ui selection dropdown fat-sb-services-cat-dic"
                                 data-onChange="FatSbBooking_FE.serviceCatOnChange">
                                <input type="hidden" name="services_cat" id="services_cat" required>
                                <i class="dropdown icon"></i>
                                <div class="default text"><?php echo esc_html__('Select category', 'fat-services-booking'); ?></div>
                                <div class="menu">
                                </div>
                            </div>
                            <div class="field-error-message">
                                <?php echo esc_html__('Please select category', 'fat-services-booking'); ?>
                            </div>

                        </div>

                    </div>

                    <div class="one fields">
                        <div class="field">
                            <label for="service"><?php echo ucfirst($service_label); ?> <span
                                        class="required"> *</span></label>
                            <div class="ui selection dropdown fat-sb-services-dic"
                                 data-onChange="FatSbBooking_FE.serviceOnChange"
                                 data-empty="<?php echo sprintf(esc_attr__('Don\'t have %s for this category', 'fat-services-booking'),$service_label); ?>"
                                 data-default-text="<?php echo sprintf(esc_attr__('Select %s', 'fat-services-booking'), $service_label); ?>">
                                <input type="hidden" name="service" id="service" required>
                                <i class="dropdown icon"></i>
                                <div class="default text"><?php echo sprintf(esc_html__('Select %s', 'fat-services-booking'), $service_label); ?></div>
                                <div class="menu">
                                </div>
                            </div>
                            <div class="field-error-message">
                                <?php echo sprintf(esc_html__('Please select %s', 'fat-services-booking'), $service_label); ?>
                            </div>
                        </div>
                    </div>

                    <div class="one fields fat-sb-hidden">
                        <div class="field">
                            <label for="service"><?php echo sprintf(esc_html__('%s extra','fat-services-booking'), $service_label); ?></label>
                            <div class="ui selection multiple dropdown fat-sb-services-extra-dic clearable" data-onChange="FatSbBooking_FE.serviceExtraOnChange">
                                <input type="hidden" name="service_extra" id="service_extra" >
                                <i class="dropdown icon"></i>
                                <div class="default text"><?php echo sprintf(esc_html__('Select %s extra', 'fat-services-booking'), $service_label); ?></div>
                                <div class="menu">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="one fields employee-field">
                        <div class="field">
                            <label for="employee"><?php echo esc_html($employee_label); ?> <span
                                        class="required"> *</span> </label>
                            <div class="ui selection dropdown fat-sb-employee-dic"
                                 data-onChange="FatSbBooking_FE.employeeOnChange"
                                 data-empty="<?php echo sprintf(esc_attr__('Don\'t have %s for this %s', 'fat-services-booking'),$employee_label, $service_label); ?>"
                                 data-default-text="<?php echo sprintf(esc_attr__('Select %s', 'fat-services-booking'), $employee_label); ?>">
                                <input type="hidden" name="employee" id="employee" required>
                                <i class="dropdown icon"></i>
                                <div class="default text"><?php echo sprintf(esc_html__('Select %s', 'fat-services-booking'),$employee_label); ?></div>
                                <div class="menu">

                                </div>
                            </div>
                            <div class="field-error-message">
                                <?php echo sprintf(esc_html__('Please select %s', 'fat-services-booking'), $employee_label); ?>
                            </div>
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
                        $locale='en';
                    }
                    ?>

                    <div class="one fields">
                        <div class="field">
                            <label for="b_date"><?php echo esc_html__('Date', 'fat-services-booking'); ?> <span
                                        class="required"> *</span></label>
                            <div class="fat-sb-booking-date-wrap">
                                <input type="text" class="air-date-picker datepicker-here"
                                       data-locale="<?php echo esc_attr($locale); ?>" required autocomplete="off"
                                       name="b_date" id="b_date">
                            </div>
                            <div class="field-error-message">
                                <?php echo esc_html__('Please select date', 'fat-services-booking'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="one fields">
                        <div class="field">
                            <label><?php echo esc_html__('Time', 'fat-services-booking'); ?> <span
                                        class="required"> *</span></label>
                            <div class="ui pointing selection dropdown has-icon fat-sb-booking-time-wrap" data-onChange="FatSbBooking_FE.timeOnChange">
                                <input type="hidden" name="b_time" id="b_time" required>
                                <i class="dropdown icon"></i>
                                <div class="text"
                                     data-text="<?php echo esc_attr__('Select time', 'fat-services-booking'); ?>">
                                    <?php echo esc_html__('Select time', 'fat-services-booking'); ?>
                                </div>
                                <div class="menu">
                                    <?php
                                    $db_setting = FAT_DB_Setting::instance();
                                    $setting = $db_setting->get_setting();
                                    $time_step = isset($setting['time_step']) && $setting['time_step'] ? $setting['time_step'] : 15;
                                    $work_hours = FAT_SB_Utils::getWorkHours($time_step); ?>
                                    <?php foreach ($work_hours as $key => $value) { ?>
                                        <div class="item disabled"
                                             data-value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="field-error-message">
                                <?php echo esc_html__('Please select time', 'fat-services-booking'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="one fields">
                        <div class="field fat-sb-multiple-days">
                            <label><?php echo esc_html__('Selected dates', 'fat-services-booking'); ?></label>
                            <ul class="list-multiple-days">

                            </ul>
                        </div>
                    </div>

                    <div class="fat-sb-button-group">
                        <button class="ui primary button fat-next-step" data-onClick="FatSbBooking_FE.nextOnClick"
                                data-next-step="customer">
                            <?php esc_html_e('Next', 'fat-services-booking'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <div class="ui step-tab fat-sb-hidden" data-step="customer">
                <div class="fat-sb-col-left">
                    <div class="ui form">
                        <div class="one fields">
                            <div class="field ">
                                <label for="c_first_name"><?php echo esc_html__('First name', 'fat-services-booking'); ?><span
                                            class="required"> *</span></label>
                                <div class="ui left input ">
                                    <input type="text" name="c_first_name" id="c_first_name" data-onChange="FatSbBooking_FE.resetValidateField"
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
                                    <input type="text" name="c_last_name" id="c_last_name" data-onChange="FatSbBooking_FE.resetValidateField"
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
                                    <input type="email" name="c_email" id="c_email" data-onChange="FatSbBooking_FE.resetValidateField"
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
                                <label for="phone"><?php echo esc_html__('Phone', 'fat-services-booking'); ?>
                                    <span
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
                                    <input type="text" name="c_phone" id="c_phone" data-onChange="FatSbBooking_FE.resetValidateField"
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
                                <div class="ui pointing selection dropdown has-icon fat-sb-number-of-person-wrap"
                                     data-onChange="FatSbBooking_FE.numberOnChange">
                                    <input type="hidden" name="number_of_person" id="number_of_person" required>
                                    <i class="dropdown icon"></i>
                                    <div class="default text"><?php echo esc_html__('Select number of persons', 'fat-services-booking'); ?></div>
                                    <div class="menu">
                                    </div>
                                </div>
                                <div class="field-error-message">
                                    <?php echo esc_html__('Please select number of person', 'fat-services-booking'); ?>
                                </div>
                            </div>
                        </div>

                        <?php
                        if ($booking_form !== '' && $booking_form !== '[]'):
                            $booking_form = json_decode($booking_form);
                            $onChange = 'FatSbBooking_FE.resetValidateField';
                            foreach ($booking_form as $field) {
                                include FAT_SERVICES_DIR_PATH . '/templates/form-builder/fields/' . $field->type . '.php';
                            }
                        endif; ?>

                        <div class="one fields">
                            <div class="field ">
                                <label for="note"><?php echo esc_html__('Note', 'fat-services-booking'); ?></label>
                                <div class="ui left input">
                                    <textarea rows="2" name="note" id="note"></textarea>
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
                            <span class="fat-item-value"></span>
                        </li>
                        <li class="fat-sb-order-extra-service-duration">
                            <span class="fat-item-label"><?php echo esc_html($extra_service_duration_label); ?></span>
                            <span class="fat-item-value"></span>
                        </li>
                        <li class="fat-sb-order-tax">
                            <span class="fat-item-label"><?php esc_html_e('Tax:', 'fat-services-booking'); ?></span>
                            <span class="fat-item-value"></span>
                        </li>

                        <?php
                        $coupon_db = FAT_DB_Coupons::instance();
                        $is_exist_coupon = $coupon_db->exists_coupon();
                        if($is_exist_coupon): ?>
                            <li class="fat-sb-order-coupon">
                                <span class="fat-item-label"><?php esc_html_e('Coupon:', 'fat-services-booking'); ?></span>
                                <span class="fat-item-value">
                                    <div class="ui left input">
                                        <input type="text" name="coupon" id="coupon" data-onChange="FatSbBooking_FE.couponOnChange"
                                               placeholder="<?php esc_attr_e('Coupon code', 'fat-services-booking'); ?>">
                                    </div>
                                    <button class="ui icon button" data-onClick="FatSbBooking_FE.initCoupon"
                                            data-content="<?php esc_attr_e('Click here to apply coupon code', 'fat-services-booking'); ?>">
                                        <i class="sync icon"></i>
                                    </button>
                                </span>
                                <div class="fat-coupon-error"></div>
                            </li>
                        <?php endif; ?>

                        <li class="fat-sb-order-payment-method">
                            <span class="fat-item-label"><?php echo esc_html($payment_method_label); ?></span>
                            <span class="fat-item-value">
                                <div class="ui pointing selection dropdown  fat-sb-payment-method-wrap">
                                    <input type="hidden" name="payment_method" id="payment_method" value="onsite" required>
                                    <i class="dropdown icon"></i>
                                    <div class="default text"><?php echo esc_html__('Select payment method', 'fat-services-booking'); ?></div>
                                    <div class="menu">
                                        <?php if (!isset($setting['onsite_enable']) || $setting['onsite_enable'] == "1") : ?>
                                            <div class="item"
                                                 data-value="onsite"><?php esc_html_e('Onsite payment', 'fat-services-booking'); ?></div>
                                        <?php endif; ?>

                                        <?php if (isset($setting['paypal_enable']) && $setting['paypal_enable']=="1") : ?>
                                            <div class="item"
                                                 data-value="paypal"><?php esc_html_e('Paypal', 'fat-services-booking'); ?></div>
                                        <?php endif; ?>

                                        <?php if (isset($setting['stripe_enable']) && $setting['stripe_enable']=="1") : ?>
                                            <div class="item"
                                                 data-value="stripe"><?php esc_html_e('Stripe', 'fat-services-booking'); ?></div>
                                        <?php endif; ?>

                                        <?php if (isset($setting['myPOS_enable']) && $setting['myPOS_enable']=="1") : ?>
                                            <div class="item"
                                                 data-value="myPOS"><?php esc_html_e('myPOS', 'fat-services-booking'); ?></div>
                                        <?php endif; ?>

                                        <?php if (!isset($setting['price_package_enable']) || $setting['price_package_enable'] == "1") : ?>
                                            <div class="item"
                                                 data-value="price-package"><?php esc_html_e('Price Package', 'fat-services-booking'); ?></div>
                                        <?php endif; ?>

                                        <?php if (!isset($setting['przelewy24_enable']) || $setting['przelewy24_enable'] == "1") : ?>
                                            <div class="item"
                                                 data-value="przelewy24"><?php esc_html_e('Przelewy24', 'fat-services-booking'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </span>
                        </li>
                        <?php if(isset($setting['stripe_enable'])  &&  $setting['stripe_enable']) : ?>
                        <li class="fat-sb-order-stripe">
                            <form method="post" id="stripe-payment-form"
                                  data-pk="<?php echo(isset($setting['stripe_publish_key']) ? $setting['stripe_publish_key'] : 'pk_test_9q3BpuszZDNlnc8uppYQYQH7'); ?>">
                                <div class="form-row">
                                    <div id="card-element">
                                        <!-- A Stripe Element will be inserted here. -->
                                    </div>
                                    <!-- Used to display form errors. -->
                                    <div id="card-errors" role="alert"></div>
                                </div>
                                <button></button>
                            </form>
                        </li>
                        <?php endif; ?>

                        <?php if($is_exist_coupon): ?>
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
                    <button class="ui primary button fat-next-step fat-bt-payment fat-bt" data-onClick="FatSbBooking_FE.submitBooking"
                            data-next-step="completed">
                        <?php esc_html_e('Next', 'fat-services-booking'); ?>
                    </button>
                </div>
            </div>

            <div class="ui step-tab fat-sb-hidden" data-step="completed">
                <h3><?php esc_html_e('Appointment booked', 'fat-services-booking');?></h3>
                <div>
                    <?php echo esc_html($booked_message);?>
                </div>
                <div class="fat-mg-top-15">
                    <button class="ui primary button fat-bt-add-google-calendar" data-onClick="FatSbBooking_FE.addToGoogleCalendar">
                        <?php esc_html_e('Add to Google calendar', 'fat-services-booking'); ?>
                    </button>
                    <button class="ui primary button fat-bt-add-icalendar" data-onClick="FatSbBooking_FE.addToICalendar">
                        <?php esc_html_e('Add to iCalendar', 'fat-services-booking'); ?>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
