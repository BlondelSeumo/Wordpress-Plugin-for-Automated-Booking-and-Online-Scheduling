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
    <div class="fat-sb-header-title"><?php echo esc_html__('Calendar','fat-services-booking');?></div>
    <?php
    $part = 'features/calendar.html';
    include plugin_dir_path(__FILE__) . 'tool-tip.php'; ?>
</div>
<div class="fat-sb-calendar-container fat-semantic-container fat-min-height-300 fat-pd-right-15">

    <div class="ui card full-width">
        <div class="content has-button-group">
            <div class="toolbox-action-group">

                <div class="ui transparent left date-input input no-border-radius">
                    <?php
                    $start_date = new DateTime();
                    $end_date = new DateTime();
                    $end_date->modify('+6 day');
                    $date_format = get_option('date_format');
                    $locale = get_locale();
                    $locale = explode('_',$locale)[0];
                    ?>
                    <input type="text"  class="date-range-picker"  name="date_of_book" id="date_of_book" data-auto-update="1" data-onChange="FatSbCalendar.dateOnChange"
                           data-start="<?php echo esc_attr($start_date->format('Y-m-d'));?>" data-end="<?php echo esc_attr($end_date->format('Y-m-d'));?>"
                           data-locale="<?php echo esc_attr($locale);?>"
                           data-start-init="<?php echo date_i18n($date_format, $start_date->format('U'));?>"
                           data-end-init="<?php echo date_i18n($date_format, $end_date->format('U'));?>" >
                </div>

                <div class="fat-checkbox-dropdown-wrap fat-mg-right-10 fat-sb-location-dic">
                    <select multiple="multiple" name="location" data-onChange="FatSbCalendar.sumoSearchOnChange"
                            data-prev-value=""
                            data-placeholder="<?php echo esc_attr__('Select location'); ?>"
                            data-caption-format="<?php echo esc_attr__('Location selected'); ?>"
                            data-search-text="<?php echo esc_attr__('Enter location\'s name'); ?>"
                            id="location" class="SumoUnder fat-sb-sumo-select" tabindex="-1">
                    </select>
                </div>

                <div class="fat-checkbox-dropdown-wrap fat-mg-right-10 fat-sb-employee-dic">
                    <select multiple="multiple" name="employees" data-onChange="FatSbCalendar.sumoSearchOnChange"
                            data-prev-value=""
                            data-placeholder="<?php echo esc_attr__('Select employees'); ?>"
                            data-caption-format="<?php echo esc_attr__('Employee selected'); ?>"
                            data-search-text="<?php echo esc_attr__('Enter employee\'s name'); ?>"
                            id="employees" class="SumoUnder fat-sb-sumo-select" tabindex="-1">
                    </select>
                </div>

                <div class="fat-checkbox-dropdown-wrap fat-mg-right-10 fat-sb-customer-dic">
                    <select multiple="multiple" name="customers" data-onChange="FatSbCalendar.sumoSearchOnChange"
                            data-prev-value=""
                            data-placeholder="<?php echo esc_attr__('Select customer'); ?>"
                            data-caption-format="<?php echo esc_attr__('Customer selected'); ?>"
                            data-search-text="<?php echo esc_attr__('Enter customer\'s name'); ?>"
                            id="customer" class="SumoUnder fat-sb-sumo-select" tabindex="-1">
                    </select>
                </div>

                <div class="fat-checkbox-dropdown-wrap fat-mg-right-10 fat-sb-service-dic">
                    <select multiple="multiple" name="services" data-onChange="FatSbCalendar.sumoSearchOnChange"
                            data-prev-value = ""
                            data-placeholder="<?php echo esc_attr__('Select services'); ?>"
                            data-search-text="<?php echo esc_attr__('Enter service\'s name'); ?>"
                            data-caption-format="<?php echo esc_attr__('Sevices selected'); ?>"
                            id="services" class="SumoUnder fat-sb-sumo-select" tabindex="-1">
                    </select>
                </div>

                <div class="fat-sb-button-group">
                    <button class="ui primary basic button fat-bt-add" data-onClick="FatSbBooking.showPopupBooking"
                            data-submit-callback="FatSbCalendar.addBookingToCalendar">
                        <i class="file alternate outline icon"></i>
                        <?php echo esc_html__('New booking','fat-services-booking'); ?>
                    </button>
                </div>
            </div>

        </div>
        <div class="content">
            <?php $setting = FAT_DB_Setting::instance();
            $setting = $setting->get_setting();?>
            <div class="fat-sb-calendar" id='fat_sb_calendar' data-view="<?php echo (isset($setting['calendar_view']) ? $setting['calendar_view'] : 'month'); ?>"
            data-locale="<?php echo esc_attr($locale);?>">
                <div class="ui active inverted dimmer">
                    <div class="ui text loader"><?php echo esc_html__('Loading','fat-services-booking');?></div>
                </div>
            </div>
        </div>
    </div>

</div>

<script type="text/html" id="tmpl-fat-sb-popup-calendar-template">
    <div data-popup-id="" class="fat-sb-calendar-popup ui flowing popup top left transition hidden">
        <h4 class="ui header">{{data.customer}}</h4>
        <div class="fat-sb-calendar-time">
           <i class="clock icon"></i><span>{{data.time}}</span>
        </div>
        <div class="fat-sb-calendar-service">
            <i class="cog icon"></i><span>{{data.service}}</span>
        </div>
        <div class="fat-sb-calendar-location">
           <i class="map marker alternate icon"></i><span>{{data.location}}</span>
            <div class="meta">{{data.location_address}}</div>
        </div>
        <div class="fat-sb-calendar-employee">
            <img src="{{data.e_avatar_url}}">
            <span>{{data.employee}}</span>
        </div>
        <# if(data.b_editable == 1){ #>
            <div class="fat-sb-calendar-edit fat-text-right">
                <button class="circular ui icon primary button" data-id="{{data.id}}" data-onClick="FatSbBooking.showPopupBooking"
                        data-submit-callback="FatSbCalendar.addBookingToCalendar">
                    <i class="edit outline icon"></i>
                </button>
            </div>
        <# } #>
    </div>
</script>