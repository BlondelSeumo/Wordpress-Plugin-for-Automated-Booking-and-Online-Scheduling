<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 3/7/2019
 * Time: 9:10 AM
 */
if (!class_exists('FAT_DB_Locations')) {
    class FAT_DB_Locations
    {
        private static $instance = NULL;

        public static function instance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function get_locations()
        {
            global $wpdb;
            $loc_name = isset($_REQUEST['loc_name']) ? $_REQUEST['loc_name'] : '';

            $sql = "SELECT loc_id, loc_image_id, loc_name, loc_address, loc_description FROM {$wpdb->prefix}fat_sb_locations WHERE 1=%d AND  loc_name LIKE '%{$loc_name}%'";
            $sql = $wpdb->prepare($sql, 1);
            $locations = $wpdb->get_results($sql);
            foreach ($locations as $loc) {
                $loc->loc_image_url = isset($loc->loc_image_id) ? wp_get_attachment_image_src($loc->loc_image_id, 'thumbnail') : '';
                $loc->loc_image_url = isset($loc->loc_image_url[0]) ? $loc->loc_image_url[0] : '';
            }
            return $locations;
        }

        public function get_location_by_id()
        {
            $loc_id = isset($_REQUEST['loc_id']) ? $_REQUEST['loc_id'] : 0;
            global $wpdb;
            $sql = "SELECT loc_id, loc_image_id, loc_name, loc_address, loc_link, loc_latitude_x, loc_latitude_y, loc_description FROM {$wpdb->prefix}fat_sb_locations WHERE loc_id=%d";
            $sql = $wpdb->prepare($sql, $loc_id);
            $locations = $wpdb->get_results($sql);
            $locations = is_array($locations) && count($locations) > 0 ? $locations[0] : array();
            $locations->loc_image_url = isset($locations->loc_image_id) ? wp_get_attachment_image_src($locations->loc_image_id, 'thumbnail') : '';
            $locations->loc_image_url = isset($locations->loc_image_url[0]) ? $locations->loc_image_url[0] : '';
            return $locations;
        }

        public function save_location()
        {
            $data = isset($_REQUEST['data']) && $_REQUEST['data'] ? $_REQUEST['data'] : '';
            if ($data != '' && is_array($data)) {
                global $wpdb;
                if (isset($data['loc_id']) && $data['loc_id'] != '') {
                    $result = $wpdb->update($wpdb->prefix . 'fat_sb_locations', $data, array('loc_id' => $data['loc_id']));
                } else {
                    $data['loc_create_date'] = current_time( 'mysql', 0);
                    $result = $wpdb->insert($wpdb->prefix . 'fat_sb_locations', $data);
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

        public function delete_location()
        {
            $loc_id = isset($_REQUEST['loc_id']) && $_REQUEST['loc_id'] ? $_REQUEST['loc_id'] : '';
            global $wpdb;

            $sql = "SELECT b_id FROM {$wpdb->prefix}fat_sb_booking WHERE b_loc_id = %d";
            $sql = $wpdb->prepare($sql, $loc_id);
            $booking = $wpdb->get_results($sql);
            if(is_array($booking) && count($booking)>0){
                return array(
                    'result' => -1,
                    'message' => esc_html__('You need to delete the appointment of this location before deleting the location', 'fat-services-booking')
                );
            }

            $sql = "DELETE FROM {$wpdb->prefix}fat_sb_locations WHERE loc_id = %d";
            $sql = $wpdb->prepare($sql, $loc_id);
            $result = $wpdb->query($sql);
            return array(
                'result' => $result,
            );
        }
    }
}