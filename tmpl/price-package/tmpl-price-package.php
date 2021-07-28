<?php
/**
 * Created by PhpStorm.
 * User: PhuongTH
 * Date: 2/6/2020
 * Time: 2:53 PM
 */
?>
<script type="text/html" id="tmpl-fat-sb-price-package-template">
    <div class="ui modal tiny fat-semantic-container fat-price-package-form">
        <div class="header fat-sb-popup-title"><?php echo esc_html__('Add new package','fat-services-booking');?></div>
        <div class="scrolling content">
            <div class="ui form">
                <div class="one fields">
                    <div class="ui image-field " id="pk_image_id" data-image-id="{{data.pk_image_id}}"
                         data-image-url="{{data.pk_image_url}}">
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="name"><?php echo esc_html__('Package name','fat-services-booking');?><span class="required"> *</span></label>
                        <div class="ui left icon input ">
                            <input type="text" name="pk_name" id="pk_name" value="{{data.pk_name}}" tabindex="1" placeholder="<?php echo esc_attr__('Name','fat-services-booking');?>" required >
                            <i class="edit outline icon"></i>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter package name','fat-services-booking');?>
                        </div>
                    </div>
                </div>

                <div class="field ">
                    <label><?php echo esc_html__('Price','fat-services-booking'); ?> <span
                            class="required"> *</span>
                        <div class="ui icon ui-tooltip"
                             data-content="<?php echo esc_attr__('This is the amount you need to pay to buy the package','fat-services-booking'); ?>">
                            <i class="question circle icon"></i>
                        </div>
                    </label>
                    <div class="ui left icon input number fat-sb-price-package-amount" >
                        <input type="text" name="pk_price"  data-type="decimal" id="pk_price" value="{{data.pk_price}}" tabindex="2"
                               placeholder="<?php echo esc_attr__('Price','fat-services-booking'); ?>" required>
                        <i class="dollar sign icon"></i>
                    </div>
                    <div class="field-error-message">
                        <?php echo esc_html__('Please enter price amount','fat-services-booking'); ?>
                    </div>
                </div>

                <div class="field ">
                    <label><?php echo esc_html__('Price for payment service','fat-services-booking'); ?> <span
                            class="required"> *</span>
                        <div class="ui icon ui-tooltip"
                             data-content="<?php echo esc_attr__('This is the amount that will be used to pay for the booking service','fat-services-booking'); ?>">
                            <i class="question circle icon"></i>
                        </div>
                    </label>
                    <div class="ui left icon input number fat-sb-price-package-amount" >
                        <input type="text" name="pk_price_for_payment"  data-type="decimal" id="pk_price_for_payment" value="{{data.pk_price_for_payment}}" tabindex="3"
                               placeholder="<?php echo esc_attr__('Price for payment service','fat-services-booking'); ?>" required>
                        <i class="dollar sign icon"></i>
                    </div>
                    <div class="field-error-message">
                        <?php echo esc_html__('Please enter price for payment service','fat-services-booking'); ?>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <label><?php echo esc_html__('Notes','fat-services-booking');?></label>
                        <textarea rows="5" id="pk_description" name="pk_description" tabindex="4">{{data.pk_description}}</textarea>
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
                <div class="ui button fat-submit-modal" data-onClick="FatSbPricePackage.processSubmitPackage"
                     data-id="{{data.pk_id}}" data-success-message="<?php echo esc_attr__('Package has been saved','fat-services-booking'); ?>">
                    <i class="save outline icon"></i>
                    <?php echo esc_html__('Save','fat-services-booking');?>
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-price-package-item-template">
    <# _.each(data, function(item){ #>
    <tr data-id="{{item.pk_id}}">
        <td>
            <div class="ui checkbox">
                <input type="checkbox" name="pk_id" class="check-item"  data-id="{{item.pk_id}}">
                <label></label>
            </div>
        </td>
        <td class="fat-pk-name" data-label="<?php echo esc_attr__('Name','fat-services-booking');?>">{{item.pk_name }}</td>
        <td class="fat-pk-price" data-label="<?php echo esc_attr__('Price','fat-services-booking');?>">{{item.pk_price}}</td>
        <td class="fat-pk-price-for-payment" data-label="<?php echo esc_attr__('Price For Payment Service','fat-services-booking');?>">{{item.pk_price_for_payment}}</td>
        <td class="fat-pk-note" data-label="<?php echo esc_attr__('Notes','fat-services-booking');?>">{{item.pk_description}}</td>
        <td class="fat-pk-note" data-label="<?php echo esc_attr__('Create Date','fat-services-booking');?>">{{item.pk_create_date}}</td>
        <td>
            <button class=" ui icon button fat-item-bt-inline fat-sb-delete" data-onClick="FatSbPricePackage.processDelete"
                    data-id="{{item.pk_id}}" data-title="<?php echo esc_attr__('Delete','fat-services-booking');?>">
                <i class="trash alternate outline icon"></i>
            </button>

            <button class=" ui icon button fat-item-bt-inline fat-sb-edit" data-onClick="FatSbPricePackage.processViewDetail"
                    data-id="{{item.pk_id}}" data-title="<?php echo esc_attr__('Edit','fat-services-booking');?>">
                <i class="edit outline icon"></i>
            </button>
        </td>
    </tr>
    <# }) #>
</script>
