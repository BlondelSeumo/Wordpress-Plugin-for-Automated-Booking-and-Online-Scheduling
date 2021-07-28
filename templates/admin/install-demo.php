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
    <div class="fat-sb-header-title"><?php echo esc_html__('Install Demo Data','fat-services-booking'); ?></div>
</div>
<div class="fat-sb-import-export-container fat-semantic-container fat-min-height-300 fat-pd-right-15">
    <div class="ui placeholder segment">
        <div class="ui icon header">
            <i class="laptop icon"></i>
            <?php echo esc_html__('Please click \'Install demo\'  to import demo data','fat-services-booking');?>
        </div>
        <div class="ui primary button" data-onClick="FatSbImportExport.processInstallDemo" data-success-message="<?php esc_attr_e('Demo data has been installed','fat-services-booking');?>">
            <?php echo esc_html__('Install demo','fat-services-booking');?>
        </div>
    </div>
</div>