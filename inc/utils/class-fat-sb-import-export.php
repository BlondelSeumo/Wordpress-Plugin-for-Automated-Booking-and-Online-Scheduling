<?php
/**
 * Created by PhpStorm.
 * User: RoninWP
 * Date: 5/22/2019
 * Time: 10:03 AM
 */
if (!class_exists('FAT_SB_Import_Export')) {
    class FAT_SB_Import_Export
    {
        private static $instance = NULL;
        private $admin_notice = '';

        public static function instance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function import()
        {
            $is_valid = 1;
            $is_valid = apply_filters('fat_save_data',$is_valid);
            if(is_array($is_valid)){
                add_action('fat_import_notices', array($this, 'notice_error'));
                return;
            }

            global $wpdb;
            if (empty($_POST['fat_sb_action']) || 'import' != $_POST['fat_sb_action'])
                return;

            if (!wp_verify_nonce($_POST['fat_sb_import_nonce'], 'fat_sb_import_nonce'))
                return;

            $file_name = $_FILES['import_file']['name'];
            $file_name = explode('.', $file_name);
            $extension = end($file_name);

            wp_die(esc_html__('Please upload a file to import', 'fat-services-booking'));

            if ($extension != 'json') {
                $this->admin_notice = esc_html__('Please upload a valid .json file', 'fat-services-booking');
                add_action('fat_import_notices', array($this, 'notice_error'));
            } else {
                $import_file = $_FILES['import_file']['tmp_name'];
                if (empty($import_file)) {
                    wp_die(esc_html__('Please upload a file to import', 'fat-services-booking'));
                }
                $resource_folder = FAT_SERVICES_DIR_PATH . 'assets/export/images/';
                $result = $this->process_import($import_file, $resource_folder);
                if ($result['result'] > 0) {
                    $this->admin_notice = esc_html__('Data has been imported', 'fat-services-booking');
                    add_action('fat_import_notices', array($this, 'notice_success'));
                } else {
                    $this->admin_notice = $result['message'];
                    add_action('fat_import_notices', array($this, 'notice_error'));
                }
            }
        }

        public function install_demo()
        {
            $import_file = FAT_SERVICES_DIR_PATH . 'assets/demo-data/demo-data.json';
            $resource_folder = FAT_SERVICES_DIR_PATH . 'assets/demo-data/images/';
            return $this->process_import($import_file, $resource_folder);
        }

        private function process_import($import_file, $resource_folder)
        {
            try {
                global $wpdb;
                $data = file_get_contents($import_file);
                $data = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data), true);

                /*
                 * import setting
                */
                if (isset($data['settings'])) {
                    update_option('fat_sb_settings', $data['settings']);
                }
                if (isset($data['working_hour_setting'])) {
                    update_option('fat_sb_working_hour_setting', $data['working_hour_setting']);
                }
                if (isset($data['email_template'])) {
                    update_option('fat_sb_email_template_setting', $data['email_template']);
                }

                $old_id = 0;
                /*
                 * import services
                */
                if (isset($data['services_category'])) {
                    $services_cat_ids = array();
                    foreach ($data['services_category'] as $cat) {
                        $old_id = $cat['sc_id'];
                        unset($cat['sc_id']);
                        $wpdb->insert($wpdb->prefix . 'fat_sb_services_category', $cat);
                        $services_cat_ids[$old_id] = $wpdb->insert_id;
                    }
                }

                if (isset($data['services_extra'])) {
                    $services_extra_ids = array();
                    foreach ($data['services_extra'] as $extra) {
                        $old_id = $extra['se_id'];
                        unset($extra['se_id']);
                        $wpdb->insert($wpdb->prefix . 'fat_sb_services_extra', $extra);
                        $services_extra_ids[$old_id] = $wpdb->insert_id;
                    }
                }

                if (isset($data['services'])) {
                    $services_ids = array();
                    $s_extra_ids = '';
                    $is_syn_extra_id = isset($s_extra_ids);
                    foreach ($data['services'] as $service) {
                        $old_id = $service['s_id'];
                        unset($service['s_id']);

                        //syn category id
                        if (isset($services_cat_ids[$service['s_category_id']])) {
                            $service['s_category_id'] = $services_cat_ids[$service['s_category_id']];
                        }

                        //syn service extra id
                        if ($is_syn_extra_id) {
                            $s_extra_ids = explode(',', $service['s_extra_ids']);
                            $service['s_extra_ids'] = array();
                            foreach ($s_extra_ids as $id) {
                                if (isset($services_extra_ids[$id])) {
                                    $service['s_extra_ids'][] = $services_extra_ids[$id];
                                }
                            }
                            $service['s_extra_ids'] = implode(',', $service['s_extra_ids']);
                        }

                        $wpdb->insert($wpdb->prefix . 'fat_sb_services', $service);
                        $services_ids[$old_id] = $wpdb->insert_id;
                    }
                }

                /*
                * import location
                */
                if (isset($data['location'])) {
                    $location_ids = array();
                    foreach ($data['location'] as $location) {
                        $old_id = $location['loc_id'];
                        unset($location['loc_id']);
                        $wpdb->insert($wpdb->prefix . 'fat_sb_locations', $location);
                        $location_ids[$old_id] = $wpdb->insert_id;
                    }
                }

                /*
                * import employee
                */
                if (isset($data['employees'])) {
                    $employees_ids = array();
                    $e_location_ids = '';
                    $is_syn_location = isset($location_ids);
                    $is_syn_service = isset($services_ids);
                    foreach ($data['employees'] as $employee) {
                        $old_id = $employee['e_id'];
                        unset($employee['e_id']);

                        if ($is_syn_service) {
                            $employee['e_schedules'] = unserialize($employee['e_schedules']);
                            for ($es_index = 0; $es_index < count($employee['e_schedules']); $es_index++) {
                                if(isset($employee['e_schedules'][$es_index]['work_hours'])){
                                    for ($w_index = 0; $w_index < count($employee['e_schedules'][$es_index]['work_hours']); $w_index++) {
                                        for ($s_index = 0; $s_index < count($employee['e_schedules'][$es_index]['work_hours'][$w_index]['s_id']); $s_index++) {
                                            if (isset($services_ids[$employee['e_schedules'][$es_index]['work_hours'][$w_index]['s_id'][$s_index]])) {
                                                $employee['e_schedules'][$es_index]['work_hours'][$w_index]['s_id'][$s_index] = $services_ids[$employee['e_schedules'][$es_index]['work_hours'][$w_index]['s_id'][$s_index]];
                                            }
                                        }
                                    }
                                }
                            }
                            $employee['e_schedules'] = serialize($employee['e_schedules']);
                        }

                        if ($is_syn_location) {
                            $e_location_ids = explode(',', $employee['e_location_ids']);
                            $employee['e_location_ids'] = array();
                            foreach ($e_location_ids as $id) {
                                if (isset($location_ids[$id])) {
                                    $employee['e_location_ids'][] = $location_ids[$id];
                                }
                            }
                            $employee['e_location_ids'] = implode(',', $employee['e_location_ids']);
                        }
                        $wpdb->insert($wpdb->prefix . 'fat_sb_employees', $employee);
                        $employees_ids[$old_id] = $wpdb->insert_id;
                    }
                }

                if (isset($data['services_employee'])) {
                    foreach ($data['services_employee'] as $se) {
                        unset($se['se_id']);
                        if (isset($services_ids[$se['s_id']])) {
                            $se['s_id'] = $services_ids[$se['s_id']];
                        }
                        if (isset($employees_ids[$se['e_id']])) {
                            $se['e_id'] = $employees_ids[$se['e_id']];
                        }
                        $wpdb->insert($wpdb->prefix . 'fat_sb_services_employee', $se);
                    }
                }

                if (isset($data['employees_schedule'])) {
                    foreach ($data['employees_schedule'] as $es) {
                        unset($es['es_id']);
                        //syn employee id
                        if (isset($employees_ids[$es['e_id']])) {
                            $es['e_id'] = $employees_ids[$es['e_id']];
                        }
                        //syn service id
                        $es['s_id'] = !is_null($es['s_id']) ? $es['s_id'] : 0;
                        $es['es_work_hour_start'] = !is_null($es['es_work_hour_start']) ? $es['es_work_hour_start'] : 0;
                        $es['es_work_hour_end'] = !is_null($es['es_work_hour_end']) ? $es['es_work_hour_end'] : 0;

                        $es['s_id'] = $es['s_id'] ? $services_ids[$es['s_id']] : $es['s_id'];
                        $wpdb->insert($wpdb->prefix . 'fat_sb_employees_schedule', $es);
                    }
                }

                if (isset($data['employees_break_time'])) {
                    foreach ($data['employees_break_time'] as $eb) {
                        unset($eb['eb_id']);
                        //syn employee id
                        if (isset($employees_ids[$eb['e_id']])) {
                            $eb['e_id'] = $employees_ids[$eb['e_id']];
                        }
                        $wpdb->insert($wpdb->prefix . 'fat_sb_employees_break_time', $eb);
                    }
                }

                if (isset($data['employees_day_off'])) {
                    foreach ($data['employees_day_off'] as $dof) {
                        unset($dof['dof_id']);
                        //syn employee id
                        if (isset($employees_ids[$dof['e_id']])) {
                            $dof['e_id'] = $employees_ids[$dof['e_id']];
                        }
                        $wpdb->insert($wpdb->prefix . 'fat_sb_employees_day_off', $dof);
                    }
                }

                if (isset($data['employees_location'])) {
                    foreach ($data['employees_location'] as $el) {
                        unset($el['el_id']);
                        //syn employee id
                        if (isset($employees_ids[$el['e_id']])) {
                            $el['e_id'] = $employees_ids[$el['e_id']];
                        }
                        //syn location id
                        if (isset($location_ids[$el['loc_id']])) {
                            $el['loc_id'] = $location_ids[$el['loc_id']];
                        }
                        $wpdb->insert($wpdb->prefix . 'fat_sb_employees_location', $el);

                    }
                }

                /*
                 * import customer
                 */
                if (isset($data['customers'])) {
                    foreach ($data['customers'] as $customer) {
                        unset($customer['c_id']);
                        $wpdb->insert($wpdb->prefix . 'fat_sb_customers', $customer);
                    }
                }

                /*
                 * import coupon
                 */
                if (isset($data['coupon'])) {
                    $coupon_ids = array();
                    foreach ($data['coupon'] as $coupon) {
                        $old_id = $coupon['cp_id'];
                        unset($coupon['cp_id']);
                        $wpdb->insert($wpdb->prefix . 'fat_sb_coupons', $coupon);
                        $coupon_ids[$old_id] = $wpdb->insert_id;
                    }
                }

                if (isset($data['coupon_logs'])) {
                    foreach ($data['coupon_logs'] as $log) {
                        unset($log['cp_log_id']);
                        //syn coupon id
                        if (isset($coupon_ids[$log['cp_id']])) {
                            $log['cp_id'] = $coupon_ids[$log['cp_id']];
                        }
                        //syn service id
                        if (isset($services_ids[$log['s_id']])) {
                            $log['s_id'] = $services_ids[$log['s_id']];
                        }
                        $log['cp_log_create_date'] =  current_time( 'mysql', 0);
                        $wpdb->insert($wpdb->prefix . 'fat_sb_coupon_logs', $log);
                    }
                }

                /*
                 * add attachment
                 */
                if (isset($data['attachments'])) {

                    $upload_dir = wp_get_upload_dir();
                    $upload_folder = $upload_dir['path'];
                    $upload_url = $upload_dir['url'];
                    $attach_ids = array();
                    $filename = '';
                    $from_file = '';
                    $to_file = '';
                    $filetype = '';
                    $attach_id = '';
                    $attach_data = '';
                    foreach ($data['attachments'] as $id => $name) {
                        $from_file = $resource_folder . $name;
                        $to_file = $upload_folder . '/' . $name;
                        if(is_readable($from_file)){
                            copy($from_file, $to_file);
                            $filetype = wp_check_filetype(basename($to_file), null);
                            $attach_id = wp_insert_attachment(array(
                                'guid' => $upload_url . '/' . $name,
                                'post_mime_type' => $filetype['type'],
                                'post_title' => preg_replace('/\.[^.]+$/', '', basename($to_file)),
                                'post_content' => '',
                                'post_status' => 'inherit'
                            ), $to_file, 0);
                            if (!is_wp_error($attach_id)) {
                                $attach_ids[$id]  = $attach_id;
                                $attach_data = wp_generate_attachment_metadata( $attach_id, $to_file );
                                wp_update_attachment_metadata( $attach_id, $attach_data );
                            }
                        }
                    }

                    /*
                     * syn attachment
                     */
                    foreach ($attach_ids as $old_id => $new_id) {
                        $sql = "UPDATE {$wpdb->prefix}fat_sb_services SET  s_image_id = {$new_id} WHERE  s_image_id = {$old_id}";
                        $wpdb->query($sql);

                        $sql = "UPDATE {$wpdb->prefix}fat_sb_services_category SET  sc_image_id = {$new_id} WHERE  sc_image_id = {$old_id}";
                        $wpdb->query($sql);

                        $sql = "UPDATE {$wpdb->prefix}fat_sb_employees SET  e_avatar_id = {$new_id} WHERE  e_avatar_id = {$old_id}";
                        $wpdb->query($sql);
                    }
                }

                return array(
                    'result' => 1
                );


            } catch (Exception  $err) {
                return array(
                    'result' => -1,
                    'message' => $err->getMessage()
                );

            }
        }

        function notice_success()
        {
            ?>
            <div class="notice notice-success">
                <p><?php echo esc_html($this->admin_notice); ?></p>
            </div>
            <?php
        }

        function notice_error()
        {
            ?>
            <div class="notice notice-error">
                <p><?php echo esc_html($this->admin_notice); ?></p>
            </div>
            <?php
        }

        public function export()
        {
            $data = isset($_REQUEST['data']) ? $_REQUEST['data'] : '';
            if ($data) {
                global $wpdb;
                $export = array();
                $sql = '';

                if (isset($data['services']) && $data['services'] == 1) {
                    $sql = "SELECT s_id, s_image_id, s_name, s_description, s_category_id, s_price, s_tax, s_duration, s_break_time, s_minimum_person, 
                                  s_maximum_person, s_extra_ids, s_employee_ids, s_available, s_allow_booking_online, s_create_date
                                FROM {$wpdb->prefix}fat_sb_services";
                    $export['services'] = $wpdb->get_results($sql);

                    $sql = "SELECT sc_id, sc_image_id, sc_name, sc_description, sc_total_service
                                FROM {$wpdb->prefix}fat_sb_services_category";
                    $export['services_category'] = $wpdb->get_results($sql);

                    $sql = "SELECT se_id, se_name, se_price, se_tax, se_min_quantity, se_max_quantity, se_duration, se_description, se_create_date
                                FROM {$wpdb->prefix}fat_sb_services_extra";
                    $export['services_extra'] = $wpdb->get_results($sql);

                    $sql = "SELECT se_id, s_id, e_id, s_price, s_min_cap, s_max_cap, s_create_date
                                FROM {$wpdb->prefix}fat_sb_services_employee";
                    $export['services_employee'] = $wpdb->get_results($sql);
                }

                if (isset($data['employees']) && $data['employees'] == 1) {
                    $sql = "SELECT e_id, e_first_name, e_last_name, e_avatar_id, e_phone, e_email, e_location_ids, e_description, e_schedules, e_day_off,
                                  e_break_times, e_enable, e_create_date
                                FROM {$wpdb->prefix}fat_sb_employees";
                    $export['employees'] = $wpdb->get_results($sql);

                    $sql = "SELECT es_id, e_id, es_day, es_work_hour_start, es_work_hour_end, s_id, es_enable, es_create_date
                                FROM {$wpdb->prefix}fat_sb_employees_schedule";
                    $export['employees_schedule'] = $wpdb->get_results($sql);

                    $sql = "SELECT el_id, e_id, loc_id
                                FROM {$wpdb->prefix}fat_sb_employees_location";
                    $export['employees_location'] = $wpdb->get_results($sql);

                    $sql = "SELECT eb_id, e_id, es_day, es_break_time_start, es_break_time_end, eb_create_date
                                FROM {$wpdb->prefix}fat_sb_employees_break_time";
                    $export['employees_break_time'] = $wpdb->get_results($sql);

                    $sql = "SELECT dof_id, e_id, dof_name, dof_start, dof_end, dof_create_date
                                FROM {$wpdb->prefix}fat_sb_employees_day_off";
                    $export['employees_day_off'] = $wpdb->get_results($sql);
                }

                if (isset($data['customers']) && $data['customers'] == 1) {
                    $sql = "SELECT c_id, c_first_name, c_last_name, c_gender, c_phone, c_email, c_dob, c_user_id, c_description, c_last_booking, c_create_date
                                FROM {$wpdb->prefix}fat_sb_customers";
                    $export['customers'] = $wpdb->get_results($sql);
                }

                if (isset($data['location']) && $data['location'] == 1) {
                    $sql = "SELECT loc_id, loc_name, loc_address, loc_latitude_x, loc_latitude_y, loc_description, loc_create_date
                                FROM {$wpdb->prefix}fat_sb_locations";
                    $export['location'] = $wpdb->get_results($sql);
                }

                if (isset($data['coupon']) && $data['coupon'] == 1) {
                    $sql = "SELECT cp_id, cp_code, cp_description, cp_discount_type, cp_amount, cp_start_date, cp_expire, cp_apply_to, cp_exclude, 
                                    cp_times_use, cp_use_count, cp_create_date
                                FROM {$wpdb->prefix}fat_sb_coupons";
                    $export['coupon'] = $wpdb->get_results($sql);

                    $sql = "SELECT cp_log_id, cp_id, c_email, s_id, cp_log_create_date
                                FROM {$wpdb->prefix}fat_sb_coupon_logs";
                    $export['coupon_logs'] = $wpdb->get_results($sql);
                }

                if (isset($data['booking']) && $data['booking'] == 1) {
                    $sql = "SELECT b_id, b_customer_id, b_customer_number, b_loc_id, b_employee_id, b_service_cat_id, b_service_id, b_service_break_time,
                                b_services_extra, b_total_extra, b_total_tax_extra, b_date, b_time, b_price, b_service_tax, b_service_tax_amount, b_total_amount,
                                b_coupon_id, b_coupon_code, b_discount, b_total_pay, b_gateway_type, b_gateway_status, b_description, b_pay_now, b_process_status,
                                b_create_date, b_send_notify
                                FROM {$wpdb->prefix}fat_sb_booking";
                    $export['booking'] = $wpdb->get_results($sql);
                }

                if (isset($data['settings']) && $data['settings'] == 1) {
                    $setting = FAT_DB_Setting::instance();
                    $export['settings'] = $setting->get_setting();
                    $export['working_hour_setting'] = $setting->get_working_hour_setting();
                    $export['email_template'] = $setting->get_email_template();
                }

                //export image
                $attach_files = array();
                if (isset($data['services']) && $data['services'] == 1) {
                    foreach ($export['services_category'] as $cat) {
                        $attach_files[$cat->sc_image_id] = $cat->sc_image_id;
                    }
                    foreach ($export['services'] as $s) {
                        $attach_files[$s->s_image_id] = $s->s_image_id;
                    }
                }

                if (isset($data['employees']) && $data['employees'] == 1) {
                    foreach ($export['employees'] as $emp) {
                        $attach_files[$emp->e_avatar_id] = $emp->e_avatar_id;
                    }
                }

                if (isset($data['employees']) && $data['employees'] == 1) {
                    foreach ($export['employees'] as $emp) {
                        $attach_files[$emp->e_avatar_id] = $emp->e_avatar_id;
                    }
                }
                $export['attachments'] = $this->export_files($attach_files);
                return array(
                    'result' => 1,
                    'file' => json_encode($export),
                    'file_name' => 'fat_booking_export.json'
                );
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Please select data that need export', 'fat-services-booking')
                );
            }
        }

        private function export_files($attach_ids)
        {
            $img_url = $file_path = $dest_file_name = '';
            $resource_folder = FAT_SERVICES_DIR_PATH . 'assets/export/images';
            foreach ($attach_ids as $id) {
                $img_url = wp_get_attachment_url($id);
                $file_path = parse_url($img_url);
                if (isset($file_path['path'])) {
                    $file_path = $file_path['path'];
                    $info = pathinfo($file_path);
                    $ext = $info['extension'];
                    $name = wp_basename($file_path, ".$ext");
                    $dest_file_name = "{$resource_folder}/{$name}.{$ext}";
                    $file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path;
                    $attach_ids[$id] = $name . '.' . $ext;
                    $editor = wp_get_image_editor($file_path);
                    if (!is_wp_error($editor)) {
                        $editor->save($dest_file_name);
                    }
                }
            }
            return $attach_ids;
        }
    }
}