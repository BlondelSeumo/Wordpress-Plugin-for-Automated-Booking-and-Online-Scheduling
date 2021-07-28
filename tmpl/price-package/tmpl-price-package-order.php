<?php
/**
 * Created by PhpStorm.
 * User: PhuongTH
 * Date: 2/7/2020
 * Time: 8:08 PM
 */
?>
<script type="text/html" id="tmpl-fat-sb-package-order-item-template">
    <# _.each(data, function(item){ #>
    <tr data-id="{{item.pko_id}}">
       <!-- <td>
            <div class="ui checkbox">
                <input type="checkbox" name="pko_id" class="check-item"  data-id="{{item.pko_id}}">
                <label></label>
            </div>
        </td>-->
        <td class="fat-pk-name" data-label="<?php echo esc_attr__('Order Date','fat-services-booking');?>">{{item.pko_create_date }}</td>
        <td class="fat-pk-user" data-label="<?php echo esc_attr__('User','fat-services-booking');?>">{{item.pko_user_email}}</td>
        <td class="fat-pk-user" data-label="<?php echo esc_attr__('Package name','fat-services-booking');?>">{{item.pk_name}}</td>
        <td class="fat-pk-price" data-label="<?php echo esc_attr__('Price','fat-services-booking');?>">{{item.pk_price}}</td>
        <td class="fat-pk-price-for-payment" data-label="<?php echo esc_attr__('Price For Payment Service','fat-services-booking');?>">{{item.pk_price_for_payment}}</td>
        <td class="fat-pk-description" data-label="<?php echo esc_attr__('Description','fat-services-booking');?>">{{item.pko_description}}</td>
        <td class="fat-pk-gateway" data-label="<?php echo esc_attr__('Gateway','fat-services-booking');?>">{{item.pko_gateway_type}}</td>
        <td class="fat-pk-gateway-status" data-label="<?php echo esc_attr__('Gateway status','fat-services-booking');?>">
            <# if(item.pko_gateway_status=='1' || item.pko_gateway_status=='approved'){ #>
                    <?php echo esc_html__('Success','fat-services-booking');?>
            <# }else{ #>
                    <?php echo esc_html__('Cancel','fat-services-booking');?>
            <# } #>
        </td>
        <td class="fat-pk-gateway-status" data-label="<?php echo esc_attr__('Gateway status','fat-services-booking');?>">
            <# if(item.pko_gateway_type=='onsite'){ #>
                <button class=" ui icon button fat-item-bt-inline fat-sb-delete" data-onClick="FatSbPricePackageOrder.processDelete"
                        data-id="{{item.pko_id}}" data-title="<?php echo esc_attr__('Delete','fat-services-booking');?>">
                    <i class="trash alternate outline icon"></i>
                </button>
            <# } #>
        </td>

    </tr>
    <# }) #>
</script>
