<?php
/**
 * Created by PhpStorm.
 * User: PhuongTH
 * Date: 6/9/2020
 * Time: 10:02 AM
 */

$location_db = FAT_DB_Locations::instance();
$locations = $location_db->get_locations();

$services_db = FAT_DB_Services::instance();
$categories = $services_db->get_service_category();
$services = $services_db->get_services();
$services_extra = $services_db->get_services_extra();
$services_employee = $services_db->get_service_employee();
$services_employee = json_encode($services_employee);

$employee_db = FAT_DB_Employees::instance();
$employees = $employee_db->get_employees();

$setting_db = FAT_DB_Setting::instance();
$setting = $setting_db->get_setting();
$setting_currency = $setting_db->get_currency_setting();

$container_class = "fat-semantic-container fat-booking-container fat-sb-step-vertical-layout";
$container_class .= isset($atts['hide_number_of_person']) && $atts['hide_number_of_person'] == '1' ? ' hide-number-person' : '';
$container_class .= isset($atts['hide_employee']) && $atts['hide_employee'] == '1' ? ' hide-employee' : '';
$container_class .= isset($atts['hide_service_extra']) && $atts['hide_service_extra'] == '1' ? ' hide-service-extra' : '';
$container_class .= isset($setting['time_format']) && $setting['time_format'] ? ' time-' . $setting['time_format'] : '';


$employee_label = isset($setting['employee_label']) && $setting['employee_label'] ? $setting['employee_label'] : esc_html__('Employee', 'fat-services-booking');
$service_label = isset($setting['service_label']) && $setting['service_label'] ? $setting['service_label'] : esc_html__('Service', 'fat-services-booking');
$extra_service_label = esc_html__('Extra ', 'fat-services-booking') . strtolower($service_label);
$price_label = isset($setting['price_label']) && $setting['price_label'] ? $setting['price_label'] : esc_html__('Price:', 'fat-services-booking');
$number_of_person_label = isset($setting['number_of_person_label']) && $setting['number_of_person_label'] ? $setting['number_of_person_label'] : esc_html__('Number of persons', 'fat-services-booking');
$total_cost_label = isset($setting['total_cost_label']) && $setting['total_cost_label'] ? $setting['total_cost_label'] : esc_html__('Total cost:', 'fat-services-booking');
$payment_method_label = isset($setting['payment_method_label']) && $setting['payment_method_label'] ? $setting['payment_method_label'] : esc_html__('Payment method:', 'fat-services-booking');
$disable_customer_email = isset($setting['disable_customer_email']) && $setting['disable_customer_email'] == '1' ? 1 : 0;

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
<div class="<?php echo esc_attr($container_class); ?>" data-se="<?php echo esc_attr($services_employee); ?>">
    <div class="ui vertical steps fat-ui-step-desktop">
        <div class="step active" data-step="location" data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <i class="map marker alternate icon"></i>
            <div class="content">
                <div class="title"><?php echo esc_html__('Locations', 'fat-services-booking'); ?></div>
                <div class="description"><?php echo esc_html__('Choose location', 'fat-services-booking'); ?> </div>
            </div>
        </div>

        <div class="step disabled" data-step="category" data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <i class="folder outline icon"></i>
            <div class="content">
                <div class="title"><?php echo esc_html__('Categories', 'fat-services-booking'); ?></div>
                <div class="description"><?php echo esc_html__('Choose category', 'fat-services-booking'); ?> </div>
            </div>
        </div>

        <div class="step disabled" data-step="service" data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <i class="list alternate outline icon"></i>
            <div class="content">
                <div class="title"><?php echo esc_html__('Services', 'fat-services-booking'); ?></div>
                <div class="description"><?php echo esc_html__('Choose service', 'fat-services-booking'); ?> </div>
            </div>
        </div>

        <div class="step disabled service-extra" data-step="service-extra" data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <i class="tasks icon"></i>
            <div class="content">
                <div class="title"><?php echo esc_html__('Services Extra', 'fat-services-booking'); ?></div>
                <div class="description"><?php echo esc_html__('Choose service extra', 'fat-services-booking'); ?> </div>
            </div>
        </div>

        <div class="step disabled employee" data-step="employee" data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <i class="user circle outline icon"></i>
            <div class="content">
                <div class="title"><?php echo esc_html__('Employees', 'fat-services-booking'); ?></div>
                <div class="description"><?php echo esc_html__('Choose employee', 'fat-services-booking'); ?> </div>
            </div>
        </div>

        <div class="step disabled" data-step="date-time" data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <i class="clock outline icon"></i>
            <div class="content">
                <div class="title"><?php echo esc_html__('Date & Time', 'fat-services-booking'); ?></div>
                <div class="description"><?php echo esc_html__('Choose date & time', 'fat-services-booking'); ?> </div>
            </div>
        </div>

        <div class="step disabled" data-step="information" data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <i class="info icon"></i>
            <div class="content">
                <div class="title"><?php echo esc_html__('Information', 'fat-services-booking'); ?></div>
                <div class="description"><?php echo esc_html__('Fill your information', 'fat-services-booking'); ?></div>
            </div>
        </div>

        <div class="step disabled" data-step="order" data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <i class="credit card outline icon"></i>
            <div class="content">
                <div class="title"><?php echo esc_html__('Confirm Order', 'fat-services-booking'); ?></div>
                <div class="description"><?php echo esc_html__('Confirm and payment', 'fat-services-booking'); ?></div>
            </div>
        </div>

        <div class="step disabled" data-step="calendar" data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <i class="calendar alternate outline icon"></i>
            <div class="content">
                <div class="title"><?php echo esc_html__('Add to Calendar', 'fat-services-booking'); ?></div>
                <div class="description"></div>
            </div>
        </div>
    </div>

    <div class="fat-ui-step-mobile">
        <div class="step active" data-order="1" data-step="location"
             data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <span>1</span>
        </div>

        <div class="step disabled" data-order="2" data-step="category"
             data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <span>2</span>
        </div>

        <div class="step disabled" data-order="3" data-step="service"
             data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <span>3</span>
        </div>

        <div class="step disabled" data-order="4" data-step="service-extra"
             data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <span>4</span>
        </div>

        <div class="step disabled" data-order="5" data-step="employee"
             data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <span>5</span>
        </div>

        <div class="step disabled" data-order="6" data-step="date-time"
             data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <span>6</span>
        </div>

        <div class="step disabled" data-order="7" data-step="information"
             data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <span>7</span>
        </div>

        <div class="step disabled" data-order="8" data-step="order"
             data-onClick="FatSbBookingStepVertical_FE.stepClick">
            <span>8</span>
        </div>

        <div class="step disabled" data-order="9" data-step="calendar">
            <span>9</span>
        </div>
    </div>

    <div class="fat-sb-tab-content-wrap">
        <div class="fat-sb-tab-content locations active" data-tab="location">
            <h3><?php echo esc_html__('Select location', 'fat-services-booking'); ?></h3>
            <div class="fat-sb-list-locations fat-sb-item-wrap">
                <div class="fat-sb-item-inner-wrap">
                    <?php foreach ($locations as $loc) { ?>
                        <div class="fat-sb-item fat-align-center">
                            <div class="fat-sb-item-inner"
                                 data-loc="loc_<?php echo esc_attr($loc->loc_id); ?>"
                                 data-id="<?php echo esc_attr($loc->loc_id); ?>"
                                 data-name="<?php echo esc_attr($loc->loc_name); ?>"
                                 data-onClick="FatSbBookingStepVertical_FE.itemOnClick">
                                <div class="fat-sb-item-avatar">
                                    <?php if ($loc->loc_image_url != ''): ?>
                                        <img class="fat-border-round fat-box-shadow fat-img-80"
                                             src="<?php echo esc_url($loc->loc_image_url); ?>">
                                    <?php else: ?>
                                        <span class="fat-no-thumb fat-img-80"></span>
                                    <?php endif; ?>
                                </div>
                                <div class="fat-sb-item-content">
                                    <div class="loc-title item-title"><?php echo esc_html($loc->loc_name); ?></div>
                                    <div class="meta">
                                        <div class="address">
                                            <?php echo esc_html($loc->loc_address); ?>
                                        </div>
                                        <div class="description">
                                            <?php echo esc_html($loc->loc_description); ?>
                                        </div>
                                    </div>
                                </div>
                                <span class="fat-check"></span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="fat-sb-tab-content categories" data-tab="category">
            <h3><?php echo esc_html__('Select category', 'fat-services-booking'); ?></h3>
            <div class="fat-sb-list-categories fat-sb-item-wrap">
                <div class="fat-sb-item-inner-wrap">
                    <?php foreach ($categories as $cat) { ?>
                        <div class="fat-sb-item fat-align-center">
                            <div class="fat-sb-item-inner" data-cat="cat-<?php echo esc_attr($cat->sc_id); ?>"
                                 data-onClick="FatSbBookingStepVertical_FE.itemOnClick">
                                <div class="fat-sb-item-avatar">
                                    <?php if ($cat->sc_image_url != ''): ?>
                                        <img class="fat-border-round fat-box-shadow fat-img-80"
                                             src="<?php echo esc_url($cat->sc_image_url); ?>">
                                    <?php else: ?>
                                        <span class="fat-no-thumb fat-img-80"></span>
                                    <?php endif; ?>
                                </div>
                                <div class="fat-sb-item-content">
                                    <div class="service-title item-title"><?php echo esc_html($cat->sc_name); ?></div>
                                    <div class="meta">
                                        <div class="total-services">
                                            <span class="total-label">
                                                <?php echo esc_html($cat->sc_total_service); ?>
                                            </span>
                                            <?php echo esc_html__(' service(s)', 'fat-services-booking'); ?>
                                        </div>
                                        <div class="description">
                                            <?php echo esc_html($cat->sc_description); ?>
                                        </div>
                                    </div>
                                </div>
                                <span class="fat-check"></span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="fat-sb-tab-content services" data-tab="service">
            <h3><?php echo esc_html__('Select service', 'fat-services-booking'); ?></h3>
            <div class="fat-sb-list-services fat-sb-item-wrap">
                <div class="fat-sb-item-inner-wrap">
                    <?php
                    $duration = FAT_SB_Utils::getDurations(0, 'duration_step');
                    foreach ($services as $ser) { ?>
                        <div class="fat-sb-item fat-align-center cat-<?php echo esc_attr($ser->s_category_id); ?>">
                            <div class="fat-sb-item-inner" data-cat="cat-<?php echo esc_attr($ser->s_category_id); ?>"
                                 data-id="<?php echo esc_attr($ser->s_id); ?>"
                                 data-s-multiple-days="<?php echo esc_attr($ser->s_multiple_days); ?>"
                                 data-s-min-slot="<?php echo esc_attr($ser->s_min_multiple_slot); ?>"
                                 data-s-max-slot="<?php echo esc_attr($ser->s_max_multiple_slot); ?>"
                                 data-name="<?php echo esc_attr($ser->s_name); ?>"
                                 data-duration="<?php echo esc_attr($ser->s_duration); ?>"
                                 data-break-time="<?php echo esc_attr($ser->s_break_time); ?>"
                                 data-tax="<?php echo esc_attr($ser->s_tax); ?>"
                                 data-se-id="<?php echo esc_attr($ser->s_extra_ids); ?>"
                                 data-onClick="FatSbBookingStepVertical_FE.itemOnClick">
                                <div class="fat-sb-item-avatar">
                                    <?php if ($ser->s_image_url != ''): ?>
                                        <img class="fat-border-round fat-box-shadow fat-img-80"
                                             src="<?php echo esc_url($ser->s_image_url); ?>">
                                    <?php else: ?>
                                        <span class="fat-no-thumb fat-img-80"></span>
                                    <?php endif; ?>
                                </div>
                                <div class="fat-sb-item-content">
                                    <div class="service-title item-title"><?php echo esc_html($ser->s_name); ?></div>
                                    <div class="meta">
                                        <div class="price-duration">
                                            <span class="duration"><i
                                                        class="clock outline icon"></i><?php echo esc_html($duration[$ser->s_duration]); ?></span>
                                        </div>
                                        <div class="description">
                                            <?php echo esc_html($ser->s_description); ?>
                                        </div>
                                    </div>
                                </div>
                                <span class="fat-check"></span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="fat-sb-tab-content services-extra" data-tab="service-extra">
            <h3><?php echo esc_html__('Select service extra', 'fat-services-booking'); ?></h3>
            <div class="fat-sb-list-services-extra fat-sb-item-wrap">
                <div class="fat-sb-item-inner-wrap">
                    <?php
                    foreach ($services_extra as $ser) { ?>
                        <div class="fat-sb-item fat-align-center se-<?php echo esc_attr($ser->se_id); ?>">
                            <div class="fat-sb-item-inner"
                                 data-onClick="FatSbBookingStepVertical_FE.itemServiceExtraOnClick"
                                 data-id="<?php echo esc_attr($ser->se_id); ?>"
                                 data-name="<?php echo esc_attr($ser->se_name); ?>"
                                 data-duration="<?php echo esc_attr($ser->se_duration); ?>"
                                 data-price="<?php echo esc_attr($ser->se_price); ?>"
                                 data-price-on-total="<?php echo esc_attr($ser->se_price_on_total); ?>" >
                                <div class="fat-sb-item-avatar">
                                    <?php if ($ser->se_image_url != ''): ?>
                                        <img class="fat-border-round fat-box-shadow fat-img-80"
                                             src="<?php echo esc_url($ser->se_image_url); ?>">
                                    <?php else: ?>
                                        <span class="fat-no-thumb fat-img-80"></span>
                                    <?php endif; ?>
                                </div>
                                <div class="fat-sb-item-content">
                                    <div class="service-title item-title"><?php echo esc_html($ser->se_name); ?></div>
                                    <div class="meta">
                                        <div class="price-duration">
                                            <span class="price"><?php echo esc_html($setting_currency['symbol']);?><?php echo esc_html($ser->se_price); ?></span>
                                            <span class="duration"><i
                                                        class="clock outline icon"></i><?php echo esc_html($duration[$ser->se_duration]); ?></span>
                                        </div>
                                        <div class="description">
                                            <?php echo esc_html($ser->se_description); ?>
                                        </div>
                                    </div>
                                </div>
                                <span class="fat-check"></span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="fat-sb-button-group">
                <button class="ui right blue labeled icon button"
                        data-onClick="FatSbBookingStepVertical_FE.nextServiceExtra">
                    <?php echo esc_html__('Next', 'fat-services-booking'); ?>
                    <i class="right arrow icon"></i>
                </button>
            </div>
        </div>

        <div class="fat-sb-tab-content employees" data-tab="employee">
            <h3><?php echo esc_html__('Select employee', 'fat-services-booking'); ?></h3>
            <div class="fat-sb-list-employees fat-sb-item-wrap">
                <div class="fat-sb-item-inner-wrap">
                    <?php
                    $emp_avatar_url = '';
                    $emp_loc = '';
                    foreach ($employees as $emp) {
                        $emp_avatar_url = isset($emp->e_avatar_id) ? wp_get_attachment_image_src($emp->e_avatar_id, 'thumbnail') : '';
                        $emp_avatar_url = isset($emp_avatar_url[0]) ? $emp_avatar_url[0] : '';
                        if (isset($emp->e_location_ids) && $emp->e_location_ids) {
                            $emp_loc = explode(',', $emp->e_location_ids);
                            $emp_loc = 'loc-' . implode(' loc-', $emp_loc);
                        }
                        ?>
                        <div class="fat-sb-item fat-align-center emp-<?php echo esc_attr($emp->e_id); ?> <?php echo esc_attr($emp_loc); ?>">
                            <div class="fat-sb-item-inner" data-id="<?php echo esc_attr($emp->e_id); ?>"
                                 data-name="<?php echo esc_attr($emp->e_first_name . ' ' . $emp->e_last_name); ?>"
                                 data-onClick="FatSbBookingStepVertical_FE.employeeOnClick">
                                <div class="fat-sb-item-avatar">
                                    <?php if ($emp_avatar_url != ''): ?>
                                        <img class="fat-border-round fat-box-shadow fat-img-80"
                                             src="<?php echo esc_url($emp_avatar_url); ?>">
                                    <?php else: ?>
                                        <span class="fat-no-thumb fat-img-80"></span>
                                    <?php endif; ?>
                                </div>
                                <div class="fat-sb-item-content">
                                    <div class="employee-title item-title"><?php echo esc_html($emp->e_first_name . ' ' . $emp->e_last_name); ?></div>
                                    <div class="email">
                                        <?php echo esc_html($emp->e_email); ?>
                                    </div>
                                    <div class="meta">

                                        <div class="price-capacity">
                                            <span class="price"><?php echo esc_html($setting_currency['symbol']);?>><?php echo esc_html($ser->se_price); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <span class="fat-check"></span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="fat-sb-tab-content date-time" data-tab="date-time">
            <h3><?php echo esc_html__('Select date & time', 'fat-services-booking'); ?></h3>
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
            $now = current_time('mysql', 0);
            $now = DateTime::createFromFormat('Y-m-d H:i:s', $now);
            ?>
            <div class="fat-sb-date-time-wrap">
                <div class="fat-sb-datetime-inner">
                    <div class="fat-sb-date">
                        <input type='text' class="air-date-picker"
                               data-date="<?php echo esc_attr($now->format('Y-m-d')); ?>"
                               data-default="<?php echo esc_attr($now->format('Y-m-d')); ?>"
                               data-locale="<?php echo esc_attr($locale); ?>" required autocomplete="off"
                               name="b_date" id="b_date"/>
                    </div>
                    <div class="fat-sb-time">
                        <h4><?php echo esc_html('Time', 'fat-service-booking'); ?></h4>
                        <div class="fat-sb-time-slot">

                        </div>
                    </div>
                </div>
                <div class="fat-sb-multiple-days">
                    <h4><?php echo esc_html('Selected dates', 'fat-services-booking'); ?></h4>
                    <ul class="list-multiple-days">

                    </ul>
                </div>
            </div>

            <div class="fat-sb-button-group">
                <button class="ui primary button fat-next-step fat-bt-payment fat-bt disabled" data-onclick="FatSbBookingStepVertical_FE.nextToInformation">
                    <?php echo esc_html__('Next','fat-services-booking');?>
                </button>
            </div>
        </div>

        <div class="fat-sb-tab-content information" data-tab="information">
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

                        <div class="ui fluid search selection dropdown phone-code">
                            <input type="hidden" name="phone_code" id="phone_code" autocomplete="nope"
                                   value="<?php echo esc_attr($phone_code_default); ?>">
                            <i class="dropdown icon"></i>
                            <div class="default text"></div>
                            <div class="menu">
                                <?php
                                foreach ($phoneCode as $pc) {
                                    $pc = explode(',', $pc); ?>
                                    <div class="item" data-value="<?php echo esc_attr($pc[1] . ',' . $pc[2]); ?>"><i
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
                <button class="ui right blue labeled icon button"
                        data-onClick="FatSbBookingStepVertical_FE.nextInformation">
                    <?php echo esc_html__('Next', 'fat-services-booking'); ?>
                    <i class="right arrow icon"></i>
                </button>
            </div>
        </div>

        <div class="fat-sb-tab-content order" data-tab="order">
            <h3><?php echo esc_html__('Confirm order', 'fat-services-booking'); ?></h3>
            <div class="fat-sb-order-wrap">
                <div class="fat-sb-col-left">
                    <div class="fat-sb-content-inner">
                        <ul class="fat-sb-order-info">
                            <li>
                                <div class="fat-sb-label">
                                    <span><?php echo esc_html__('Location :', 'fat-services-booking'); ?></span></div>
                                <div class="fat-sb-value fat-sb-order-location">345 Gymer, Hondurat</div>
                            </li>
                            <li>
                                <div class="fat-sb-label">
                                    <span><?php echo esc_html__('Employee :', 'fat-services-booking'); ?></span></div>
                                <div class="fat-sb-value fat-sb-order-employee"></div>
                            </li>
                            <li class="fat-sb-date-item-label">
                                <div class="fat-sb-label">
                                    <span><?php echo esc_html__('Date :', 'fat-services-booking'); ?></span></div>
                                <div class="fat-sb-value fat-sb-order-date"></div>
                            </li>
                            <li class="fat-sb-time-item-label">
                                <div class="fat-sb-label">
                                    <span><?php echo esc_html__('Time :', 'fat-services-booking'); ?></span></div>
                                <div class="fat-sb-value fat-sb-order-time"></div>
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
                                               data-onChange="FatSbBookingStepVertical_FE.couponOnChange"
                                               placeholder="<?php esc_attr_e('Coupon code', 'fat-services-booking'); ?>">

                                    </div>
                                    <div class="fat-coupon-error"></div>
                                    <button class="ui icon button" data-onClick="FatSbBookingStepVertical_FE.getCoupon"
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
                        </ul>
                    </div>
                </div>
                <div class="fat-sb-col-right">
                    <div class="fat-sb-content-inner fat-sb-multiple-date-time-wrap">
                        <div class="fat-sb-head">
                            <div class="fat-sb-head-left"><?php echo esc_html__('Date','fat-services-booking');?></div>
                            <div class="fat-sb-head-right"><?php echo esc_html__('Time','fat-services-booking');?></div>
                            <div class="list-date-time">

                            </div>
                        </div>
                    </div>
                    
                    <div class="fat-sb-content-inner">
                        <ul class="fat-sb-list-payment">
                            <?php if (!isset($setting['onsite_enable']) || $setting['onsite_enable'] == "1") : ?>
                                <li>
                                    <div class="payment-item" data-payment="onsite"
                                         data-onClick="FatSbBookingStepVertical_FE.paymentClick">
                                        <i class="money bill alternate outline icon"></i>
                                        <span><?php esc_html_e('Onsite payment', 'fat-services-booking'); ?></span>
                                    </div>
                                </li>
                            <?php endif; ?>

                            <?php if (isset($setting['paypal_enable']) && $setting['paypal_enable'] == "1") : ?>
                                <li>
                                    <div class="payment-item" data-payment="paypal"
                                         data-onClick="FatSbBookingStepVertical_FE.paymentClick">
                                        <i class="cc paypal icon"></i>
                                        <span><?php esc_html_e('Paypal', 'fat-services-booking'); ?></span>
                                    </div>
                                </li>
                            <?php endif; ?>

                            <?php if (isset($setting['stripe_enable']) && $setting['stripe_enable'] == "1") : ?>
                                <li>
                                    <div class="payment-item" data-payment="stripe"
                                         data-onClick="FatSbBookingStepVertical_FE.paymentClick">
                                        <i class="cc stripe icon"></i>
                                        <span><?php esc_html_e('Stripe', 'fat-services-booking'); ?></span>
                                    </div>
                                </li>
                            <?php endif; ?>

                            <?php if (isset($setting['myPOS_enable']) && $setting['myPOS_enable'] == "1") : ?>
                                <li>
                                    <div class="payment-item" data-payment="myPOS"
                                         data-onClick="FatSbBookingStepVertical_FE.paymentClick">
                                        <i class="credit card outline icon"></i>
                                        <span><?php esc_html_e('myPOS', 'fat-services-booking'); ?></span>
                                    </div>
                                </li>
                            <?php endif; ?>

                            <?php if (!isset($setting['price_package_enable']) || $setting['price_package_enable'] == "1") : ?>
                                <li>
                                    <div class="payment-item" data-payment="price-package"
                                         data-onClick="FatSbBookingStepVertical_FE.paymentClick">
                                        <i class="credit card outline icon"></i>
                                        <span><?php esc_html_e('Price Package', 'fat-services-booking'); ?></span>
                                    </div>
                                </li>
                            <?php endif; ?>

                            <?php if (!isset($setting['przelewy24_enable']) || $setting['przelewy24_enable'] == "1") : ?>
                                <li>
                                    <div class="payment-item" data-payment="przelewy24"
                                         data-onClick="FatSbBookingStepVertical_FE.paymentClick">
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
                                <div class="fat-sb-back-payment">
                                    <a href="javascript:" data-onClick="FatSbBookingStepVertical_FE.goBackPayment">
                                        <i class="arrow left icon"></i>
                                        <?php echo esc_html__('Back','fat-services-booking');?>
                                    </a>
                                </div>
                            </div>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="fat-sb-error-message">

            </div>
            <div class="fat-sb-button-group">
                <button class="ui right blue labeled icon button disabled fat-bt-payment"
                        data-onClick="FatSbBookingStepVertical_FE.confirmOrderClick">
                    <?php echo esc_html__('Confirm order', 'fat-services-booking'); ?>
                    <i class="right arrow icon"></i>
                </button>
            </div>
        </div>

        <div class="fat-sb-tab-content calendar" data-tab="calendar">
            <h3><?php esc_html_e('Appointment booked', 'fat-services-booking'); ?></h3>
            <div class="fat-mg-top-60">
                <?php echo esc_html($booked_message);?>
            </div>
            <div class="fat-mg-top-30">
                <button class="ui primary button fat-bt-add-google-calendar fat-bt"
                        data-onClick="FatSbBookingStepVertical_FE.addToGoogleCalendar">
                    <?php esc_html_e('Add to Google calendar', 'fat-services-booking'); ?>
                </button>

                <button class="ui primary button fat-bt-add-icalendar fat-bt"
                        data-onClick="FatSbBookingStepVertical_FE.addToICalendar">
                    <?php esc_html_e('Add to iCalendar', 'fat-services-booking'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
