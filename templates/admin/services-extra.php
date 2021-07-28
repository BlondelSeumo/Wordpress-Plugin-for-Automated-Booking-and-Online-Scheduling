<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 11/20/2018
 * Time: 10:42 AM
 */
?>
<div class="fat-sb-header">
    <img src="<?php echo esc_url(FAT_SERVICES_ASSET_URL.'/images/plugin_logo.png');?>">
    <div class="fat-sb-header-title"><?php echo esc_html__('Services Extra','fat-services-booking');?></div>
    <?php
    $part = 'features/services-extra.html';
    include plugin_dir_path(__FILE__) . 'tool-tip.php'; ?>
</div>

<div class="fat-sb-services-extra-container fat-semantic-container fat-min-height-300 fat-pd-right-15">
    <div class="ui card full-width">
        <div class="content has-button-group">
            <div class="fat-inline fat-float-right">
                <button class="ui primary basic button fat-bt-add" data-onClick="FatSbServiceExtra.addServiceExtraOnClick">
                    <i class="cogs icon"></i>
                    <?php echo esc_html__('Add Services Extra','fat-services-booking');?>
                </button>
                <button class="ui negative basic button fat-bt-delete disabled" data-onClick="FatSbServiceExtra.btDeleteGroupOnClick">
                    <i class="trash alternate outline icon"></i>
                    <?php echo esc_html__('Delete','fat-services-booking');?>
                </button>
            </div>
        </div>
        <div class="content">
            <table class="ui single line table fat-sb-list-services-extra">
                <thead>
                <tr>
                    <th>
                        <div class="ui checkbox">
                            <input type="checkbox" name="example" class="table-check-all">
                            <label></label>
                        </div>
                    </th>
                    <th><?php echo esc_html__('Service Extra Name','fat-services-booking');?></th>
                    <th><?php echo esc_html__('Duration','fat-services-booking');?></th>
                    <th><?php echo esc_html__('Price','fat-services-booking');?></th>
                    <th><?php echo esc_html__('Tax','fat-services-booking');?></th>
                    <th><?php echo esc_html__('Maximum quantity','fat-services-booking');?></th>
                    <th><?php echo esc_html__('Allow mutiple book','fat-services-booking');?></th>
                    <th><?php echo esc_html__('Description','fat-services-booking');?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    <tr class="fat-tr-not-found">
                        <td colspan="9">
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