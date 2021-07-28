<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 5/15/2019
 * Time: 2:04 PM
 */
$currency = FAT_SB_Utils::getCurrency();

$db_setting = FAT_DB_Setting::instance();
$currency_setting = $db_setting->get_currency_setting();
$currency_symbol = isset($currency_setting['symbol']) ? $currency_setting['symbol'] : '$';
?>
<script type="text/html" id="tmpl-fat-sb-setting-general-template">
    <div class="ui modal tiny fat-semantic-container fat-setting-modal">
        <div class="header fat-sb-popup-title"><?php echo esc_html__('General setting','fat-services-booking'); ?></div>
        <div class="scrolling content">
            <div class="ui form">
                <div class="one fields">
                    <div class="field">
                        <label for="b_process_status"><?php echo esc_html__('Default calendar view','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip" data-position="right center"
                                 data-content="<?php echo esc_attr__('Set up default view for calendar.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui floating dropdown labeled icon selection dropdown">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="calendar_view" id="calendar_view"
                                   value="{{data.calendar_view}}" tabindex="1">
                            <span class="text"><?php echo esc_html__('Select view','fat-services-booking'); ?></span>
                            <div class="menu">
                                <div class="item" data-value="month">
                                    <?php echo esc_html__('Month','fat-services-booking'); ?>
                                </div>
                                <div class="item" data-value="agendaWeek">
                                    <?php echo esc_html__('Week','fat-services-booking'); ?>
                                </div>
                                <div class="item" data-value="agendaDay">
                                    <?php echo esc_html__('Day','fat-services-booking'); ?>
                                </div>
                                <div class="item" data-value="listWeek">
                                    <?php echo esc_html__('List','fat-services-booking'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <label for="b_process_status"><?php echo esc_html__('Default booking status','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip" data-position="right center"
                                 data-content="<?php echo esc_attr__('Set up default booking status when add new booking.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui floating dropdown labeled icon selection dropdown">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="b_process_status" id="b_process_status"
                                   value="{{data.b_process_status}}" tabindex="1">
                            <span class="text"><?php echo esc_html__('Select status','fat-services-booking'); ?></span>
                            <div class="menu">
                                <div class="item" data-value="0">
                                    <div class="ui yellow empty circular label"></div>
                                    <?php echo esc_html__('Pending','fat-services-booking'); ?>
                                </div>
                                <div class="item" data-value="1">
                                    <div class="ui green empty circular label"></div>
                                    <?php echo esc_html__('Approved','fat-services-booking'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <label for="b_process_status"><?php echo esc_html__('Allow client cancel booking','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip" data-position="right center"
                                 data-content="<?php echo esc_attr__('If select Yes, client can be cancel booking from booking history.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui floating dropdown  selection ">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="allow_client_cancel" id="allow_client_cancel"
                                   value="{{data.allow_client_cancel}}" tabindex="1">
                            <span class="text"><?php echo esc_html__('Select option','fat-services-booking'); ?></span>
                            <div class="menu">
                                <div class="item" data-value="0">
                                    <?php echo esc_html__('No','fat-services-booking'); ?>
                                </div>
                                <div class="item" data-value="1">
                                    <?php echo esc_html__('Yes','fat-services-booking'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <label for="service_tax"><?php echo esc_html__('Default service tax','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('Set up default value for Tax field in new service form.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui selection dropdown  ">
                            <i class="money bill alternate outline icon"></i>
                            <input type="hidden" name="service_tax" id="service_tax" value="{{data.service_tax}}"
                                   tabindex="2"
                                   required>
                            <i class="dropdown icon"></i>
                            <div class="text"><?php echo esc_html__('Select tax','fat-services-booking'); ?></div>
                            <div class="menu">
                                <div class="item" data-value="0">0%</div>
                                <div class="item" data-value="5">5%</div>
                                <div class="item" data-value="10">10%</div>
                            </div>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter tax','fat-services-booking'); ?>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <label for="duration_step"><?php echo esc_html__('Duration step','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('Default is 15 minute, but you can setting from 5 minutes to 60 minutes.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui selection dropdown  ">
                            <i class="clock outline icon"></i>
                            <input type="hidden" name="duration_step" id="duration_step" value="{{data.duration_step}}"
                                   tabindex="2"
                                   required>
                            <i class="dropdown icon"></i>
                            <div class="text"><?php echo esc_html__('Select duration step','fat-services-booking'); ?></div>
                            <div class="menu">
                                <div class="item" data-value="5">
                                    5 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="10">
                                    10 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="15">
                                    15 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="20">
                                    20 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="25">
                                    25 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="30">
                                    30 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="35">
                                    35 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="40">
                                    40 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="45">
                                    45 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="50">
                                    50 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="55">
                                    55 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="60">
                                    60 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="90">
                                    90 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <label for="time_step"><?php echo esc_html__('Time step','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('Default is 15 minute, but you can setting from 5 minutes to 60 minutes.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui selection dropdown  ">
                            <i class="clock outline icon"></i>
                            <input type="hidden" name="time_step" id="time_step" value="{{data.time_step}}"
                                   tabindex="2"
                                   required>
                            <i class="dropdown icon"></i>
                            <div class="text"><?php echo esc_html__('Select time step','fat-services-booking'); ?></div>
                            <div class="menu">
                                <div class="item" data-value="5">
                                    5 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="10">
                                    10 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="15">
                                    15 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="20">
                                    20 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="25">
                                    25 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="30">
                                    30 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="35">
                                    35 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="40">
                                    40 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="45">
                                    45 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="50">
                                    50 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="55">
                                    55 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="60">
                                    60 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                                <div class="item" data-value="90">
                                    90 <?php echo esc_html__('minutes','fat-services-booking'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <label for="time_format"><?php echo esc_html__('Time format','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip" data-position="right center"
                                 data-content="<?php echo esc_attr__('Set up time slot format.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui floating dropdown labeled icon selection dropdown">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="time_format" id="time_format"
                                   value="{{data.time_format}}" tabindex="1">
                            <span class="text"><?php echo esc_html__('Select time format','fat-services-booking'); ?></span>
                            <div class="menu">
                                <div class="item" data-value="24h">
                                    <?php echo esc_html__('24h','fat-services-booking'); ?>
                                </div>
                                <div class="item" data-value="12h">
                                    <?php echo esc_html__('12h','fat-services-booking'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="day_limit"><?php echo esc_html__('Day limit','fat-services-booking'); ?><span
                                    class="required">*</span>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('How far in the future the clients can book.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui action input number has-button">
                            <button class="ui icon button number-decrease">
                                <i class="minus-icon"></i>
                            </button>
                            <input type="text" name="day_limit" data-type="int" data-step="1" data-min="1"
                                   tabindex="3" required
                                   id="day_limit" value="{{data.day_limit}}">
                            <button class="ui icon button number-increase">
                                <i class="plus-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="day_limit"><?php echo esc_html__('Time to automatic change status (hours)','fat-services-booking'); ?><span
                                    class="required">*</span>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('Time to automatic change status to approved if admin not confirm. Set to 0 if you want disable this feature.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui action input number has-button">
                            <button class="ui icon button number-decrease">
                                <i class="minus-icon"></i>
                            </button>
                            <input type="text" name="time_to_change_status" data-type="int" data-step="1" data-min="0"
                                   tabindex="3" required
                                   id="time_to_change_status" value="{{data.time_to_change_status}}">
                            <button class="ui icon button number-increase">
                                <i class="plus-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="limit_booking_per_day"><?php echo esc_html__('Limit booking per day','fat-services-booking'); ?><span
                                    class="required">*</span>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('Daily reservation limit per service for user logged. Set 0 to ignore limit','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui action input number has-button">
                            <button class="ui icon button number-decrease">
                                <i class="minus-icon"></i>
                            </button>
                            <input type="text" name="limit_booking_per_day" data-type="int" data-step="1" data-min="0"
                                   tabindex="3" required
                                   id="limit_booking_per_day" value="{{data.limit_booking_per_day}}">
                            <button class="ui icon button number-increase">
                                <i class="plus-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="item_per_page"><?php echo esc_html__('Default items per page','fat-services-booking'); ?>
                            <span class="required">*</span>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('Use to set up paging for list.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui action input number has-button">
                            <button class="ui icon button number-decrease">
                                <i class="minus-icon"></i>
                            </button>
                            <input type="text" name="item_per_page" data-type="int" data-step="1" data-min="5" required
                                   tabindex="4" id="item_per_page" value="{{data.item_per_page}}">
                            <button class="ui icon button number-increase">
                                <i class="plus-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="day_limit"><?php echo esc_html__('Cancel booking before (hours)','fat-services-booking'); ?><span
                                    class="required">*</span>
                        </label>
                        <div class="ui action input number has-button">
                            <button class="ui icon button number-decrease">
                                <i class="minus-icon"></i>
                            </button>
                            <input type="text" name="cancel_before" data-type="int" data-step="1" data-min="1"
                                   tabindex="3" required
                                   id="cancel_before" value="{{data.cancel_before}}">
                            <button class="ui icon button number-increase">
                                <i class="plus-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="item_per_page"><?php echo esc_html__('Default phone country code','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('This is the phone code shown by default in the booking form.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui fluid search selection dropdown phone-code">
                            <input type="hidden" name="default_phone_code" id="default_phone_code" autocomplete="nope" value="{{data.default_phone_code}}">
                            <i class="dropdown icon"></i>
                            <div class="default text"></div>
                            <div class="menu">
                                <?php
                                $phoneCode = FAT_SB_Utils::getPhoneCountry();
                                foreach($phoneCode as $pc){
                                    $pc = explode(',',$pc);?>
                                    <div class="item"  data-value="<?php echo esc_attr($pc[1].','.$pc[2]);?>"><i class="<?php echo esc_attr($pc[2]);?> flag"></i><?php echo esc_html($pc[0]);?><span> (<?php echo esc_html($pc[1]);?>)</span></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="one inline fields service-available">
                    <label for="service_available"><?php echo esc_html__('Default service available for','fat-services-booking'); ?>
                        <div class="ui icon ui-tooltip"
                             data-content="<?php echo esc_attr__('Set up default value for Service available field in new service form.','fat-services-booking'); ?>">
                            <i class="question circle icon"></i>
                        </div>
                    </label>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <# if(data.service_available==1){ #>
                            <input type="radio" name="service_available" id="service_available" value="1"
                                   checked="checked"
                                   tabindex="5">
                            <# }else{ #>
                            <input type="radio" name="service_available" id="service_available" value="1" tabindex="10">
                            <# } #>
                            <label><?php echo esc_html__('Every one','fat-services-booking'); ?></label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <# if(data.service_available==2){ #>
                            <input type="radio" name="service_available" id="service_available" value="2"
                                   checked="checked"
                                   tabindex="6">
                            <# }else{ #>
                            <input type="radio" name="service_available" id="service_available" value="2" tabindex="11">
                            <# } #>
                            <label><?php echo esc_html__('Male only','fat-services-booking'); ?></label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <# if(data.service_available==3){ #>
                            <input type="radio" name="service_available" id="service_available" value="3"
                                   checked="checked"
                                   tabindex="7">
                            <# }else{ #>
                            <input type="radio" name="service_available" id="service_available" value="3" tabindex="12">
                            <# } #>
                            <label><?php echo esc_html__('Female only','fat-services-booking'); ?></label>
                        </div>
                    </div>
                </div>

                <div class="one inline fields">
                    <label for="service_available"><?php echo esc_html__('Enable','fat-services-booking'); ?>
                        <div class="ui icon ui-tooltip"
                             data-content="<?php echo esc_attr__('In some case, it have conflict between default modal popup and date time picker. With this case, you can enable use default modal popup or date time picker of theme','fat-services-booking'); ?>">
                            <i class="question circle icon"></i>
                        </div>
                    </label>
                    <div class="field">
                        <div class="ui  checkbox">
                            <# if(data.enable_modal_popup==1){ #>
                            <input type="checkbox" name="enable_modal_popup" id="enable_modal_popup" value="1"
                                   checked="checked"
                                   tabindex="5">
                            <# }else{ #>
                            <input type="checkbox" name="enable_modal_popup" id="enable_modal_popup" value="1" tabindex="10">
                            <# } #>
                            <label><?php echo esc_html__('Modal popup','fat-services-booking'); ?></label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui  checkbox">
                            <# if(data.enable_datetime_picker==1){ #>
                            <input type="checkbox" name="enable_datetime_picker" id="enable_datetime_picker" value="1"
                                   checked="checked"
                                   tabindex="6">
                            <# }else{ #>
                            <input type="checkbox" name="enable_datetime_picker" id="enable_datetime_picker" value="1" tabindex="6">
                            <# } #>
                            <label><?php echo esc_html__('Datetime picker','fat-services-booking'); ?></label>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <div class="ui toggle checkbox">
                            <# if(data.enable_time_slot_deactive==1){ #>
                            <input type="checkbox" name="enable_time_slot_deactive" id="enable_time_slot_deactive" value="1"
                                   checked tabindex="4">
                            <# }else{ #>
                            <input type="checkbox" name="enable_time_slot_deactive" id="enable_time_slot_deactive" value="1"
                                   tabindex="4">
                            <# } #>
                            <label><?php echo esc_html('Show/hide time slot not available','fat-services-booking'); ?>
                                <div class="ui icon ui-tooltip"
                                     data-content="<?php echo esc_attr__('If select, it will be display time slot not available','fat-services-booking'); ?>">
                                    <i class="question circle icon"></i>
                                </div>
                            </label>
                        </div>

                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <div class="ui toggle checkbox">
                            <# if(data.disable_customer_email==1){ #>
                            <input type="checkbox" name="disable_customer_email" id="disable_customer_email" value="1"
                                   checked tabindex="4">
                            <# }else{ #>
                            <input type="checkbox" name="disable_customer_email" id="disable_customer_email" value="1"
                                   tabindex="4">
                            <# } #>
                            <label><?php echo esc_html('Email is not required for customers','fat-services-booking'); ?>
                                <div class="ui icon ui-tooltip"
                                     data-content="<?php echo esc_attr__('If select, client don\'t need fill email in booking form','fat-services-booking'); ?>">
                                    <i class="question circle icon"></i>
                                </div>
                            </label>
                        </div>

                    </div>
                </div>

                <div class="fat-section-wrap" data-depend="enable_time_slot_deactive" data-depend-value="1" style="display: none;">
                    <div class="one fields">
                        <div class="field ">
                            <label for="bg_time_slot_not_active"><?php echo esc_html__('Background color of time slot ( example: #bbbbbb )', 'fat-services-booking'); ?>
                            </label>
                            <div class="ui left input ">
                                <input type="text" name="bg_time_slot_not_active" id="bg_time_slot_not_active"
                                       value="{{data.bg_time_slot_not_active}}" tabindex="6"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="name"><?php echo esc_html__('Employee label','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('This is label of employee what show up in booking form at frontend.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui left input ">
                            <input type="text" name="employee_label" id="employee_label" value="{{data.employee_label}}"
                                   autocomplete="nope"
                                   placeholder="<?php echo esc_attr__('Employee label','fat-services-booking'); ?>">
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="name"><?php echo esc_html__('Service label','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('This is label of service what show up in booking form at frontend.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui left input ">
                            <input type="text" name="service_label" id="service_label" value="{{data.service_label}}"
                                   autocomplete="nope"
                                   placeholder="<?php echo esc_attr__('Service label','fat-services-booking'); ?>">
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="name"><?php echo esc_html__('Price label','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('This is label of price what show up in booking form at frontend.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui left input ">
                            <input type="text" name="price_label" id="price_label" value="{{data.price_label}}"
                                   autocomplete="nope"
                                   placeholder="<?php echo esc_attr__('Price label','fat-services-booking'); ?>">
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="number_of_person_label"><?php echo esc_html__('Number of person label','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('This is label of number of person what show up in booking form at frontend.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui left input ">
                            <input type="text" name="number_of_person_label" id="number_of_person_label"
                                   value="{{data.number_of_person_label}}"
                                   autocomplete="nope"
                                   placeholder="<?php echo esc_attr__('Number of person label label','fat-services-booking'); ?>">
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="person_label"><?php echo esc_html__('Person label in order review','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('This is label of person in order review. Exam: 2 person(s) x $11','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui left input ">
                            <input type="text" name="person_label" id="person_label"
                                   value="{{data.person_label}}"
                                   autocomplete="nope">
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="name"><?php echo esc_html__('Payment method label','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('This is label of payment method what show up in booking form at frontend.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui left input ">
                            <input type="text" name="payment_method_label" id="payment_method_label"
                                   value="{{data.payment_method_label}}"
                                   autocomplete="nope"
                                   placeholder="<?php echo esc_attr__('payment_method_label','fat-services-booking'); ?>">
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="name"><?php echo esc_html__('Total cost label','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('This is label of total cost what show up in booking form at frontend.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui left input ">
                            <input type="text" name="total_cost_label" id="total_cost_label"
                                   value="{{data.total_cost_label}}"
                                   autocomplete="nope"
                                   placeholder="<?php echo esc_attr__('Total cost label','fat-services-booking'); ?>">
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="actions">
            <button class="ui basic button fat-close-modal">
                <i class="times circle outline icon"></i>
                <?php echo esc_html__('Cancel','fat-services-booking'); ?>
            </button>

            <button class="ui blue button fat-submit-modal"
                    data-onClick="FatSbSetting.submitOnClick"
                    data-success-message="<?php echo esc_attr__('Setting has been saved','fat-services-booking'); ?>">
                <i class="save outline icon"></i>
                <?php echo esc_html__('Save','fat-services-booking'); ?>
            </button>

        </div>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-setting-company-template">
    <div class="ui modal tiny fat-semantic-container fat-setting-modal">
        <div class="header fat-sb-popup-title"><?php echo esc_html__('Company setting', 'fat-services-booking'); ?></div>
        <div class="scrolling content">
            <div class="ui form">
                <div class="one fields">
                    <div class="field ">
                        <label for="name"><?php echo esc_html__('Name', 'fat-services-booking'); ?><span
                                    class="required"> *</span></label>
                        <div class="ui left icon input ">
                            <input type="text" name="company_name" id="company_name" value="{{data.company_name}}"
                                   autocomplete="nope"
                                   placeholder="<?php echo esc_attr__('Company name', 'fat-services-booking'); ?>"
                                   required>
                            <i class="edit outline icon"></i>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter company name', 'fat-services-booking'); ?>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="name"><?php echo esc_html__('Address', 'fat-services-booking'); ?><span
                                    class="required"> *</span></label>
                        <div class="ui left icon input ">
                            <input type="text" name="company_address" id="company_address"
                                   value="{{data.company_address}}" autocomplete="nope"
                                   placeholder="<?php echo esc_attr__('Address', 'fat-services-booking'); ?>" required>
                            <i class="edit outline icon"></i>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter address', 'fat-services-booking'); ?>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="phone"><?php echo esc_html__('Phone', 'fat-services-booking'); ?></label>
                        <div class="ui left icon input">
                            <input type="text" name="company_phone" id="company_phone" autocomplete="nope"
                                   placeholder="<?php echo esc_attr__('Phone', 'fat-services-booking'); ?>"
                                   value="{{data.company_phone}}">
                            <i class="phone volume icon"></i>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="email"><?php echo esc_html__('Email', 'fat-services-booking'); ?> <span
                                    class="required"> *</span></label>
                        <div class="ui left icon input">
                            <input type="email" name="company_email" id="company_email" value="{{data.company_email}}"
                                   autocomplete="nope"
                                   placeholder="<?php echo esc_attr__('Email', 'fat-services-booking'); ?>" required>
                            <i class="envelope outline icon"></i>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter email', 'fat-services-booking'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="actions">
            <button class="ui basic button fat-close-modal">
                <i class="times circle outline icon"></i>
                <?php echo esc_html__('Cancel', 'fat-services-booking'); ?>
            </button>

            <button class="ui blue button fat-submit-modal"
                    data-onClick="FatSbSetting.submitOnClick"
                    data-success-message="<?php echo esc_attr__('Setting has been saved', 'fat-services-booking'); ?>">
                <i class="save outline icon"></i>
                <?php echo esc_html__('Save', 'fat-services-booking'); ?>
            </button>

        </div>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-setting-notification-template">
    <div class="ui modal tiny fat-semantic-container fat-setting-modal">
        <div class="header fat-sb-popup-title"><?php echo esc_html__('Notification setting', 'fat-services-booking'); ?></div>
        <div class="scrolling content">
            <div class="ui form">
                <div class="one fields">
                    <div class="field">
                        <label for="mailer"><?php echo esc_html__('Mailer', 'fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip" data-position="right center"
                                 data-content="<?php echo esc_attr__('Set up mail server which handler all outgoing email from your website.', 'fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui floating dropdown icon selection dropdown">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="mailer" id="mailer" value="{{data.mailer}}" tabindex="1"
                                   data-onChange="FatSbSetting.dependFieldOnChange">
                            <span class="text"><?php echo esc_html__('Select mail server', 'fat-services-booking'); ?></span>
                            <div class="menu">
                                <div class="item" data-value="default">
                                    <?php echo esc_html__('Default (use mail server from your hosting) ', 'fat-services-booking'); ?>
                                </div>
                                <div class="item" data-value="smtp">
                                    <?php echo esc_html__('SMTP', 'fat-services-booking'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="two fields" data-depend="mailer" data-depend-value="smtp" style="display: none;">
                    <div class="field">
                        <label for="smtp_host"><?php echo esc_html__('SMTP Host', 'fat-services-booking'); ?></label>
                        <div class="ui left input ">
                            <input type="text" name="smtp_host" id="smtp_host" value="{{data.smtp_host}}"
                                   autocomplete="nope" tabindex="2"
                                   placeholder="<?php echo esc_attr__('SMTP Host', 'fat-services-booking'); ?>">
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter SMTP Host', 'fat-services-booking'); ?>
                        </div>
                    </div>

                    <div class="field">
                        <label for="smtp_port"><?php echo esc_html__('SMTP Port', 'fat-services-booking'); ?></label>
                        <div class="ui left  input ">
                            <input type="text" name="smtp_port" id="smtp_port" value="{{data.smtp_port}}"
                                   autocomplete="nope" tabindex="3"
                                   placeholder="<?php echo esc_attr__('SMTP Port', 'fat-services-booking'); ?>">
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter SMTP Port', 'fat-services-booking'); ?>
                        </div>
                    </div>
                </div>

                <div class="one inline fields" data-depend="mailer" data-depend-value="smtp" style="display: none">
                    <label for="smpt_encryption"><?php echo esc_html__('Encryption', 'fat-services-booking'); ?></label>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <# if(data.smpt_encryption=='none'){ #>
                            <input type="radio" name="smpt_encryption" id="smpt_encryption" value="none"
                                   checked="checked"
                                   tabindex="4">
                            <# }else{ #>
                            <input type="radio" name="smpt_encryption" id="smpt_encryption" value="none" tabindex="4">
                            <# } #>
                            <label><?php echo esc_html__('None', 'fat-services-booking'); ?></label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <# if(data.smpt_encryption=='tls'){ #>
                            <input type="radio" name="smpt_encryption" id="smpt_encryption" value="tls"
                                   checked="checked"
                                   tabindex="5">
                            <# }else{ #>
                            <input type="radio" name="smpt_encryption" id="smpt_encryption" value="tls" tabindex="5">
                            <# } #>
                            <label><?php echo esc_html__('TLS', 'fat-services-booking'); ?></label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <# if(data.smpt_encryption=='ssl'){ #>
                            <input type="radio" name="smpt_encryption" id="smpt_encryption" value="ssl"
                                   checked="checked"
                                   tabindex="6">
                            <# }else{ #>
                            <input type="radio" name="smpt_encryption" id="smpt_encryption" value="ssl" tabindex="6">
                            <# } #>
                            <label><?php echo esc_html__('SSL', 'fat-services-booking'); ?></label>
                        </div>
                    </div>
                </div>


                <div class="two fields" data-depend="mailer" data-depend-value="smtp" style="display: none;">
                    <div class="field">
                        <label for="smtp_username"><?php echo esc_html__('SMTP Username', 'fat-services-booking'); ?></label>
                        <div class="ui left input ">
                            <input type="text" name="smtp_username" id="smtp_username" value="{{data.smtp_username}}"
                                   autocomplete="off" tabindex="7"
                                   placeholder="<?php echo esc_attr__('SMTP Username', 'fat-services-booking'); ?>">
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter SMTP Username', 'fat-services-booking'); ?>
                        </div>
                    </div>

                    <div class="field">
                        <label for="smtp_password"><?php echo esc_html__('SMTP Password', 'fat-services-booking'); ?></label>
                        <div class="ui left input ">
                            <input type="password" name="smtp_password" id="smtp_password"
                                   data-onChange="FatSbSetting.passwordOnChange"
                                   data-value="{{data.smtp_password}}"
                                   value="{{data.smtp_password}}" autocomplete="new-password" tabindex="7"
                                   placeholder="<?php echo esc_attr__('SMTP Password', 'fat-services-booking'); ?>">
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter SMTP Password', 'fat-services-booking'); ?>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="send_from_name"><?php echo esc_html__('Send from mail', 'fat-services-booking'); ?>
                            <span
                                    class="required"> *</span></label>
                        <div class="ui left input ">
                            <input type="text" name="send_from_name" id="send_from_name" value="{{data.send_from_name}}"
                                   autocomplete="nope" tabindex="8"
                                   placeholder="<?php echo esc_attr__('From mail', 'fat-services-booking'); ?>"
                                   required>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter from email address', 'fat-services-booking'); ?>
                        </div>
                    </div>
                </div>

                <div class="one fields" >
                    <div class="field ">
                        <label for="send_from_name"><?php echo esc_html__('Send from name', 'fat-services-booking'); ?>
                            <span
                                    class="required"> *</span></label>
                        <div class="ui left input ">
                            <input type="text" name="send_from_name_label" id="send_from_name_label" value="{{data.send_from_name_label}}"
                                   autocomplete="nope" tabindex="8"
                                   placeholder="<?php echo esc_attr__('From name', 'fat-services-booking'); ?>"
                                   required>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter from name', 'fat-services-booking'); ?>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="cc_to"><?php echo esc_html__('CC to', 'fat-services-booking'); ?></label>
                        <div class="ui left input ">
                            <input type="email" name="cc_to" id="cc_to" value="{{data.cc_to}}" tabindex="9"
                                   autocomplete="nope">
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="bcc_to"><?php echo esc_html__('BCC to', 'fat-services-booking'); ?></label>
                        <div class="ui left input ">
                            <input type="email" name="bcc_to" id="bcc_to" tabindex="10" value="{{data.bcc_to}}"
                                   autocomplete="nope">
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <a href="javascript:" class="fat-show-send-mail"
                           data-open="<?php echo esc_attr__('Test send mail', 'fat-services-booking'); ?>"
                           data-close="<?php echo esc_attr__('Close test send mail', 'fat-services-booking'); ?>"><?php echo esc_html__('Test send mail', 'fat-services-booking'); ?></a>
                    </div>
                </div>
                <div class="one fields fat-test-send-mail-wrap fat-sb-hidden">
                    <div class="field">
                        <div class="ui left input ">
                            <input type="email" name="send_to" id="send_to" tabindex="11" autocomplete="nope"
                                   placeholder="Send mail to">
                        </div>
                        <button class="ui icon button" data-onClick="FatSbSetting.sendMailOnClick"
                                data-invalid-message="<?php echo esc_attr__('Please input valid email', 'fat-services-booking'); ?>">
                            <?php echo esc_html__('Send mail', 'fat-services-booking'); ?>
                        </button>
                    </div>
                </div>


            </div>
        </div>
        <div class="actions">
            <button class="ui basic button fat-close-modal">
                <i class="times circle outline icon"></i>
                <?php echo esc_html__('Cancel', 'fat-services-booking'); ?>
            </button>

            <button class="ui blue button fat-submit-modal"
                    data-onClick="FatSbSetting.submitOnClick"
                    data-success-message="<?php echo esc_attr__('Setting has been saved', 'fat-services-booking'); ?>">
                <i class="save outline icon"></i>
                <?php echo esc_html__('Save', 'fat-services-booking'); ?>
            </button>

        </div>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-setting-sms-notification-template">
    <div class="ui modal tiny fat-semantic-container fat-setting-modal">
        <div class="header fat-sb-popup-title"><?php echo esc_html__('SMS Notification setting', 'fat-services-booking'); ?></div>
        <div class="scrolling content">
            <div class="ui form">
                <div class="one fields">
                    <div class="field">
                        <label for="mailer"><?php echo esc_html__('SMS Provider', 'fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip" data-position="right center"
                                 data-content="<?php echo esc_attr__('Select SMS provider.', 'fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui floating dropdown icon selection dropdown">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="sms_provider" id="sms_provider" value="{{data.sms_provider}}" tabindex="1">
                            <span class="text"><?php echo esc_html__('Select SMS provider', 'fat-services-booking'); ?></span>
                            <div class="menu">
                                <div class="item" data-value="twilio">
                                    <?php echo esc_html('Twilio'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="sms_owner_phone_number"><?php echo esc_html__('Your phone number', 'fat-services-booking'); ?></label>
                        <div class="ui left input ">
                            <input type="text" name="sms_phone_number" id="sms_owner_phone_number" value="{{data.sms_owner_phone_number}}" tabindex="9"
                                   autocomplete="nope">
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="sms_sid"><?php echo esc_html__('Account SID (AUTHEN_ID)', 'fat-services-booking'); ?></label>
                        <div class="ui left input ">
                            <input type="text" name="sms_sid" id="sms_sid" value="{{data.sms_sid}}" tabindex="9"
                                   autocomplete="nope">
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="sms_token"><?php echo esc_html__('Authen Token (AUTH_TOKEN)', 'fat-services-booking'); ?></label>
                        <div class="ui left input ">
                            <input type="text" name="sms_token" id="sms_token" tabindex="10" value="{{data.sms_token}}"
                                   autocomplete="nope">
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <a href="javascript:" class="fat-show-send-sms"
                           data-open="<?php echo esc_attr__('Test send sms', 'fat-services-booking'); ?>"
                           data-close="<?php echo esc_attr__('Close test send sms', 'fat-services-booking'); ?>"><?php echo esc_html__('Test send SMS', 'fat-services-booking'); ?></a>
                    </div>
                </div>

                <div class="one fields fat-test-send-sms-wrap fat-sb-hidden">
                    <div class="field">
                        <div class="ui left input ">
                            <input type="text" name="sms_phone_number" id="sms_phone_number" tabindex="11" autocomplete="nope"
                                   placeholder="<?php echo esc_html__('Input phone number','fat-services-booking');?>">
                        </div>
                        <button class="ui icon button" data-onClick="FatSbSetting.sendSMSOnClick"
                                data-invalid-message="<?php echo esc_attr__('Please input valid phone number', 'fat-services-booking'); ?>">
                            <?php echo esc_html__('Send SMS', 'fat-services-booking'); ?>
                        </button>
                    </div>
                </div>

            </div>
        </div>
        <div class="actions">
            <button class="ui basic button fat-close-modal">
                <i class="times circle outline icon"></i>
                <?php echo esc_html__('Cancel', 'fat-services-booking'); ?>
            </button>

            <button class="ui blue button fat-submit-modal"
                    data-onClick="FatSbSetting.submitOnClick"
                    data-success-message="<?php echo esc_attr__('Setting has been saved', 'fat-services-booking'); ?>">
                <i class="save outline icon"></i>
                <?php echo esc_html__('Save', 'fat-services-booking'); ?>
            </button>

        </div>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-setting-payment-template">
    <div class="ui modal tiny fat-semantic-container fat-setting-modal fat-setting-payment">
        <div class="header fat-sb-popup-title"><?php echo esc_html__('Payment setting', 'fat-services-booking'); ?></div>
        <div class="scrolling content">
            <div class="ui form">
                <div class="one fields">
                    <div class="field ">
                        <label for="item_per_page"><?php echo esc_html__('Number of decimals','fat-services-booking'); ?>
                            <span class="required">*</span>
                            <div class="ui icon ui-tooltip" data-position="bottom center"
                                 data-content="<?php echo esc_attr__('Specify the number of decimals. Ex: if price is 510 and currency decimal is 2, the price displayed will be 510.00','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui action input number has-button">
                            <button class="ui icon button number-decrease">
                                <i class="minus-icon"></i>
                            </button>
                            <input type="text" name="number_of_decimals" data-type="int" data-step="1" data-min="0" required
                                   tabindex="4" id="number_of_decimals" value="{{data.number_of_decimals}}">
                            <button class="ui icon button number-increase">
                                <i class="plus-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <label for="mailer"><?php echo esc_html__('Currency', 'fat-services-booking'); ?></label>
                        <div class="ui floating dropdown icon search selection dropdown fat-sb-currency-dic">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="currency" id="currency" value="{{data.currency}}" tabindex="1">
                            <span class="text"><?php echo esc_html__('Select currency', 'fat-services-booking'); ?></span>
                            <div class="menu">
                                <div class="ui icon search input">
                                    <i class="search icon"></i>
                                    <input type="text"
                                           placeholder="<?php echo esc_attr__('Search currency...', 'fat-services-booking'); ?> ">
                                </div>
                                <div class="scrolling menu">
                                    <?php foreach ($currency as $c) { ?>
                                        <div class="item" data-value="<?php echo esc_attr($c['code']); ?>">
                                            <span class="currency-name"><?php echo esc_html($c['name']); ?></span>
                                            <span class="currency-symbol"><?php echo esc_html($c['symbol']); ?></span>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="one fields">
                    <div class="field">
                        <label for="mailer"><?php echo esc_html__('Price symbol position', 'fat-services-booking'); ?></label>
                        <div class="ui floating dropdown icon selection dropdown">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="symbol_position" id="symbol_position"
                                   value="{{data.symbol_position}}" tabindex="2">
                            <span class="text"><?php echo esc_html__('Select currency', 'fat-services-booking'); ?></span>
                            <div class="menu">
                                <div class="item" data-value="before">
                                    <?php echo esc_html__('Before ', 'fat-services-booking').$currency_symbol.'50'; ?>
                                </div>
                                <div class="item" data-value="after">
                                    <?php echo esc_html__('After ', 'fat-services-booking').'50'.$currency_symbol; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <div class="ui toggle checkbox">
                            <# if(data.hide_payment==1){ #>
                            <input type="checkbox" name="hide_payment" id="hide_payment" value="1"
                                   checked tabindex="4">
                            <# }else{ #>
                            <input type="checkbox" name="hide_payment" id="hide_payment" value="1"
                                   tabindex="4">
                            <# } #>
                            <label><?php echo esc_html__('Hide payment method & price', 'fat-services-booking'); ?>
                                <div class="fat-field-description"><?php esc_html_e('If you select this, the payment method and price section on booking form will be hide','fat-services-booking');?></div>
                            </label>
                        </div>

                    </div>
                </div>

                <div class="one fields ">
                    <div class="field">
                        <label for="booked_message"><?php echo esc_html__('Appointment booked message', 'fat-services-booking'); ?></label>
                        <div class="ui left input ">
                            <input type="text" name="booked_message" id="booked_message" tabindex="11" autocomplete="nope"
                                   placeholder="<?php echo esc_html__('Appointment booked message','fat-services-booking');?>">
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <label for="mailer"><?php echo esc_html__('Default payment method', 'fat-services-booking'); ?></label>
                        <div class="ui floating dropdown icon selection dropdown fat-sb-payment-method-default">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="default_payment_method" id="default_payment_method"
                                   value="{{data.default_payment_method}}" tabindex="3">
                            <span class="text"><?php echo esc_html__('Select payment method', 'fat-services-booking'); ?></span>
                            <div class="menu">
                                <# if(data.onsite_enable=='1'){ #>
                                <div class="item" data-value="onsite">
                                    <?php echo esc_html__('Onsite payment', 'fat-services-booking'); ?>
                                </div>
                                <# } #>

                                <# if(data.paypal_enable=='1'){ #>
                                <div class="item" data-value="paypal">
                                    <?php echo esc_html__('Paypal', 'fat-services-booking'); ?>
                                </div>
                                <# } #>

                                <# if(data.stripe_enable=='1'){ #>
                                <div class="item" data-value="stripe">
                                    <?php echo esc_html__('Stripe', 'fat-services-booking'); ?>
                                </div>
                                <# } #>

                                <# if(data.myPOS_enable=='1'){ #>
                                <div class="item" data-value="myPOS">
                                    <?php echo esc_html__('myPOS', 'fat-services-booking'); ?>
                                </div>
                                <# } #>

                                <# if(data.przelewy24_enable=='1'){ #>
                                <div class="item" data-value="przelewy24">
                                    <?php echo esc_html__('Przelewy24', 'fat-services-booking'); ?>
                                </div>
                                <# } #>

                                <# if(data.price_package_enable=='1'){ #>
                                <div class="item" data-value="price_package">
                                    <?php echo esc_html__('Price package', 'fat-services-booking'); ?>
                                </div>
                                <# } #>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <div class="ui toggle checkbox">
                            <# if(data.onsite_enable==1){ #>
                            <input type="checkbox" name="onsite_enable" id="onsite_enable" value="1"
                                   data-onChange="FatSbSetting.dependFieldOnChange"
                                   checked tabindex="4">
                            <# }else{ #>
                            <input type="checkbox" name="onsite_enable" id="onsite_enable" value="1"
                                   data-onChange="FatSbSetting.dependFieldOnChange"
                                   tabindex="4">
                            <# } #>
                            <label><?php echo esc_html('Onsite payment'); ?></label>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <div class="ui toggle checkbox">
                            <# if(data.price_package_enable==1){ #>
                            <input type="checkbox" name="price_package_enable" id="price_package_enable" value="1"
                                   checked tabindex="4">
                            <# }else{ #>
                            <input type="checkbox" name="price_package_enable" id="price_package_enable" value="1"
                                   tabindex="4">
                            <# } #>
                            <label><?php echo esc_html('Price package payment'); ?></label>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <div class="ui toggle checkbox">
                            <# if(data.paypal_enable==1){ #>
                            <input type="checkbox" name="paypal_enable" id="paypal_enable" value="1"
                                   data-onChange="FatSbSetting.dependFieldOnChange"
                                   checked tabindex="4">
                            <# }else{ #>
                            <input type="checkbox" name="paypal_enable" id="paypal_enable" value="1"
                                   data-onChange="FatSbSetting.dependFieldOnChange"
                                   tabindex="4">
                            <# } #>
                            <label><?php echo esc_html('Paypal'); ?></label>
                        </div>
                    </div>
                </div>
                <div class="fat-section-wrap" data-depend="paypal_enable" data-depend-value="1" style="display: none;">
                    <div class="one fields">
                        <div class="field">
                            <label for="paypal_sandbox"><?php echo esc_html__('Paypal Mode', 'fat-services-booking'); ?></label>
                            <div class="ui floating dropdown icon selection dropdown">
                                <i class="dropdown icon"></i>
                                <input type="hidden" name="paypal_sandbox" id="paypal_sandbox"
                                       value="{{data.paypal_sandbox}}" tabindex="5">
                                <span class="text"><?php echo esc_html__('Select mode', 'fat-services-booking'); ?></span>
                                <div class="menu">
                                    <div class="item" data-value="test">
                                        <?php echo esc_html__('Sandbox mode', 'fat-services-booking'); ?>
                                    </div>
                                    <div class="item" data-value="live">
                                        <?php echo esc_html__('Live mode', 'fat-services-booking'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="one fields">
                        <div class="field ">
                            <label for="paypal_client_id"><?php echo esc_html__('Client ID', 'fat-services-booking'); ?></label>
                            <div class="ui left input ">
                                <input type="text" name="paypal_client_id" id="paypal_client_id"
                                       value="{{data.paypal_client_id}}" tabindex="6"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="one fields">
                        <div class="field ">
                            <label for="paypal_secret"><?php echo esc_html__('Secret', 'fat-services-booking'); ?></label>
                            <div class="ui left input ">
                                <input type="text" name="paypal_secret" id="paypal_secret"
                                       value="{{data.paypal_secret}}" tabindex="7"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields" data-depend="paypal_enable" data-depend-value="1" style="display: none">
                    <div class="field">
                        <label for="success_page"><?php echo esc_html__('Success page', 'fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip" data-position="right center"
                                 data-content="<?php echo esc_attr__('The page will be opened when payment success', 'fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui bottom left pointing dropdown search icon selection dropdown">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="success_page" id="success_page" value="{{data.success_page}}"
                                   tabindex="17">
                            <span class="text"><?php echo esc_html__('Select success page', 'fat-services-booking'); ?></span>
                            <div class="menu">
                                <div class="ui icon search input">
                                    <i class="search icon"></i>
                                    <input type="text" tabindex="17"
                                           placeholder="<?php echo esc_attr__('Fill page title...', 'fat-services-booking'); ?>">
                                </div>
                                <div class="scrolling menu">
                                    <?php $pages = get_pages(array('post_status' => 'publish'));
                                    foreach ($pages as $page) { ?>
                                        <div class="item" data-value="<?php echo esc_attr($page->ID); ?>">
                                            <?php echo esc_html($page->post_title); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields" data-depend="paypal_enable" data-depend-value="1" style="display: none">
                    <div class="field">
                        <label for="error_page">
                            <?php echo esc_html__('Error page', 'fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip" data-position="right center"
                                 data-content="<?php echo esc_attr__('The page will be opened when payment fail', 'fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui floating dropdown icon selection dropdown">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="error_page" id="error_page" value="{{data.error_page}}"
                                   tabindex="18">
                            <span class="text"><?php echo esc_html__('Select error page', 'fat-services-booking'); ?></span>
                            <div class="menu">
                                <?php foreach ($pages as $page) { ?>
                                    <div class="item" data-value="<?php echo esc_attr($page->ID); ?>">
                                        <?php echo esc_html($page->post_title); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="one fields">
                    <div class="field">
                        <div class="ui toggle checkbox">
                            <# if(data.stripe_enable==1){ #>
                            <input type="checkbox" name="stripe_enable" id="stripe_enable" value="1"
                                   data-onChange="FatSbSetting.dependFieldOnChange"
                                   checked tabindex="8">
                            <# }else{ #>
                            <input type="checkbox" name="stripe_enable" id="stripe_enable" value="1"
                                   data-onChange="FatSbSetting.dependFieldOnChange"
                                   tabindex="8">
                            <# } #>
                            <label><?php echo esc_html('Stripe'); ?></label>
                        </div>
                    </div>
                </div>

                <div class="fat-section-wrap" data-depend="stripe_enable" data-depend-value="1" style="display: none;">
                    <div class="one fields">
                        <div class="field">
                            <label for="stripe_sandbox"><?php echo esc_html__('Stripe Mode', 'fat-services-booking'); ?></label>
                            <div class="ui floating dropdown icon selection dropdown">
                                <i class="dropdown icon"></i>
                                <input type="hidden" name="stripe_sandbox" id="stripe_sandbox"
                                       value="{{data.stripe_sandbox}}" tabindex="9">
                                <span class="text"><?php echo esc_html__('Select mode', 'fat-services-booking'); ?></span>
                                <div class="menu">
                                    <div class="item" data-value="test">
                                        <?php echo esc_html__('Test mode', 'fat-services-booking'); ?>
                                    </div>
                                    <div class="item" data-value="live">
                                        <?php echo esc_html__('Live mode', 'fat-services-booking'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="one fields">
                        <div class="field ">
                            <label for="stripe_publish_key"><?php echo esc_html__('Publish Key', 'fat-services-booking'); ?></label>
                            <div class="ui left input ">
                                <input type="text" name="stripe_publish_key" id="stripe_publish_key"
                                       value="{{data.stripe_publish_key}}" tabindex="10"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="one fields">
                        <div class="field ">
                            <label for="stripe_secret_key"><?php echo esc_html__('Secret Key', 'fat-services-booking'); ?></label>
                            <div class="ui left input ">
                                <input type="text" name="stripe_secret_key" id="stripe_secret_key"
                                       value="{{data.stripe_secret_key}}" tabindex="11"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- myPOS -->
                <div class="one fields">
                    <div class="field">
                        <div class="ui toggle checkbox">
                            <# if(data.myPOS_enable==1){ #>
                            <input type="checkbox" name="myPOS_enable" id="myPOS_enable" value="1"
                                   data-onChange="FatSbSetting.dependFieldOnChange"
                                   checked tabindex="8">
                            <# }else{ #>
                            <input type="checkbox" name="myPOS_enable" id="myPOS_enable" value="1"
                                   data-onChange="FatSbSetting.dependFieldOnChange"
                                   tabindex="8">
                            <# } #>
                            <label><?php echo esc_html('myPOS'); ?></label>
                        </div>
                    </div>
                </div>

                <div class="fat-section-wrap" data-depend="myPOS_enable" data-depend-value="1" style="display: none;">
                    <div class="one fields">
                        <div class="field">
                            <label for="myPOS_sandbox"><?php echo esc_html__('myPOS Mode', 'fat-services-booking'); ?></label>
                            <div class="ui floating dropdown icon selection dropdown">
                                <i class="dropdown icon"></i>
                                <input type="hidden" name="myPOS_sandbox" id="myPOS_sandbox"
                                       value="{{data.myPOS_sandbox}}" tabindex="9">
                                <span class="text"><?php echo esc_html__('Select mode', 'fat-services-booking'); ?></span>
                                <div class="menu">
                                    <div class="item" data-value="test">
                                        <?php echo esc_html__('Test mode', 'fat-services-booking'); ?>
                                    </div>
                                    <div class="item" data-value="live">
                                        <?php echo esc_html__('Live mode', 'fat-services-booking'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="one fields">
                        <div class="field ">
                            <label for="myPOS_storeID"><?php echo esc_html__('Store ID', 'fat-services-booking'); ?></label>
                            <div class="ui left input ">
                                <input type="text" name="myPOS_storeID" id="myPOS_storeID"
                                       value="{{data.myPOS_storeID}}" tabindex="10"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="one fields">
                        <div class="field ">
                            <label for="myPOS_client_number"><?php echo esc_html__('Client ID (or Wallet ID)', 'fat-services-booking'); ?></label>
                            <div class="ui left input ">
                                <input type="text" name="myPOS_client_number" id="myPOS_client_number"
                                       value="{{data.myPOS_client_number}}" tabindex="10"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="one fields">
                        <div class="field ">
                            <label for="myPOS_key_index"><?php echo esc_html__('Key index', 'fat-services-booking'); ?></label>
                            <div class="ui left input ">
                                <input type="text" name="myPOS_key_index" id="myPOS_key_index"
                                       value="{{data.myPOS_key_index}}" tabindex="10"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="one fields">
                        <div class="field ">
                            <label for="myPOS_private_key"><?php echo esc_html__('myPOS private key', 'fat-services-booking'); ?></label>
                            <div class="ui left input ">
                                <textarea name="myPOS_private_key" id="myPOS_private_key" tabindex="10"
                                          autocomplete="off">{{data.myPOS_private_key}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="one fields">
                        <div class="field ">
                            <label for="myPOS_public_certificate"><?php echo esc_html__('myPOS public certificate', 'fat-services-booking'); ?></label>
                            <div class="ui left input ">
                                <textarea name="myPOS_public_certificate" id="myPOS_public_certificate"
                                          tabindex="11"
                                          autocomplete="off">{{data.myPOS_public_certificate}}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields" data-depend="myPOS_enable" data-depend-value="1" style="display: none">
                    <div class="field">
                        <label for="myPOS_success_page"><?php echo esc_html__('Success page', 'fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip" data-position="right center"
                                 data-content="<?php echo esc_attr__('The page will be opened when payment success', 'fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui bottom left pointing dropdown search icon selection dropdown">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="myPOS_success_page" id="myPOS_success_page"
                                   value="{{data.myPOS_success_page}}"
                                   tabindex="17">
                            <span class="text"><?php echo esc_html__('Select success page', 'fat-services-booking'); ?></span>
                            <div class="menu">
                                <div class="ui icon search input">
                                    <i class="search icon"></i>
                                    <input type="text" tabindex="17"
                                           placeholder="<?php echo esc_attr__('Fill page title...', 'fat-services-booking'); ?>">
                                </div>
                                <div class="scrolling menu">
                                    <?php $pages = get_pages(array('post_status' => 'publish'));
                                    foreach ($pages as $page) { ?>
                                        <div class="item" data-value="<?php echo esc_attr($page->ID); ?>">
                                            <?php echo esc_html($page->post_title); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields" data-depend="myPOS_enable" data-depend-value="1" style="display: none">
                    <div class="field">
                        <label for="error_page">
                            <?php echo esc_html__('Error page', 'fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip" data-position="right center"
                                 data-content="<?php echo esc_attr__('The page will be opened when payment fail', 'fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui floating dropdown icon selection dropdown">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="myPOS_error_page" id="myPOS_error_page"
                                   value="{{data.myPOS_error_page}}"
                                   tabindex="18">
                            <span class="text"><?php echo esc_html__('Select error page', 'fat-services-booking'); ?></span>
                            <div class="menu">
                                <?php foreach ($pages as $page) { ?>
                                    <div class="item" data-value="<?php echo esc_attr($page->ID); ?>">
                                        <?php echo esc_html($page->post_title); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- for Przelewy24 -->

                <div class="one fields">
                    <div class="field">
                        <div class="ui toggle checkbox">
                            <# if(data.przelewy24_enable==1){ #>
                            <input type="checkbox" name="przelewy24_enable" id="przelewy24_enable" value="1"
                                   data-onChange="FatSbSetting.dependFieldOnChange"
                                   checked tabindex="8">
                            <# }else{ #>
                            <input type="checkbox" name="przelewy24_enable" id="przelewy24_enable" value="1"
                                   data-onChange="FatSbSetting.dependFieldOnChange"
                                   tabindex="8">
                            <# } #>
                            <label><?php echo esc_html('Przelewy24'); ?></label>
                        </div>
                    </div>
                </div>

                <div class="fat-section-wrap" data-depend="przelewy24_enable" data-depend-value="1" style="display: none;">
                    <div class="one fields">
                        <div class="field">
                            <label for="p24_mode"><?php echo esc_html__('Przelewy Mode', 'fat-services-booking'); ?></label>
                            <div class="ui floating dropdown icon selection dropdown">
                                <i class="dropdown icon"></i>
                                <input type="hidden" name="p24_mode" id="p24_mode"
                                       value="{{data.p24_mode}}" tabindex="5">
                                <span class="text"><?php echo esc_html__('Select mode', 'fat-services-booking'); ?></span>
                                <div class="menu">
                                    <div class="item" data-value="sandbox">
                                        <?php echo esc_html__('Sandbox mode', 'fat-services-booking'); ?>
                                    </div>
                                    <div class="item" data-value="live">
                                        <?php echo esc_html__('Live mode', 'fat-services-booking'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="one fields">
                        <div class="field ">
                            <label for="p24_merchant_id"><?php echo esc_html__('P24 merchant id', 'fat-services-booking'); ?></label>
                            <div class="ui left input ">
                                <input type="text" name="p24_merchant_id" id="p24_merchant_id"
                                       value="{{data.p24_merchant_id}}" tabindex="10"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="one fields">
                        <div class="field ">
                            <label for="p24_pos_id"><?php echo esc_html__('P24 pos id', 'fat-services-booking'); ?></label>
                            <div class="ui left input ">
                                <input type="text" name="p24_pos_id" id="p24_pos_id"
                                       value="{{data.p24_pos_id}}" tabindex="10"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="one fields">
                        <div class="field ">
                            <label for="p24_crc"><?php echo esc_html__('P24 CRC key', 'fat-services-booking'); ?></label>
                            <div class="ui left input ">
                                <input type="text" name="p24_crc" id="p24_crc"
                                       value="{{data.p24_crc}}" tabindex="10"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="one fields" >
                        <div class="field">
                            <label for="przelewy24_success_page"><?php echo esc_html__('Przelewy24 success page', 'fat-services-booking'); ?>
                                <div class="ui icon ui-tooltip" data-position="right center"
                                     data-content="<?php echo esc_attr__('The page will be opened when payment success', 'fat-services-booking'); ?>">
                                    <i class="question circle icon"></i>
                                </div>
                            </label>
                            <div class="ui bottom left pointing dropdown search icon selection dropdown">
                                <i class="dropdown icon"></i>
                                <input type="hidden" name="przelewy24_success_page" id="przelewy24_success_page"
                                       value="{{data.przelewy24_success_page}}"
                                       tabindex="17">
                                <span class="text"><?php echo esc_html__('Select success page', 'fat-services-booking'); ?></span>
                                <div class="menu">
                                    <div class="ui icon search input">
                                        <i class="search icon"></i>
                                        <input type="text" tabindex="17"
                                               placeholder="<?php echo esc_attr__('Fill page title...', 'fat-services-booking'); ?>">
                                    </div>
                                    <div class="scrolling menu">
                                        <?php $pages = get_pages(array('post_status' => 'publish'));
                                        foreach ($pages as $page) { ?>
                                            <div class="item" data-value="<?php echo esc_attr($page->ID); ?>">
                                                <?php echo esc_html($page->post_title); ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="one fields">
                        <div class="field">
                            <label for="przelewy24_error_page">
                                <?php echo esc_html__('Przelewy24 error page', 'fat-services-booking'); ?>
                                <div class="ui icon ui-tooltip" data-position="right center"
                                     data-content="<?php echo esc_attr__('The page will be opened when payment fail', 'fat-services-booking'); ?>">
                                    <i class="question circle icon"></i>
                                </div>
                            </label>
                            <div class="ui floating dropdown icon selection dropdown">
                                <i class="dropdown icon"></i>
                                <input type="hidden" name="przelewy24_error_page" id="przelewy24_error_page"
                                       value="{{data.przelewy24_error_page}}"
                                       tabindex="18">
                                <span class="text"><?php echo esc_html__('Select error page', 'fat-services-booking'); ?></span>
                                <div class="menu">
                                    <?php foreach ($pages as $page) { ?>
                                        <div class="item" data-value="<?php echo esc_attr($page->ID); ?>">
                                            <?php echo esc_html($page->post_title); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="actions">
            <button class="ui basic button fat-close-modal">
                <i class="times circle outline icon"></i>
                <?php echo esc_html__('Cancel', 'fat-services-booking'); ?>
            </button>

            <button class="ui blue button fat-submit-modal"
                    data-onClick="FatSbSetting.submitOnClick"
                    data-success-message="<?php echo esc_attr__('Payment setting has been saved', 'fat-services-booking'); ?>">
                <i class="save outline icon"></i>
                <?php echo esc_html__('Save', 'fat-services-booking'); ?>
            </button>

        </div>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-setting-google-api-template">
    <div class="ui modal tiny fat-semantic-container fat-setting-modal">
        <div class="header fat-sb-popup-title"><?php echo esc_html__('Google API setting', 'fat-services-booking'); ?></div>
        <div class="scrolling content">
            <div class="ui form">
                <div class="one fields">
                    <div class="field ">
                        <label for="name"><?php echo esc_html__('Map API', 'fat-services-booking'); ?></label>
                        <div class="ui left input ">
                            <input type="text" name="google_map_api" id="google_map_api" value="{{data.google_map_api}}"
                                   autocomplete="off"
                                   placeholder="<?php echo esc_attr__('Google map API', 'fat-services-booking'); ?>"
                                   required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="actions">
            <button class="ui basic button fat-close-modal">
                <i class="times circle outline icon"></i>
                <?php echo esc_html__('Cancel', 'fat-services-booking'); ?>
            </button>

            <button class="ui blue button fat-submit-modal"
                    data-onClick="FatSbSetting.submitOnClick"
                    data-success-message="<?php echo esc_attr__('Google API has been saved', 'fat-services-booking'); ?>">
                <i class="save outline icon"></i>
                <?php echo esc_html__('Save', 'fat-services-booking'); ?>
            </button>

        </div>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-setting-working-hour-template">
    <div class="ui modal tiny fat-semantic-container fat-setting-modal">
        <div class="header fat-sb-popup-title"><?php echo esc_html__('Working hours setting', 'fat-services-booking'); ?></div>
        <div class="scrolling content">
            <div class="ui pointing secondary menu tabular fat-tabs">
                <a class="item active"
                   data-tab="schedule"><?php echo esc_html__('Schedule', 'fat-services-booking'); ?></a>
                <a class="item" data-tab="day-off"><?php echo esc_html__('Day off', 'fat-services-booking'); ?></a>
            </div>

            <!-- Schedule -->
            <div class="ui active tab segment simple" data-tab="schedule">
                <div class="ui list">

                    <!-- Monday -->
                    <div class="item schedule-item">
                        <div class="ui toggle checkbox checked">
                            <input type="checkbox" name="schedule_monday" id="schedule_monday" checked="">
                            <label><?php echo esc_html__('Monday', 'fat-services-booking'); ?></label>
                        </div>

                        <!-- popup clone for monday -->
                        <button class="ui basic simple button fat-bt-clone-work-hour fat-fl-right ui-popup"
                                data-position="bottom right">
                            <i class="clone outline icon"></i>
                            <?php echo esc_html__('Clone', 'fat-services-booking'); ?>
                        </button>
                        <div class="ui flowing popup top left transition hidden fat-popup-work-hour-clone">
                            <div><?php echo esc_html__('Applies monday shedule to:', 'fat-services-booking'); ?></div>
                            <div class="ui list">
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_tuesday" value="schedule_tuesday"
                                               checked="">
                                        <label><?php echo esc_html__('Tuesday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_wednesday" value="schedule_wednesday"
                                               checked="">
                                        <label><?php echo esc_html__('Wednesday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_thursday" value="schedule_thursday"
                                               checked="">
                                        <label><?php echo esc_html__('Thursday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_friday" value="schedule_friday"
                                               checked="">
                                        <label><?php echo esc_html__('Friday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_saturday" value="schedule_saturday"
                                               checked="">
                                        <label><?php echo esc_html__('Saturday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_sunday" checked=""
                                               value="schedule_sunday">
                                        <label><?php echo esc_html__('Sunday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <button class="ui mini primary  button fat-bt-applies-clone-work-hour fat-mg-top-15"
                                        data-onClick="FatSbSetting.processCloneSchedule">
                                    <i class="clone outline icon"></i>
                                    <?php echo esc_html__('Applies', 'fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>

                        <div class="fat-sb-work-hour-wrap fat-sb-hidden fat-mg-top-15 schedule-monday"
                             data-depend="schedule_monday" data-depend-value="1">
                            <div class="fat-sb-work-hour-item-wrap">
                            </div>
                            <div class="fat-sb-break-time-item-wrap">
                            </div>
                            <div class="fat-sb-bottom-action-group fat-mg-top-15">
                                <button class="ui basic simple button fat-bt-add-work-hour"
                                        data-onClick="FatSbSetting.btAddWorkHourOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add work hour', 'fat-services-booking'); ?>
                                </button>
                                <button class="ui basic simple button fat-bt-add-break-time"
                                        data-onClick="FatSbSetting.btAddBreakTimeOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add break time', 'fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tuesday -->
                    <div class="item fat-mg-top-15 schedule-item">
                        <div class="ui toggle checkbox checked">
                            <input type="checkbox" name="schedule_tuesday" id="schedule_tuesday" checked="">
                            <label><?php echo esc_html__('Tuesday', 'fat-services-booking'); ?></label>
                        </div>

                        <!-- popup clone for tuesday -->
                        <button class="ui basic simple button fat-bt-clone-work-hour fat-fl-right ui-popup"
                                data-position="left center">
                            <i class="clone outline icon"></i>
                            <?php echo esc_html__('Clone', 'fat-services-booking'); ?>
                        </button>
                        <div class="ui flowing popup top left transition hidden fat-popup-work-hour-clone">
                            <div><?php echo esc_html__('Applies tuesday\'s schedule to:', 'fat-services-booking'); ?></div>
                            <div class="ui list">
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_monday" value="schedule_monday"
                                               checked="">
                                        <label><?php echo esc_html__('Monday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_wednesday" value="schedule_wednesday"
                                               checked="">
                                        <label><?php echo esc_html__('Wednesday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_thursday" value="schedule_thursday"
                                               checked="">
                                        <label><?php echo esc_html__('Thursday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_friday" value="schedule_friday"
                                               checked="">
                                        <label><?php echo esc_html__('Friday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_saturday" value="schedule_saturday"
                                               checked="">
                                        <label><?php echo esc_html__('Saturday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_sunday" value="schedule_sunday"
                                               checked="">
                                        <label><?php echo esc_html__('Sunday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <button class="ui mini primary  button fat-bt-applies-clone-work-hour fat-mg-top-15"
                                        data-onClick="FatSbSetting.processCloneSchedule">
                                    <i class="clone outline icon"></i>
                                    <?php echo esc_html__('Applies', 'fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>

                        <div class="fat-sb-work-hour-wrap fat-sb-hidden fat-mg-top-15 schedule-tuesday"
                             data-depend="schedule_tuesday" data-depend-value="1">
                            <div class="fat-sb-work-hour-item-wrap">
                            </div>
                            <div class="fat-sb-break-time-item-wrap">
                            </div>
                            <div class="fat-sb-bottom-action-group fat-mg-top-15">
                                <button class="ui basic simple button fat-bt-add-work-hour"
                                        data-onClick="FatSbSetting.btAddWorkHourOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add work hour', 'fat-services-booking'); ?>
                                </button>
                                <button class="ui basic simple button fat-bt-add-break-time"
                                        data-onClick="FatSbSetting.btAddBreakTimeOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add break time', 'fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Wednesday -->
                    <div class="item fat-mg-top-15 schedule-item">
                        <div class="ui toggle checkbox checked">
                            <input type="checkbox" name="schedule_wednesday" id="schedule_wednesday" checked="">
                            <label><?php echo esc_html__('Wednesday', 'fat-services-booking'); ?></label>
                        </div>

                        <!-- popup clone for wednesday -->
                        <button class="ui basic simple button fat-bt-clone-work-hour fat-fl-right ui-popup"
                                data-position="left center">
                            <i class="clone outline icon"></i>
                            <?php echo esc_html__('Clone', 'fat-services-booking'); ?>
                        </button>
                        <div class="ui flowing popup top left transition hidden fat-popup-work-hour-clone">
                            <div><?php echo esc_html__('Applies wednesday\'s schedule to:', 'fat-services-booking'); ?></div>
                            <div class="ui list">
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_monday" value="schedule_monday"
                                               checked="">
                                        <label><?php echo esc_html__('Monday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_tuesday" value="schedule_tuesday"
                                               checked="">
                                        <label><?php echo esc_html__('Tuesday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_thursday" value="schedule_thursday"
                                               checked="">
                                        <label><?php echo esc_html__('Thursday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_friday" value="schedule_friday"
                                               checked="">
                                        <label><?php echo esc_html__('Friday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_saturday" value="schedule_saturday"
                                               checked="">
                                        <label><?php echo esc_html__('Saturday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_sunday" value="schedule_sunday"
                                               checked="">
                                        <label><?php echo esc_html__('Sunday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <button class="ui mini primary  button fat-bt-applies-clone-work-hour fat-mg-top-15"
                                        data-onClick="FatSbSetting.processCloneSchedule">
                                    <i class="clone outline icon"></i>
                                    <?php echo esc_html__('Applies', 'fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>

                        <div class="fat-sb-work-hour-wrap fat-sb-hidden fat-mg-top-15 schedule-wednesday"
                             data-depend="schedule_wednesday" data-depend-value="1">
                            <div class="fat-sb-work-hour-item-wrap">
                            </div>
                            <div class="fat-sb-break-time-item-wrap">
                            </div>
                            <div class="fat-sb-bottom-action-group fat-mg-top-15">
                                <button class="ui basic simple button fat-bt-add-work-hour"
                                        data-onClick="FatSbSetting.btAddWorkHourOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add work hour', 'fat-services-booking'); ?>
                                </button>
                                <button class="ui basic simple button fat-bt-add-break-time"
                                        data-onClick="FatSbSetting.btAddBreakTimeOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add break time', 'fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Thursday -->
                    <div class="item fat-mg-top-15 schedule-item">
                        <div class="ui toggle checkbox checked">
                            <input type="checkbox" name="schedule_thursday" id="schedule_thursday" checked="">
                            <label><?php echo esc_html__('Thursday', 'fat-services-booking'); ?></label>
                        </div>

                        <!-- popup clone for thursday -->
                        <button class="ui basic simple button fat-bt-clone-work-hour fat-fl-right ui-popup"
                                data-position="left center">
                            <i class="clone outline icon"></i>
                            <?php echo esc_html__('Clone', 'fat-services-booking'); ?>
                        </button>
                        <div class="ui flowing popup top left transition hidden fat-popup-work-hour-clone">
                            <div><?php echo esc_html__('Applies thursday\'s schedule to:', 'fat-services-booking'); ?></div>
                            <div class="ui list">
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_monday" value="schedule_monday"
                                               checked="">
                                        <label><?php echo esc_html__('Monday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_tuesday" value="schedule_tuesday"
                                               checked="">
                                        <label><?php echo esc_html__('Tuesday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_wednesday" value="schedule_wednesday"
                                               checked="">
                                        <label><?php echo esc_html__('Wednesday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_friday" value="schedule_friday"
                                               checked="">
                                        <label><?php echo esc_html__('Friday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_saturday" value="schedule_saturday"
                                               checked="">
                                        <label><?php echo esc_html__('Saturday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_sunday" value="schedule_sunday"
                                               checked="">
                                        <label><?php echo esc_html__('Sunday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <button class="ui mini primary  button fat-bt-applies-clone-work-hour fat-mg-top-15"
                                        data-onClick="FatSbSetting.processCloneSchedule">
                                    <i class="clone outline icon"></i>
                                    <?php echo esc_html__('Applies', 'fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>

                        <div class="fat-sb-work-hour-wrap fat-sb-hidden fat-mg-top-15 schedule-thursday"
                             data-depend="schedule_thursday" data-depend-value="1">
                            <div class="fat-sb-work-hour-item-wrap">
                            </div>
                            <div class="fat-sb-break-time-item-wrap">
                            </div>
                            <div class="fat-sb-bottom-action-group fat-mg-top-15">
                                <button class="ui basic simple button fat-bt-add-work-hour"
                                        data-onClick="FatSbSetting.btAddWorkHourOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add work hour', 'fat-services-booking'); ?>
                                </button>
                                <button class="ui basic simple button fat-bt-add-break-time"
                                        data-onClick="FatSbSetting.btAddBreakTimeOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add break time', 'fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Friday -->
                    <div class="item fat-mg-top-15 schedule-item">
                        <div class="ui toggle checkbox checked">
                            <input type="checkbox" name="schedule_friday" id="schedule_friday" checked="">
                            <label><?php echo esc_html__('Friday', 'fat-services-booking'); ?></label>
                        </div>

                        <!-- popup clone for thursday -->
                        <button class="ui basic simple button fat-bt-clone-work-hour fat-fl-right ui-popup"
                                data-position="left center">
                            <i class="clone outline icon"></i>
                            <?php echo esc_html__('Clone', 'fat-services-booking'); ?>
                        </button>
                        <div class="ui flowing popup top left transition hidden fat-popup-work-hour-clone">
                            <div><?php echo esc_html__('Applies friday\'s schedule to:', 'fat-services-booking'); ?></div>
                            <div class="ui list">
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_monday" value="schedule_monday"
                                               checked="">
                                        <label><?php echo esc_html__('Monday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_tuesday" value="schedule_tuesday"
                                               checked="">
                                        <label><?php echo esc_html__('Tuesday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_wednesday" value="schedule_wednesday"
                                               checked="">
                                        <label><?php echo esc_html__('Wednesday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_thursday" value="schedule_thursday"
                                               checked="">
                                        <label><?php echo esc_html__('Thursday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_saturday" value="schedule_saturday"
                                               checked="">
                                        <label><?php echo esc_html__('Saturday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_sunday" checked=""
                                               value="schedule_sunday">
                                        <label><?php echo esc_html__('Sunday', 'fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <button class="ui mini primary button fat-bt-applies-clone-work-hour fat-mg-top-15"
                                        data-onClick="FatSbSetting.processCloneSchedule">
                                    <i class="clone outline icon"></i>
                                    <?php echo esc_html__('Applies', 'fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>

                        <div class="fat-sb-work-hour-wrap fat-sb-hidden fat-mg-top-15 schedule-friday"
                             data-depend="schedule_friday" data-depend-value="1">
                            <div class="fat-sb-work-hour-item-wrap">
                            </div>
                            <div class="fat-sb-break-time-item-wrap">
                            </div>
                            <div class="fat-sb-bottom-action-group fat-mg-top-15">
                                <button class="ui basic simple button fat-bt-add-work-hour"
                                        data-onClick="FatSbSetting.btAddWorkHourOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add work hour', 'fat-services-booking'); ?>
                                </button>
                                <button class="ui basic simple button fat-bt-add-break-time"
                                        data-onClick="FatSbSetting.btAddBreakTimeOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add break time', 'fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Saturday -->
                    <div class="item fat-mg-top-15 schedule-item">
                        <div class="ui toggle checkbox ">
                            <input type="checkbox" name="schedule_saturday" id="schedule_saturday">
                            <label><?php echo esc_html__('Saturday', 'fat-services-booking'); ?></label>
                        </div>
                        <div class="fat-sb-work-hour-wrap fat-sb-hidden fat-mg-top-15 schedule-saturday"
                             data-depend="schedule_saturday" data-depend-value="1">
                            <div class="fat-sb-work-hour-item-wrap">
                            </div>
                            <div class="fat-sb-break-time-item-wrap">
                            </div>
                            <div class="fat-sb-bottom-action-group fat-mg-top-15">
                                <button class="ui basic simple button fat-bt-add-work-hour"
                                        data-onClick="FatSbSetting.btAddWorkHourOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add work hour', 'fat-services-booking'); ?>
                                </button>
                                <button class="ui basic simple button fat-bt-add-break-time"
                                        data-onClick="FatSbSetting.btAddBreakTimeOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add break time', 'fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sunday -->
                    <div class="item fat-mg-top-15 schedule-item">
                        <div class="ui toggle checkbox">
                            <input type="checkbox" name="schedule_sunday" id="schedule_sunday">
                            <label><?php echo esc_html__('Sunday', 'fat-services-booking'); ?></label>
                        </div>
                        <div class="fat-sb-work-hour-wrap fat-sb-hidden fat-mg-top-15 schedule-sunday"
                             data-depend="schedule_sunday" data-depend-value="1">
                            <div class="fat-sb-work-hour-item-wrap">
                            </div>
                            <div class="fat-sb-break-time-item-wrap">
                            </div>
                            <div class="fat-sb-bottom-action-group fat-mg-top-15">
                                <button class="ui basic simple button fat-bt-add-work-hour"
                                        data-onClick="FatSbSetting.btAddWorkHourOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add work hour', 'fat-services-booking'); ?>
                                </button>
                                <button class="ui basic simple button fat-bt-add-break-time"
                                        data-onClick="FatSbSetting.btAddBreakTimeOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add break time', 'fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Day off tab -->
            <div class="ui tab segment simple fat-min-height-300" data-tab="day-off">
                <div class="fat-day-off-wrap">
                    <div class="fat-day-off-inner">

                    </div>
                    <div class="fat-sb-bottom-action-group fat-mg-top-15">
                        <button class="ui basic simple button fat-bt-add-day-off"
                                data-onClick="FatSbSetting.btAddDayOfOnClick">
                            <i class="plus square outline icon"></i>
                            <?php echo esc_html__('Add day off', 'fat-services-booking'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="actions">
            <button class="ui basic button fat-close-modal">
                <i class="times circle outline icon"></i>
                <?php echo esc_html__('Cancel', 'fat-services-booking'); ?>
            </button>

            <button class="ui blue button fat-submit-modal"
                    data-onClick="FatSbSetting.submitWorkingHourOnClick"
                    data-invalid-message="<?php echo esc_attr__('Please select working hour before save', 'fat-services-booking'); ?>"
                    data-success-message="<?php echo esc_attr__('Working hours setting has been saved', 'fat-services-booking'); ?>">
                <i class="save outline icon"></i>
                <?php echo esc_html__('Save', 'fat-services-booking'); ?>
            </button>

        </div>
    </div>
</script>

<?php
$users = get_users();
?>
<script type="text/html" id="tmpl-fat-sb-setting-user-role-template">
    <div class="ui modal tiny fat-semantic-container fat-setting-modal fat-user-role-modal">
        <div class="header fat-sb-popup-title"><?php echo esc_html__('User role setting', 'fat-services-booking'); ?></div>
        <div class="scrolling content">
            <div class="ui form">

                <div class="one fields">
                    <div class="field">
                        <label for="limit_user"><?php echo esc_html__('Limit user create appointment', 'fat-services-booking'); ?>
                        </label>
                        <div class="ui floating dropdown labeled icon selection dropdown">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="limit_user" id="limit_user" data-onChange="FatSbSetting.dependFieldOnChange"
                                   value="{{data.limit_user}}" tabindex="1">
                            <span class="text"><?php echo esc_html__('Select limit', 'fat-services-booking'); ?></span>
                            <div class="menu">
                                <div class="item" data-value="none">
                                    <?php echo esc_html__('No limit','fat-services-booking'); ?>
                                </div>
                                <div class="item" data-value="limit_by_user">
                                    <?php echo esc_html__('Limit by users','fat-services-booking'); ?>
                                </div>
                                <div class="item" data-value="limit_by_role">
                                    <?php echo esc_html__('Limit by role','fat-services-booking'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields" data-depend="limit_user" data-depend-value="limit_by_user" style="display: none;">
                    <div class="field">
                        <label for="allow_user_booking"><?php echo esc_html__('Allow user create appointment', 'fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip" data-position="right center"
                                 data-content="<?php echo esc_attr__('Set up user who can create appointment. You can select multiple user', 'fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui floating dropdown labeled icon selection multiple dropdown">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="allow_user_booking" id="allow_user_booking"
                                   value="{{data.allow_user_booking}}" tabindex="1">
                            <span class="text"><?php echo esc_html__('All users who logged', 'fat-services-booking'); ?></span>
                            <div class="menu">
                                <?php $users = get_users();
                                foreach ($users as $user) {
                                    ?>
                                    <div class="item" data-value="<?php echo esc_attr($user->ID);?>">
                                        <?php echo esc_html($user->user_login); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields" data-depend="limit_user" data-depend-value="limit_by_role" style="display: none;">
                    <div class="field">
                        <label for="allow_user_booking"><?php echo esc_html__('All users who have role', 'fat-services-booking'); ?>
                        </label>
                        <div class="ui floating dropdown labeled icon selection multiple dropdown">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="allow_user_role_booking" id="allow_user_role_booking"
                                   value="{{data.allow_user_role_booking}}" tabindex="1">
                            <span class="text"><?php echo esc_html__('Select role', 'fat-services-booking'); ?></span>
                            <div class="menu">
                                <?php 
                                global $wp_roles;
                                $roles = $wp_roles->get_names();
                                foreach ($roles as $key => $value){
                                    ?>
                                    <div class="item" data-value="<?php echo esc_attr($key);?>">
                                        <?php echo esc_html($value); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields" >
                    <div class="field">
                        <label for="allow_user_booking"><?php echo esc_html__('Validate user at', 'fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip" data-position="right center"
                                 data-content="<?php echo esc_attr__('You can set position of validate if you select limit user', 'fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui floating dropdown labeled icon selection dropdown">
                            <i class="dropdown icon"></i>
                            <input type="hidden" name="validate_user_at" id="validate_user_at"
                                   value="{{data.validate_user_at}}" tabindex="1">
                            <span class="text"><?php echo esc_html__('Select position', 'fat-services-booking'); ?></span>
                            <div class="menu">
                                <div class="item" data-value="before">
                                    <?php echo esc_html__('Before display booking form. User only see notice message instead of booking form','fat-services-booking'); ?>
                                </div>
                                <div class="item" data-value="after">
                                    <?php echo esc_html__('After user click submit. User can be see booking form','fat-services-booking'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields" >
                    <div class="field ">
                        <label for="name"><?php echo esc_html__('Warning login','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('Message content what display when user not login to create appointment.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui left input ">
                            <input type="text" name="warning_message" id="warning_message" value="{{data.warning_message}}"
                                   autocomplete="nope"
                                   placeholder="<?php echo esc_attr__('Warning message','fat-services-booking'); ?>">
                        </div>
                    </div>
                </div>

                <div class="one fields" >
                    <div class="field ">
                        <label for="name"><?php echo esc_html__('Warning role','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('Message content what display when user don\'t have role to create appointment.','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui left input ">
                            <input type="text" name="warning_message" id="warning_limit_user_message" value="{{data.warning_limit_user_message}}"
                                   autocomplete="nope"
                                   placeholder="<?php echo esc_attr__('Warning message','fat-services-booking'); ?>">
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="actions">
            <button class="ui basic button fat-close-modal">
                <i class="times circle outline icon"></i>
                <?php echo esc_html__('Cancel', 'fat-services-booking'); ?>
            </button>

            <button class="ui blue button fat-submit-modal"
                    data-onClick="FatSbSetting.submitUserRoleOnClick"
                    data-success-message="<?php echo esc_attr__('Setting has been saved', 'fat-services-booking'); ?>">
                <i class="save outline icon"></i>
                <?php echo esc_html__('Save', 'fat-services-booking'); ?>
            </button>

        </div>
    </div>
</script>

<?php $work_hours = FAT_SB_Utils::getWorkHours(); ?>

<script type="text/html" id="tmpl-fat-sb-work-hour-template">
    <div class="fat-sb-work-hour-item fat-mg-top-5">
        <label><?php echo esc_html__('Work hour', 'fat-services-booking'); ?></label>
        <div class="ui selection search dropdown top left pointing has-icon fat-time-dropdown fat-work-hour-start-dropdown">
            <i class="clock outline icon"></i>
            <input type="hidden" name="work_hour_start" id="work_hour_start" required>
            <div class="text"></div>
            <i class="dropdown icon"></i>

            <div class="menu">
                <?php foreach ($work_hours as $key => $value) { ?>
                    <div class="item" data-value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></div>
                <?php } ?>
            </div>
        </div>
        <div class="ui selection search dropdown top left pointing has-icon fat-time-dropdown fat-work-hour-end-dropdown">
            <i class="clock outline icon"></i>
            <input type="hidden" name="work_hour_end" id="work_hour_end" required>
            <div class="text"></div>
            <i class="dropdown icon"></i>
            <div class="menu">
                <?php foreach ($work_hours as $key => $value) { ?>
                    <div class="item" data-value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></div>
                <?php } ?>
            </div>
        </div>

        <button class="ui basic simple button fat-mg-left-15 fat-hover-red fat-bt-remove-work-hour">
            <i class="minus square outline icon"></i>
        </button>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-break-time-template">
    <div class="fat-sb-break-time-item fat-mg-top-5">
        <label><?php echo esc_html__('Break time', 'fat-services-booking'); ?></label>
        <div class="ui selection search dropdown top left pointing has-icon fat-time-dropdown fat-break-time-start-dropdown">
            <i class="clock outline icon"></i>
            <input type="hidden" name="break_time_start" id="break_time_start" required>
            <div class="text"></div>
            <i class="dropdown icon"></i>
            <div class="menu">
                <?php foreach ($work_hours as $key => $value) { ?>
                    <div class="item" data-value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></div>
                <?php } ?>
            </div>
        </div>
        <div class="ui selection search dropdown top left pointing has-icon fat-time-dropdown fat-break-time-end-dropdown">
            <i class="clock outline icon"></i>
            <input type="hidden" name="break_time_end" id="break_time_end" required>
            <div class="text"></div>
            <i class="dropdown icon"></i>
            <div class="menu">
                <?php foreach ($work_hours as $key => $value) { ?>
                    <div class="item" data-value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></div>
                <?php } ?>
            </div>
        </div>
        <button class="ui basic simple button fat-mg-left-15 fat-hover-red fat-bt-remove-break-time">
            <i class="minus square outline icon"></i>
        </button>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-day-off-template">
    <div class="fat-sb-day-off-item fat-mg-top-5">
        <div class="ui input">
            <input type="text" name="day_off_name" placeholder="Name of day off">
        </div>
        <div class="ui input">
            <input type="text" value="" class="date-range-picker" name="day_off_schedule">
        </div>

        <button class="ui basic simple button fat-mg-left-15 fat-hover-red fat-bt-remove-day-off">
            <i class="minus square outline icon"></i>
        </button>
    </div>
</script>
