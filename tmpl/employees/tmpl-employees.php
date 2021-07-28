<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 1/9/2019
 * Time: 11:23 AM
 */

$db_setting = FAT_DB_Setting::instance();
$setting = $db_setting->get_setting();
$time_step = isset($setting['time_step']) && $setting['time_step'] ? $setting['time_step'] : 15;
$work_hours = FAT_SB_Utils::getWorkHours($time_step);
?>
<script type="text/html" id="tmpl-fat-sb-employees-template">
    <div class="ui modal tiny fat-semantic-container fat-sb-employee-form">
        <div class="header fat-sb-popup-title"><?php echo esc_html__('Add new employee','fat-services-booking'); ?></div>
        <div class="scrolling content">
            <div class="ui pointing secondary menu tabular fat-tabs">
                <a class="active item" data-tab="detail"><?php echo esc_html__('Detail','fat-services-booking'); ?></a>
                <a class="item" data-tab="services"><?php echo esc_html__('Services','fat-services-booking'); ?></a>
                <a class="item" data-tab="schedule"><?php echo esc_html__('Schedule','fat-services-booking'); ?></a>
                <a class="item" data-tab="day-off"><?php echo esc_html__('Day off','fat-services-booking'); ?></a>
            </div>
            <div class="ui active tab segment simple" data-tab="detail">
                <div class="ui form">
                    <div class="one fields">
                        <div class="ui image-field" id="e_avatar_id" data-image-id="{{data.employee.e_avatar_id}}"
                             data-image-url="{{data.employee.e_avatar_url}}">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field ">
                            <label><?php echo esc_html__('First Name','fat-services-booking'); ?><span
                                        class="required"> *</span></label>
                            <div class="ui left icon input ">
                                <input type="text" name="e_first_name" autocomplete="nope" id="e_first_name" value="{{data.employee.e_first_name}}"
                                       placeholder="<?php echo esc_attr__('First name','fat-services-booking'); ?>" required>
                                <i class="user circle outline icon"></i>
                            </div>
                            <div class="field-error-message">
                                <?php echo esc_html__('Please enter first name','fat-services-booking'); ?>
                            </div>
                        </div>
                        <div class="field ">
                            <label><?php echo esc_html__('Last Name','fat-services-booking'); ?><span
                                        class="required"> *</span></label>
                            <div class="ui left icon input ">
                                <input type="text" name="e_last_name" autocomplete="nope" id="e_last_name" value="{{data.employee.e_last_name}}"
                                       placeholder="<?php echo esc_attr__('Last name','fat-services-booking'); ?>" required>
                                <i class="user circle outline icon"></i>
                            </div>
                            <div class="field-error-message">
                                <?php echo esc_html__('Please enter last name','fat-services-booking'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="two fields">
                        <div class="field ">
                            <label><?php echo esc_html__('Email','fat-services-booking'); ?> <span
                                        class="required"> *</span></label>
                            <div class="ui left icon input">
                                <input type="email" name="e_email" autocomplete="nope" id="e_email" value="{{data.employee.e_email}}"
                                       placeholder="<?php echo esc_attr__('Email','fat-services-booking'); ?>" required>
                                <i class="envelope outline icon"></i>
                            </div>
                            <div class="field-error-message">
                                <?php echo esc_html__('Please enter email','fat-services-booking'); ?>
                            </div>
                        </div>
                        <div class="field ">
                            <label><?php echo esc_html__('Phone','fat-services-booking'); ?></label>
                            <div class="ui left icon input number">
                                <input type="text" name="e_phone" autocomplete="nope" id="e_phone" value="{{data.employee.e_phone}}"
                                       placeholder="<?php echo esc_attr__('Phone','fat-services-booking'); ?>">
                                <i class="phone volume icon"></i>
                            </div>
                        </div>

                    </div>

                    <div class="one fields">
                        <div class="field">
                            <label><?php echo esc_html__('Location','fat-services-booking'); ?> <span
                                        class="required"> *</span></label>

                            <div class="ui bottom left pointing multiple  selection dropdown has-icon fat-sb-location-dic">
                                <i class="map marker alternate icon field-icon"></i>
                                <input type="hidden" name="e_location_ids" id="e_location_ids" value="{{data.employee.e_location_ids}}" required >
                                <i class="dropdown icon"></i>
                                <div class="text"><?php echo esc_html__('Select location','fat-services-booking'); ?></div>
                                <div class="menu">

                                </div>
                            </div>

                            <div class="field-error-message">
                                <?php echo esc_html__('Please enter location','fat-services-booking'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="one fields">
                        <div class="field">
                            <label><?php echo esc_html__('Description','fat-services-booking'); ?></label>
                            <textarea rows="3" id="e_description" name="e_description">{{data.employee.e_description}}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- service tab -->
            <div class="ui tab segment simple" data-tab="services">
                <# _.each(data.services, function(item){ #>
                <table class="ui stackable small table fat-sb-list-employees-services">
                    <thead>
                    <tr>
                        <th>
                            <div class="ui checkbox">
                                <input type="checkbox" name="example" class="table-check-all" data-onChange="FatSbEmployees.serviceCheckAllOnChange">
                                <label></label>
                            </div>
                        </th>
                        <th>{{item.cat}}</th>
                        <th><?php echo esc_html__('Price','fat-services-booking'); ?></th>
                        <th><?php echo esc_html__('Min Cap','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip"
                                 data-content="<?php echo esc_attr__('Minimum number of person per one booking of this service','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </th>
                        <th><?php echo esc_html__('Max Cap','fat-services-booking'); ?>
                            <div class="ui icon ui-tooltip" data-position="top right"
                                 data-content="<?php echo esc_attr__('Maximum number of person per one booking of this service','fat-services-booking'); ?>">
                                <i class="question circle icon"></i>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <# _.each(item.sers, function(serv){ #>
                    <tr>
                        <td class="td-20">
                            <div class="ui checkbox">
                                <input type="checkbox" name="s_id" class="check-item" value="{{serv.s_id}}" data-onChange="FatSbEmployees.serviceCheckItemOnChange"
                                       data-name="{{serv.s_name}}">
                                <label></label>
                            </div>
                        </td>
                        <td class="td-150" data-label="<?php echo esc_attr__('Name','fat-services-booking'); ?>">
                            {{serv.s_name}}
                        </td>
                        <td class="td-90" data-label="<?php echo esc_attr__('Price','fat-services-booking'); ?>">
                            <div class="ui left icon input number text-w-70 disabled service-price" >
                                <input type="text" name="s_price" value="{{serv.s_price}}" data-type="decimal">
                                <i class="dollar sign icon"></i>
                            </div>
                        </td>
                        <td class="td-90"  data-label="<?php echo esc_attr__('Min Capacity','fat-services-booking'); ?>">
                            <div class="ui left icon input disabled number text-w-70 service-capacity" data-min="1">
                                <input type="text" name="s_min_cap" value="{{serv.s_minimum_person}}">
                                <i class="users icon"></i>
                            </div>
                        </td>
                        <td class="td-90"  data-label="<?php echo esc_attr__('Max Capacity','fat-services-booking'); ?>">
                            <div class="ui left icon input number disabled text-w-70 service-capacity" data-min="1">
                                <input type="text" name="s_max_cap" value="{{serv.s_maximum_person}}">
                                <i class="users icon"></i>
                            </div>
                        </td>
                    </tr>
                    <# }) #>
                    </tbody>
                </table>
                <# }) #>
            </div>

            <!-- schedule tab -->
            <div class="ui tab segment simple" data-tab="schedule">
                <div class="ui list">

                    <!-- Monday -->
                    <div class="item schedule-item">
                        <div class="ui toggle checkbox checked">
                            <input type="checkbox" name="schedule_monday" id="schedule_monday" checked="">
                            <label><?php echo esc_html__('Monday','fat-services-booking'); ?></label>
                        </div>

                        <!-- popup clone for monday -->
                        <button class="ui basic simple button fat-bt-clone-work-hour fat-fl-right ui-popup"
                                data-position="bottom right">
                            <i class="clone outline icon"></i>
                            <?php echo esc_html__('Clone','fat-services-booking'); ?>
                        </button>
                        <div class="ui flowing popup top left transition hidden fat-popup-work-hour-clone">
                            <div><?php echo esc_html__('Applies monday shedule to:','fat-services-booking'); ?></div>
                            <div class="ui list">
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_tuesday" value="schedule_tuesday"
                                               checked="">
                                        <label><?php echo esc_html__('Tuesday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_wednesday" value="schedule_wednesday"
                                               checked="">
                                        <label><?php echo esc_html__('Wednesday','fat-services-booking'); ?></label>
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_thursday" value="schedule_thursday"
                                               checked="">
                                        <label><?php echo esc_html__('Thursday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_friday" value="schedule_friday"
                                               checked="">
                                        <label><?php echo esc_html__('Friday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_saturday" value="schedule_saturday"
                                               checked="">
                                        <label><?php echo esc_html__('Saturday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_sunday" checked="" value="schedule_sunday">
                                        <label><?php echo esc_html__('Sunday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <button class="ui mini primary  button fat-bt-applies-clone-work-hour fat-mg-top-15" data-onClick="FatSbEmployees.processCloneSchedule">
                                    <i class="clone outline icon"></i>
                                    <?php echo esc_html__('Applies','fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>

                        <div class="fat-sb-work-hour-wrap fat-sb-hidden fat-mg-top-15 schedule-monday"
                             data-depend="schedule_monday">
                            <div class="fat-sb-work-hour-item-wrap">
                            </div>
                            <div class="fat-sb-break-time-item-wrap">
                            </div>
                            <div class="fat-sb-bottom-action-group fat-mg-top-15">
                                <button class="ui basic simple button fat-bt-add-work-hour" data-onClick="FatSbEmployees.btAddWorkHourOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add work hour','fat-services-booking'); ?>
                                </button>
                                <button class="ui basic simple button fat-bt-add-break-time" data-onClick="FatSbEmployees.btAddBreakTimeOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add break time','fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tuesday -->
                    <div class="item fat-mg-top-15 schedule-item">
                        <div class="ui toggle checkbox checked">
                            <input type="checkbox" name="schedule_tuesday" id="schedule_tuesday" checked="">
                            <label><?php echo esc_html__('Tuesday','fat-services-booking'); ?></label>
                        </div>

                        <!-- popup clone for tuesday -->
                        <button class="ui basic simple button fat-bt-clone-work-hour fat-fl-right ui-popup"
                                data-position="left center">
                            <i class="clone outline icon"></i>
                            <?php echo esc_html__('Clone','fat-services-booking'); ?>
                        </button>
                        <div class="ui flowing popup top left transition hidden fat-popup-work-hour-clone">
                            <div><?php echo esc_html__('Applies tuesday\'s schedule to:','fat-services-booking'); ?></div>
                            <div class="ui list">
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_monday" value="schedule_monday"
                                               checked="">
                                        <label><?php echo esc_html__('Monday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_wednesday" value="schedule_wednesday"
                                               checked="">
                                        <label><?php echo esc_html__('Wednesday','fat-services-booking'); ?></label>
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_thursday" value="schedule_thursday"
                                               checked="">
                                        <label><?php echo esc_html__('Thursday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_friday" value="schedule_friday"
                                               checked="">
                                        <label><?php echo esc_html__('Friday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_saturday" value="schedule_saturday"
                                               checked="">
                                        <label><?php echo esc_html__('Saturday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_sunday" value="schedule_sunday"
                                               checked="">
                                        <label><?php echo esc_html__('Sunday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <button class="ui mini primary  button fat-bt-applies-clone-work-hour fat-mg-top-15" data-onClick="FatSbEmployees.processCloneSchedule">
                                    <i class="clone outline icon"></i>
                                    <?php echo esc_html__('Applies','fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>

                        <div class="fat-sb-work-hour-wrap fat-sb-hidden fat-mg-top-15 schedule-tuesday"
                             data-depend="schedule_tuesday">
                            <div class="fat-sb-work-hour-item-wrap">
                            </div>
                            <div class="fat-sb-break-time-item-wrap">
                            </div>
                            <div class="fat-sb-bottom-action-group fat-mg-top-15">
                                <button class="ui basic simple button fat-bt-add-work-hour" data-onClick="FatSbEmployees.btAddWorkHourOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add work hour','fat-services-booking'); ?>
                                </button>
                                <button class="ui basic simple button fat-bt-add-break-time" data-onClick="FatSbEmployees.btAddBreakTimeOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add break time','fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Wednesday -->
                    <div class="item fat-mg-top-15 schedule-item">
                        <div class="ui toggle checkbox checked">
                            <input type="checkbox" name="schedule_wednesday" id="schedule_wednesday" checked="">
                            <label><?php echo esc_html__('Wednesday','fat-services-booking'); ?></label>
                        </div>

                        <!-- popup clone for wednesday -->
                        <button class="ui basic simple button fat-bt-clone-work-hour fat-fl-right ui-popup"
                                data-position="left center">
                            <i class="clone outline icon"></i>
                            <?php echo esc_html__('Clone','fat-services-booking'); ?>
                        </button>
                        <div class="ui flowing popup top left transition hidden fat-popup-work-hour-clone">
                            <div><?php echo esc_html__('Applies wednesday\'s schedule to:','fat-services-booking'); ?></div>
                            <div class="ui list">
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_monday" value="schedule_monday"
                                               checked="">
                                        <label><?php echo esc_html__('Monday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_tuesday" value="schedule_tuesday"
                                               checked="">
                                        <label><?php echo esc_html__('Tuesday','fat-services-booking'); ?></label>
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_thursday" value="schedule_thursday"
                                               checked="">
                                        <label><?php echo esc_html__('Thursday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_friday" value="schedule_friday"
                                               checked="">
                                        <label><?php echo esc_html__('Friday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_saturday" value="schedule_saturday"
                                               checked="">
                                        <label><?php echo esc_html__('Saturday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_sunday" value="schedule_sunday"
                                               checked="">
                                        <label><?php echo esc_html__('Sunday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <button class="ui mini primary  button fat-bt-applies-clone-work-hour fat-mg-top-15" data-onClick="FatSbEmployees.processCloneSchedule">
                                    <i class="clone outline icon"></i>
                                    <?php echo esc_html__('Applies','fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>

                        <div class="fat-sb-work-hour-wrap fat-sb-hidden fat-mg-top-15 schedule-wednesday"
                             data-depend="schedule_wednesday">
                            <div class="fat-sb-work-hour-item-wrap">
                            </div>
                            <div class="fat-sb-break-time-item-wrap">
                            </div>
                            <div class="fat-sb-bottom-action-group fat-mg-top-15">
                                <button class="ui basic simple button fat-bt-add-work-hour"  data-onClick="FatSbEmployees.btAddWorkHourOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add work hour','fat-services-booking'); ?>
                                </button>
                                <button class="ui basic simple button fat-bt-add-break-time" data-onClick="FatSbEmployees.btAddBreakTimeOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add break time','fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Thursday -->
                    <div class="item fat-mg-top-15 schedule-item">
                        <div class="ui toggle checkbox checked">
                            <input type="checkbox" name="schedule_thursday" id="schedule_thursday" checked="">
                            <label><?php echo esc_html__('Thursday','fat-services-booking'); ?></label>
                        </div>

                        <!-- popup clone for thursday -->
                        <button class="ui basic simple button fat-bt-clone-work-hour fat-fl-right ui-popup"
                                data-position="left center">
                            <i class="clone outline icon"></i>
                            <?php echo esc_html__('Clone','fat-services-booking'); ?>
                        </button>
                        <div class="ui flowing popup top left transition hidden fat-popup-work-hour-clone">
                            <div><?php echo esc_html__('Applies thursday\'s schedule to:','fat-services-booking'); ?></div>
                            <div class="ui list">
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_monday" value="schedule_monday"
                                               checked="">
                                        <label><?php echo esc_html__('Monday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_tuesday" value="schedule_tuesday"
                                               checked="">
                                        <label><?php echo esc_html__('Tuesday','fat-services-booking'); ?></label>
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_wednesday" value="schedule_wednesday"
                                               checked="">
                                        <label><?php echo esc_html__('Wednesday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_friday" value="schedule_friday"
                                               checked="">
                                        <label><?php echo esc_html__('Friday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_saturday" value="schedule_saturday"
                                               checked="">
                                        <label><?php echo esc_html__('Saturday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_sunday" value="schedule_sunday"
                                               checked="">
                                        <label><?php echo esc_html__('Sunday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <button class="ui mini primary  button fat-bt-applies-clone-work-hour fat-mg-top-15" data-onClick="FatSbEmployees.processCloneSchedule">
                                    <i class="clone outline icon"></i>
                                    <?php echo esc_html__('Applies','fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>

                        <div class="fat-sb-work-hour-wrap fat-sb-hidden fat-mg-top-15 schedule-thursday"
                             data-depend="schedule_thursday">
                            <div class="fat-sb-work-hour-item-wrap">
                            </div>
                            <div class="fat-sb-break-time-item-wrap">
                            </div>
                            <div class="fat-sb-bottom-action-group fat-mg-top-15">
                                <button class="ui basic simple button fat-bt-add-work-hour" data-onClick="FatSbEmployees.btAddWorkHourOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add work hour','fat-services-booking'); ?>
                                </button>
                                <button class="ui basic simple button fat-bt-add-break-time" data-onClick="FatSbEmployees.btAddBreakTimeOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add break time','fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Friday -->
                    <div class="item fat-mg-top-15 schedule-item">
                        <div class="ui toggle checkbox checked">
                            <input type="checkbox" name="schedule_friday" id="schedule_friday" checked="">
                            <label><?php echo esc_html__('Friday','fat-services-booking'); ?></label>
                        </div>

                        <!-- popup clone for thursday -->
                        <button class="ui basic simple button fat-bt-clone-work-hour fat-fl-right ui-popup"
                                data-position="left center">
                            <i class="clone outline icon"></i>
                            <?php echo esc_html__('Clone','fat-services-booking'); ?>
                        </button>
                        <div class="ui flowing popup top left transition hidden fat-popup-work-hour-clone">
                            <div><?php echo esc_html__('Applies friday\'s schedule to:','fat-services-booking'); ?></div>
                            <div class="ui list">
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_monday" value="schedule_monday"
                                               checked="">
                                        <label><?php echo esc_html__('Monday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_tuesday" value="schedule_tuesday"
                                               checked="">
                                        <label><?php echo esc_html__('Tuesday','fat-services-booking'); ?></label>
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_wednesday" value="schedule_wednesday"
                                               checked="">
                                        <label><?php echo esc_html__('Wednesday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_thursday" value="schedule_thursday"
                                               checked="">
                                        <label><?php echo esc_html__('Thursday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_saturday" value="schedule_saturday"
                                               checked="">
                                        <label><?php echo esc_html__('Saturday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui checkbox checked">
                                        <input type="checkbox" name="cb_apply_for_sunday" checked=""
                                               value="schedule_sunday">
                                        <label><?php echo esc_html__('Sunday','fat-services-booking'); ?></label>
                                    </div>
                                </div>
                                <button class="ui mini primary button fat-bt-applies-clone-work-hour fat-mg-top-15" data-onClick="FatSbEmployees.processCloneSchedule">
                                    <i class="clone outline icon"></i>
                                    <?php echo esc_html__('Applies','fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>

                        <div class="fat-sb-work-hour-wrap fat-sb-hidden fat-mg-top-15 schedule-friday"
                             data-depend="schedule_friday">
                            <div class="fat-sb-work-hour-item-wrap">
                            </div>
                            <div class="fat-sb-break-time-item-wrap">
                            </div>
                            <div class="fat-sb-bottom-action-group fat-mg-top-15">
                                <button class="ui basic simple button fat-bt-add-work-hour" data-onClick="FatSbEmployees.btAddWorkHourOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add work hour','fat-services-booking'); ?>
                                </button>
                                <button class="ui basic simple button fat-bt-add-break-time" data-onClick="FatSbEmployees.btAddBreakTimeOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add break time','fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Saturday -->
                    <div class="item fat-mg-top-15 schedule-item">
                        <div class="ui toggle checkbox ">
                            <input type="checkbox" name="schedule_saturday" id="schedule_saturday">
                            <label><?php echo esc_html__('Saturday','fat-services-booking'); ?></label>
                        </div>
                        <div class="fat-sb-work-hour-wrap fat-sb-hidden fat-mg-top-15 schedule-saturday"
                             data-depend="schedule_saturday">
                            <div class="fat-sb-work-hour-item-wrap">
                            </div>
                            <div class="fat-sb-break-time-item-wrap">
                            </div>
                            <div class="fat-sb-bottom-action-group fat-mg-top-15">
                                <button class="ui basic simple button fat-bt-add-work-hour" data-onClick="FatSbEmployees.btAddWorkHourOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add work hour','fat-services-booking'); ?>
                                </button>
                                <button class="ui basic simple button fat-bt-add-break-time" data-onClick="FatSbEmployees.btAddBreakTimeOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add break time','fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sunday -->
                    <div class="item fat-mg-top-15 schedule-item">
                        <div class="ui toggle checkbox">
                            <input type="checkbox" name="schedule_sunday" id="schedule_sunday">
                            <label><?php echo esc_html__('Sunday','fat-services-booking'); ?></label>
                        </div>
                        <div class="fat-sb-work-hour-wrap fat-sb-hidden fat-mg-top-15 schedule-sunday"
                             data-depend="schedule_sunday">
                            <div class="fat-sb-work-hour-item-wrap">
                            </div>
                            <div class="fat-sb-break-time-item-wrap">
                            </div>
                            <div class="fat-sb-bottom-action-group fat-mg-top-15">
                                <button class="ui basic simple button fat-bt-add-work-hour" data-onClick="FatSbEmployees.btAddWorkHourOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add work hour','fat-services-booking'); ?>
                                </button>
                                <button class="ui basic simple button fat-bt-add-break-time" data-onClick="FatSbEmployees.btAddBreakTimeOnClick">
                                    <i class="plus square outline icon"></i>
                                    <?php echo esc_html__('Add break time','fat-services-booking'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Day off tab -->
            <div class="ui tab segment simple fat-min-height-300" data-tab="day-off">
                <div class="fat-day-off-wrap">
                    <div class="fat-day-off-inner">

                    </div>
                    <div class="fat-sb-bottom-action-group fat-mg-top-15">
                        <button class="ui basic simple button fat-bt-add-day-off" data-onClick="FatSbEmployees.btAddDayOfOnClick">
                            <i class="plus square outline icon"></i>
                            <?php echo esc_html__('Add day off','fat-services-booking'); ?>
                        </button>
                    </div>
                </div>
            </div>

        </div>
        <div class="actions">
            <# if(data.employee.e_id !=''){ #>
            <div class="left-button-groups">
                <button class="ui basic blue button fat-bt-hide fat-has-popup popup-hover"
                        data-popup-id="popup_bt_hide_employee" data-loading-color="loading-blue">
                    <# if(data.employee.e_enable==1){ #>
                        <i class="eye icon"></i>
                    <# }else{ #>
                        <i class="eye  slash outline icon"></i>
                    <# } #>
                </button>
                <div class="ui flowing popup top left transition hidden" data-popup-id="popup_bt_hide_employee">
                    <h4 class="ui header">
                        <# if(data.employee.e_enable==1){ #>
                            <?php echo esc_html__('Are you sure you want to hide employee ?','fat-services-booking'); ?>
                        <# }else{ #>
                            <?php echo esc_html__('Are you sure you want to show employee ?','fat-services-booking'); ?>
                        <# } #>

                    </h4>
                    <div>
                        <button class="ui mini button fat-bt-confirm-cancel" data-onClick="FatSbEmployees.cancelPopupToolTipOnClick">
                            <?php echo esc_html__('Cancel','fat-services-booking'); ?>
                        </button>
                        <button class="ui mini primary button fat-bt-confirm-ok fat-bt-confirm-enable" data-onClick="FatSbEmployees.processEnableEmployee"
                                data-id="{{data.employee.e_id}}" data-enable="{{data.employee.e_enable}}">
                            <?php echo esc_html__('Yes','fat-services-booking'); ?>
                        </button>
                    </div>
                </div>

                <button class="ui basic button fat-bt-delete fat-has-popup popup-hover"
                        data-popup-id="popup_bt_delete_employee" data-loading-color="loading-red">
                    <i class="trash alternate outline icon"></i>
                </button>
                <div class="ui flowing popup top left transition hidden" data-popup-id="popup_bt_delete_employee">
                    <h4 class="ui header"><?php echo esc_html__('Are you sure you want to delete employee ?','fat-services-booking'); ?></h4>
                    <div>
                        <button class="ui mini button fat-bt-confirm-cancel" data-onClick="FatSbEmployees.cancelPopupToolTipOnClick">
                            <?php echo esc_html__('Cancel','fat-services-booking'); ?>
                        </button>
                        <button class="ui mini primary button fat-bt-confirm-ok fat-bt-confirm-delete"  data-onClick="FatSbEmployees.processPopupDeleteEmployee"
                                data-id="{{data.employee.e_id}}">
                            <?php echo esc_html__('Yes','fat-services-booking'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <# } #>
            <button class="ui basic button fat-close-modal">
                <i class="times circle outline icon"></i>
                <?php echo esc_html__('Cancel','fat-services-booking'); ?>
            </button>
            <div class="blue ui buttons">
                <div class="ui button fat-submit-modal fat-bt-submit-employee" data-id="{{data.employee.e_id}}"
                     data-onClick="FatSbEmployees.processSubmitEmployee"
                     data-success-message="<?php echo esc_attr__('Employee has been saved','fat-services-booking'); ?>">
                    <i class="save outline icon"></i>
                    <?php echo esc_html__('Save','fat-services-booking'); ?>
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-break-time-template">
    <div class="fat-sb-break-time-item fat-mg-top-5">
        <label><?php echo esc_html__('Break time','fat-services-booking'); ?></label>
        <div class="ui selection search dropdown top left pointing has-icon fat-time-dropdown fat-break-time-start-dropdown">
            <i class="clock outline icon"></i>
            <input type="hidden" name="break_time_start" id="break_time_start" required>
            <div class="text"></div>
            <i class="dropdown icon"></i>
            <div class="menu">
                <?php foreach ($work_hours as $key => $value) { ?>
                    <div class="item" data-value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></div>
                <?php }; ?>
            </div>
        </div>
        <div class="ui selection search dropdown top left pointing has-icon fat-time-dropdown fat-break-time-end-dropdown">
            <i class="clock outline icon"></i>
            <input type="hidden" name="break_time_end" id="break_time_end" required>
            <div class="text"></div>
            <i class="dropdown icon"></i>
            <div class="menu">
                <?php foreach ($work_hours as $key => $value) { ?>
                    <div class="item" data-value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></div>
                <?php }; ?>
            </div>
        </div>
        <button class="ui basic simple button fat-mg-left-15 fat-hover-red fat-bt-remove-break-time">
            <i class="minus square outline icon"></i>
        </button>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-work-hour-template">
    <div class="fat-sb-work-hour-item fat-mg-top-5">
        <label><?php echo esc_html__('Work hour','fat-services-booking'); ?></label>
        <div class="ui selection search dropdown top left pointing has-icon fat-time-dropdown fat-work-hour-start-dropdown">
            <i class="clock outline icon"></i>
            <input type="hidden" name="work_hour_start" id="work_hour_start" required>
            <div class="text"></div>
            <i class="dropdown icon"></i>

            <div class="menu">
                <?php foreach ($work_hours as $key => $value) { ?>
                    <div class="item" data-value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></div>
                <?php }; ?>
            </div>
        </div>
        <div class="ui selection search dropdown top left pointing has-icon fat-time-dropdown fat-work-hour-end-dropdown">
            <i class="clock outline icon"></i>
            <input type="hidden" name="work_hour_end" id="work_hour_end" required>
            <div class="text"></div>
            <i class="dropdown icon"></i>
            <div class="menu">
                <?php foreach ($work_hours as $key => $value) { ?>
                    <div class="item" data-value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></div>
                <?php }; ?>
            </div>
        </div>
        <div class="fat-checkbox-dropdown-wrap">
            <select multiple="multiple" name="assign-services"
                    data-placeholder="<?php echo esc_attr__('Applied for all services'); ?>"
                    data-caption-format="<?php echo esc_attr__('Services selected'); ?>"
                    id="assign-services" class="assign-services SumoUnder" tabindex="-1">
            </select>
        </div>

        <button class="ui basic simple button fat-mg-left-15 fat-hover-red fat-bt-remove-work-hour">
            <i class="minus square outline icon"></i>
        </button>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-day-off-template">
    <div class="fat-sb-day-off-item fat-mg-top-5">
        <div class="ui input">
            <input type="text" name="day_off_name" placeholder="Name of day off">
        </div>
        <div class="ui input">
            <input type="text" value="" class="date-range-picker" name="day_off_schedule">
        </div>

        <button class="ui basic simple button fat-mg-left-15 fat-hover-red fat-bt-remove-day-off">
            <i class="minus square outline icon"></i>
        </button>
    </div>
</script>

<script type="text/html" id="tmpl-fat-sb-employee-item-template">
    <# _.each(data, function(item){ #>
    <div class="six wide column" data-e-id="{{item.e_id}}">
        <div class="ui card full-width fat-hover-link fat-box-shadow-hover" >
            <# if(item.e_enable==1){ #>
                <div class="enable-status enable">
                    <i class="eye icon"></i>
                </div>
            <# }else{ #>
                <div class="enable-status disable">
                    <i class="eye  slash outline icon"></i>
                </div>
            <# } #>

            <div class="image">
                <# if(item.e_avatar_url!=''){ #>
                    <img class="fat-border-round fat-box-shadow fat-img-150" src="{{item.e_avatar_url}}">
                <# }else{ #>
                    <span class="fat-no-thumb fat-img-150"></span>
                <# } #>
            </div>
            <div class="content">
                <a class="header full-name">{{item.e_first_name}} {{item.e_last_name}}</a>
                <div class="meta">
                    <div class="email">{{item.e_email}}</div>
                    <div class="phone">{{item.e_phone}}</div>
                    <div class="id">ID: {{item.e_id}}</div>
                </div>
            </div>
            <div class="extra content">
            </div>
            <div class="fat-sb-button-group">
                <button class=" ui icon button fat-item-bt-inline fat-sb-delete-employee" data-onClick="FatSbEmployees.processDeleteEmployee"
                        data-id="{{item.e_id}}" data-title="<?php echo esc_attr__('Delete','fat-services-booking');?>">
                    <i class="trash alternate outline icon"></i>
                </button>

                <button class=" ui icon button fat-item-bt-inline fat-sb-clone-employee" data-onClick="FatSbEmployees.processCloneEmployee"
                        data-id="{{item.e_id}}"  data-title="<?php echo esc_attr__('Clone','fat-services-booking');?>">
                    <i class="clone outline icon"></i>
                </button>

                <button class=" ui icon button fat-item-bt-inline fat-sb-edit-employee" data-onClick="FatSbEmployees.showPopupEmployee"
                        data-id="{{item.e_id}}" data-title="<?php echo esc_attr__('Edit','fat-services-booking');?>">
                    <i class="edit outline icon"></i>
                </button>
            </div>
        </div>
    </div>
    <# }) #>
</script>
