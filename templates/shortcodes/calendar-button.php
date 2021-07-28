<?php
/**
 * Created by PhpStorm.
 * User: RoninWP
 * Date: 9/18/2019
 * Time: 5:09 PM
 */
$bid = isset($_REQUEST['bid']) ? $_REQUEST['bid'] : 0;
if(!$bid){
    return;
}
?>
<div class="fat-sb-booking-calendar-button-wrap">
    <div class="fat-mg-top-15">
        <button class="ui primary button fat-bt-add-google-calendar fat-bt" data-id="<?php echo esc_attr($bid);?>"
                data-onClick="FatSbCalendarButton_FE.addToGoogleCalendar">
            <?php esc_html_e('Add to Google calendar','fat-services-booking'); ?>
        </button>

        <button class="ui primary button fat-bt-add-icalendar fat-bt" data-id="<?php echo esc_attr($bid);?>"
                data-onClick="FatSbCalendarButton_FE.addToICalendar">
            <?php esc_html_e('Add to iCalendar','fat-services-booking'); ?>
        </button>
    </div>
</div>
