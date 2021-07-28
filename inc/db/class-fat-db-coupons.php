<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 3/7/2019
 * Time: 9:10 AM
 */
if (!class_exists('FAT_DB_Coupons')) {
    class FAT_DB_Coupons
    {
        private static $instance = NULL;

        public static function instance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function get_coupons()
        {
            global $wpdb;
            $page = isset($_REQUEST['page']) && $_REQUEST['page'] ? $_REQUEST['page'] : 1;
            $sql = "SELECT cp_id, cp_code, cp_discount_type, cp_amount, cp_start_date, cp_expire, cp_times_use, cp_use_count
                                        FROM {$wpdb->prefix}fat_sb_coupons WHERE 1=%d ";

            if (isset($_REQUEST['cp_code']) && $_REQUEST['cp_code']) {
                $sql .= " AND cp_code LIKE '%{$_REQUEST['cp_code']}%'";
            }
            $sql = $wpdb->prepare($sql, 1);
            $coupons = $wpdb->get_results($sql);
            $total = count($coupons);

            $fat_db_setting = FAT_DB_Setting::instance();
            $setting =  $fat_db_setting->get_setting();

            $item_per_page = isset($setting['item_per_page']) ? $setting['item_per_page'] : 10;
            $number_of_page = $total / $item_per_page + ($total % $item_per_page > 0 ? 1 : 0);
            $page = $page > $number_of_page ? $number_of_page : $page;
            $page = ($page - 1) * $item_per_page;
            $coupons = array_slice($coupons, $page, $item_per_page);
            return array(
                'total' => $total,
                'coupons' => $coupons
            );
        }

        public function exists_coupon()
        {
            $now = current_time( 'mysql', 0);
            $now = DateTime::createFromFormat('Y-m-d H:i:s',$now);
            $now = $now->format('Y-m-d').' 00:00:00';
            global $wpdb;
            $sql = "SELECT cp_id FROM {$wpdb->prefix}fat_sb_coupons WHERE  cp_start_date <= %s AND %s <= cp_expire ";
            $sql = $wpdb->prepare($sql, $now, $now);
            $is_exist = $wpdb->get_results($sql);

            return count($is_exist);
        }

        public function save_coupon()
        {
            $data = isset($_REQUEST['data']) && $_REQUEST['data'] ? $_REQUEST['data'] : '';
            if ($data != '' && is_array($data)) {
                $date_format = get_option('date_format');

                $cp_start_date = isset($data['cp_start_date']) && $data['cp_start_date'] ? DateTime::createFromFormat($date_format, $data['cp_start_date']) : '';
                $cp_expire = isset($data['cp_expire']) && $data['cp_expire'] ? DateTime::createFromFormat($date_format, $data['cp_expire']) : '';
                if ($cp_start_date instanceof DateTime && $cp_expire instanceof DateTime && $cp_expire->diff($cp_start_date)->days <= 0) {
                    echo json_encode(array(
                        'result' => -1,
                        'message' => esc_html__('Expire date need bigger than start date apply', 'fat-services-booking')
                    ));
                    wp_die();
                }

                $cp_id = isset($data['cp_id']) && $data['cp_id'] != '' ? $data['cp_id'] : 0;
                global $wpdb;

                $sql = "SELECT cp_id
                                        FROM {$wpdb->prefix}fat_sb_coupons
                                        WHERE cp_id <> %d AND cp_code=%s";
                $sql = $wpdb->prepare($sql, $cp_id, $data['cp_code']);
                $is_exist_code = $wpdb->get_results($sql);

                if (count($is_exist_code) > 0) {
                    return array(
                        'result' => -2,
                        'message' => esc_html__('This code has been used. Please use another code', 'fat-services-booking')
                    );
                }

                if ($cp_id > 0) {
                    $result = $wpdb->update($wpdb->prefix . 'fat_sb_coupons', $data, array('cp_id' => $data['cp_id']));
                } else {
                    $data['cp_create_date'] =  current_time( 'mysql', 0);
                    $result = $wpdb->insert($wpdb->prefix . 'fat_sb_coupons', $data);
                    $result = $result > 0 ? $wpdb->insert_id : $result;
                }
                return array(
                    'result' => $result,
                );
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Please input data for field', 'fat-services-booking')
                );
            }
        }

        public function get_coupon_by_id()
        {
            $cp_id = isset($_REQUEST['cp_id']) ? $_REQUEST['cp_id'] : 0;
            global $wpdb;
            if ($cp_id) {
                $sql = "SELECT cp_id, cp_code, cp_apply_to, cp_exclude, cp_discount_type, cp_amount, cp_start_date, cp_expire, cp_times_use, cp_use_count, cp_description
                                        FROM {$wpdb->prefix}fat_sb_coupons
                                        WHERE cp_id=%d";
                $sql = $wpdb->prepare($sql, $cp_id);
                $coupon = $wpdb->get_results($sql);
                if (count($coupon) > 0) {
                    $coupon = $coupon[0];
                    $coupon->data_start_date = explode(' ',$coupon->cp_start_date)[0];
                    $coupon->data_expire_date = explode(' ',$coupon->cp_expire)[0];
                } else {
                    $coupon = array(
                        'cp_id' => 0,
                        'cp_code' => '',
                    );
                }
            } else {
                $coupon = array(
                    'cp_id' => 0,
                    'cp_code' => '',
                );
            }
            return $coupon;
        }

        public function delete_coupon()
        {
            $cp_ids = isset($_REQUEST['cp_ids']) ? $_REQUEST['cp_ids'] : '';
            if ($cp_ids) {
                global $wpdb;
                $number_cp_detele = count($cp_ids);
                $cp_ids = implode(',', $cp_ids);

                $sql = "SELECT cp_id
                                        FROM {$wpdb->prefix}fat_sb_coupons 
                                        WHERE cp_use_count = %d AND cp_id IN ({$cp_ids})";
                $sql = $wpdb->prepare($sql, 0);
                $cp_ids_delete = $wpdb->get_results($sql);

                if (count($cp_ids_delete) == 0) {
                    return array(
                        'result' => -1,
                        'message_error' => esc_html__('You cannot delete coupon(s) because it has been used', 'fat-services-booking')
                    );
                } else {
                    $cp_ids = array();
                    foreach ($cp_ids_delete as $cp_id) {
                        $cp_ids[] = $cp_id->cp_id;
                    }
                    $cp_ids_delete = implode(',', $cp_ids);

                    $sql = "DELETE FROM {$wpdb->prefix}fat_sb_coupons WHERE  cp_use_count = %d AND cp_id IN ({$cp_ids_delete}) ";
                    $sql = $wpdb->prepare($sql, 0);
                    $result = $wpdb->query($sql);

                    return array(
                        'result' => $result,
                        'ids_delete' => $cp_ids,
                        'message_success' => $result > 0 ? $result . esc_html__(' coupon(s) have been deleted', 'fat-services-booking') : '',
                        'message_error' => ($result < $number_cp_detele && ($number_cp_detele - $result) > 0) ? ($number_cp_detele - $result) . esc_html__(' coupon(s) can not delete because it has been used', 'fat-services-booking') : ''
                    );
                }
            } else {
                return array(
                    'result' => 1,
                );
            }
        }

        public function get_coupon_discount()
        {
            $coupon_code = isset($_REQUEST['coupon']) && $_REQUEST['coupon'] ? $_REQUEST['coupon'] : '';
            $s_id = isset($_REQUEST['s_id']) && $_REQUEST['s_id'] ? $_REQUEST['s_id'] : '';
            if ($coupon_code) {
                $result = FAT_SB_Utils::getCoupon($coupon_code, $s_id);
                return $result;
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('The coupon is invalid', 'fat-services-booking')
                );
            }
        }
    }
}