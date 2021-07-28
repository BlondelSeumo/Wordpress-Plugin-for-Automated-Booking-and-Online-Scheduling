<?php
$fat_db_setting = FAT_DB_Setting::instance();
$custom_css = $fat_db_setting->get_custom_css();
if ($custom_css): ?>
    <style type='text/css'>
        <?php echo sprintf('%s',$custom_css); ?>
    </style>
<?php endif; ?>