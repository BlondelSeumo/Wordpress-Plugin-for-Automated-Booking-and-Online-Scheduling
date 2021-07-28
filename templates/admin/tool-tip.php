<?php
/**
 * Created by PhpStorm.
 * User: RoninWP
 * Date: 10/4/2019
 * Time: 9:41 AM
 */
$document_url = 'http://document.roninwp.com/plugins/services-booking/';
$document_url .= isset($part) ? $part : 'getting-started.html';
?>
<div class="fat-sb-booking-header-tooltip">
    <a href="#" class="fat-sb-shortcode-tooltip fat-has-popup" data-popup-id="popup_tooltip_shortcode" data-last-resort="bottom right">
        <i class="code icon"></i>
    </a>
    <a href="<?php echo esc_url($document_url);?>" target="_blank"  class="fat-sb-document-tooltip ui icon ui-tooltip" data-position="bottom right"  data-content="Click to see the documentation for this function">
        <i class="question circle outline icon"></i>
    </a>
    <div class="ui flowing popup transition hidden popup-tooltip-shortcode" data-popup-id="popup_tooltip_shortcode">
        <h5><?php echo esc_html__('Click the copy icon to copy the shortcode to the clipboard and paste it into the page that you want display','fat-services-booking');?></h5>
        <ul>
            <li>
                <span>Step layout:</span>
                <span class="fat-sb-shortcode" id="fat-step-shortcode">[fat_sb_booking]</span>
                <a href="#" class="fat-sb-copy-clipboard"  data-clipboard-target="#fat-step-shortcode"><i class="clipboard outline icon"></i></a>
            </li>
            <li>
                <span>Booking form with tab:</span>
                <span class="fat-sb-shortcode" id="fat-services-shortcode"> [fat_sb_booking layout="services"]</span>
                <a href="#" class="fat-sb-copy-clipboard"  data-clipboard-target="#fat-services-shortcode"><i class="clipboard outline icon"></i></a>
            </li>
            <li>
                <span>Booking form without tab:</span>
                <span class="fat-sb-shortcode" id="fat-services-no-tab-shortcode"> [fat_sb_booking layout="services-no-tab"]</span>
                <a href="#" class="fat-sb-copy-clipboard"  data-clipboard-target="#fat-services-no-tab-shortcode"><i class="clipboard outline icon"></i></a>
            </li>
            <li>
                <span>One Service:</span>
                <span class="fat-sb-shortcode" id="fat-one-services-shortcode"> [fat_sb_booking layout="one-services"]</span>
                <a href="#" class="fat-sb-copy-clipboard"  data-clipboard-target="#fat-one-services-shortcode"><i class="clipboard outline icon"></i></a>
            </li>
            <li>
                <span>Popup button:</span>
                <span class="fat-sb-shortcode" id="fat-button-shortcode"> [fat_sb_booking_button label="Booking now" background_color="#2185d0" color="#fff" font_size="14px"]</span>
                <a href="#" class="fat-sb-copy-clipboard"   data-clipboard-target="#fat-button-shortcode"><i class="clipboard outline icon"></i></a>
            </li>
            <li>
                <span>One Service & Provider:</span>
                <span class="fat-sb-shortcode" id="fat-one-service-provider-shortcode"> [fat_sb_booking layout="one-service-provider"]</span>
                <a href="#" class="fat-sb-copy-clipboard"    data-clipboard-target="#fat-one-service-provider-shortcode"><i class="clipboard outline icon"></i></a>
            </li>
        </ul>
    </div>
</div>
