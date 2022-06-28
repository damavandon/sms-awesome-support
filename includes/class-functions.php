<?php
// ═══════════════════════════ :هشدار: ═══════════════════════════

// ‫ تمامی حقوق مادی و معنوی این افزونه متعلق به سایت پیامیتو به آدرس payamito.com می باشد
// ‫ و هرگونه تغییر در سورس یا استفاده برای درگاهی غیراز پیامیتو ،
// ‫ قانوناً و شرعاً غیرمجاز و دارای پیگرد قانونی می باشد.

// © 2022 Payamito.com, Kian Dev Co. All rights reserved.

// ════════════════════════════════════════════════════════════════

?><?php

namespace Payamito\AwesomeSupport\Funtions;

/**
 * Plugin public functions
 *
 * @package"Payamito_Awesome_Support
 * @since   1.0.0
 */

defined('ABSPATH') || exit;

if (!class_exists("Payamito_Awesome_Support_Functions")) {

    class Payamito_Awesome_Support_Functions
    {

        /**
         * Getting actions  paymant
         *
         * @access public
         * @since 1.0.0
         * @return array
         * @static
         */

        public static function actions()
        {
            $final = array();

            $actions = ["open", "reply", "assign", "close", 'close_by_admin', 'close_by_user', 'close_by_agent','close_by_cron'];

            foreach ($actions as $action) {
                switch ($action) {
                    case "open":
                        $title = __("Open", "payamito-awesome-support");
                        break;
                    case "reply":
                        $title =  __("Reply", "payamito-awesome-support");
                        break;
                    case "assign":
                        $title =  __("Assign", "payamito-awesome-support");
                        break;
                    case "close":
                        $title =  __("Close", "payamito-awesome-support");
                        break;
                    case "close_by_admin":
                        $title =  __("Close by admin", "payamito-awesome-support");
                        break;
                    case "close_by_agent":
                        $title =  __("Close by agent", "payamito-awesome-support");
                        break;
                    case "close_by_user":
                        $title =  __("Close by user", "payamito-awesome-support");
                        break;
                    case "close_by_cron":
                        $title =  __("Automatic Ticket Close ", "payamito-awesome-support");
                        break;
                }
                array_push($final, array($action => $title));
            }
            $status = self::status();
            $final = array_merge($final, $status);
            return $final;
        }
        public static function status()
        {
            $final = array();
            array_push($final, array('processing' => esc_html__('processing', 'payamito-awesome-support')));

            array_push($final, array('hold' => esc_html__('hold', 'payamito-awesome-support')));

            $custom_statuses = get_posts(array('numberposts' => -1, 'post_type' => 'wpass_status', 'post_status' => 'publish'));
            if (is_array($custom_statuses)) {
                foreach ($custom_statuses as  $result) {

                    array_push($final, array($result->ID => $result->post_title));
                }
            }


            return  $final;
        }
        public static function user_type()
        {
            return ["administrator", "user", "agent"];
        }

        public static function option_preparation($options)
        {
            $option_preparation = [];

            $user_type = self::user_type();

            $actions = self::actions();

            if (!is_array($options)) {
                return [];
            }
            if ($options['administrator_active'] != '1' && $options['user_active'] != '1' && $options['agent_active'] != '1') {

                return $option_preparation['active'] = false;
            } else {
                $option_preparation['active'] = true;
            }

            foreach ($user_type as $user) {

                if ($options[$user . '_active'] == '1') {

                    $option_preparation[$user]['active'] = true;

                    if ($user == 'administrator') {

                        $option_preparation[$user]['phone_number'] = isset($options['admin_phone_number_repeater']) ? $options['admin_phone_number_repeater'] : '';
                    }

                    $option_preparation[$user]['meta_key'] = isset($options[$user . '_meta_key']) ? $options[$user . '_meta_key'] : '';
                } else {

                    $option_preparation[$user]['active'] = false;
                }

                foreach ($actions as $action) {
                    $slug = key($action);
                    if (isset($options[$user . '_' . $slug . '_active']) && $options[$user . '_' . $slug . '_active'] == '1') {

                        $option_preparation[$user][$slug] = true;

                        if (isset($options[$user . '_' . $slug . '_active_p']) && $options[$user . '_' . $slug . '_active_p'] == '1') {

                            $option_preparation[$user][$slug . '_pattern_active'] = true;

                            $option_preparation[$user][$slug . '_pattern'] = isset($options[$user . '_' . $slug . '_pattern_ticket']) ? $options[$user . '_' . $slug . '_pattern_ticket'] : "";

                            $option_preparation[$user][$slug . '_pattern_id'] = isset($options[$user . '_' . $slug . '_p']) ? $options[$user . '_' . $slug . '_p'] : '';
                        } else {

                            $option_preparation[$user][$slug]['pattern_active'] = false;

                            $option_preparation[$user][$slug . '_text_active'] = true;

                            $option_preparation[$user][$slug . '_text'] = isset($options[$user . '_' . $slug . '_text']) ? $options[$user . '_' . $slug . '_text'] : '';
                        }
                    } else {
                        $option_preparation[$user][$slug] = false;
                    }
                }
            }
            return  $option_preparation;
        }
        /**
         * Getting user meta key from database
         *
         * @access public
         * @since 1.0.0
         * @return array
         * @static
         */
        public static function get_meta_keys()
        {
            global $wpdb;

            $final = array();

            $results = $wpdb->get_results("SELECT DISTINCT `meta_key` FROM $wpdb->usermeta ", ARRAY_A);
            if (is_array($results)) {
                foreach ($results as  $result) {
                 $final[$result['meta_key']]=$result['meta_key'];
                }
            }
            return  $final;
        }


        /**
         * What type of request is this?
         *
         * @param  string $type admin, ajax, cron or frontend.
         * @return bool
         */
        public static function is_request($type)
        {
            switch ($type) {
                case 'admin':
                    return is_admin();
                case 'ajax':
                    return defined('DOING_AJAX');
                case 'cron':
                    return defined('DOING_CRON');
                case 'frontend':
                    return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON');
            }
        }
    }
}
