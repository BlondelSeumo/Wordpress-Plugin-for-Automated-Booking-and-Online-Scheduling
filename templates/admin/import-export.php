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
    <div class="fat-sb-header-title"><?php echo esc_html__('Import Export','fat-services-booking'); ?></div>
    <?php
    $part = 'features/import-export.html';
    include plugin_dir_path(__FILE__) . 'tool-tip.php'; ?>
</div>
<div class="fat-sb-import-export-container fat-semantic-container fat-min-height-300 fat-pd-right-15">
    <div class="ui placeholder segment">
        <div class="ui two column stackable center aligned grid">
            <div class="ui vertical divider"><?php echo esc_html__('OR','fat-services-booking');?></div>
            <div class="middle aligned row">
                <div class="column">
                    <div class="ui icon header">
                        <i class="download icon"></i>
                        <div class="description"><?php esc_attr_e('Please select .json file and click import button','fat-services-booking');?></div>
                    </div>
                    <div class="field">
                        <form method="post" enctype="multipart/form-data" id="form_import">
                            <p>
                                <input type="file" name="import_file"/>
                                <input type="hidden" name="fat_sb_action" value="import" />
                                <?php wp_nonce_field( 'fat_sb_import_nonce', 'fat_sb_import_nonce' ); ?>
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr__('Import','fat-services-booking');?>">
                            </p>
                        </form>
                    </div>
                    <?php do_action('fat_import_notices');?>
                </div>
                <div class="column">
                    <div class="ui icon header">
                        <i class="upload icon"></i>
                        <div class="description"><?php esc_attr_e('Please select data that need export and click export button','fat-services-booking');?></div>
                    </div>
                    <div class="fat-sb-select-list">
                        <div class="ui checkbox">
                            <input type="checkbox" name="services" id="services" value="services">
                            <label for="services"><?php echo esc_html__('Services','fat-services-booking');?></label>
                        </div>
                        <div class="ui checkbox">
                            <input type="checkbox" name="employees" id="employees" value="employees">
                            <label for="employees"><?php echo esc_html__('Employees','fat-services-booking');?></label>
                        </div>
                        <div class="ui checkbox">
                            <input type="checkbox" name="customers" id="customers" value="customers">
                            <label for="customers"><?php echo esc_html__('Customers','fat-services-booking');?></label>
                        </div>
                        <div class="ui checkbox">
                            <input type="checkbox" name="location" id="location" value="location">
                            <label for="location"><?php echo esc_html__('Location','fat-services-booking');?></label>
                        </div>
                        <div class="ui checkbox">
                            <input type="checkbox" name="coupon" id="coupon" value="coupon">
                            <label for="coupon"><?php echo esc_html__('Coupon','fat-services-booking');?></label>
                        </div>
                        <div class="ui checkbox">
                            <input type="checkbox" name="booking" id="booking" value="booking">
                            <label for="booking"><?php echo esc_html__('Booking','fat-services-booking');?></label>
                        </div>
                        <div class="ui checkbox">
                            <input type="checkbox" name="settings" id="settings" value="settings">
                            <label for="settings"><?php echo esc_html__('Settings','fat-services-booking');?></label>
                        </div>
                    </div>
                    <div class="ui primary button" data-onClick="FatSbImportExport.processExport" data-invalid-message="<?php echo esc_attr__('Please select data that need export','fat-services-booking');?>">
                       <?php echo esc_html__('Export','fat-services-booking');?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>