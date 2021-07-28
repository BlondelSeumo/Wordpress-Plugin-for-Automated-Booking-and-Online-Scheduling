<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 11/21/2018
 * Time: 9:49 AM
 */
$db_setting = FAT_DB_Setting::instance();
$currency = $db_setting->get_currency_setting();
$currency_symbol = isset($currency['symbol']) ? $currency['symbol'] : '$';
$symbol_position = isset($currency['symbol_position']) ? $currency['symbol_position'] : 'after';
?>
<script type="text/html" id="tmpl-fat-sb-services-template">
    <div class="ui modal tiny fat-semantic-container fat-services-modal">
        <div class="header fat-sb-popup-title"><?php echo esc_html__('Add new service','fat-services-booking'); ?></div>
        <div class="scrolling content">
            <div class="ui form">
                <div class="one fields">
                    <div class="ui image-field " id="s_image_id" data-image-id="{{data.service.s_image_id}}"
                         data-image-url="{{data.service.s_image_url}}">
                    </div>
                </div>
                <div class="two fields">
                    <div class="field ">
                        <label for="s_name"><?php echo esc_html__('Name','fat-services-booking'); ?><span
                                    class="required"> *</span></label>
                        <div class="ui left icon input ">
                            <input type="text" name="s_name" id="s_name" value="{{data.service.s_name}}" tabindex="0"
                                   placeholder="<?php echo esc_attr__('Service name','fat-services-booking'); ?>" required>
                            <i class="edit outline icon"></i>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter name','fat-services-booking'); ?>
                        </div>
                    </div>
                    <div class="field services-category">
                        <label for="s_category_id"><?php echo esc_html__('Category','fat-services-booking'); ?> <span
                                    class="required"> *</span>
                            <a class="fat-bt-add-inline fat-bt-add-category" href="javascript:"
                               data-onClick="FatSbService.addCategoryOnClick" data-callback="FatSbService.addCategoryToDropdown"
                               data-tooltip="<?php echo esc_attr('Add category','fat-services-booking'); ?>">
                                <i class="plus circle icon"></i>
                            </a>
                        </label>
                        <div class="ui selection search dropdown top left pointing has-icon">
                            <i class="folder outline icon"></i>
                            <input type="hidden" name="s_category_id" id="s_category_id"
                                   value="{{data.service.s_category_id}}" tabindex="1"
                                   required>
                            <div class="text"><?php echo esc_html__('Select category exists'); ?></div>
                            <i class="dropdown icon"></i>
                            <div class="menu">
                                <# _.each(data.categories, function(item){ #>
                                <div class="item" data-value="{{item.sc_id}}">{{item.sc_name}}</div>
                                <# }) #>
                            </div>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please select category','fat-services-booking'); ?>
                        </div>
                    </div>
                </div>

                <div class="two fields">
                    <div class="field ">
                        <label><?php echo esc_html__('Price','fat-services-booking'); ?> <span
                                    class="required"> *</span></label>
                        <div class="ui left icon input number">
                            <input type="text" name="s_price" data-type="decimal" id="s_price"
                                   value="{{data.service.s_price}}" tabindex="2"
                                   placeholder="<?php echo esc_attr__('Service price','fat-services-booking'); ?>" required>
                            <i class="dollar sign icon"></i>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter price','fat-services-booking'); ?>
                        </div>
                    </div>

                    <div class="field ">
                        <label><?php echo esc_html__('Tax(%)','fat-services-booking'); ?>
                            <span
                                    class="required"> *</span>
                        </label>
                        <div class="ui action input number has-button">
                            <button class="ui icon button number-decrease">
                                <i class="minus-icon"></i>
                            </button>
                            <input type="text" name="s_tax" data-type="decimal" data-step="0.5" data-min="0"
                                   tabindex="7"
                                   id="s_tax" value="{{data.service.s_tax}}">
                            <button class="ui icon button number-increase">
                                <i class="plus-icon"></i>
                            </button>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter tax','fat-services-booking'); ?>
                        </div>
                    </div>

                </div>
                <?php
                $price_extra='';
                $price_extra = apply_filters('fat_sb_price_extra', $price_extra);
                echo sprintf('%s',$price_extra); ?>
                <div class="two fields">
                    <div class="field ">
                        <label><?php echo esc_html__('Duration','fat-services-booking'); ?> <span class="required"> *</span>
                        </label>
                        <div class="ui selection search  top left pointing has-icon dropdown">
                            <i class="clock outline icon"></i>
                            <input type="hidden" name="s_duration" id="s_duration" value="{{data.service.s_duration}}"
                                   tabindex="4" required>
                            <i class="dropdown icon"></i>
                            <div class="text"
                                 id="s_duration_label"><?php echo esc_html__('Select duration','fat-services-booking'); ?></div>
                            <div class="menu">
                                <?php $durations = FAT_SB_Utils::getDurations(1,'duration_step');
                                foreach ($durations as $key => $value) { ?>
                                    <div class="item"
                                         data-value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></div>
                                <?php }; ?>
                            </div>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please select duration','fat-services-booking'); ?>
                        </div>
                    </div>

                    <div class="field ">
                        <label>
                            <?php echo esc_html__('Break time','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('Time after the appointment (rest, clean up,...)','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui selection dropdown search  top left pointing has-icon  ">
                            <i class="clock outline icon"></i>
                            <input type="hidden" name="s_break_time" id="s_break_time"
                                   value="{{data.service.s_break_time}}" tabindex="5">
                            <i class="dropdown icon"></i>
                            <div class="text"><?php echo esc_html__('Select break time','fat-services-booking'); ?></div>
                            <div class="menu up">
                                <div class="item" data-value="0"><?php echo esc_html__('No break times','fat-services-booking'); ?></div>
                                <?php foreach ($durations as $key => $value) { ?>
                                    <div class="item"
                                         data-value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></div>
                                <?php }; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="two fields">
                    <div class="field ">
                        <label><?php echo esc_html__('Minimum Capacity','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('Minimum number of person per one booking of this service','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui action input number has-button">
                            <button class="ui icon button number-decrease">
                                <i class="minus-icon"></i>
                            </button>
                            <input type="text" name="s_minimum_person" data-type="int" data-step="1" data-min="1"
                                   tabindex="6"
                                   id="s_minimum_person" value="{{data.service.s_minimum_person}}">
                            <button class="ui icon button number-increase">
                                <i class="plus-icon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="field ">
                        <label><?php echo esc_html__('Maximum Capacity','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('Maximum number of person per one booking of this service','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui action input number has-button">
                            <button class="ui icon button number-decrease">
                                <i class="minus-icon"></i>
                            </button>
                            <input type="text" name="s_maximum_person" data-type="int" data-step="1" data-min="1"
                                   tabindex="7"
                                   id="s_maximum_person" value="{{data.service.s_maximum_person}}">
                            <button class="ui icon button number-increase">
                                <i class="plus-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>



                <div class="one fields">
                    <div class="field">
                        <label>
                            <?php echo esc_html__('Employees','fat-services-booking'); ?>
                        </label>
                        <div class="ui bottom left pointing multiple search selection dropdown fat-sb-service-employee">
                            <input type="hidden" name="s_employee_ids" id="s_employee_ids"
                                   value="{{data.service.s_employee_ids}}" tabindex="8">
                            <i class="dropdown icon"></i>
                            <div class="text"><?php echo esc_html__('Select employees','fat-services-booking'); ?></div>
                            <div class="menu">
                                <div class="ui icon search input">
                                    <i class="search icon"></i>
                                    <input type="text"
                                           placeholder="<?php echo esc_attr__('Search employees...','fat-services-booking'); ?> ">
                                </div>
                                <div class="scrolling menu">
                                    <# _.each(data.employees, function(item){ #>
                                    <div class="item" data-value="{{item.e_id}}">
                                        <# if(item.e_avatar_url!=''){ #>
                                        <img class="ui mini avatar image"
                                             src="{{item.e_avatar_url}}">
                                        <# } #>
                                        {{item.e_first_name}} {{item.e_last_name}}
                                    </div>
                                    <# }) #>
                                </div>
                            </div>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please select employees','fat-services-booking'); ?>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field  service-extra">
                        <label><?php echo esc_html__('Service Extra','fat-services-booking'); ?>
                            <a class="fat-bt-add-inline fat-bt-add-service-extra" href="javascript:"
                               data-onClick="FatSbServiceExtra.addServiceExtraOnClick" data-callback="FatSbService.addServiceExtraToDropdown"
                               data-tooltip="<?php echo esc_attr('Add service extra','fat-services-booking'); ?>">
                                <i class="plus circle icon"></i>
                            </a>
                        </label>
                        <div class="ui selection multiple dropdown bottom left  pointing clearable">
                            <input type="hidden" name="s_extra_ids" id="s_extra_ids"
                                   value="{{data.service.s_extra_ids}}" tabindex="9">
                            <div class="text"><?php echo esc_html__('Select service extra','fat-services-booking'); ?></div>
                            <i class="dropdown icon"></i>
                            <div class="menu">
                                <# _.each(data.services_extra, function(item){ #>
                                <div class="item" data-value="{{item.se_id}}">{{item.se_name}}</div>
                                <# }) #>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one inline fields">
                    <label for="s_available"><?php echo esc_html__('Service available','fat-services-booking'); ?></label>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <# if(data.service.s_available==1){ #>
                            <input type="radio" name="s_available" id="s_available" value="1" checked="checked"
                                   tabindex="10">
                            <# }else{ #>
                            <input type="radio" name="s_available" id="s_available" value="1" tabindex="10">
                            <# } #>
                            <label><?php echo esc_html__('Every one','fat-services-booking'); ?></label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <# if(data.service.s_available==2){ #>
                            <input type="radio" name="s_available" id="s_available" value="2" checked="checked"
                                   tabindex="11">
                            <# }else{ #>
                            <input type="radio" name="s_available" id="s_available" value="2" tabindex="11">
                            <# } #>
                            <label><?php echo esc_html__('Male only','fat-services-booking'); ?></label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <# if(data.service.s_available==3){ #>
                            <input type="radio" name="s_available" id="s_available" value="3" checked="checked"
                                   tabindex="12">
                            <# }else{ #>
                            <input type="radio" name="s_available" id="s_available" value="3" tabindex="12">
                            <# } #>
                            <label><?php echo esc_html__('Female only','fat-services-booking'); ?></label>
                        </div>
                    </div>
                </div>
                <div class="one fields">
                    <div class="field ">
                        <label for="address"><?php echo esc_html__('Link','fat-services-booking');?>
                            <div class="ui icon ui-tooltip" data-content="<?php echo esc_attr__('If want display link of service in email template, please add link at here','fat-services-booking');?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui left icon input ">
                            <input type="text" value="{{data.service.s_link}}" name="s_link" id="s_link" placeholder="<?php echo esc_attr__('Link','fat-services-booking');?>" >
                            <i class="edit outline icon"></i>
                        </div>

                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <label><?php echo esc_html__('Description','fat-services-booking'); ?></label>
                        <textarea rows="3" id="s_description" tabindex="13">{{data.service.s_description}}</textarea>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <div class="ui toggle checkbox">
                            <# if(data.service.s_multiple_days==1){ #>
                            <input type="checkbox" name="s_multiple_days" id="s_multiple_days" value="1"
                                   checked tabindex="14">
                            <# }else{ #>
                            <input type="checkbox" name="s_multiple_days" id="s_multiple_days" value="1"
                                   tabindex="14">
                            <# } #>
                            <label><?php echo esc_html__('Allow book multiple days','fat-services-booking'); ?>
                                <div class="ui icon ui-tooltip"
                                     data-content="<?php echo esc_attr__('If checked, client can book multiple days for this service','fat-services-booking'); ?>">
                                    <i class="question circle icon"></i>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="two fields fat-hidden" data-depend="s_multiple_days" data-depend-value="1">
                    <div class="field ">
                        <label><?php echo esc_html__('Minimum day(s)','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('Minimum number of days for book multiple days','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui action input number has-button">
                            <button class="ui icon button number-decrease">
                                <i class="minus-icon"></i>
                            </button>
                            <input type="text" name="s_min_multiple_slot" data-type="int" data-step="1" data-min="1"
                                   tabindex="6"
                                   id="s_min_multiple_slot" value="{{data.service.s_min_multiple_slot}}">
                            <button class="ui icon button number-increase">
                                <i class="plus-icon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="field ">
                        <label><?php echo esc_html__('Maximum days(s)','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('Maximum number of days for book multiple days','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui action input number has-button">
                            <button class="ui icon button number-decrease">
                                <i class="minus-icon"></i>
                            </button>
                            <input type="text" name="s_max_multiple_slot" data-type="int" data-step="1" data-min="1"
                                   tabindex="7"
                                   id="s_max_multiple_slot" value="{{data.service.s_max_multiple_slot}}">
                            <button class="ui icon button number-increase">
                                <i class="plus-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label><?php echo esc_html__('Display order in booking form','fat-services-booking'); ?></label>
                        <div class="ui action input number has-button">
                            <button class="ui icon button number-decrease">
                                <i class="minus-icon"></i>
                            </button>
                            <input type="text" name="s_order" data-type="int" data-step="1" data-min="1"
                                   tabindex="7" id="s_order" value="{{data.service.s_order}}">
                            <button class="ui icon button number-increase">
                                <i class="plus-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <div class="ui toggle checkbox">
                            <# if(data.service.s_allow_booking_online==1){ #>
                            <input type="checkbox" name="s_allow_booking_online" id="s_allow_booking_online" value="1"
                                   checked tabindex="14">
                            <# }else{ #>
                            <input type="checkbox" name="s_allow_booking_online" id="s_allow_booking_online" value="1"
                                   tabindex="14">
                            <# } #>
                            <label><?php echo esc_html__('Publish to frontend','fat-services-booking'); ?>
                                <div class="ui icon ui-tooltip"
                                     data-content="<?php echo esc_attr__('If checked, services will be displayed on booking form','fat-services-booking'); ?>">
                                    <i class="question circle icon"></i>
                                </div>
                            </label>
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

            <button class="ui blue button fat-submit-modal fat-bt-submit-service" data-popup-id="popup_bt_update_service" data-loading-color="loading-blue"
                    data-onClick="FatSbService.processSubmitService"
                    data-id="{{data.service.s_id}}" data-success-message="<?php echo esc_attr__('Service has been saved','fat-services-booking'); ?>">
                <i class="save outline icon"></i>
                <?php echo esc_html__('Save','fat-services-booking'); ?>
            </button>

            <div class="ui flowing popup top left transition hidden fat-popup-submit-service-confirm" data-popup-id="popup_bt_update_service">
                <h4 class="ui header">
                    <?php echo esc_html__('Your changes related to specific settings for each employee.','fat-services-booking'); ?>
                    <br/>
                    <?php echo esc_html__('Do you want to update employee settings according to this setting ?','fat-services-booking'); ?>
                </h4>
                <div>
                    <button class="ui mini button fat-bt-confirm-cancel" >
                        <?php echo esc_html__('No','fat-services-booking'); ?>
                    </button>
                    <button class="ui mini primary button fat-bt-confirm-ok fat-bt-confirm-enable" >
                        <?php echo esc_html__('Yes','fat-services-booking'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-services-category-template">
    <div class="ui modal tiny fat-semantic-container fat-sb-category-form">
        <div class="header"><?php echo esc_html__('Add new category','fat-services-booking'); ?></div>
        <div class="scrolling content">
            <div class="ui form">
                <div class="one fields">
                    <div class="ui image-field" id="sc_image_id" data-image-id="{{data.sc_image_id}}"
                         data-image-url="{{data.sc_image_url}}">
                    </div>
                </div>
                <div class="one fields">
                    <div class="field ">
                        <label><?php echo esc_html__('Name','fat-services-booking'); ?><span
                                    class="required"> *</span></label>
                        <div class="ui left icon input ">
                            <input type="text" name="sc_name" id="sc_name" value="{{data.sc_name}}" autocomplete="off"
                                   placeholder="<?php echo esc_attr__('Category name','fat-services-booking'); ?>" required>
                            <i class="edit outline icon"></i>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter name','fat-services-booking'); ?>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <label><?php echo esc_html__('Description','fat-services-booking'); ?></label>
                        <textarea rows="3" id="sc_description">{{data.sc_description}}</textarea>
                    </div>
                </div>

            </div>
        </div>
        <div class="actions">
            <button class="ui basic button fat-close-modal">
                <i class="times circle outline icon"></i>
                <?php echo esc_html__('Cancel','fat-services-booking'); ?>
            </button>

            <button class="ui blue button fat-submit-modal submit-category"
                    data-onClick="FatSbService.processSubmitCategory" data-id="{{data.sc_id}}"
                    data-success-message="<?php echo esc_attr__('Category has been saved','fat-services-booking'); ?>">
                <i class="save outline icon"></i>
                <?php echo esc_html__('Save','fat-services-booking'); ?>
            </button>

        </div>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-category-item-template">
    <# _.each(data, function(item){ #>
    <div class="column">
        <div class="ui items">
            <div class="item fat-hover-link fat-pd-10 fat-border-spin" data-onClick="FatSbService.loadServiceByCat"
                 data-id="{{item.sc_id}}">
                <div class="ui tiny image">
                    <# if (item.sc_image_url!=''){ #>
                    <img class="fat-border-round fat-box-shadow fat-img-80" src="{{item.sc_image_url}}"
                         data-image-id="{{item.sc_image_id}}">
                    <# }else{ #>
                    <span class="fat-no-thumb fat-img-80"></span>
                    <# } #>
                </div>
                <div class="content">
                    <div class="header thin">{{item.sc_name}}</div>
                    <div>ID: {{item.sc_id}}</div>
                    <div class="meta">
                        <div>
                            <span class="category-total-service" data-total="{{item.sc_total_service}}">{{item.sc_total_service}}</span><?php echo esc_html__(' services','fat-services-booking'); ?>
                        </div>
                    </div>
                    <div class="description">{{item.sc_description}}</div>
                </div>
                <div class="fat-bt-group">
                    <button class=" ui icon button fat-item-bt-inline fat-sb-delete-category"
                            data-onClick="FatSbService.processDeleteCategory" data-id="{{item.sc_id}}"
                            data-prevent-event="1" data-title="<?php echo esc_attr__('Delete','fat-services-booking'); ?>">
                        <i class="trash alternate outline icon"></i>
                    </button>

                    <button class=" ui icon button fat-item-bt-inline fat-sb-edit-category"
                            data-onClick="FatSbService.processViewCategoryDetail" data-id="{{item.sc_id}}"
                            data-prevent-event="1" data-title="<?php echo esc_attr__('Edit','fat-services-booking'); ?>">
                        <i class="edit outline icon"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>
    <# }) #>
</script>

<script type="text/html" id="tmpl-fat-sb-service-item-template">
    <# _.each(data, function(item){ #>
    <div class="four wide column">
        <div class="ui items ">
            <div class="item fat-pd-10 fat-border-spin fat-hover fat-hover-link" data-id="{{item.s_id}}">
                <div class="ui tiny image">
                    <# if (item.s_image_url!=''){ #>
                    <img class="fat-border-round fat-box-shadow fat-img-80" src="{{item.s_image_url}}"
                         data-image-id="{{item.s_image_id}}">
                    <# }else{ #>
                    <span class="fat-no-thumb fat-img-80"></span>
                    <# } #>
                </div>
                <div class="content">
                    <div class="header thin">{{item.s_name}}</div>
                    <div class="meta">
                        <div>
                            <strong><?php echo esc_html__('Durations:','fat-services-booking'); ?></strong>
                            <span class="duration-label">
                            {{item.s_duration_label}}
                        </span>
                        </div>
                        <div><strong><?php echo esc_html('Price:','fat-services-booking'); ?></strong>
                            <?php if($symbol_position=='before'){ echo esc_html($currency_symbol);};?><span class="price">{{item.s_price}}</span><?php if($symbol_position=='after'){ echo esc_html($currency_symbol);};?>
                        </div>
                        <div><strong><?php echo esc_html('ID:','fat-services-booking'); ?></strong>
                            <span class="id">{{item.s_id}}</span>
                        </div>


                    </div>
                    <div class="description">
                        {{item.description}}
                    </div>
                </div>

                <button class=" ui icon button fat-item-bt-inline fat-sb-delete-service"
                        data-onClick="FatSbService.processDeleteService" data-id="{{item.s_id}}"  data-cat-id="{{item.s_category_id}}"
                        data-title="<?php echo esc_attr__('Delete','fat-services-booking'); ?>">
                    <i class="trash alternate outline icon"></i>
                </button>

                <button class=" ui icon button fat-item-bt-inline fat-sb-service-work-day"
                        data-onClick="FatSbService.processServiceWorkDay" data-id="{{item.s_id}}" data-position="top right"
                        data-title="<?php echo esc_attr__('Limit work day for service','fat-services-booking'); ?>">
                    <i class="clock outline icon"></i>
                </button>

                <button class=" ui icon button fat-item-bt-inline fat-sb-edit-service"
                        data-onClick="FatSbService.showPopupService" data-id="{{item.s_id}}"
                        data-title="<?php echo esc_attr__('Edit','fat-services-booking'); ?>">
                    <i class="edit outline icon"></i>
                </button>
            </div>
        </div>
    </div>
    <# }) #>
</script>

<script  type="text/html" id="tmpl-fat-sb-service-work-day">
    <div class="ui modal tiny fat-semantic-container fat-service-work-day-form">
        <div class="header fat-sb-popup-title"><?php echo esc_html__('Service\'s work day','fat-services-booking');?></div>
        <div class="scrolling content">
            <div class="ui form">
                <div class="fat-work-day-wrap">
                    <div class="fat-work-day-inner">

                    </div>
                    <div class="fat-sb-bottom-action-group fat-mg-top-15">
                        <button class="ui basic simple button fat-bt-add-work-day" data-onClick="FatSbService.btAddWorkDayOnClick">
                            <i class="plus square outline icon"></i>
                            <?php echo esc_html__('Add work day','fat-services-booking'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="actions">
            <button class="ui basic button fat-close-modal">
                <i class="times circle outline icon"></i>
                <?php echo esc_html__('Cancel','fat-services-booking');?>
            </button>
            <div class="blue ui buttons">
                <div class="ui button fat-submit-modal" data-onClick="FatSbService.processSubmitWorkDay"
                     data-id="{{data.s_id}}" data-success-message="<?php echo esc_attr__('Work day has been saved','fat-services-booking'); ?>">
                    <i class="save outline icon"></i>
                    <?php echo esc_html__('Save','fat-services-booking');?>
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-service-item-work-day">
    <div class="fat-sb-work-day-item fat-mg-top-5">
        <div class="ui input">
            <input type="text" value="" class="date-range-picker" name="service_work_day">
        </div>

        <button class="ui basic simple button fat-mg-left-15 fat-hover-red fat-bt-remove-work-day">
            <i class="minus square outline icon"></i>
        </button>
    </div>
</script>
