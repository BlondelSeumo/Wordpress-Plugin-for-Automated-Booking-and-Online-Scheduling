<?php
/**
 * Created by PhpStorm.
 * User: RoninWP
 * Date: 10/22/2019
 * Time: 3:07 PM
 */

$setting_db = FAT_DB_Setting::instance();
$setting = $setting_db->get_setting();
$employee_label = isset($setting['employee_label']) && $setting['employee_label'] ? $setting['employee_label'] : esc_html__('Employee', 'fat-services-booking');
$service_label = isset($setting['service_label']) && $setting['service_label'] ? $setting['service_label'] : esc_html__('Service', 'fat-services-booking');
$extra_service_label = esc_html__('Extra ', 'fat-services-booking') . strtolower($service_label);

$price_label = isset($setting['price_label']) && $setting['price_label'] ? $setting['price_label'] : esc_html__('Price:', 'fat-services-booking');
$number_of_person_label = isset($setting['number_of_person_label']) && $setting['number_of_person_label'] ? $setting['number_of_person_label'] : esc_html__('Number of persons', 'fat-services-booking');
$total_cost_label = isset($setting['total_cost_label']) && $setting['total_cost_label'] ? $setting['total_cost_label'] : esc_html__('Total cost:', 'fat-services-booking');
$payment_method_label = isset($setting['payment_method_label']) && $setting['payment_method_label'] ? $setting['payment_method_label'] : esc_html__('Payment method:', 'fat-services-booking');
$disable_customer_email =  isset($setting['disable_customer_email']) && $setting['disable_customer_email'] == '1' ? 1 : 0;

$booking_form = get_option('fat_sb_booking_form', '[]');
$booking_form = stripslashes($booking_form);
$container_class = 'fat-semantic-container fat-booking-container fat-sb-services-layout services-date-first fat-sb-layout-' . $column . '-column';
$container_class .= isset($atts['hide_number_of_person']) && $atts['hide_number_of_person'] == '1' ? ' hide-number-person' : '';
$container_class .= isset($atts['hide_payment_info']) && $atts['hide_payment_info'] == '1' ? ' hide-payment-info' : '';

$current_user = wp_get_current_user();
$first_name = $last_name = $email = $phone = '';
$disable_field = '';
$email = $disable_customer_email ? uniqid().'@no_email.com' : '';
if ($current_user->exists()) {
    $first_name = $current_user->first_name ? $current_user->first_name : $current_user->user_login;
    $last_name = $current_user->last_name ? $current_user->last_name : $current_user->user_login;
    $email = $current_user->user_email;
    $phone = get_user_meta($current_user->ID, 'user_registration_billing_phone',true);
    $phone = $phone ? $phone : get_user_meta($current_user->ID, 'phone_number', true);
    $disable_field = 'disabled';
}
$locale = get_locale();
$locale = explode('_', $locale)[0];
$locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.' . $locale . '.js';
if($locale=='pl'){
    $locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.pl-PL.js';
}
if(!file_exists($locale_path)){
    $locale = 'en';
}

$db_setting = FAT_DB_Setting::instance();
$working_hour = $db_setting->get_working_hour_setting();

$day_off = isset($working_hour['day_off']) ? $working_hour['day_off'] : array();
$day_off = json_encode($day_off);

$working_hour = isset($working_hour['schedules']) ? $working_hour['schedules'] : array();
$working_hour = json_encode($working_hour);

$db_services = FAT_DB_Services::instance();
$services = $db_services->get_services();

$db_location = FAT_DB_Locations::instance();
$locations = $db_location->get_locations();

$now = current_time('mysql',0);
$now = DateTime::createFromFormat('Y-m-d H:i:s',$now);
$date_format = get_option('date_format');
$now = $now->format($date_format);

$phone_code_default = isset($setting['default_phone_code']) && $setting['default_phone_code'] ? $setting['default_phone_code'] : '+44';

$booked_message = isset($setting['booked_message']) && $setting['booked_message'] ? $setting['booked_message'] : esc_html__('Thank you! Your booking is complete. An email with detail of your booking has been send to you.','fat-services-booking');
?>
<div class="<?php echo esc_attr($container_class); ?>" style="opacity: 0;" data-column="<?php echo esc_attr($column);?>">
    <div class="ui step-tab-content fat-sb-tab-content-wrap">
        <div class="ui step-tab date-time active" data-step="date-time">
            <div class="ui form">
                <div class="one fields">
                    <div class="field">
                        <label for="b_date"><?php echo esc_html__('Date', 'fat-services-booking'); ?></label>
                        <div class="fat-sb-booking-date-wrap" data-working-hour="<?php echo esc_attr($working_hour) ;?>" data-day-off="<?php echo esc_attr($day_off);?>">
                            <input type="text" class="air-date-picker datepicker-here"
                                   data-locale="<?php echo esc_attr($locale); ?>" required autocomplete="off"
                                   name="b_date" id="b_date" data-date-label="<?php echo esc_attr($now); ?>">
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please select date', 'fat-services-booking'); ?>
                        </div>
                    </div>
                </div>

                <div class="two fields">
                    <div class="field">
                        <label><?php echo esc_html__('Time ranger', 'fat-services-booking'); ?></label>
                        <div class="ui pointing selection dropdown fat-sb-booking-time-wrap fat-sb-booking-time-start clearable">
                            <input type="hidden" name="start_time" id="start_time">
                            <i class="dropdown icon"></i>
                            <div class="text">
                                <?php echo esc_html__('From', 'fat-services-booking'); ?>
                            </div>
                            <div class="menu">
                                <?php
                                $db_setting = FAT_DB_Setting::instance();
                                $setting = $db_setting->get_setting();
                                $time_step = isset($setting['time_step']) && $setting['time_step'] ? $setting['time_step'] : 15;
                                $work_hours = FAT_SB_Utils::getWorkHours($time_step); ?>
                                <?php foreach ($work_hours as $key => $value) { ?>
                                    <div class="item "
                                         data-value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please select time', 'fat-services-booking'); ?>
                        </div>
                    </div>

                    <div class="field">
                        <label class="fat-sb-hidden-mobile">&nbsp;</label>
                        <div class="ui pointing selection dropdown fat-sb-booking-time-wrap fat-sb-booking-time-end clearable">
                            <input type="hidden" name="end_time" id="end_time" >
                            <i class="dropdown icon"></i>
                            <div class="text">
                                <?php echo esc_html__('To', 'fat-services-booking'); ?>
                            </div>
                            <div class="menu">
                                <?php
                                $db_setting = FAT_DB_Setting::instance();
                                $setting = $db_setting->get_setting();
                                $time_step = isset($setting['time_step']) && $setting['time_step'] ? $setting['time_step'] : 15;
                                $work_hours = FAT_SB_Utils::getWorkHours($time_step); ?>
                                <?php foreach ($work_hours as $key => $value) { ?>
                                    <div class="item "
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
                    <div class="field">
                        <label for="s_id"><?php echo esc_html__('Location','fat-services-booking'); ?></label>
                        <div class="ui search selection dropdown fat-sb-location-dic">
                            <input type="hidden" name="loc_id" id="loc_id" value="<?php echo esc_attr($locations[0]->loc_id);?>">
                            <i class="dropdown icon"></i>
                            <div class="default text"><?php echo esc_html__('Select location', 'fat-services-booking'); ?></div>
                            <div class="menu">
                                <?php foreach ($locations as $loc){ ?>
                                    <div class="item" data-value="<?php echo esc_attr($loc->loc_id); ?>">
                                        <i class="fa fa-map-marker"></i>
                                        <?php echo esc_html($loc->loc_name); ?></div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please select location', 'fat-services-booking'); ?>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <label for="s_id"><?php echo ucfirst($service_label); ?></label>
                        <div class="ui search selection dropdown fat-sb-services-dic clearable">
                            <input type="hidden" name="s_id" id="s_id">
                            <i class="dropdown icon"></i>
                            <div class="default text"><?php echo sprintf(esc_html__('Select %s', 'fat-services-booking'), $service_label); ?></div>
                            <div class="menu">
                                <?php foreach ($services as $service){ ?>
                                    <div class="item"
                                         data-value="<?php echo esc_attr($service->s_id); ?>">
                                        <img src="<?php echo esc_url($service->s_image_url);?>">
                                        <?php echo esc_html($service->s_name); ?></div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="field-error-message">
                            <?php echo sprintf(esc_html__('Please select %s', 'fat-services-booking'), $service_label); ?>
                        </div>
                    </div>
                </div>

                <div class="fat-sb-button-group">
                    <div class="fat-sb-not-found-message fat-sb-hidden"><?php echo esc_html__('No services found','fat-services-booking');?></div>
                    <button class="ui button fat-next-step" data-onClick="FatSbBookingDateFirst_FE.nextServiceProvideOnClick"
                            data-next-step="customer">
                        <?php esc_html_e('Next', 'fat-services-booking'); ?>
                        <i class="arrow right icon"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="ui step-tab fat-sb-tab-content has-box-shadow service-provider fat-sb-hidden" data-tab="services-provider">
            <h3><?php echo esc_html__('Select service and employee','fat-services-booking');?></h3>
            <div class="fat-sb-list-provider-wrap">
                <div class="fat-sb-list-provider">

                </div>
            </div>

            <div class="fat-sb-button-group">
                <button class="ui button fat-next-step" data-onClick="FatSbBookingDateFirst_FE.nextCustomerOnClick"
                        data-next-step="customer">
                    <?php esc_html_e('Next', 'fat-services-booking'); ?>
                    <i class="arrow right icon"></i>
                </button>
            </div>
        </div>

        <div class="ui step-tab fat-sb-tab-content customer fat-sb-hidden" data-tab="customer" data-next-tab="completed">
            <div class="fat-sb-customer-wrap">
                <div class="fat-sb-col-left">
                    <h3><?php esc_html_e('Your information', 'fat-services-booking'); ?></h3>
                    <div class="ui form">
                        <div class="one fields">
                            <div class="field ">
                                <label for="c_first_name"><?php echo esc_html__('First name', 'fat-services-booking'); ?><span
                                            class="required"> *</span></label>
                                <div class="ui left input ">
                                    <input type="text" name="c_first_name" id="c_first_name" data-onChange="FatSbBookingServices_FE.resetValidateField"
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
                                    <input type="text" name="c_last_name" id="c_last_name" data-onChange="FatSbBookingServices_FE.resetValidateField"
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
                                    <input type="email" name="c_email" id="c_email" data-onChange="FatSbBookingServices_FE.resetValidateField"
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
                                    <input type="text" name="c_phone" id="c_phone"  data-onChange="FatSbBookingServices_FE.resetValidateField"
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
                                <select class="fat-sb-number-of-person-wrap" name="number_of_person" id="number_of_person" data-onChange="FatSbBookingDateFirst_FE.numberPersonOnChange">
                                    <option value="1">1</option>
                                </select>
                            </div>
                        </div>

                        <?php
                        if ($booking_form !== '' && $booking_form !== '[]'):
                            $booking_form = json_decode($booking_form);
                            $onChange = 'FatSbBookingDateFirst_FE.resetValidateField';
                            foreach ($booking_form as $field) {
                                include FAT_SERVICES_DIR_PATH . '/templates/form-builder/fields/' . $field->type . '.php';
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
                            <span class="fat-item-label"><?php echo ucfirst($service_label); ?>:</span>
                            <span class="fat-item-value"></span>
                        </li>
                        <li class="fat-sb-order-employee">
                            <span class="fat-item-label"><?php echo ucfirst($employee_label); ?>:</span>
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
                        <li class="fat-sb-location">
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
                                    <input type="text" name="coupon" id="coupon" data-onChange="FatSbBookingDateFirst_FE.couponOnChange"
                                           placeholder="<?php esc_attr_e('Coupon code', 'fat-services-booking'); ?>">
                                </div>
                                <button class="ui icon button" data-onClick="FatSbBookingDateFirst_FE.initCoupon"
                                        data-content="<?php esc_attr_e('Click here to apply coupon code', 'fat-services-booking'); ?>">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </span>
                                <div class="fat-coupon-error"></div>
                            </li>
                        <?php endif; ?>

                        <li class="fat-sb-order-payment-method">
                            <span class="fat-item-label"><?php echo esc_html($payment_method_label) ?></span>
                            <select class="fat-sb-payment-method-wrap" name="payment_method" id="payment_method" data-onChange="FatSbBookingDateFirst_FE.paymentOnChange">
                                <?php if (!isset($setting['onsite_enable']) || $setting['onsite_enable'] == "1") : ?>
                                    <option value="onsite"><?php esc_html_e('Onsite payment', 'fat-services-booking'); ?></option>
                                <?php endif; ?>
                                <?php if (isset($setting['paypal_enable']) && $setting['paypal_enable']=="1") : ?>
                                    <option value="paypal"><?php esc_html_e('Paypal', 'fat-services-booking'); ?></option>
                                <?php endif; ?>
                                <?php if (isset($setting['stripe_enable']) && $setting['stripe_enable']=="1") : ?>
                                    <option value="stripe"><?php esc_html_e('Stripe', 'fat-services-booking'); ?></option>
                                <?php endif; ?>
                                <?php if (isset($setting['myPOS_enable']) && $setting['myPOS_enable']=="1") : ?>
                                    <option value="myPOS"><?php esc_html_e('myPOS', 'fat-services-booking'); ?></option>
                                <?php endif; ?>
                                <?php if (!isset($setting['przelewy24_enable']) || $setting['przelewy24_enable'] == "1") : ?>
                                    <option value="przelewy24"><?php esc_html_e('Przelewy24', 'fat-services-booking'); ?></option>
                                <?php endif; ?>
                            </select>
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

                        <?php  if($is_exist_coupon): ?>
                            <li class="fat-sb-order-discount">
                                <span class="fat-item-label"><?php esc_html_e('Discount:', 'fat-services-booking'); ?></span>
                                <span class="fat-item-value" data-value="0">0$</span>
                            </li>
                        <?php endif; ?>
                        <li class="fat-sb-order-total">
                            <span class="fat-item-label"><?php echo esc_html($total_cost_label); ?></span>
                            <span class="fat-item-value" data-value="0"></span>
                        </li>
                    </ul>
                </div>

                <div class="fat-sb-button-group">
                    <div class="fat-sb-error-message fat-sb-hidden"></div>
                    <button class="ui button fat-next-step fat-bt-payment" data-onClick="FatSbBookingDateFirst_FE.submitBooking"
                            data-next-step="completed">
                        <?php esc_html_e('Next', 'fat-services-booking'); ?>
                        <i class="arrow right icon"></i>
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
                        data-onClick="FatSbBookingDateFirst_FE.addToGoogleCalendar">
                    <?php esc_html_e('Add to Google calendar', 'fat-services-booking'); ?>
                </button>

                <button class="ui primary button fat-bt-add-icalendar fat-bt"
                        data-onClick="FatSbBookingDateFirst_FE.addToICalendar">
                    <?php esc_html_e('Add to iCalendar', 'fat-services-booking'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/html" id="tmpl-fat-sb-service-item-template">
    <# _.each(data, function(item){ #>
    <div class="fat-sb-service-item">
        <div class="ui items ">
            <div class="item fat-pd-10 fat-border-spin fat-hover fat-hover-link" data-index="{{item.index}}"
                 data-eid="{{item.e_id}}"
                 data-sid="{{item.s_id}}" data-e-img="{{item.e_image_url}}">
                <div class="ui tiny image">
                    <img class="fat-border-round fat-box-shadow fat-img-80" src="{{item.s_image_url}}">
                </div>
                <div class="content">
                    <div class="header thin services-name">{{item.s_name}}</div>
                    <div class="meta">
                        <div>
                            <strong> <i class="user circle outline icon"></i></strong>
                            <span class="employee-name"> {{item.e_first_name}} {{item.e_last_name}} </span>
                        </div>
                        <div>
                            <strong> <i class="clock outline icon"></i></strong>
                            <span class="duration-label"> {{item.s_duration_label}} </span>
                            |
                            <strong> <i class="user outline icon"></i></strong>
                            <span class="duration-label"> {{item.available}} <?php esc_html_e(' seat(s)','fat-services-booking');?></span>
                        </div>

                        <div data-pice="{{item.s_price}}"><strong><i class="dollar sign icon"></i></strong>
                            <span class="price">{{item.s_price}}</span>
                        </div>
                    </div>
                </div>

                <button class=" ui icon button fat-item-bt-inline fat-sb-book-now ui-tooltip"
                        data-onclick="FatSbBookingDateFirst_FE.processGetTimeSlot" data-id="{{item.s_id}}_{{item.e_id}}" data-position="top right"
                        data-title="<?php esc_html_e('Select time slot','fat-services-booking');?>">
                    <i class="clock outline icon"></i>
                </button>
            </div>
        </div>

    </div>
    <# }) #>
</script>

<script type="text/html" id="tmpl-fat-sb-time-slot-template">
<div class="fat-sb-time-slot-container">
    <div class="fat-sb-time-slot-inner">
        <# _.each(data, function(item, slot){ #>
        <div class="time-slot-item" data-value="{{slot}}" data-seat="{{item.seat}}" data-onClick="FatSbBookingDateFirst_FE.timeSlotSelected">
            <div class="item-inner">
                {{item.title}}
            </div>
        </div>
        <# }) #>
    </div>
</div>
</script>

