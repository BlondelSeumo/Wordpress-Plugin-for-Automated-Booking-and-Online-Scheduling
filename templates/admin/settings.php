<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 3/4/2019
 * Time: 3:54 PM
 */
?>
<div class="fat-sb-header">
    <img src="<?php echo esc_url(FAT_SERVICES_ASSET_URL.'/images/plugin_logo.png');?>">
    <div class="fat-sb-header-title"><?php echo esc_html__('Settings','fat-services-booking');?></div>
    <?php
    $part = 'configuring/general-setting.html';
    include plugin_dir_path(__FILE__) . 'tool-tip.php'; ?>
</div>
<div class="fat-sb-settings-container fat-semantic-container fat-min-height-300 fat-pd-right-15">
    <div class="ui card full-width">
        <div class="content">
            <div class="ui grid">
                <div class="five wide column">
                    <div class="ui items">
                        <div class="item" data-onClick="FatSbSetting.itemOnClick" data-template="fat-sb-setting-general-template">
                            <div class="image">
                                <i class="cogs icon"></i>
                            </div>
                            <div class="content">
                                <a class="header"><?php echo esc_html__('General Settings','fat-services-booking');?></a>
                                <div class="meta">
                                    <span><?php echo esc_html__('Use this setting to set up day limit, tax default, booking default status, item per page','fat-services-booking');?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="five wide column">
                    <div class="ui items">
                        <div class="item" data-onClick="FatSbSetting.itemOnClick" data-template="fat-sb-setting-company-template">
                            <div class="image">
                                <i class="building icon"></i>
                            </div>
                            <div class="content">
                                <a class="header"><?php echo esc_html__('Company','fat-services-booking');?></a>
                                <div class="meta">
                                    <span><?php echo esc_html__('Use this setting to set up your company logo, name, address, phone','fat-services-booking');?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="five wide column">
                    <div class="ui items">
                        <div class="item" data-onClick="FatSbSetting.itemOnClick" data-template="fat-sb-setting-notification-template">
                            <div class="image">
                                <i class="envelope outline icon"></i>
                            </div>
                            <div class="content">
                                <a class="header"><?php echo esc_html__('Email notification','fat-services-booking');?></a>
                                <div class="meta">
                                    <span><?php echo esc_html__('Use this setting to set up send mail and action after booking','fat-services-booking');?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="five wide column">
                    <div class="ui items">
                        <div class="item" data-onClick="FatSbSetting.itemOnClick" data-template="fat-sb-setting-sms-notification-template">
                            <div class="image">
                                <i class="mobile alternate icon"></i>
                            </div>
                            <div class="content">
                                <a class="header"><?php echo esc_html__('SMS notification','fat-services-booking');?></a>
                                <div class="meta">
                                    <span><?php echo esc_html__('Use this setting to set up send SMS and action after booking','fat-services-booking');?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="five wide column">
                    <div class="ui items">
                        <div class="item" data-onClick="FatSbSetting.itemOnClick" data-template="fat-sb-setting-payment-template">
                            <div class="image">
                                <i class="dollar sign icon"></i>
                            </div>
                            <div class="content">
                                <a class="header"><?php echo esc_html__('Payments','fat-services-booking');?></a>
                                <div class="meta">
                                    <span><?php echo esc_html__('Use this setting to set up currency, paypal, stripe and onsite payment','fat-services-booking');?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="five wide column">
                    <div class="ui items">
                        <div class="item" data-onClick="FatSbSetting.itemOnClick" data-template="fat-sb-setting-working-hour-template">
                            <div class="image">
                                <i class="clock outline icon"></i>
                            </div>
                            <div class="content">
                                <a class="header"><?php echo esc_html__('Working hours','fat-services-booking');?></a>
                                <div class="meta">
                                    <span><?php echo esc_html__('Use this setting to set up working hour and day off for your company','fat-services-booking');?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="five wide column">
                    <div class="ui items">
                        <div class="item" data-onClick="FatSbSetting.itemOnClick" data-template="fat-sb-setting-google-api-template">
                            <div class="image">
                                <i class="google icon"></i>
                            </div>
                            <div class="content">
                                <a class="header"><?php echo esc_html__('Google API','fat-services-booking');?></a>
                                <div class="meta">
                                    <span><?php echo esc_html__('Use this setting to set up Google map and Google calendar API','fat-services-booking');?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="five wide column">
                    <div class="ui items">
                        <div class="item" data-onClick="FatSbSetting.itemOnClick" data-template="fat-sb-setting-user-role-template">
                            <div class="image">
                                <i class="user outline icon"></i>
                            </div>
                            <div class="content">
                                <a class="header"><?php echo esc_html__('User Role','fat-services-booking');?></a>
                                <div class="meta">
                                    <span><?php echo esc_html__('Use this setting to set up user role','fat-services-booking');?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>