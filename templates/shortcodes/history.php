<?php
/**
 * Created by PhpStorm.
 * User: RoninWP
 * Date: 8/9/2019
 * Time: 9:50 AM
 */
$setting_db = FAT_DB_Setting::instance();
$setting = $setting_db->get_setting();
$current_user = wp_get_current_user();
$class_custom_code = $current_user->exists() ? 'fat-hidden' : '';
$default_process_status = $current_user->exists() ? 1 : 0;
?>
<div class="fat-sb-booking-history <?php echo($current_user->exists() ? 'has-login' : ''); ?>">
    <div class="fat-sb-customer-code" >
        <?php  if ($current_user->exists()) { ?>
        <div class="fat-sb-remain-package" style="float: left; display: inline-block">
            <label style="color: #343434 !important;"><?php echo esc_html__('Remaining balance : ','fat-services-booking');?><?php
                $remain = FAT_DB_Price_Package::get_price_amount_by_user($current_user->user_email);
                echo ($remain['buy_amount'] - $remain['has_payment'] >= 0) ?  ($remain['buy_amount'] - $remain['has_payment']) : 0;?> <?php echo esc_html__('Credits','fat-services-booking');?>
            </label>
        </div>
        <?php } ?>
        <label class="<?php echo esc_attr($class_custom_code); ?>"><?php echo esc_html__('Customer code:', 'fat-services-booking'); ?></label>
        <input type="text" name="c_code" class="<?php echo esc_attr($class_custom_code); ?>"
               data-error="<?php echo esc_attr__('Please input customer code before view history', 'fat-services-booking'); ?>"
               placeholder="<?php echo esc_attr__('Input customer code and click view history', 'fat-services-booking'); ?>">

        <div class="ui floating dropdown labeled icon selection dropdown fat-mg-right-10">
            <i class="dropdown icon"></i>
            <input type="hidden" name="b_process_status" id="b_process_status" value="<?php echo esc_attr($default_process_status);?>">
            <span class="text"><?php echo esc_html__('Select status','fat-services-booking');?></span>
            <div class="menu">

                <div class="item"  data-value="0">
                    <div class="ui yellow empty circular label"></div>
                    <?php echo esc_html__('Pending','fat-services-booking');?>
                </div>
                <div class="item"  data-value="1">
                    <div class="ui green empty circular label"></div>
                    <?php echo esc_html__('Approved','fat-services-booking');?>
                </div>
                <div class="item" data-value="2">
                    <div class="ui red empty circular label"></div>
                    <?php echo esc_html__('Cancel','fat-services-booking');?>
                </div>
                <div class="item"  data-value="3">
                    <div class="ui empty empty circular label"></div>
                    <?php echo esc_html__('Reject','fat-services-booking');?>
                </div>
            </div>
        </div>

        <div class="fat-sb-history-button-group">
            <a class="fat-sb-view-history fat-bt" data-prevent-event="1" data-onClick="FatSbBookingHistory.viewHistory"
               href="#"><?php echo esc_html__('View history', 'fat-services-booking'); ?></a>
            <a class="fat-sb-get-customer-code fat-bt <?php echo esc_attr($class_custom_code); ?>"
               data-prevent-event="1"
               data-onClick="FatSbBookingHistory.openPopupGetCustomerCode"
               href="#"><?php echo esc_html__('Get customer code', 'fat-services-booking'); ?></a>
        </div>
    </div>
    <table>
        <thead>
        <tr>
            <th><?php echo esc_html__('Appointment Date', 'fat-services-booking'); ?></th>
            <th><?php echo esc_html__('Customer', 'fat-services-booking'); ?></th>
            <th><?php echo esc_html__('Employee', 'fat-services-booking'); ?></th>
            <th><?php echo esc_html__('Services', 'fat-services-booking'); ?></th>
            <th><?php echo esc_html__('Duration', 'fat-services-booking'); ?></th>
            <th class="fat-sb-payment"><?php echo esc_html__('Payment', 'fat-services-booking'); ?></th>
            <th class="fat-sb-status"><?php echo esc_html__('Status', 'fat-services-booking'); ?></th>
            <th class="fat-sb-create-date"><?php echo esc_html__('Create date', 'fat-services-booking'); ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="9">
                <div class="fat-sb-not-found">
                    <?php
                    if ($current_user->exists()) {
                        echo esc_html__('Please input customer code and click \'View History\' to display your booking history', 'fat-services-booking');
                    } else {
                        echo esc_html__('Please click \'View History\' to display your booking history', 'fat-services-booking');
                    } ?>
            </td>
        </tr>

        </tbody>
    </table>
    <div class="fat-sb-pagination" data-obj="FatSbBookingHistory" data-func="loadHistory">

    </div>
</div>

<script type="text/html" id="tmpl-fat-sb-history-item-template">
    <# _.each(data, function(item){ #>
    <tr data-id="{{item.b_id}}" data-edit="{{item.editable}}">
        <td data-label="<?php echo esc_attr__('Appointment Date', 'fat-services-booking'); ?>">{{item.b_date}}</td>
        <td data-label="<?php echo esc_attr__('Customer', 'fat-services-booking'); ?>">
            {{item.c_first_name}} {{item.c_last_name}}
            <span class="extra-info">{{item.c_email}}</span>
        </td>
        <td data-label="<?php echo esc_attr__('Employee', 'fat-services-booking'); ?>">
            {{item.e_first_name}} {{item.e_last_name}}
            <span class="extra-info">{{item.e_email}}</span>
        </td>
        <td data-label="<?php echo esc_attr__('Services', 'fat-services-booking'); ?>">{{item.s_name}}</td>
        <td data-label="<?php echo esc_attr__('Duration', 'fat-services-booking'); ?>">
            {{ item.b_service_duration_display }}
        </td>
        <td class="fat-sb-payment" data-label="<?php echo esc_attr__('Payment', 'fat-services-booking'); ?>">
            {{item.b_total_pay}}
        </td>
        <td class="fat-sb-status" data-label="<?php echo esc_attr__('Status', 'fat-services-booking'); ?>">
            {{ item.b_status_display }}
        </td>
        <td data-label="<?php echo esc_attr__('Create Date', 'fat-services-booking'); ?>">{{item.b_create_date}}</td>
        <td>
            <?php if (!isset($setting['allow_client_cancel']) || $setting['allow_client_cancel'] == 1): ?>
                <a href="#" data-prevent-event="1" class="fat-sb-cancel"
                   data-onClick="FatSbBookingHistory.openPopupCancel"><?php echo esc_attr__('Cancel', 'fat-services-booking'); ?></a>
            <?php endif; ?>
        </td>

    </tr>
    <# }) #>
</script>

<script type="text/html" id="tmpl-fat-sb-get-customer-code-template">
    <div class="fat-sb-popup-modal">
        <div class="fat-sb-popup-modal-content" style="display: none">
            <lable><?php echo esc_html__('Your email:', 'fat-services-booking'); ?></lable>
            <input type="email" name="c_email" id="c_email"
                   data-error="<?php echo esc_html__('Please input email before get code', 'fat-services-booking'); ?>"/>
            <div class="fat-sb-popup-bt-group">
                <a href="#" data-prevent-event="1" class="fat-bt-submit fat-bt"
                   data-onClick="FatSbBookingHistory.getCustomerCode"><?php echo esc_html__('Get code', 'fat-services-booking'); ?></a>
                <a href="#" data-prevent-event="1" class="fat-bt-cancel fat-bt"
                   data-onClick="FatSbBookingHistory.closePopupModal"><?php echo esc_html__('Cancel', 'fat-services-booking'); ?></a>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-popup-cancel-template">
    <div class="fat-sb-popup-modal">
        <div class="fat-sb-popup-modal-content fat-sb-cancel-booking-popup" style="display: none">
            <lable><?php echo esc_html__('Your code:', 'fat-services-booking'); ?></lable>
            <input type="text" name="c_code" id="c_code" class="customer-code"
                   data-error="<?php echo esc_html__('Please input customer code to cancel booking', 'fat-services-booking'); ?>"/>
            <div class="fat-sb-popup-bt-group">
                <a href="#" data-prevent-event="1" class="fat-bt-submit fat-bt"
                   data-onClick="FatSbBookingHistory.submitCancel"><?php echo esc_html__('Cancel appointment', 'fat-services-booking'); ?></a>
                <a href="#" data-prevent-event="1" class="fat-bt-cancel fat-bt"
                   data-onClick="FatSbBookingHistory.closePopupModal"><?php echo esc_html__('Close', 'fat-services-booking'); ?></a>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-popup-cancel-confirm-template">
    <div class="fat-sb-popup-modal">
        <div class="fat-sb-popup-modal-content fat-sb-cancel-booking-popup" style="display: none">
            <div class="fat-sb-confirm-message">
                <?php echo esc_html__('Confirm cancel this appointment', 'fat-sb-booking'); ?>
            </div>
            <div class="fat-sb-popup-bt-group">
                <a href="#" data-prevent-event="1" class="fat-bt-submit fat-bt"
                   data-onClick="FatSbBookingHistory.submitCancel"><?php echo esc_html__('Cancel appointment', 'fat-services-booking'); ?></a>
                <a href="#" data-prevent-event="1" class="fat-bt-cancel fat-bt"
                   data-onClick="FatSbBookingHistory.closePopupModal"><?php echo esc_html__('Close', 'fat-services-booking'); ?></a>
            </div>
        </div>
    </div>
</script>
