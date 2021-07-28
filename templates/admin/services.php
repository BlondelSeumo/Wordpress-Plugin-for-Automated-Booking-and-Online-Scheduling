<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 11/20/2018
 * Time: 10:42 AM
 */

$owl_options = array(
    "responsive"      => array(
        1900 => array(
            "items" =>  5
        ),
        980  => array(
            "items" =>  4
        ),
        768         => array(
            "items" => 3
        ),
        480   => array(
            "items" =>  2
        ),
        320         => array(
            "items" => 1
        )
    ),
    "loop" => false,
    "margin"          => 20,
    "dots"            => true,
    "nav"             => false,
    "autoHeight"        => 'true',
    "autoplay"        => false,
    "autoplayTimeout" => 4000
);
$owl_options = json_encode($owl_options);
$service_mode = FAT_DB_Services::instance();
$categories = $service_mode->get_categories();
$services = array();

$fat_sb_booking = FAT_Services_Booking::getInstance();
$fat_sb_booking->require_file(FAT_SERVICES_DIR_PATH.'/tmpl/services/tmpl-services.php');
$fat_sb_booking->require_file(FAT_SERVICES_DIR_PATH.'/tmpl/services/tmpl-services-extra.php');

?>
<div class="fat-sb-header">
    <img src="<?php echo esc_url(FAT_SERVICES_ASSET_URL.'/images/plugin_logo.png');?>">
    <div class="fat-sb-header-title"><?php echo esc_html__('Services','fat-services-booking');?></div>
    <?php
    $part = 'features/services.html';
    include plugin_dir_path(__FILE__) . 'tool-tip.php'; ?>
</div>
<div class="fat-sb-services-container fat-semantic-container fat-min-height-300 fat-pd-right-15">
    <div class="ui card full-width">
        <div class="content">
            <div class="header thin">
                <?php echo esc_html__('Categories','fat-services-booking');?>
                <div class="toolbox-action-group float-right">
                    <button class="ui primary basic button no-border fat-bt-add-category" data-onClick="FatSbService.addCategoryOnClick">
                        <i class="file alternate outline icon"></i>
                        <?php echo esc_html__('New Category','fat-services-booking');?>
                    </button>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="ui grid ">
                <div class="five column fat-sb-list-services-category" data-owl-options="<?php echo esc_attr($owl_options); ?>">
                    <div class="ui fluid placeholder">
                        <div class="image header">
                            <div class="medium line"></div>
                            <div class="full line"></div>
                        </div>
                        <div class="paragraph">
                            <div class="full line"></div>
                            <div class="medium line"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ui card full-width">
        <div class="content">
            <div class="header thin">
                <?php echo esc_html__('Services','fat-services-booking');?>
                <div class="toolbox-action-group float-right">
                    <div class="ui transparent left icon input ui-search fat-sb-search">
                        <input type="text" id="sc_name_search" data-onKeyUp="FatSbService.serviceNameKeyUp" placeholder="<?php echo esc_attr__('Search ...','fat-services-booking');?>">
                        <i class="search icon"></i>
                        <a class="fat-close" data-onClick="FatSbService.clearSearchOnClick">
                            <i class="times icon"></i>
                        </a>
                    </div>
                    <button class="ui primary basic button no-border fat-bt-add-service" data-onClick="FatSbService.showPopupService">
                        <i class="file alternate outline icon"></i>
                        <?php echo esc_html__('New Service','fat-services-booking');?>
                    </button>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="ui grid fat-sb-list-services">
                <div class="sixteen wide column">
                    <div class="ui fluid placeholder">
                        <div class="image header">
                            <div class="medium line"></div>
                            <div class="full line"></div>
                        </div>
                        <div class="paragraph">
                            <div class="full line"></div>
                            <div class="medium line"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
