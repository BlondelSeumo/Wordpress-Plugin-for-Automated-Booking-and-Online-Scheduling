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
    <div class="fat-sb-header-title"><?php echo esc_html__('Employees','fat-services-booking');?></div>
    <?php
    $part = 'features/employees.html';
    include plugin_dir_path(__FILE__) . 'tool-tip.php'; ?>
</div>
<div class="fat-sb-employees-container fat-semantic-container fat-min-height-300 fat-pd-right-15">
    <div class="ui card full-width">
        <div class="content">
            <div class="toolbox-action-group">
                <div class="ui transparent left icon input ui-search">
                    <input type="text" id="e_name" name="e_name" autocomplete="nope" placeholder="Search employees" data-onKeyUp="FatSbEmployees.nameOnKeyUp">
                    <i class="search icon"></i>
                    <a class="fat-close" data-onClick="FatSbEmployees.closeSearchOnClick">
                        <i class="times icon"></i>
                    </a>
                </div>

                <div class="fat-checkbox-dropdown-wrap fat-sb-services-dic fat-mg-right-10">
                    <select multiple="multiple" name="s_id" id="s_id" data-onChange="FatSbEmployees.sumoSearchOnChange"
                            data-placeholder="<?php echo esc_attr__('Select services'); ?>"
                            data-search-text="<?php echo esc_attr__('Enter service\'s name'); ?>"
                            data-caption-format="<?php echo esc_attr__('Service selected'); ?>"
                            class="SumoUnder fat-sb-sumo-select" tabindex="-1"
                            data-prev-value="">
                    </select>
                </div>

                <div class="fat-checkbox-dropdown-wrap fat-sb-locations-dic fat-mg-right-10">
                    <select multiple="multiple" name="e_location_ids" id="e_location_ids" data-onChange="FatSbEmployees.sumoSearchOnChange"
                            data-placeholder="<?php echo esc_attr__('Select locations'); ?>"
                            data-search-text="<?php echo esc_attr__('Enter location\'s name'); ?>"
                            data-caption-format="<?php echo esc_attr__('Locations selected'); ?>"
                            class="SumoUnder fat-sb-sumo-select" tabindex="-1"
                            data-prev-value="">
                    </select>
                </div>

                <button class="ui primary basic button fat-bt-add fat-fl-right" data-onClick="FatSbEmployees.showPopupEmployee">
                    <i class="user plus icon"></i>
                    <?php echo esc_html__('Add employee','fat-services-booking');?>
                </button>
            </div>

        </div>
        <div class="content">
            <div class="ui grid fat-sb-list-employees">
                <div class="doubling six column row">
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