<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 3/7/2019
 * Time: 9:10 AM
 */
if (!class_exists('FAT_DB_Services_Extra')) {
    class FAT_DB_Services_Extra
    {
        private static $instance = NULL;

        public static function instance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function get_services_extra()
        {
            global $wpdb;
            $sql = "SELECT se_id, se_name, se_image_id, se_price, se_tax, se_min_quantity, se_max_quantity, se_duration, se_description, se_multiple_book
                                        FROM {$wpdb->prefix}fat_sb_services_extra";
            $services_extra = $wpdb->get_results($sql);
            foreach ($services_extra as $ser) {
                $ser->se_image_url = isset($ser->se_image_id) ? wp_get_attachment_image_src($ser->se_image_id, 'thumbnail') : '';
                $ser->se_image_url = isset($ser->se_image_url[0]) ? $ser->se_image_url[0] : '';
            }
            return $services_extra;
        }

        public function get_service_extra_by_id()
        {
            $se_id = isset($_REQUEST['se_id']) ? $_REQUEST['se_id'] : 0;
            global $wpdb;
            $sql = "SELECT se_id, se_name, se_image_id, se_price, se_tax, se_min_quantity, se_max_quantity, se_duration, se_description, se_multiple_book, se_price_on_total
                                        FROM {$wpdb->prefix}fat_sb_services_extra 
                                        WHERE se_id=%d";
            $sql = $wpdb->prepare($sql, $se_id);
            $services = $wpdb->get_results($sql);
            $services = count($services) > 0 ? $services[0] : array();
            if(isset($services->se_tax)){
                $services->se_tax = $services->se_tax ? floatval($services->se_tax) : 0;
                $services->se_image_url = isset($services->se_image_id) ? wp_get_attachment_image_src($services->se_image_id, 'thumbnail') : '';
                $services->se_image_url = isset($services->se_image_url[0]) ? $services->se_image_url[0] : '';
            }
            return $services;
        }

        public function save_service_extra()
        {
            $data = isset($_REQUEST['data']) && $_REQUEST['data'] ? $_REQUEST['data'] : '';
            if ($data != '' && is_array($data)) {
                global $wpdb;
                if (isset($data['se_id']) && $data['se_id'] != '') {
                    $result = $wpdb->update($wpdb->prefix . 'fat_sb_services_extra', $data, array('se_id' => $data['se_id']));
                } else {
                    $data['se_create_date'] = current_time( 'mysql', 0);
                    $result = $wpdb->insert($wpdb->prefix . 'fat_sb_services_extra', $data);
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

        public function delete_service_extra()
        {
            $se_ids = isset($_REQUEST['se_ids']) && $_REQUEST['se_ids'] ? $_REQUEST['se_ids'] : '';
            if ($se_ids && is_array($se_ids)) {
                global $wpdb;
                $se_ids = implode($se_ids, ',');
                $sql = "DELETE FROM {$wpdb->prefix}fat_sb_services_extra WHERE 1=%d AND se_id IN ({$se_ids})";
                $sql = $wpdb->prepare($sql, 1);
                $result = $wpdb->query($sql);
                return array(
                    'result' => $result,
                );
            } else {
                return array(
                    'result' => -1,
                    'message' => esc_html__('Data is invalid', 'fat-services-booking')
                );
            }
        }
    }
}