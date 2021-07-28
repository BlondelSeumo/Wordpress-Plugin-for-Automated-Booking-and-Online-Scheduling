<?php
/**
 * Created by PhpStorm.
 * User: PhuongTH
 * Date: 8/3/2020
 * Time: 4:47 PM
 */

$setting_db = FAT_DB_Setting::instance();
$setting = $setting_db->get_setting();
$setting_currency = $setting_db->get_currency_setting();

$employee_label = isset($setting['employee_label']) && $setting['employee_label'] ? $setting['employee_label'] : esc_html__('Employee', 'fat-services-booking');
$service_label = isset($setting['service_label']) && $setting['service_label'] ? $setting['service_label'] : esc_html__('Service', 'fat-services-booking');
$extra_service_label = esc_html__('Extra ', 'fat-services-booking') . strtolower($service_label);
$price_label = isset($setting['price_label']) && $setting['price_label'] ? $setting['price_label'] : esc_html__('Price:', 'fat-services-booking');
$number_of_person_label = isset($setting['number_of_person_label']) && $setting['number_of_person_label'] ? $setting['number_of_person_label'] : esc_html__('Number of persons', 'fat-services-booking');
$total_cost_label = isset($setting['total_cost_label']) && $setting['total_cost_label'] ? $setting['total_cost_label'] : esc_html__('Total cost:', 'fat-services-booking');
$payment_method_label = isset($setting['payment_method_label']) && $setting['payment_method_label'] ? $setting['payment_method_label'] : esc_html__('Payment method:', 'fat-services-booking');
$disable_customer_email = isset($setting['disable_customer_email']) && $setting['disable_customer_email'] == '1' ? 1 : 0;

$service_id = isset($atts['service_id']) && $atts['service_id'] ? $atts['service_id'] : '';

$booking_form = get_option('fat_sb_booking_form', '[]');
$booking_form = stripslashes($booking_form);

$current_user = wp_get_current_user();
$first_name = $last_name = $email = $phone = '';
$disable_field = '';
$email = $disable_customer_email ? uniqid() . '@no_email.com' : '';
if ($current_user->exists()) {
    $first_name = $current_user->first_name ? $current_user->first_name : $current_user->user_login;
    $last_name = $current_user->last_name ? $current_user->last_name : $current_user->user_login;
    $email = $current_user->user_email;
    $phone = get_user_meta($current_user->ID, 'user_registration_billing_phone', true);
    $phone = $phone ? $phone : get_user_meta($current_user->ID, 'phone_number', true);
    $disable_field = 'disabled';
}

$phoneCode = FAT_SB_Utils::getPhoneCountry();
$phone_code_default = isset($setting['default_phone_code']) && $setting['default_phone_code'] ? $setting['default_phone_code'] : '+44';


$booked_message = isset($setting['booked_message']) && $setting['booked_message'] ? $setting['booked_message'] : esc_html__('Thank you! Your booking is complete. An email with detail of your booking has been send to you.','fat-services-booking');
?>
<div class="fat-semantic-container fat-booking-container fat-sb-calendar-layout" style="opacity: 0" data-service="<?php echo esc_attr($service_id);?>">
    <div class="fat-sb-date-time-wrap">
        <div class="fat-sb-calendar-wrap">
            <div class="calendar-filter" data-week="">
            <span class="prev-week">
                <i class="fa fa-angle-left"></i>
            </span>
                <input type="text" class="week-filter" disabled="disabled" value="Nov 18- Nov 24,2019">
                <span class="next-week">
                <i class="fa fa-angle-right"></i>
            </span>
            </div>
            <div class="week-detail">
                <div class="week-header">
                    <div class="week-day-header mon"><?php echo esc_html__('Mon', 'fat-services-booking'); ?><span
                                class="week-date">18</span></div>
                    <div class="week-day-header tue"><?php echo esc_html__('Tue', 'fat-services-booking'); ?> <span
                                class="week-date">19</span></div>
                    <div class="week-day-header wed"><?php echo esc_html__('Wed', 'fat-services-booking'); ?> <span
                                class="week-date">20</span></div>
                    <div class="week-day-header thu"><?php echo esc_html__('Thu', 'fat-services-booking'); ?> <span
                                class="week-date">21</span></div>
                    <div class="week-day-header fri"><?php echo esc_html__('Fri', 'fat-services-booking'); ?> <span
                                class="week-date">22</span></div>
                    <div class="week-day-header sat"><?php echo esc_html__('Sat', 'fat-services-booking'); ?> <span
                                class="week-date">23</span></div>
                    <div class="week-day-header sun"><?php echo esc_html__('Sun', 'fat-services-booking'); ?> <span
                                class="week-date">24</span></div>
                </div>
                <div class="week-content">
                    <div class="week-day-content mon day-2">
                        <div class="week-header-mobile"></div>
                        <ul class="list-services">
                        </ul>
                    </div>
                    <div class="week-day-content tue day-3">
                        <div class="week-header-mobile"></div>
                        <ul class="list-services">
                        </ul>
                    </div>
                    <div class="week-day-content wed day-4">
                        <div class="week-header-mobile"></div>
                        <ul class="list-services">
                        </ul>
                    </div>
                    <div class="week-day-content thu day-5">
                        <div class="week-header-mobile"></div>
                        <ul class="list-services">
                        </ul>
                    </div>
                    <div class="week-day-content fri day-6">
                        <div class="week-header-mobile"></div>
                        <ul class="list-services">
                        </ul>
                    </div>
                    <div class="week-day-content sat day-7">
                        <div class="week-header-mobile"></div>
                        <ul class="list-services">
                        </ul>
                    </div>
                    <div class="week-day-content sun day-8">
                        <div class="week-header-mobile"></div>
                        <ul class="list-services">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="fat-sb-service-info">
            <div class="fat-sb-service-info-inner">
                <div class="fat-sb-thumb">

                </div>
                <div class="fat-sb-service-meta">
                    <div class="fat-sb-service-name"></div>
                    <div class="fat-sb-service-desc"></div>
                </div>
            </div>
        </div>
        <div class="fat-sb-time-slot-wrap">
            <h4><?php echo esc_html__('Time slot', 'fat-services-booking'); ?></h4>
            <div class="fat-sb-time-list">
                <ul>
                </ul>
            </div>
        </div>
    </div>

    <div class="fat-sb-information">
        <h3><?php echo esc_html__('Your information', 'fat-services-booking'); ?></h3>
        <div class="ui form">
            <div class="two fields">
                <div class="field ">
                    <label for="c_first_name"><?php echo esc_html__('First name', 'fat-services-booking'); ?>
                        <span
                                class="required"> *</span></label>
                    <div class="ui left input ">
                        <input type="text" name="c_first_name" id="c_first_name"
                               data-onChange="FatSbBookingServices_FE.resetValidateField"
                               value="<?php esc_html_e($first_name); ?>"
                            <?php echo esc_attr($disable_field); ?>
                               placeholder="<?php echo esc_attr__('First name', 'fat-services-booking'); ?>"
                               required>
                    </div>
                    <div class="field-error-message">
                        <?php echo esc_html__('Please enter first name', 'fat-services-booking'); ?>
                    </div>
                </div>

                <div class="field ">
                    <label for="c_last_name"><?php echo esc_html__('Last name', 'fat-services-booking'); ?>
                        <span
                                class="required"> *</span></label>
                    <div class="ui left input ">
                        <input type="text" name="c_last_name" id="c_last_name"
                               data-onChange="FatSbBookingServices_FE.resetValidateField"
                               value="<?php esc_html_e($last_name); ?>"
                            <?php echo esc_attr($disable_field); ?>
                               placeholder="<?php echo esc_attr__('Last name', 'fat-services-booking'); ?>"
                               required>
                    </div>
                    <div class="field-error-message">
                        <?php echo esc_html__('Please enter last name', 'fat-services-booking'); ?>
                    </div>
                </div>
            </div>


            <div class="two fields">
                <div class="field  <?php echo($disable_customer_email ? 'fat-sb-hidden' : ''); ?>">
                    <label for="email"><?php echo esc_html__('Email', 'fat-services-booking'); ?> <span
                                class="required"> *</span></label>
                    <div class="ui left input">
                        <input type="email" name="c_email" id="c_email"
                               data-onChange="FatSbBookingServices_FE.resetValidateField"
                            <?php echo esc_attr($disable_field); ?>
                               value="<?php esc_html_e($email); ?>"
                               placeholder="<?php echo esc_attr__('Email', 'fat-services-booking'); ?>"
                               required>
                    </div>
                    <div class="field-error-message">
                        <?php echo esc_html__('Please enter email', 'fat-services-booking'); ?>
                    </div>
                </div>

                <div class="field phone-field">
                    <label for="phone"><?php echo esc_html__('Phone', 'fat-services-booking'); ?> <span
                                class="required"> *</span></label>

                    <div class="fat-field-wrap">
                        <div class="ui fluid search selection dropdown phone-code">
                            <input type="hidden" name="phone_code" id="phone_code" autocomplete="nope"
                                   value="<?php echo esc_attr($phone_code_default); ?>">
                            <i class="dropdown icon"></i>
                            <div class="default text"></div>
                            <div class="menu">
                                <?php
                                foreach ($phoneCode as $pc) {
                                    $pc = explode(',', $pc); ?>
                                    <div class="item" data-value="<?php echo esc_attr($pc[1].','.$pc[2]); ?>"><i
                                                class="<?php echo esc_attr($pc[2]); ?> flag"></i><?php echo esc_html($pc[0]); ?>
                                        <span>(<?php echo esc_html($pc[1]); ?>)</span></div>
                                <?php } ?>
                                <div class="item"
                                     data-value="other"><?php echo esc_html__('Other', 'fat-services-booking'); ?></div>
                            </div>
                        </div>

                        <div class="ui left input phone-number">
                            <input type="text" name="c_phone" id="c_phone"
                                   data-onChange="FatSbBookingServices_FE.resetValidateField" required
                                   value="<?php esc_html_e($phone); ?>"
                                   placeholder="<?php echo esc_attr__('Phone', 'fat-services-booking'); ?>">
                        </div>
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

                    <div class="ui action input number has-button">
                        <button class="ui icon button number-decrease">
                            <i class="minus-icon"></i>
                        </button>
                        <input type="text" name="number_of_person" data-type="int" data-step="1" data-min="1"
                               data-max="5" tabindex="6" id="number_of_person" value="1">
                        <button class="ui icon button number-increase">
                            <i class="plus-icon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <?php
            if ($booking_form !== '' && $booking_form !== '[]'):
                $booking_form = json_decode($booking_form);
                $onChange = 'FatSbBookingServices_FE.resetValidateField';
                foreach ($booking_form as $field) {
                    include FAT_SERVICES_DIR_PATH . '/templates/form-builder/fields/' . $field->type . '.php';
                }
            endif; ?>

        </div>
        <div class="fat-sb-button-group">
            <button class="ui right blue labeled icon button fat-bt-next-information">
                <?php echo esc_attr('Next', 'fat-services-booking'); ?>
                <i class="right arrow icon"></i>
            </button>
        </div>
    </div>

    <div class="fat-sb-order">
        <h3><?php echo esc_html__('Confirm order', 'fat-services-booking'); ?></h3>
        <div class="fat-sb-order-wrap">
            <div class="fat-sb-col-left">
                <div class="fat-sb-content-inner">
                    <ul class="fat-sb-order-info">
                        <li>
                            <div class="fat-sb-label">
                                <span><?php echo esc_html__('Date :', 'fat-services-booking'); ?></span></div>
                            <div class="fat-sb-value fat-sb-order-date">2020-06-19</div>
                        </li>
                        <li>
                            <div class="fat-sb-label">
                                <span><?php echo esc_html__('Time :', 'fat-services-booking'); ?></span></div>
                            <div class="fat-sb-value fat-sb-order-time">10:00 - 10:55</div>
                        </li>

                        <li class="fat-sb-order-service">
                            <div class="fat-sb-label"><span></span></div>
                            <div class="fat-sb-value"></div>
                        </li>
                        <li class="fat-sb-order-service-extra">
                        </li>
                    </ul>
                </div>

                <div class="fat-sb-content-inner">
                    <ul class="fat-sb-total-info">
                        <li class="fat-sb-order-tax">
                            <div class="fat-sb-label">
                                <span><?php echo esc_html__('Tax :', 'fat-services-booking'); ?></span></div>
                            <div class="fat-sb-value"></div>
                        </li>

                        <li class="fat-sb-order-subtotal">
                            <div class="fat-sb-label">
                                <span><?php echo esc_html__('Sub total :', 'fat-services-booking'); ?></span></div>
                            <div class="fat-sb-value"></div>
                        </li>
                        <li class=" fat-sb-order-discount">
                            <div class="fat-sb-label">
                                <span><?php echo esc_html__('Discount :', 'fat-services-booking'); ?></span></div>
                            <div class="fat-sb-value"></div>
                        </li>
                        <li>
                            <div class="fat-sb-coupon-wrap">
                                <div class="ui left input">
                                    <input type="text" name="coupon" id="coupon"
                                           placeholder="<?php esc_attr_e('Coupon code', 'fat-services-booking'); ?>">

                                </div>
                                <div class="fat-coupon-error"></div>
                                <button class="ui icon button fat-bt-get-coupon"
                                        data-content="<?php esc_attr_e('Click here to apply coupon code', 'fat-services-booking'); ?>">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </div>
                        </li>
                        <li class="total-order-item">
                            <div class="fat-sb-label">
                                <span><?php echo esc_html__('Total price :', 'fat-services-booking'); ?></span>
                            </div>
                            <div class="fat-sb-value fat-sb-order-total"></div>
                        </li>

                        <?php if (is_user_logged_in()):
                            $user = wp_get_current_user();
                            $email = $user->user_email;
                            $price_amount = FAT_DB_Price_Package::get_price_amount_by_user($email);
                            $price_amount = $price_amount['buy_amount'] - $price_amount['has_payment'];
                            $price_amount = $price_amount > 0 ? $price_amount : 0;
                            ?>
                            <li class="total-pricing-package">
                                <div class="fat-sb-label">
                                    <span><?php echo esc_html__('Total credit :', 'fat-services-booking'); ?></span>
                                </div>
                                <div class="fat-sb-value"><?php echo esc_html($price_amount);?></div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="fat-sb-col-right">
                <div class="fat-sb-content-inner">
                    <ul class="fat-sb-list-payment">
                        <?php if (!isset($setting['onsite_enable']) || $setting['onsite_enable'] == "1") : ?>
                            <li>
                                <div class="payment-item" data-payment="onsite">
                                    <i class="money bill alternate outline icon"></i>
                                    <span><?php esc_html_e('Onsite payment', 'fat-services-booking'); ?></span>
                                </div>
                            </li>
                        <?php endif; ?>

                        <?php if (isset($setting['paypal_enable']) && $setting['paypal_enable'] == "1") : ?>
                            <li>
                                <div class="payment-item" data-payment="paypal">
                                    <i class="cc paypal icon"></i>
                                    <span><?php esc_html_e('Paypal', 'fat-services-booking'); ?></span>
                                </div>
                            </li>
                        <?php endif; ?>

                        <?php if (isset($setting['stripe_enable']) && $setting['stripe_enable'] == "1") : ?>
                            <li>
                                <div class="payment-item" data-payment="stripe">
                                    <i class="cc stripe icon"></i>
                                    <span><?php esc_html_e('Stripe', 'fat-services-booking'); ?></span>
                                </div>
                            </li>
                        <?php endif; ?>

                        <?php if (isset($setting['myPOS_enable']) && $setting['myPOS_enable'] == "1") : ?>
                            <li>
                                <div class="payment-item" data-payment="myPOS">
                                    <i class="credit card outline icon"></i>
                                    <span><?php esc_html_e('myPOS', 'fat-services-booking'); ?></span>
                                </div>
                            </li>
                        <?php endif; ?>

                        <?php
                        $is_show_price_package = !isset($setting['price_package_enable']) || $setting['price_package_enable'] == "1";
                        if ($is_show_price_package) : ?>
                            <li>
                                <div class="payment-item" data-payment="price-package">
                                    <i class="credit card outline icon"></i>
                                    <span><?php esc_html_e('Price Package', 'fat-services-booking'); ?></span>
                                </div>
                            </li>
                        <?php endif; ?>

                        <?php if (!isset($setting['przelewy24_enable']) || $setting['przelewy24_enable'] == "1") : ?>
                            <li>
                                <div class="payment-item" data-payment="przelewy24">
                                    <i class="credit card icon"></i>
                                    <span><?php esc_html_e('Przelewy24', 'fat-services-booking'); ?></span>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <?php if (isset($setting['stripe_enable']) && $setting['stripe_enable'] == "1") : ?>
                        <div class="fat-sb-order-stripe fat-sb-hidden">
                            <form method="post" class="stripe-form" id="stripe-payment-form"
                                  data-pk="<?php echo(isset($setting['stripe_publish_key']) ? $setting['stripe_publish_key'] : 'pk_test_9q3BpuszZDNlnc8uppYQYQH7'); ?>">
                                <div class="form-row">
                                    <div id="card-element-<?php echo uniqid(); ?>" class="card-element">
                                        <!-- A Stripe Element will be inserted here. -->
                                    </div>
                                    <!-- Used to display form errors. -->
                                    <div id="card-errors-<?php echo uniqid(); ?>" class="card-errors"
                                         role="alert"></div>
                                </div>
                                <button></button>
                            </form>
                        </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="fat-sb-error-message">

        </div>
        <div class="fat-sb-button-group">
            <button class="ui right blue labeled icon button disabled fat-bt-payment">
                <?php echo esc_attr('Confirm order', 'fat-services-booking'); ?>
                <i class="right arrow icon"></i>
            </button>
        </div>
    </div>

    <div class="fat-sb-order-success calendar">
        <h3><?php esc_html_e('Appointment booked', 'fat-services-booking'); ?></h3>
        <div class="fat-mg-top-60">
            <?php echo esc_html($booked_message);?>
        </div>
        <div class="fat-mg-top-30">
            <button class="ui primary button fat-bt-add-google-calendar fat-bt"
                    data-onClick="FatSbCalendarServices_FE.addToGoogleCalendar">
                <?php esc_html_e('Add to Google calendar', 'fat-services-booking'); ?>
            </button>

            <button class="ui primary button fat-bt-add-icalendar fat-bt"
                    data-onClick="FatSbCalendarServices_FE.addToICalendar">
                <?php esc_html_e('Add to iCalendar', 'fat-services-booking'); ?>
            </button>

            <button class="ui primary button fat-bt-add-book-another fat-bt"
                    data-onClick="FatSbCalendarServices_FE.bookAnotherCourse">
                <?php esc_html_e('Book another course', 'fat-services-booking'); ?>
            </button>
        </div>
    </div>

</div>
