<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 11/21/2018
 * Time: 9:49 AM
 */
?>
<script type="text/html" id="tmpl-fat-sb-services-extra-template">
    <div class="ui modal tiny fat-semantic-container fat-services-extra-form">
        <div class="header fat-sb-popup-title"><?php echo esc_html__('Add service extra','fat-services-booking'); ?></div>
        <div class="scrolling content">
            <div class="ui form">
                <div class="one fields">
                    <div class="ui image-field " id="se_image_id" data-image-id="{{data.se_image_id}}"
                         data-image-url="{{data.se_image_url}}">
                    </div>
                </div>
                <div class="two fields">
                    <div class="field ">
                        <label><?php echo esc_html__('Name','fat-services-booking');?><span class="required"> *</span></label>
                        <div class="ui left icon input ">
                            <input type="text" name="se_name" id="se_name" placeholder="<?php echo esc_attr__('Service extra name','fat-services-booking');?>"
                                 value="{{data.se_name}}"  required tabindex="0" >
                            <i class="edit outline icon"></i>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter name','fat-services-booking');?>
                        </div>
                    </div>

                    <div class="field ">
                        <label><?php echo esc_html__('Duration','fat-services-booking');?> <span class="required"> *</span>
                        </label>
                        <div class="ui selection search  top left pointing has-icon dropdown">
                            <i class="clock outline icon"></i>
                            <input type="hidden" name="se_duration" id="se_duration" tabindex="1" required  value="{{data.se_duration}}">
                            <i class="dropdown icon"  tabindex="1"></i>
                            <div class="text" id="se_duration_label"><?php echo esc_html__('Select duration','fat-services-booking');?></div>
                            <div class="menu" >
                                <?php $durations = FAT_SB_Utils::getDurations(0,'duration_step');
                                foreach ($durations as $key => $value) { ?>
                                    <div class="item"
                                         data-value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></div>
                                <?php }; ?>
                            </div>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please select duration','fat-services-booking');?>
                        </div>
                    </div>

                </div>

                <div class="two fields">
                    <div class="field ">
                        <label><?php echo esc_html__('Price','fat-services-booking');?> <span class="required"> *</span></label>
                        <div class="ui left icon input number" >
                            <input type="text" name="se_price" data-type="decimal" id="se_price" value="{{data.se_price}}" tabindex="2"  placeholder="<?php echo esc_attr__('Service price','fat-services-booking');?>"  required >
                            <i class="dollar sign icon"></i>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter price','fat-services-booking');?>
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
                            <input type="text" name="se_tax" id="se_tax" data-type="decimal" data-step="0.5" data-min="0"
                                   tabindex="7"
                                   id="s_tax" value="{{data.se_tax}}">
                            <button class="ui icon button number-increase">
                                <i class="plus-icon"></i>
                            </button>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter tax','fat-services-booking'); ?>
                        </div>
                    </div>

                </div>

                <div class="two fields">
                    <div class="field ">
                        <label><?php echo esc_html__('Minimum Capacity','fat-services-booking');?>
                            <div class="ui icon ui-tooltip" data-content="<?php echo esc_attr__('Minimum number of person per one booking of this service','fat-services-booking');?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui action input number has-button">
                            <button class="ui icon button number-decrease" >
                                <i class="minus-icon"></i>
                            </button>
                            <input type="text" name="se_min_quantity" {{data.se_min_quantity}} data-type="int" data-step="1" data-min="1" id="se_min_quantity" value="{{data.se_min_quantity}}" tabindex="4">
                            <button class="ui icon button number-increase">
                                <i class="plus-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="field ">
                        <label><?php echo esc_html__('Maximum Capacity','fat-services-booking');?>
                            <div class="ui icon ui-tooltip" data-content="<?php echo esc_attr__('Maximum number of person per one booking of this service','fat-services-booking');?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="ui action input number has-button">
                            <button class="ui icon button number-decrease">
                                <i class="minus-icon"></i>
                            </button>
                            <input type="text" name="se_max_quantity" data-type="int" data-step="1" data-min="1" id="se_max_quantity" value="{{data.se_max_quantity}}" tabindex="5">
                            <button class="ui icon button number-increase">
                                <i class="plus-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <div class="ui toggle checkbox">
                            <# if(data.se_price_on_total==1){ #>
                            <input type="checkbox" name="se_price_on_total" id="se_price_on_total" value="1"
                                   checked tabindex="14">
                            <# }else{ #>
                            <input type="checkbox" name="se_price_on_total" id="se_price_on_total" value="1"
                                   tabindex="14">
                            <# } #>
                            <label><?php echo esc_html__('Price on total order','fat-services-booking'); ?>
                                <div class="ui icon ui-tooltip"
                                     data-content="<?php echo esc_attr__('If not selected, total cost of extra service = price of service x number of person, else total cost of extra service is independent number of person ','fat-services-booking'); ?>">
                                    <i class="question circle icon"></i>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <div class="ui toggle checkbox">
                            <# if(data.se_multiple_book==1){ #>
                            <input type="checkbox" name="se_multiple_book" id="se_multiple_book" value="1"
                                   checked tabindex="14">
                            <# }else{ #>
                            <input type="checkbox" name="se_multiple_book" id="se_multiple_book" value="1"
                                   tabindex="14">
                            <# } #>
                            <label><?php echo esc_html__('Allow multiple book at same time slot','fat-services-booking'); ?>
                                <div class="ui icon ui-tooltip"
                                     data-content="<?php echo esc_attr__('If not selected, the service will not be available if it has been previously booked','fat-services-booking'); ?>">
                                    <i class="question circle icon"></i>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <label><?php echo esc_html__('Description','fat-services-booking');?></label>
                        <textarea rows="5" id="se_description" tabindex="6">{{data.se_description}}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="actions">
            <button class="ui basic button fat-close-modal" tabindex="8">
                <i class="times circle outline icon"></i>
                <?php echo esc_html__('Cancel','fat-services-booking');?>
            </button>
            <div class="blue ui buttons">
                <div class="ui button fat-submit-modal" tabindex="7" data-id="{{data.se_id}}" data-onClick="FatSbServiceExtra.processSubmitServiceExtra"
                     data-success-message="<?php echo esc_attr__('Service extra has been saved','fat-services-booking'); ?>">
                    <i class="save outline icon"></i>
                    <?php echo esc_html__('Save','fat-services-booking');?>
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-services-extra-item-template">
    <# _.each(data, function(item){ #>
        <tr data-id="{{item.se_id}}">
            <td>  <div class="ui checkbox">
                    <input type="checkbox" name="se_id" class="check-item" data-id="{{item.se_id}}">
                    <label></label>
                </div></td>
            <td class="fat-se-name" data-label="<?php echo esc_attr__('Name','fat-services-booking');?>">{{item.se_name}}</td>
            <td class="fat-se-duration" data-label="<?php echo esc_attr__('Duration','fat-services-booking');?>">{{item.se_duration_label}}</td>
            <td class="fat-se-price" data-label="<?php echo esc_attr__('Price','fat-services-booking');?>">{{item.se_price}}</td>
            <td class="fat-se-tax" data-label="<?php echo esc_attr__('Tax','fat-services-booking');?>">{{item.se_tax}}</td>
            <td class="fat-se-max-quantity" data-label="<?php echo esc_attr__('Maximum quantity','fat-services-booking');?>">{{item.se_max_quantity}}</td>
            <td class="fat-se-multiple-book" data-label="<?php echo esc_attr__('Allow multiple book','fat-services-booking');?>">
                <# if(item.se_multiple_book!='0'){ #>
                   <?php echo esc_html__('Yes','fat-services-booking');?>
                <# }else{ #>
                    <?php echo esc_html__('No','fat-services-booking');?>
                <# } #>
            </td>
            <td class="fat-se-description" data-label="<?php echo esc_attr__('Description','fat-services-booking');?>">
                {{item.se_description}}
            </td>
            <td>
                <button class=" ui icon button fat-item-bt-inline fat-sb-delete" data-id="{{item.se_id}}" data-onClick="FatSbServiceExtra.btItemDeleteOnClick"
                        data-title="<?php echo esc_attr__('Delete','fat-services-booking');?>">
                    <i class="trash alternate outline icon"></i>
                </button>

                <button class=" ui icon button fat-item-bt-inline fat-sb-edit" data-id="{{item.se_id}}" data-onClick="FatSbServiceExtra.processViewDetail"
                        data-title="<?php echo esc_attr__('Edit','fat-services-booking');?>">
                    <i class="edit outline icon"></i>
                </button>
            </td>
        </tr>
    <# }) #>
</script>