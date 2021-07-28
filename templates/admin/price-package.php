<?php
/**
 * Created by PhpStorm.
 * User: PhuongTH
 * Date: 2/6/2020
 * Time: 2:11 PM
 */
?>
<div class="fat-sb-header">
    <img src="<?php echo esc_url(FAT_SERVICES_ASSET_URL.'/images/plugin_logo.png');?>">
    <div class="fat-sb-header-title"><?php echo esc_html__('Price package','fat-services-booking');?></div>
    <?php
    $part = 'features/customers.html';
    include plugin_dir_path(__FILE__) . 'tool-tip.php'; ?>
</div>
<div class="fat-sb-price-package-container fat-semantic-container fat-min-height-300 fat-pd-right-15">
    <div class="ui card full-width">
        <div class="content has-button-group">
            <div class="toolbox-action-group">
                <div class="fat-sb-button-group">
                    <button class="ui primary basic button fat-bt-add" data-onClick="FatSbPricePackage.btAddOnClick">
                        <i class="money bill alternate outline icon"></i>
                        <?php echo esc_html__('Add package','fat-services-booking');?>
                    </button>

                    <button class="ui negative basic button fat-bt-delete disabled" data-onClick="FatSbPricePackage.processDelete">
                        <i class="trash alternate outline icon"></i>
                        <?php echo esc_html__('Delete','fat-services-booking');?>
                    </button>
                </div>
            </div>
        </div>
        <div class="content">
            <table class="ui single line table fat-sb-list-price-package">
                <thead>
                <tr>
                    <th class="column-check-all">
                        <div class="ui checkbox">
                            <input type="checkbox" name="example" class="table-check-all">
                            <label></label>
                        </div>
                    </th>
                    <th>
                        <?php echo esc_html__('Package','fat-services-booking');?>
                    </th>
                    <th><?php echo esc_html__('Price of package','fat-services-booking');?></th>
                    <th><?php echo esc_html__('Price to pay for the booking service ','fat-services-booking');?></th>
                    <th><?php echo esc_html__('Notes','fat-services-booking');?></th>
                    <th><?php echo esc_html__('Create Date','fat-services-booking');?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr class="fat-tr-not-found">
                    <td colspan="7">
                        <div class="ui fluid placeholder">
                            <div class="line"></div>
                            <div class="line"></div>
                            <div class="line"></div>
                            <div class="line"></div>
                            <div class="line"></div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
