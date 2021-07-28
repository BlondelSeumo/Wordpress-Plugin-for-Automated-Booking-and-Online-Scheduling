<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 3/4/2019
 * Time: 3:54 PM
 */
?>
<div class="fat-sb-header">
    <img src="<?php echo esc_url(FAT_SERVICES_ASSET_URL . '/images/plugin_logo.png'); ?>">
    <div class="fat-sb-header-title"><?php echo esc_html__('Email Template','fat-services-booking'); ?></div>
    <?php
    $part = 'features/email-template.html';
    include plugin_dir_path(__FILE__) . 'tool-tip.php'; ?>
</div>
<div class="fat-sb-email-template-container fat-semantic-container fat-min-height-300 fat-pd-right-15">
    <div class="ui card full-width">
        <div class="content">
            <div class="ui grid">
                <div class="four wide column">
                    <div class="ui vertical pointing menu fat-sb-template-tab">


                        <a class="item active" data-onClick="FatSbEmailTemplate.menuOnClick"
                           data-template="pending"
                           data-customer-title="<?php esc_attr_e('Booking Pending Template for Customer','fat-services-booking'); ?>"
                           data-employee-title="<?php esc_attr_e('Booking Pending Template for Employee','fat-services-booking'); ?>">
                            <?php esc_html_e('Booking Pending Template','fat-services-booking'); ?>

                            <div class="ui icon ui-tooltip" data-position="top center"
                                 data-content="<?php echo esc_attr__('Template email notification when customers book at the homepage for pending status', 'fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </a>
                        <a class="item" data-onClick="FatSbEmailTemplate.menuOnClick"
                           data-template="approved"
                           data-customer-title="<?php esc_attr_e('Booking Approved Template for Customer','fat-services-booking'); ?>"
                           data-employee-title="<?php esc_attr_e('Booking Approved Template for Employee','fat-services-booking'); ?>">
                            <?php esc_html_e('Booking Approved Template','fat-services-booking'); ?>

                            <div class="ui icon ui-tooltip" data-position="top center"
                                 data-content="<?php echo esc_attr__('Template email notification when customers book at the homepage for approved status', 'fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </a>
                        <a class="item" data-onClick="FatSbEmailTemplate.menuOnClick"
                           data-template="rejected"
                           data-customer-title="<?php esc_attr_e('Booking Rejected Template for Customer','fat-services-booking'); ?>"
                           data-employee-title="<?php esc_attr_e('Booking Rejected Template for Employee','fat-services-booking'); ?>">
                            <?php esc_html_e('Booking Rejected Template','fat-services-booking'); ?>

                            <div class="ui icon ui-tooltip" data-position="top center"
                                 data-content="<?php echo esc_attr__('Template email notification when admin reject booking', 'fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </a>
                        <a class="item" data-onClick="FatSbEmailTemplate.menuOnClick"
                           data-template="canceled"
                           data-customer-title="<?php esc_attr_e('Booking Canceled Template for Customer','fat-services-booking'); ?>"
                           data-employee-title="<?php esc_attr_e('Booking Canceled Template for Employee','fat-services-booking'); ?>">
                            <?php esc_html_e('Booking Canceled Template','fat-services-booking'); ?>

                            <div class="ui icon ui-tooltip" data-position="top center"
                                 data-content="<?php echo esc_attr__('Template email notification when admin cancel booking', 'fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </a>

                        <a class="item fat-sb-customer-code" data-template="get_customer_code" data-onClick="FatSbEmailTemplate.menuOnClick">
                            <?php esc_html_e('Get Customer Code Template','fat-services-booking'); ?>

                            <div class="ui icon ui-tooltip" data-position="top center"
                                 data-content="<?php echo esc_attr__('Template email notification when client request get customer code', 'fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </a>
                        <a class="item " data-onClick="FatSbEmailTemplate.menuOnClick"
                           data-template="backend"
                           data-customer-title="<?php esc_attr_e('Booking Template for Customer','fat-services-booking'); ?>"
                           data-employee-title="<?php esc_attr_e('Booking Template for Employee','fat-services-booking'); ?>">
                            <?php esc_html_e('Booking Template for Backend','fat-services-booking'); ?>

                            <div class="ui icon ui-tooltip" data-position="top center"
                                 data-content="<?php echo esc_attr__('Template email notification when admin create booking at backend', 'fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </a>
                    </div>

                    <div class="fat-email-keys">
                        <h4><?php esc_html_e('Please use keys bellow for email template','fat-services-booking');?></h4>
                        <ul class="list-email-key">
                            <li><span>{booking_time}</span> : <?php esc_html_e('time of booking','fat-services-booking');?> </li>
                            <li><span>{booking_end_time}</span> : <?php esc_html_e('end time of booking','fat-services-booking');?> </li>
                            <li><span>{booking_date}</span> : <?php esc_html_e('date of booking','fat-services-booking');?> </li>
                            <li><span>{location_name}</span> : <?php esc_html_e('name of location','fat-services-booking');?> </li>
                            <li><span>{location_address}</span> : <?php esc_html_e('address of location','fat-services-booking');?> </li>
                            <li><span>{location_link}</span> : <?php esc_html_e('link of location','fat-services-booking');?> </li>
                            <li><span>{service_link}</span> : <?php esc_html_e('link of service','fat-services-booking');?> </li>
                            <li><span>{service_name}</span> : <?php esc_html_e('name of service','fat-services-booking');?> </li>
                            <li><span>{service_extra}</span> : <?php esc_html_e('name of service extra','fat-services-booking');?> </li>
                            <li><span>{service_duration}</span> : <?php esc_html_e('duration of service','fat-services-booking');?> </li>
                            <li><span>{service_description}</span> : <?php esc_html_e('Service description','fat-services-booking');?> </li>
                            <li><span>{booking_price}</span> : <?php esc_html_e('total price of booking','fat-services-booking');?> </li>
                            <li><span>{customer_first_name}</span> : <?php esc_html_e('customer first name','fat-services-booking');?> </li>
                            <li><span>{customer_last_name}</span> : <?php esc_html_e('customer last name','fat-services-booking');?> </li>
                            <li><span>{customer_phone}</span> : <?php esc_html_e('customer phone','fat-services-booking');?> </li>
                            <li><span>{customer_email}</span> : <?php esc_html_e('customer email','fat-services-booking');?> </li>
                            <li><span>{customer_code}</span> : <?php esc_html_e('customer code. client can be use this code to view they booking history','fat-services-booking');?> </li>
                            <li><span>{employee_first_name}</span> : <?php esc_html_e('employee first name','fat-services-booking');?> </li>
                            <li><span>{employee_last_name}</span> : <?php esc_html_e('employee last name','fat-services-booking');?> </li>
                            <li><span>{employee_phone}</span> : <?php esc_html_e('employee phone','fat-services-booking');?> </li>
                            <li><span>{company_phone}</span> : <?php esc_html_e('company phone','fat-services-booking');?> </li>
                            <li><span>{company_name}</span> : <?php esc_html_e('company name','fat-services-booking');?> </li>
                            <li><span>{company_address}</span> : <?php esc_html_e('company address','fat-services-booking');?> </li>
                            <li><span>{company_email}</span> : <?php esc_html_e('company email','fat-services-booking');?> </li>
                            <li><span>{note}</span> : <?php esc_html_e('Notes','fat-services-booking');?> </li>
                            <li><span>{coupon_code}</span> : <?php esc_html_e('Coupon code','fat-services-booking');?> </li>
                        </ul>
                        <p><?php esc_html_e('If you want to use the field in the booking formbuilder, use the field\'s name as the keyword. For example if the name of field is txtExtra then the keyword is {txtExtra}','fat-services-booking');?></p>
                    </div>

                </div>
                <div class="twelve wide column">
                    <div class="fat-sb-pending-template">
                        <div class="fat-sb-checkbox-wrap right">
                            <h4 class="fat-sb-customer-label"><?php esc_html_e('Booking Pending Template for Customer','fat-services-booking'); ?></h4>
                            <div class="ui toggle checkbox" data-tooltip="<?php esc_attr_e('On/Off send email for customer','fat-services-booking');?>" data-position="top right">
                                <input type="checkbox" name="customer_template_enable" id="customer_template_enable" data-onChange="FatSbEmailTemplate.dependFieldOnChange"
                                       value="1" checked>
                                <label>&nbsp;</label>
                            </div>
                        </div>

                        <div class="fields customer-template" data-depend="customer_template_enable">
                            <div class="field">
                                <label><?php esc_html_e('Subject','fat-services-booking'); ?></label>
                                <div class="ui input">
                                    <input type="text" id="customer_subject" name="customer_subject" autocomplete="off">
                                </div>
                            </div>
                            <div class="field fat-editor">
                                <label><?php esc_html_e('Message','fat-services-booking'); ?></label>
                                <?php wp_editor('', 'customer_template', array('textarea_rows' => 10, 'media_buttons' => false)); ?>
                            </div>
                        </div>

                        <div class="fat-sb-checkbox-wrap right">
                            <h4 class="fat-sb-employee-label"><?php esc_html_e('Booking Pending Template for Employee','fat-services-booking'); ?></h4>
                            <div class="ui toggle checkbox" data-tooltip="<?php esc_attr_e('On/Off send email for employee','fat-services-booking');?>" data-position="top right">
                                <input type="checkbox" name="employee_template_enable" id="employee_template_enable" data-onChange="FatSbEmailTemplate.dependFieldOnChange"
                                       value="1" checked>
                                <label>&nbsp;</label>
                            </div>
                        </div>

                        <div class="fields employee-template" data-depend="employee_template_enable">
                            <div class="field">
                                <label><?php esc_html_e('Subject','fat-services-booking'); ?></label>
                                <div class="ui input">
                                    <input type="text" id="employee_subject" name="employee_subject" autocomplete="off">
                                </div>
                            </div>

                            <div class="field fat-editor">
                                <label><?php esc_html_e('Message','fat-services-booking'); ?></label>
                                <?php wp_editor('', 'employee_template', array('textarea_rows' => 10, 'media_buttons' => false)); ?>
                            </div>
                        </div>

                        <div class="fields">
                            <div class="field fat-text-right">
                                <div class="ui basic button" data-onClick="FatSbEmailTemplate.sendTestMailTemplateOnClick">
                                    <?php echo esc_html__('Send test mail','fat-services-booking');?>
                                </div>

                                <div class="ui primary button" data-onClick="FatSbEmailTemplate.submitTemplate"
                                     data-invalid-message="<?php echo esc_attr__('Please input data ','fat-services-booking');?>"
                                     data-success-message="<?php esc_attr_e('Template have been saved','fat-services-booking');?>" >
                                    <?php echo esc_html__('Save','fat-services-booking');?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fat-sb-get-customer-code-template fat-hidden">
                        <h4 ><?php esc_html_e('Get Customer Code Template','fat-services-booking'); ?></h4>

                        <div class="fields customer-template">
                            <div class="field">
                                <label><?php esc_html_e('Subject','fat-services-booking'); ?></label>
                                <div class="ui input">
                                    <input type="text" id="customer_code_subject" name="customer_subject" autocomplete="off">
                                </div>
                            </div>
                            <div class="field fat-editor">
                                <label><?php esc_html_e('Message','fat-services-booking'); ?></label>
                                <?php wp_editor('', 'customer_code_template', array('textarea_rows' => 10, 'media_buttons' => false)); ?>
                                <p class="fat-field-description"><?php esc_html_e('Please use keyword {customer_code}, {customer_first_name}, {customer_last_name} in email template to display customer code in message','fat-services-booking');?></p>
                            </div>
                        </div>

                        <div class="fields">
                            <div class="field fat-text-right">
                                <div class="ui primary button" data-onClick="FatSbEmailTemplate.submitTemplate"
                                     data-invalid-message="<?php echo esc_attr__('Please input data ','fat-services-booking');?>"
                                     data-success-message="<?php esc_attr_e('Template have been saved','fat-services-booking');?>" >
                                    <?php echo esc_html__('Save','fat-services-booking');?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>