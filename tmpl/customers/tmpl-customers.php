<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 11/21/2018
 * Time: 9:49 AM
 */
$setting = FAT_DB_Setting::instance();
$setting = $setting->get_setting();
$disable_customer_email =  isset($setting['disable_customer_email']) && $setting['disable_customer_email'] == '1' ? 1 : 0;

$price_package_enable = isset($setting['price_package_enable']) && $setting['price_package_enable'] == '1' ? 1 : 0;
$price_package = array();

$number_decimal = 0;
if ($price_package_enable == 1) {
    $price_package = FAT_DB_Price_Package::instance();
    $price_package = $price_package->get_package();

    $currency_setting =  FAT_DB_Setting::instance();
    $currency_setting = $currency_setting->get_currency_setting();
    $prefix_currency = isset($currency_setting['symbol_position']) && $currency_setting['symbol_position']=='before' ? $currency_setting['symbol'] : '';
    $suffix_currency = isset($currency_setting['symbol_position']) && $currency_setting['symbol_position']=='after' ? $currency_setting['symbol'] : '';
    $number_decimal = isset($setting['number_of_decimals']) ? $setting['number_of_decimals'] : 0;
}
?>
<script type="text/html" id="tmpl-fat-sb-customers-template">
    <div class="ui modal tiny fat-semantic-container fat-customer-form">
        <div class="header fat-sb-popup-title"><?php echo esc_html__('Add new customer', 'fat-services-booking'); ?></div>
        <div class="scrolling content">
            <div class="ui form">
                <div class="two fields">
                    <div class="field ">
                        <label for="name"><?php echo esc_html__('First name', 'fat-services-booking'); ?><span
                                    class="required"> *</span></label>
                        <div class="ui left icon input ">
                            <input type="text" name="c_first_name" id="c_first_name" value="{{data.c_first_name}}"
                                   placeholder="<?php echo esc_attr__('First name', 'fat-services-booking'); ?>"
                                   required>
                            <i class="edit outline icon"></i>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter first name', 'fat-services-booking'); ?>
                        </div>
                    </div>
                    <div class="field ">
                        <label for="name"><?php echo esc_html__('Last name', 'fat-services-booking'); ?><span
                                    class="required"> *</span></label>
                        <div class="ui left icon input ">
                            <input type="text" name="c_last_name" id="c_last_name" value="{{data.c_last_name}}"
                                   placeholder="<?php echo esc_attr__('Last name', 'fat-services-booking'); ?>"
                                   required>
                            <i class="edit outline icon"></i>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter last name', 'fat-services-booking'); ?>
                        </div>
                    </div>
                </div>

                <div class="two fields">
                    <div class="field ">
                        <label for="email"><?php echo esc_html__('Email', 'fat-services-booking'); ?> <span
                                    class="required"> *</span></label>
                        <div class="ui left icon input">
                            <?php if($disable_customer_email): ?>
                                <input type="email" name="c_email" id="c_email" value="<?php echo (uniqid().'@no_email.com');?>" disabled="disabled"
                                       placeholder="<?php echo esc_attr__('Email', 'fat-services-booking'); ?>" required>
                            <?php endif; ?>

                            <?php if(!$disable_customer_email): ?>
                                <input type="email" name="c_email" id="c_email" value="{{data.c_email}}"
                                       placeholder="<?php echo esc_attr__('Email', 'fat-services-booking'); ?>" required>
                            <?php endif; ?>

                            <i class="envelope outline icon"></i>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter email', 'fat-services-booking'); ?>
                        </div>
                    </div>

                    <div class="field ">
                        <label for="phone"><?php echo esc_html__('Phone', 'fat-services-booking'); ?></label>
                        <div class="ui fluid search selection dropdown phone-code">
                            <input type="hidden" name="c_phone_code" id="c_phone_code" autocomplete="nope" value="{{data.c_phone_code}}">
                            <i class="dropdown icon"></i>
                            <div class="default text"></div>
                            <div class="menu">
                                <?php
                                $phoneCode = FAT_SB_Utils::getPhoneCountry();
                                foreach($phoneCode as $pc){
                                    $pc = explode(',',$pc);?>
                                    <div class="item"  data-value="<?php echo esc_attr($pc[1].','.$pc[2]);?>"><i class="<?php echo esc_attr($pc[2]);?> flag"></i><?php echo esc_html($pc[0]);?><span>(<?php echo esc_html($pc[1]);?>)</span></div>
                                <?php }; ?>
                                <div class="item" data-value="other"><?php echo esc_html__('Other','fat-services-booking');?></div>
                            </div>
                        </div>

                        <div class="ui left icon input phone-number">
                            <input type="text" name="c_phone" id="c_phone"
                                   placeholder="<?php echo esc_attr__('Phone', 'fat-services-booking'); ?>"
                                   value="{{data.c_phone}}">
                            <i class="phone volume icon"></i>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <label for="c_dob"><?php echo esc_html__('Date of birth', 'fat-services-booking'); ?></label>
                        <?php
                        $start_date = new DateTime();
                        $start_date = $start_date->modify('-18 years');
                        $date_format = get_option('date_format');
                        $locale = get_locale();
                        $locale = explode('_',$locale)[0];
                        ?>
                        <# if(data.c_dob!=null && data.c_dob!=''){ #>
                        <input type="text" value="{{data.c_dob}}" data-dropdown="1" class="date-picker" name="c_dob"  data-locale="<?php echo esc_attr($locale);?>"
                               id="c_dob">
                        <# }else{ #>
                        <input type="text" value="<?php echo $start_date->format('Y-m-d'); ?>" data-dropdown="1" class="date-picker" name="c_dob"  data-locale="<?php echo esc_attr($locale);?>"
                               id="c_dob" data-start-init="<?php echo date_i18n($date_format, $start_date->format('U')); ?>">
                        <# } #>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <label><?php echo esc_html__('Notes', 'fat-services-booking'); ?></label>
                        <textarea rows="5" id="c_description" name="c_description">{{data.c_description}}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="actions">
            <button class="ui basic button fat-close-modal">
                <i class="times circle outline icon"></i>
                <?php echo esc_html__('Cancel', 'fat-services-booking'); ?>
            </button>
            <div class="blue ui buttons">
                <div class="ui button fat-submit-modal" data-onClick="FatSbCustomers.processSubmitCustomer"
                     data-id="{{data.c_id}}"
                     data-success-message="<?php echo esc_attr__('Customer has been saved', 'fat-services-booking'); ?>">
                    <i class="save outline icon"></i>
                    <?php echo esc_html__('Save', 'fat-services-booking'); ?>
                </div>
            </div>
        </div>
    </div>
</script>

<?php if ($price_package_enable == 1): ?>
    <script type="text/html" id="tmpl-fat-sb-customer-add-credit-template">
        <div class="ui modal tiny fat-semantic-container fat-customer-add-credit-form">
            <div class="header fat-sb-popup-title"><?php echo esc_html__('Add credit', 'fat-services-booking'); ?></div>
            <div class="scrolling content">
                <div class="ui form">
                    <div class="two fields">
                        <div class="field ">
                            <label for="name"><?php echo esc_html__('Customer', 'fat-services-booking'); ?></label>
                            <div class="ui left icon input ">
                                {{data.full_name}}
                            </div>
                        </div>
                        <div class="field ">
                            <label for="name"><?php echo esc_html__('Email', 'fat-services-booking'); ?></label>
                            <div class="ui left icon input ">
                                {{data.email}}
                            </div>
                        </div>
                    </div>

                    <div class="one fields">
                        <div class="field">
                            <label><?php echo esc_html__('Select package', 'fat-services-booking'); ?></label>
                            <div class="ui selection search dropdown top left pointing has-icon">
                                <i class="folder outline icon"></i>
                                <input type="hidden" name="pk_id" id="pk_id"
                                       value="" tabindex="1" required>
                                <div class="text"><?php echo esc_html__('Select package'); ?></div>
                                <i class="dropdown icon"></i>
                                <div class="menu">
                                    <?php if (is_array($price_package)):
                                        foreach ($price_package as $pk) { ?>
                                            <div class="item"
                                                 data-value="<?php echo esc_attr($pk->pk_id); ?>"><?php echo esc_attr($pk->pk_name . ' ('. $prefix_currency . number_format($pk->pk_price, $number_decimal) . $suffix_currency . ')'); ?></div>
                                        <?php }
                                    endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="one fields">
                        <div class="field">
                            <label><?php echo esc_html__('Notes', 'fat-services-booking'); ?></label>
                            <textarea rows="5" id="pko_description" name="pko_description"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="actions">
                <button class="ui basic button fat-close-modal">
                    <i class="times circle outline icon"></i>
                    <?php echo esc_html__('Cancel', 'fat-services-booking'); ?>
                </button>
                <div class="blue ui buttons">
                    <div class="ui button fat-submit-modal" data-onClick="FatSbCustomers.processSubmitAddCredit"
                         data-id="{{data.c_id}}"
                         data-success-message="<?php echo esc_attr__('Credit has been added', 'fat-services-booking'); ?>">
                        <i class="save outline icon"></i>
                        <?php echo esc_html__('Add', 'fat-services-booking'); ?>
                    </div>
                </div>
            </div>
        </div>
    </script>
<?php endif; ?>

<script type="text/html" id="tmpl-fat-sb-customer-item-template">
    <# _.each(data, function(item){ #>
    <tr data-id="{{item.c_id}}">
        <td>
            <div class="ui checkbox">
                <input type="checkbox" name="c_id" class="check-item" data-id="{{item.c_id}}">
                <label></label>
            </div>
        </td>
        <td class="fat-c-name" data-label="<?php echo esc_attr__('Name', 'fat-services-booking'); ?>">
            {{item.c_first_name }} {{item.c_last_name}}
        </td>
        <td class="fat-c-phone" data-label="<?php echo esc_attr__('Phone', 'fat-services-booking'); ?>">
            {{item.c_phone_code_display}} {{item.c_phone}}
        </td>
        <td class="fat-c-email" data-label="<?php echo esc_attr__('Email', 'fat-services-booking'); ?>">
            {{item.c_email}}
        </td>
        <td class="fat-c-dob" data-label="<?php echo esc_attr__('Date of Birth', 'fat-services-booking'); ?>">
            {{item.c_dob}}
        </td>
        <?php if($price_package_enable==1): ?>
            <td class="fat-c-credit" data-label="<?php echo esc_attr__('Credit', 'fat-services-booking'); ?>">
                {{item.c_credit}}
            </td>
        <?php endif; ?>
        <td class="fat-c-note" data-label="<?php echo esc_attr__('Notes', 'fat-services-booking'); ?>">
            {{item.c_description}}
        </td>
        <td>
            <button class=" ui icon button fat-item-bt-inline fat-sb-delete" data-onClick="FatSbCustomers.processDelete"
                    data-id="{{item.c_id}}" data-title="<?php echo esc_attr__('Delete', 'fat-services-booking'); ?>">
                <i class="trash alternate outline icon"></i>
            </button>

            <button class=" ui icon button fat-item-bt-inline fat-sb-edit"
                    data-onClick="FatSbCustomers.processViewDetail"
                    data-id="{{item.c_id}}" data-title="<?php echo esc_attr__('Edit', 'fat-services-booking'); ?>">
                <i class="edit outline icon"></i>
            </button>
            <?php if ($price_package_enable == 1): ?>
                <button class=" ui icon button fat-item-bt-inline fat-sb-add-credit"
                        data-onClick="FatSbCustomers.processShowAddCredit"
                        data-id="{{item.c_id}}"
                        data-title="<?php echo esc_attr__('Add credit', 'fat-services-booking'); ?>">
                    <i class="money bill alternate outline icon"></i>
                </button>
            <?php endif; ?>
        </td>
    </tr>
    <# }) #>
</script>