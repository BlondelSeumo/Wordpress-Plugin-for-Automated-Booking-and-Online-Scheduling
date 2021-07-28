<?php
/**
 * Created by PhpStorm.
 * User: PhuongTH
 * Date: 8/22/2020
 * Time: 9:36 AM
 */
$action = isset($_REQUEST['action']) && $_REQUEST['action'] ? $_REQUEST['action'] : '';
$key = isset($_REQUEST['key']) && $_REQUEST['key'] ? $_REQUEST['key'] : '';
$login = isset($_REQUEST['login']) && $_REQUEST['login'] ? $_REQUEST['login'] : '';
$is_reset_pass = $action == 'rp' && $key && $login;
$prev_page =  wp_get_referer();
?>
<div class="fat-sb-login-container" style="opacity: 0" data-prev="<?php echo esc_url($prev_page);?>">
    <?php if (!$is_reset_pass): ?>
    <div class="fat-login-section-wrap">
        <h1 class="fat-text-center"><?php esc_html_e('Login', 'fat-service-booking'); ?></h1>
        <div class="fat-login-container">
            <div class="fat-login-inner">
                <div class="fat-login-form">
                    <div class="fat-tab-menu">
                        <a class="active item" data-tab="login"
                           data-onClick="FatSbBookingLogin.tabClick"><?php esc_html_e('Login', 'fat-service-booking'); ?></a>
                        <a class="item" data-tab="sign-up"
                           data-onClick="FatSbBookingLogin.tabClick"><?php esc_html_e('Sign up', 'fat-service-booking'); ?></a>
                    </div>

                    <div class="fat-tabs active login" data-tab="login">
                        <div class="fat-form">
                            <div class="fat-field ">
                                <input class="fat-transition" type="email" name="u_email" id="u_email" required
                                       data-onChange="FatSbMain_FE.resetEmailValidateField"
                                       placeholder="<?php esc_html_e('Email', 'fat-service-booking'); ?>">
                                <div class="field-error-message">
                                    <?php echo esc_html__('Please enter your email', 'fat-services-booking'); ?>
                                </div>
                            </div>
                            <div class="fat-field ">
                                <input class="fat-transition" type="password" name="u_pass" id="u_pass" required
                                       data-onChange="FatSbMain_FE.resetValidateField"
                                       placeholder="<?php esc_html_e('Password', 'fat-service-booking'); ?>">
                                <div class="field-error-message">
                                    <?php echo esc_html__('Please enter your password', 'fat-services-booking'); ?>
                                </div>
                            </div>
                            <div class="fat-field fat-remember-section">
                            <span>
                                <input type="checkbox" name="remember_me" id="remember_me""><label
                                    for="remember_me"><?php esc_html_e('Remember me', 'fat-service-booking'); ?></label>
                            </span>
                                <a href="javascript:" class="fat-forgot-pass" data-onClick="FatSbBookingLogin.showForgotPass"><?php esc_html_e('Forgot password ?', 'fat-service-booking'); ?></a>
                            </div>
                            <div class="fat-field fat-mg-top-35">
                                <div class="fat-login-message fat-sb-hidden fat-mg-bottom-5"></div>
                                <button class="fat-bt-login fat-bt fat-bt-full-size fat-bt-main-color fat-transition"
                                        data-onClick="FatSbBookingLogin.processLogin">
                                    <?php esc_html_e('Login', 'fat-service-booking'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="fat-tabs sign-up" data-tab="sign-up">
                        <div class="fat-form">
                            <div class="fat-field ">
                                <input class="fat-transition " type="email" name="u_email" id="u_email" readonly
                                       required data-onChange="FatSbMain_FE.resetEmailValidateField"
                                       onfocus="this.removeAttribute('readonly');"
                                       placeholder="<?php esc_html_e('Email', 'fat-service-booking'); ?>">
                                <div class="field-error-message">
                                    <?php echo esc_html__('Please enter your email', 'fat-services-booking'); ?>
                                </div>
                            </div>
                            <div class="fat-field ">
                                <input class="fat-transition " type="text" name="u_name" id="u_name"
                                       required placeholder="<?php esc_html_e('Name', 'fat-service-booking'); ?>">
                                <div class="field-error-message">
                                    <?php echo esc_html__('Please enter your name', 'fat-services-booking'); ?>
                                </div>
                            </div>
                            <div class="fat-field ">
                                <input class="fat-transition " type="text" name="u_surname" id="u_surname"
                                       required placeholder="<?php esc_html_e('Surname', 'fat-service-booking'); ?>">
                                <div class="field-error-message">
                                    <?php echo esc_html__('Please enter your surname', 'fat-services-booking'); ?>
                                </div>
                            </div>
                            <div class="fat-field fat-pass">
                                <input class="fat-transition" type="password" name="u_pass" id="u_pass" required
                                       readonly data-onChange="FatSbMain_FE.resetValidateField"
                                       onfocus="this.removeAttribute('readonly');"
                                       placeholder="<?php esc_html_e('Password (6 characters minimum)', 'fat-service-booking'); ?>">
                                <div class="field-error-message">
                                    <?php echo esc_html__('Please enter your password', 'fat-services-booking'); ?>
                                </div>
                            </div>
                            <div class="fat-field fat-re-pass ">
                                <input class="fat-transition" type="password" name="u_re_pass" id="u_re_pass"
                                       readonly required data-onChange="FatSbMain_FE.resetValidateField"
                                       onfocus="this.removeAttribute('readonly');"
                                       placeholder="<?php esc_html_e('Password confirmation', 'fat-service-booking'); ?>">
                                <div class="field-error-message">
                                    <?php echo esc_html__('Please enter your password confirmation', 'fat-services-booking'); ?>
                                </div>
                            </div>

                            <div class="fat-field fat-mg-top-35">
                                <div class="fat-sign-up-message fat-sb-hidden fat-mg-bottom-5"></div>
                                <button class="fat-bt-signup fat-bt fat-bt-full-size fat-bt-main-color fat-transition"
                                        data-onClick="FatSbBookingLogin.processSignUp"><?php esc_html_e('Sign up', 'fat-service-booking'); ?></button>
                            </div>

                        </div>

                        <div class="fat-sign-up-notifier fat-sb-hidden">
                            <?php echo esc_html__('Hurray! You are registered. Please check your e-mail to activate your Account.', 'fat-service-booking'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fat-forgot-section-wrap">
        <h2 class="fat-text-center"><?php esc_html_e('Forgot your password?', 'fat-services-booking'); ?></h2>
        <div class="fat-forgot-container">
            <div class="fat-forgot-inner">
                <div class="fat-forgot-form">
                    <div class="fat-form">
                        <div class="fat-field fat-mg-bottom-25">
                            <input class="fat-transition" type="email" name="fg_email" id="fg_email" required
                                   data-onChange="FatSbMain_FE.resetEmailValidateField"
                                   placeholder="<?php esc_html_e('Email', 'fat-services-booking'); ?>">
                            <div class="field-error-message">
                                <?php echo esc_html__('Please enter your email', 'fat-services-booking'); ?>
                            </div>
                        </div>
                        <div class="fat-field fat-mg-top-15">
                            <div class="fat-forgot-message fat-sb-hidden fat-mg-bottom-15"></div>
                            <button class="fat-bt-reset-pass fat-bt fat-bt-full-size fat-bt-main-color fat-transition"
                                    data-onClick="FatSbBookingLogin.processResetPass">
                                <?php esc_html_e('Reset password', 'fat-services-booking'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($is_reset_pass): ?>
        <div class="fat-reset-section-wrap fat-forgot-section-wrap">
            <h2 class="fat-text-center"><?php esc_html_e('Reset your password?', 'fat-services-booking'); ?></h2>
            <div class="fat-forgot-container">
                <div class="fat-forgot-inner">
                    <div class="fat-forgot-form">
                        <div class="fat-form">
                            <div class="fat-field fat-mg-bottom-25">
                                <label for="new_pass"><?php echo esc_html__('New password', 'fat-services-booking'); ?></label>
                                <input class="fat-transition" type="password" name="new_pass" id="new_pass" required
                                       placeholder="<?php esc_html_e('New password', 'fat-services-booking'); ?>">
                                <div class="field-error-message">
                                    <?php echo esc_html__('Please enter your password', 'fat-services-booking'); ?>
                                </div>
                            </div>
                            <div class="fat-field fat-mg-top-15">
                                <div class="fat-forgot-message fat-sb-hidden fat-mg-bottom-15"></div>
                                <button class="fat-bt-reset-pass fat-bt fat-bt-full-size fat-bt-main-color fat-transition"
                                        data-onClick="FatSbBookingLogin.submitResetNewPass">
                                    <?php esc_html_e('Reset password', 'fat-services-booking'); ?>
                                </button>
                                <input type="hidden" name="key" id="key" value="<?php echo esc_attr($key); ?>">
                                <input type="hidden" name="login" id="login" value="<?php echo esc_attr($login); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
