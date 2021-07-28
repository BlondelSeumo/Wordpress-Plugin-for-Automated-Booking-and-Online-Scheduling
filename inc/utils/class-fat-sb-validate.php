<?php
/**
 * Created by PhpStorm.
 * User: RoninWP
 * Date: 10/1/2019
 * Time: 5:07 PM
 */
if (!class_exists('FAT_SB_Validate')) {
    class FAT_SB_Validate
    {
        public function shortcode_limit_user_validate($validate){
            $db_setting = FAT_DB_Setting::instance();
            $user_role_setting = $db_setting->get_user_role_setting();
            $limit = isset($user_role_setting['limit_user']) && $user_role_setting['limit_user'] ? $user_role_setting['limit_user'] : '';
            $validate_user_at = isset($user_role_setting['validate_user_at']) && $user_role_setting['validate_user_at'] ? $user_role_setting['validate_user_at'] : '';
            $allow_users = isset($user_role_setting['allow_user_booking']) ? explode(',',$user_role_setting['allow_user_booking']) : array();


            if( ($limit=='limit_by_user' || $limit=='limit_by_role') && $validate_user_at == 'before' && is_array($allow_users) && count($allow_users) > 0){
                $user_id = get_current_user_id();
                if($user_id==0){
                    return array(
                        'result' => -1,
                        'message' =>  stripslashes($user_role_setting['warning_message'])
                    );
                }

                if($limit=='limit_by_user' && !in_array($user_id, $allow_users)){
                    return array(
                        'result' => -1,
                        'message' =>  stripslashes($user_role_setting['warning_limit_user_message'])
                    );
                }

                if($limit=='limit_by_role'){
                    $allow_role = isset($user_role_setting['allow_user_role_booking']) ? explode(',',$user_role_setting['allow_user_role_booking']) : array();
                    $user_meta=get_userdata($user_id);
                    $user_roles=$user_meta->roles;
                    $is_valid = array_intersect($allow_role, $user_roles);
                    if(!$is_valid){
                        return array(
                            'result' => -1,
                            'message' =>  stripslashes($user_role_setting['warning_limit_user_message'])
                        );
                    }
                }
                return $validate;
            }else{
                return $validate;
            }
        }

        public function booking_limit_user_validate($validate, $data){
            $db_setting = FAT_DB_Setting::instance();
            $user_role_setting = $db_setting->get_user_role_setting();
            $setting = $db_setting->get_setting();
            $limit = isset($user_role_setting['limit_user']) && $user_role_setting['limit_user'] ? $user_role_setting['limit_user'] : '';
            $limit_booking_per_day = isset($setting['limit_booking_per_day']) && $setting['limit_booking_per_day'] ? $setting['limit_booking_per_day'] : 0;
            $validate_user_at = isset($user_role_setting['validate_user_at']) && $user_role_setting['validate_user_at'] ? $user_role_setting['validate_user_at'] : '';
            $allow_users = isset($user_role_setting['allow_user_booking']) ? explode(',',$user_role_setting['allow_user_booking']) : '';
            $user_id = get_current_user_id();

            if($limit_booking_per_day && $user_id){
                global $wpdb;
                if(isset($data['b_date'])){
                    $sql = "SELECT b_service_id 
                        FROM {$wpdb->prefix}fat_sb_booking AS B 
                        INNER JOIN {$wpdb->prefix}fat_sb_customers AS C
                        ON B.b_customer_id = C.c_id
                        WHERE C.c_user_id=%d AND B.b_date=%s AND B.b_process_status IN(0,1)
                        GROUP BY B.b_service_id";
                    $sql = $wpdb->prepare($sql,$user_id, $data['b_date']);
                    $bookings = $wpdb->get_results($sql);
                    if(is_countable($bookings) && count($bookings)>= $limit_booking_per_day){
                        return array(
                            'result' => -1,
                            'message' => sprintf(esc_html__('Each user is only allowed to order maximum %d services per day','fat-services-booking'), $limit_booking_per_day)
                        );
                    }
                }

            }

            if( ($limit=='limit_by_user' || $limit=='limit_by_role') && $validate_user_at == 'after' && is_array($allow_users) && count($allow_users) > 0){
                if($user_id==0){
                    return array(
                        'result' => -1,
                        'message' =>  stripslashes($user_role_setting['warning_message'])
                    );
                }

                if($limit=='limit_by_user' && !in_array($user_id, $allow_users)){
                    return array(
                        'result' => -1,
                        'message' =>  stripslashes($user_role_setting['warning_limit_user_message'])
                    );
                }

                if($limit=='limit_by_role'){
                    $allow_role = isset($user_role_setting['allow_user_role_booking']) ? explode(',',$user_role_setting['allow_user_role_booking']) : array();
                    $user_meta=get_userdata($user_id);
                    $user_roles=$user_meta->roles;
                    $is_valid = array_intersect($allow_role, $user_roles);
                    if(!$is_valid){
                        return array(
                            'result' => -1,
                            'message' =>  stripslashes($user_role_setting['warning_limit_user_message'])
                        );
                    }
                }

            }else{
                return $validate;
            }
        }

        public static function check_ajax_refer(){
            return  true;//check_ajax_referer( 'fat-sb-security-field', 's_field', false );
        }
    }
}