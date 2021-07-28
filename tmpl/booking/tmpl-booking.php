<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 2/21/2019
 * Time: 2:33 PM
 */
?>
<script type="text/html" id="tmpl-fat-sb-booking-template">
    <div class="ui modal tiny fat-semantic-container fat-sb-booking-form">
        <div class="header fat-sb-popup-title"><?php echo esc_attr('Add new booking','fat-services-booking');?></div>
        <div class="scrolling content">
            <div class="ui form">
                <div class="two fields">
                    <div class="field">
                        <label><?php echo esc_html__('Customers','fat-services-booking'); ?> <span
                                class="required"> *</span>
                            <a class="fat-bt-add-inline fat-bt-add-customer" href="javascript:"
                               data-onClick="FatSbBooking.addCustomerOnClick" data-callback="FatSbBooking.addCustomerToDropdown"
                               data-tooltip="<?php echo esc_attr('Add customer','fat-services-booking'); ?>" data-position="right center">
                                <i class="plus circle icon"></i>
                            </a>
                        </label>
                        <div class="ui pointing search selection dropdown has-icon fat-sb-customer-dic">
                            <i class="user outline icon"></i>
                            <input type="hidden" name="b_customer_id" id="b_customer_id" value="{{data.booking.b_customer_id}}" required>
                            <i class="dropdown icon"></i>
                            <div class="text"><?php echo esc_html__('Select customer','fat-services-booking'); ?></div>
                            <div class="menu">
                                <div class="ui icon search input">
                                    <i class="search icon"></i>
                                    <input type="text" placeholder="<?php echo esc_attr__('Search customer...','fat-services-booking');?>">
                                </div>
                                <div class="scrolling menu">
                                    <# _.each(data.customers, function(item){ #>
                                          <div class="item" data-value="{{item.c_id}}">{{item.c_first_name}} {{item.c_last_name}}</div>
                                    <# }) #>
                                </div>
                            </div>
                        </div>

                        <div class="field-error-message">
                            <?php echo esc_html__('Please select customer','fat-services-booking'); ?>
                        </div>
                    </div>
                    <div class="field">
                        <label>&nbsp;</label>
                        <div class="ui selection dropdown  fat-sb-customer-number fat-customer-number-dic" data-onChange="FatSbBooking.initPaymentInfo"
                             data-onClick="FatSbBooking.dropdownClick"
                             data-warning-message="<?php esc_attr_e('Please select service and employee to know how many customers can be serve','fat-services-booking');?>">
                            <i class="users icon"></i>
                            <input type="hidden" name="b_customer_number" id="b_customer_number" value="{{data.booking.b_customer_number}}" required>
                            <i class="dropdown icon"></i>
                            <div class="text">1</div>
                            <div class="menu">
                            </div>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please select number of person','fat-services-booking');?>
                        </div>
                    </div>
                </div>

                <div class="two fields">
                    <div class="field ">
                        <label><?php echo esc_html__('Service category','fat-services-booking'); ?> <span
                                class="required"> *</span></label>
                        <div class="ui pointing search selection dropdown has-icon fat-sb-service-cat-dic" data-onChange="FatSbBooking.serviceCatOnChange" >
                            <i class="folder outline icon"></i>
                            <input type="hidden" name="b_service_cat_id" id="b_service_cat_id" value="{{data.booking.b_service_cat_id}}" required>
                            <i class="dropdown icon"></i>
                            <div class="text"><?php echo esc_html__('Select category','fat-services-booking'); ?></div>
                            <div class="menu">
                                <div class="ui icon search input">
                                    <i class="search icon"></i>
                                    <input type="text" placeholder="<?php echo esc_attr__('Search category...','fat-services-booking');?> ">
                                </div>
                                <div class="scrolling menu">
                                    <# _.each(data.services_cat, function(item){ #>
                                        <div class="item" data-value="{{item.sc_id}}">{{item.sc_name}}</div>
                                    <# }) #>
                                </div>
                            </div>
                        </div>

                        <div class="field-error-message">
                            <?php echo esc_html__('Please select category','fat-services-booking'); ?>
                        </div>

                    </div>
                    <div class="field">
                        <label><?php echo esc_html__('Service','fat-services-booking'); ?> <span
                                class="required"> *</span></label>
                        <div class="ui pointing search selection dropdown has-icon fat-sb-services-dic" data-onChange="FatSbBooking.serviceOnChange"
                             data-onClick="FatSbBooking.dropdownClick"
                             data-warning-message="<?php esc_attr_e('Please select service category before select service','fat-services-booking');?>">
                            <i class="cog icon"></i>
                            <input type="hidden" name="b_service_id" id="b_service_id" value="{{data.booking.b_service_id}}" required>
                            <i class="dropdown icon"></i>
                            <div class="text"><?php echo esc_html__('Select service','fat-services-booking'); ?></div>
                            <div class="menu">
                                <div class="ui icon search input">
                                    <i class="search icon"></i>
                                    <input type="text" placeholder="Search service...">
                                </div>
                                <div class="scrolling menu">
                                    <# _.each(data.services, function(item){
                                            if(data.booking.b_service_cat_id == item.s_category_id){ #>
                                        <div class="item" data-value="{{item.s_id}}">{{item.s_name}}</div>
                                    <# }
                                    }) #>
                                </div>
                            </div>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please select service','fat-services-booking'); ?>
                        </div>
                    </div>
                </div>
                <div class="one fields">
                    <div class="field">
                        <label><?php echo esc_html__('Service extra','fat-services-booking'); ?></label>
                        <div class="ui pointing search selection multiple dropdown fat-sb-services-extra-dic"
                             data-onClick="FatSbBooking.dropdownClick" data-onChange="FatSbBooking.serviceExtraOnChange"
                             data-warning-message="<?php esc_attr_e('Please select service before select service extra','fat-services-booking');?>">
                            <input type="hidden" name="b_services_extra" id="b_services_extra" value="{{data.booking.b_services_extra}}">
                            <i class="dropdown icon"></i>
                            <div class="text"><?php echo esc_html__('Select service extra','fat-services-booking'); ?></div>
                            <div class="menu">
                                <div class="ui icon search input">
                                    <i class="search icon"></i>
                                    <input type="text" placeholder="Search service extra...">
                                </div>
                                <div class="scrolling menu">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="two fields">
                    <div class="field ">
                        <label><?php echo esc_html__('Location','fat-services-booking'); ?> <span
                                    class="required"> *</span></label>
                        <div class="ui pointing search selection dropdown has-icon fat-sb-locations-dic" data-onChange="FatSbBooking.initEmployees">
                            <i class="map marker alternate icon"></i>
                            <input type="hidden" name="b_loc_id" id="b_loc_id" required value="{{data.booking.b_loc_id}}">
                            <i class="dropdown icon"></i>
                            <div class="text"><?php echo esc_html__('Select location','fat-services-booking'); ?></div>
                            <div class="menu">
                                <div class="ui icon search input">
                                    <i class="search icon"></i>
                                    <input type="text" placeholder="<?php echo esc_attr__('Search location...','fat-services-booking');?> ">
                                </div>
                                <div class="scrolling menu">
                                    <# _.each(data.locations, function(item){ #>
                                    <div class="item" data-value="{{item.loc_id}}">
                                        {{item.loc_name}}
                                    </div>
                                    <# }) #>
                                </div>
                            </div>
                        </div>

                        <div class="field-error-message">
                            <?php echo esc_html__('Please select location','fat-services-booking'); ?>
                        </div>

                    </div>
                    <div class="field">
                        <label><?php echo esc_html__('Employee','fat-services-booking'); ?> <span
                                    class="required"> *</span></label>
                        <div class="ui pointing search selection dropdown has-icon fat-sb-employees-dic" data-onChange="FatSbBooking.initSlot"
                             data-onClick="FatSbBooking.dropdownClick"
                             data-warning-message="<?php esc_attr_e('Please select service and location before select employee','fat-services-booking');?>">
                            <i class="user outline icon"></i>
                            <input type="hidden" name="b_employee_id" id="b_employee_id" value="{{data.booking.b_employee_id}}" required>
                            <i class="dropdown icon"></i>
                            <div class="text"><?php echo esc_html__('Select employee','fat-services-booking'); ?></div>
                            <div class="menu">
                                <div class="ui icon search input">
                                    <i class="search icon"></i>
                                    <input type="text"  placeholder="<?php echo esc_attr__('Search employee...','fat-services-booking');?>">
                                </div>
                                <div class="scrolling menu">
                                </div>
                            </div>
                        </div>

                        <div class="field-error-message">
                            <?php echo esc_html__('Please select employee','fat-services-booking'); ?>
                        </div>
                    </div>
                </div>

                <?php
                $locale = get_locale();
                $locale = explode('_',$locale)[0];
                $locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.' . $locale . '.js';
                if($locale=='pl'){
                    $locale_path = FAT_SERVICES_DIR_PATH . 'assets/plugins/air-datepicker/js/i18n/datepicker.pl-PL.js';
                }
                if(!file_exists($locale_path)){
                    $locale='en';
                }
                ?>
                <div class="two fields">
                    <div class="field">
                        <label for="b_date"><?php echo esc_html__('Date','fat-services-booking');?> <span
                                    class="required"> *</span></label>
                        <div class="fat-sb-booking-date-wrap">
                            <input type="text" class="air-date-picker datepicker-here" data-locale="<?php echo esc_attr($locale); ?>" data-date="{{data.booking.b_date}}" required autocomplete="off" name="b_date" id="b_date">
                        </div>
                    </div>

                    <div class="field">
                        <label><?php echo esc_html__('Time','fat-services-booking'); ?> <span
                                    class="required"> *</span></label>
                        <div class="ui pointing selection dropdown has-icon fat-sb-booking-time-wrap" data-onChange="FatSbBooking.timeOnChange">
                            <i class="clock outline icon"></i>
                            <input type="hidden" name="b_time" id="b_time" required value="{{data.booking.b_time}}">
                            <i class="dropdown icon"></i>
                            <div class="text" data-no-time-slot="<?php echo esc_attr__('Don\'t have free time slot in this day','fat-services-booking');?>" data-text="<?php echo esc_attr__('Select time','fat-services-booking'); ?>">
                                <?php echo esc_html__('Select time','fat-services-booking'); ?>
                            </div>
                            <div class="menu">
                                <div class="scrolling menu">
                                    <?php
                                    $db_setting = FAT_DB_Setting::instance();
                                    $setting = $db_setting->get_setting();
                                    $time_step = isset($setting['time_step']) && $setting['time_step'] ? $setting['time_step'] : 15;
                                    $work_hours = FAT_SB_Utils::getWorkHours($time_step); ?>
                                    <?php foreach ($work_hours as $key => $value) { ?>
                                        <div class="item disabled" data-value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></div>
                                    <?php }; ?>
                                </div>
                            </div>
                        </div>

                        <div class="field-error-message">
                            <?php echo esc_html__('Please select time','fat-services-booking'); ?>
                        </div>
                    </div>
                </div>


                <div class="one fields">
                    <div class="field">
                        <label><?php echo esc_html__('Notes','fat-services-booking');?></label>
                        <textarea rows="2" id="b_description" name="b_description">{{data.booking.b_description}}</textarea>
                    </div>
                </div>

                <# if(data.booking.b_id > 0 && data.booking.b_form_builder !='' && data.booking.b_form_builder !=null){ #>
                <div class="one fields">
                    <div class="field">
                        <label><?php echo esc_html__('Custom Fields','fat-services-booking');?></label>
                        {{data.booking.b_form_builder}}
                    </div>
                </div>
                <# } #>

                <div class="one fields">
                    <div class="ui checkbox">
                        <# if(data.booking.b_send_notify==1){ #>
                            <input type="checkbox" name="send_notify" id="send_notify" checked>
                        <# }else{ #>
                            <input type="checkbox" name="send_notify" id="send_notify">
                        <# } #>
                        <label for="send_notify"><?php echo esc_html__('Send notifications','fat-services-booking');?></label>
                    </div>
                </div>

                <div class="one fields">
                    <div class="ui checkbox fat-sb-pay-now fat-fullwidth">
                        <# if(data.booking.b_pay_now==1){ #>
                            <input type="checkbox" name="pay_now" id="pay_now" checked data-onChange="FatSbBooking.payNowOnChange">
                        <# }else{ #>
                            <input type="checkbox" name="pay_now" id="pay_now" data-onChange="FatSbBooking.payNowOnChange">
                        <# } #>
                        <label for="pay_now">
                            <?php echo esc_html__('Paynow','fat-services-booking');?>
                            <div class="ui icon ui-tooltip" data-position="right center"
                                 data-content="<?php echo esc_attr__('Click here if you want update payment to Paid, uncheck to update payment to Pending','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="two fields fat-hidden" data-depend="pay_now">
                    <div class="field fat-sb-coupon-wrap">
                        <label for="name"><?php echo esc_html__('Coupon','fat-services-booking');?></label>

                        <# if(data.booking.b_pay_now==1){ #>
                            <div class="ui left icon input disabled ">
                                <input type="text" name="b_coupon_code" id="b_coupon_code" value="{{data.booking.b_coupon_code}}" placeholder="<?php echo esc_attr__('Coupon code','fat-services-booking');?>" >
                                <i class="qrcode icon"></i>
                                <button class="ui button fat-bt-apply-coupon disabled" data-onClick="FatSbBooking.initCoupon"><?php echo esc_html__('Apply','fat-services-booking');?></button>
                            </div>
                        <# }else{ #>
                            <div class="ui left icon input ">
                                <input type="text" name="b_coupon_code" id="b_coupon_code" value="{{data.booking.b_coupon_code}}" placeholder="<?php echo esc_attr__('Coupon code','fat-services-booking');?>" >
                                <i class="qrcode icon"></i>
                                <button class="ui button fat-bt-apply-coupon" data-onClick="FatSbBooking.initCoupon"><?php echo esc_html__('Apply','fat-services-booking');?></button>
                            </div>
                        <# } #>

                    </div>
                    <?php
                    $setting_db = FAT_DB_Setting::instance();
                    $setting = $setting_db->get_setting();
                    ?>
                    <div class="field">
                        <label><?php echo esc_html__('Payment method','fat-services-booking'); ?>
                        </label>
                        <div class="ui pointing selection dropdown has-icon fat-sb-payment-method-dic">
                            <i class="dollar sign icon"></i>
                            <input type="hidden" name="b_gateway_type" id="b_gateway_type" value="{{data.booking.b_gateway_type}}">
                            <i class="dropdown icon"></i>
                            <div class="text"><?php echo esc_html__('Select payment method','fat-services-booking'); ?></div>
                            <div class="menu">
                                <# if(data.booking.b_gateway_type == 'onsite'){ #>
                                    <div class="item" data-value="onsite"><?php esc_html_e('Onsite payment','fat-services-booking'); ?></div>
                                <# } #>

                                <# if(data.booking.b_gateway_type == 'paypal'){ #>
                                <div class="item" data-value="paypal"><?php esc_html_e('Paypal','fat-services-booking'); ?></div>
                                <# } #>

                                <# if(data.booking.b_gateway_type == 'stripe'){ #>
                                <div class="item" data-value="stripe"><?php esc_html_e('Stripe','fat-services-booking'); ?></div>
                                <# } #>

                                <# if(data.booking.b_gateway_type == 'myPOS'){ #>
                                <div class="item" data-value="myPOS"><?php esc_html_e('myPOS','fat-services-booking'); ?></div>
                                <# } #>

                                <# if(data.booking.b_gateway_type != 'stripe' && data.booking.b_gateway_type != 'myPOS' && data.booking.b_gateway_type != 'paypal' && data.booking.b_gateway_type != 'onsite'){ #>
                                    <div class="item" data-value="onsite"><?php esc_html_e('Onsite payment','fat-services-booking');?></div>
                                <# } #>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="two fields fat-sb-payment-booking-info fat-hidden"  data-depend="pay_now">
                    <div class="field">
                        <ul>
                            <li>
                                <label><?php echo esc_html__('Price:','fat-services-booking'); ?></label>
                                <span class="price" data-value="{{data.booking.b_price}}">
                                    {{data.booking.symbol_prefix}}{{data.booking.price_lable}}{{data.booking.symbol_sufix}}
                                </span>
                            </li>

                            <li>
                                <label><?php echo esc_html__('Extra Services:','fat-services-booking'); ?></label>
                                <span class="price-extra" data-value="{{data.booking.b_total_extra_amount}}">{{data.booking.symbol_prefix}}{{data.booking.b_total_extra_amount}} {{data.booking.symbol_sufix}}</span>
                            </li>
                        </ul>

                    </div>
                    <div class="field">
                        <ul>
                            <li>
                                <label><strong><?php echo esc_html__('Service Tax:','fat-services-booking'); ?></strong></label>
                                <span class="tax" data-value="{{data.booking.b_service_tax_amount}}">{{data.booking.symbol_prefix}}{{data.booking.b_service_tax_amount}}{{data.booking.symbol_sufix}}</span>
                            </li>
                            <li>
                                <label><strong><?php echo esc_html__('Extra Service Tax:','fat-services-booking'); ?></strong></label>
                                <span class="extra-tax" data-value="{{data.booking.b_total_tax_extra}}">{{data.booking.symbol_prefix}}{{data.booking.b_total_tax_extra}}{{data.booking.symbol_sufix}}</span>
                            </li>
                            <li>
                                <label><strong><?php echo esc_html__('Subtotal:','fat-services-booking'); ?></strong></label>
                                <span class="sub-total" data-value="{{data.booking.b_total_amount}}">{{data.booking.symbol_prefix}}{{data.booking.b_total_amount}}{{data.booking.symbol_sufix}}</span>
                            </li>
                            <li>
                                <label><strong><?php echo esc_html__('Discount:','fat-services-booking'); ?></strong></label>
                                <span class="discount" data-value="{{data.booking.b_discount}}">{{data.booking.symbol_prefix}}{{data.booking.b_discount}}{{data.booking.symbol_sufix}}</span>
                            </li>
                            <li>
                                <label><strong><?php echo esc_html__('Total:','fat-services-booking'); ?></strong></label>
                                <span class="total" data-value="{{data.booking.b_total_pay}}">{{data.booking.symbol_prefix}}{{data.booking.b_total_pay}}{{data.booking.symbol_sufix}}</span>
                            </li>
                        </ul>
                        <button class="ui basic button fat-float-right" data-onClick="FatSbBooking.initPaymentInfo">
                            <i class="cart icon"></i>
                            <?php echo esc_html__('Update payment info','fat-services-booking');?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="actions">
            <# if(data.booking.b_canceled_by_client == 1 && data.booking.b_process_status == 2){ #>
                <span class="fat-fl-left fat-text-red fat-fw-bold">
                    <?php esc_html_e('Canceled by client','fat-services-booking');?>
                </span>
            <# } #>
            <button class="ui basic button fat-close-modal">
                <i class="times circle outline icon"></i>
                <?php echo esc_html__('Cancel','fat-services-booking');?>
            </button>
            <div class="blue ui buttons">
                <div class="ui button fat-submit-modal" data-id="{{data.booking.b_id}}" data-onClick="FatSbBooking.processSubmitBooking" data-success-message="<?php echo esc_attr__('Booking has been saved','fat-services-booking'); ?>">
                    <i class="save outline icon"></i>
                    <?php echo esc_html__('Save','fat-services-booking');?>
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-booking-item-template">
    <# _.each(data, function(item){ #>
    <tr data-id="{{item.b_id}}">
        <td>
            <div class="ui checkbox">
                <input type="checkbox" name="check-item" class="check-item"  data-id="{{item.b_id}}">
                <label></label>
            </div>
        </td>
        <td data-label="<?php echo esc_attr__('Appointment Date','fat-services-booking');?>">{{item.b_date_display}}</td>
        <td data-label="<?php echo esc_attr__('Create Date','fat-services-booking');?>">{{item.b_create_date}}</td>
        <td data-label="<?php echo esc_attr__('Customer','fat-services-booking');?>">
            {{item.c_first_name}} {{item.c_last_name}}
            <span class="extra-info">{{item.c_email}}</span>
        </td>
        <td data-label="<?php echo esc_attr__('Employee','fat-services-booking');?>">
            {{item.e_first_name}} {{item.e_last_name}}
            <span class="extra-info">{{item.e_email}}</span>
        </td>
        <td data-label="<?php echo esc_attr__('Services','fat-services-booking');?>">{{item.s_name}}</td>
        <td data-label="<?php echo esc_attr__('Duration','fat-services-booking');?>">
            {{ item.b_service_duration_display }}
        </td>
        <td class="fat-sb-payment" data-label="<?php echo esc_attr__('Payment','fat-services-booking');?>">{{item.b_total_pay}}</td>
        <td class="fat-sb-status" data-label="<?php echo esc_attr__('Status','fat-services-booking');?>">

            <# if (item.editable== 1) { #>
            <div class="ui floating dropdown labeled icon selection" >
                <# }else{ #>
            <div class="ui floating dropdown labeled icon selection disabled" >
                <# } #>
                <input type="hidden" name="b_process_status" value="{{item.b_process_status}}" data-value="{{item.b_process_status}}"
                       data-onChange="FatSbBooking.processUpdateProcessStatus" data-id="{{item.b_id}}">
                <i class="dropdown icon"></i>
                <span class="text"><div class="ui yellow empty circular label"></div> <?php echo esc_html__('Pending','fat-services-booking'); ?></span>
                <div class="menu">
                    <div class="item" data-value="2">
                        <div class="ui red empty circular label"></div>
                        <?php  echo esc_html__('Canceled','fat-services-booking'); ?>
                    </div>
                    <div class="item" data-value="1">
                        <div class="ui green empty circular label"></div>
                        <?php  echo esc_html__('Approved','fat-services-booking'); ?>
                    </div>
                    <div class="item" data-value="0">
                        <div class="ui yellow empty circular label"></div>
                        <?php echo esc_html__('Pending','fat-services-booking'); ?>
                    </div>
                    <div class="item" data-value="3">
                        <div class="ui empty empty circular label"></div>
                        <?php  echo esc_html__('Rejected','fat-services-booking'); ?>
                    </div>
                </div>
            </div>
        </td>
        <td>
            <# if (item.editable== 1) { #>
                <button class=" ui icon button fat-item-bt-inline fat-sb-edit-booking" data-onClick="FatSbBooking.showPopupBooking"
                        data-id="{{item.b_id}}" data-title="<?php echo esc_attr__('Edit','fat-services-booking');?>">
                    <i class="edit outline icon"></i>
                </button>

                <button class=" ui icon button fat-item-bt-inline fat-sb-delete" data-onClick="FatSbBooking.processDeleteBooking"
                        data-id="{{item.b_id}}" data-title="<?php echo esc_attr__('Delete','fat-services-booking');?>">
                    <i class="trash alternate outline icon"></i>
                </button>
            <# }else{ #>
                <button class=" ui icon button fat-item-bt-inline fat-sb-edit-booking fat-disabled" data-position="top right"
                        data-id="{{item.b_id}}" data-title="<?php echo esc_attr__('You cannot edit booking','fat-services-booking');?>">
                    <i class="edit outline icon"></i>
                </button>

                <button class=" ui icon button fat-item-bt-inline fat-sb-delete" data-onClick="FatSbBooking.processDeleteBooking"
                        data-id="{{item.b_id}}" data-title="<?php echo esc_attr__('Delete','fat-services-booking');?>">
                    <i class="trash alternate outline icon"></i>
                </button>
            <# } #>
        </td>
    </tr>
    <# }) #>
</script>



