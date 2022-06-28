<?php
// ═══════════════════════════ :هشدار: ═══════════════════════════

// ‫ تمامی حقوق مادی و معنوی این افزونه متعلق به سایت پیامیتو به آدرس payamito.com می باشد
// ‫ و هرگونه تغییر در سورس یا استفاده برای درگاهی غیراز پیامیتو ،
// ‫ قانوناً و شرعاً غیرمجاز و دارای پیگرد قانونی می باشد.

// © 2022 Payamito.com, Kian Dev Co. All rights reserved.

// ════════════════════════════════════════════════════════════════

?><?php

/**
 * Awesome Support Customer  Add Field .
 *
 * @package  Payamito
 * @category Integration
 */

namespace Payamito\AwesomeSupport\Field;

use Payamito_OTP;
use Payamito\AwesomeSupport\Funtions\Payamito_Awesome_Support_Functions;


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('Payamito_Awesome_Support_Field')) :

    class Payamito_Awesome_Support_Field
    {

        protected static $instance = null;

        private $trust = array();

        private $OTP = null;

        function __construct()
        {
            if (self::check_once()) {
                global $payamito_awesome_support_otp_options;

                if ($payamito_awesome_support_otp_options['active'] == false) {
                    return;
                }
                add_action("plugins_loaded", [&$this, "fields"]);

                add_action("wpas_before_submit_new_ticket_checks", [$this, "submit_ticket"], 999);

                add_action("wpas_open_ticket_after", [&$this, "save_phone_number_field"], 10, 2);
            }
            add_filter('wpas_cf_wrapper_markup', [&$this, 'maybe_hide_field_wrapper'], 10, 4);

            add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);

            add_action('wp_ajax_payamito_awesome_support', [$this, 'ajax']);

            add_action('wp_ajax_nopriv_payamito_awesome_support', [$this, 'ajax']);
        }
        /**
         * Start the Class when called
         *
         * @since   1.0.0
         */
        public static function get_instance()
        {
            // If the single instance hasn't been set, set it now.
            if (null == self::$instance) {

                self::$instance = new self;
            }
            return self::$instance;
        }

        /**
         * enqueue scripts
         *
         * @since   1.0.0
         */
        public function wp_enqueue_scripts()
        {

            wp_enqueue_script("payamito-awesome-support-front-app-js",  PAYAMITO_AWESOME_SUPPORT_URL . "assets/js/app.js", array('jquery'), false, true);

            wp_enqueue_script("payamito-awesome-support-front-notification-js",  PAYAMITO_AWESOME_SUPPORT_URL . "assets/js/notification.js", array('jquery'), false, true);

            wp_localize_script("payamito-awesome-support-front-notification-js", "payamito_awesome_support_general", array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('Awoas'),
                "OTP_Success" => __("Send OTP success", "payamito-awesome-support"),
                "OTP_Fail" => __("Send OTP failed", "payamito-awesome-support"),
                'Send' => __("Send request failed please contact with support team ", "payamito-awesome-support"),
                'OTP_Wrong' => __("OTP is wrong", "payamito-awesome-support"),
                'OTP_Correct' => __("OTP is wrong", "payamito-awesome-support"),
                'invalid' => __("phone number is incorrct", "payamito-awesome-support"),
                'error' => __("Error", "payamito-awesome-support"),
                'success' => __("Success", "payamito-awesome-support"),
                "warning" => __("Warning", "payamito-awesome-support"),
                'enter' => __('Enter OTP number ', 'payamito-awesome-support'),
                'second' => __('Second', 'payamito-awesome-support'),
            ));

            wp_enqueue_script("payamito-awesome-support-front-spinner-js",  PAYAMITO_AWESOME_SUPPORT_URL . "assets/js/spinner.js", array('jquery'), false, true);

            ////////style///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            wp_enqueue_style("payamito-awesome-support-front-app-css",  PAYAMITO_AWESOME_SUPPORT_URL . "assets/css/app.css");

            wp_enqueue_style("payamito-awesome-support-front-notification-css",  PAYAMITO_AWESOME_SUPPORT_URL . "assets/css/notification.css");

            wp_enqueue_style("payamito-awesome-support-front-spinner-css",  PAYAMITO_AWESOME_SUPPORT_URL . "assets/css/spinner.css");

            wp_enqueue_style("payamito-awesome-support-front-otp-css",  PAYAMITO_AWESOME_SUPPORT_URL . "assets/css/otp-error.css");
        }

        /**
         * handling OTP ajax request
         *
         * @since 1.0.0
         *
         * @return void
         */

        public function ajax()
        {

            if (!payamito_as()->functions::is_request("ajax")) {
                wp_die();
            }
            $phone_number = payamito_to_english_number(sanitize_text_field($_REQUEST['phone_number']));

            if (
                !isset($phone_number) ||
                empty($phone_number)  ||
                !is_numeric($phone_number)
            ) {
                wp_die();
            }
            $this->phone_number_confirmation($phone_number);
        }

        /**
         * phone_number number validation and sending SMS
         *
         * @since 1.0.0
         *
         * @return void
         */
        public  function phone_number_confirmation($phone_number)
        {
            global $payamito_awesome_support_otp_options;

            $options = $payamito_awesome_support_otp_options;

            if ($options['force_otp' != '1']) {
                return;
            }
            if (!payamito_verify_moblie_number($phone_number)) {

                $this->ajax_response(-1, self::message(0));
            }
            Payamito_OTP::payamito_resent_time_check($phone_number);

            if ($options['pattern_active'] == true) {

                $pattern_id = trim($options['pattern_id']);

                if (empty($pattern_id)) {

                    return;
                }

                if (!is_array($options['pattern']) || count($options['pattern']) == 0) {

                    return;
                }
                $pattern = $this->set_otp_pattern($options['pattern'], $options['number_of_code_otp']);

                $result = payamito_as()->send->Send_pattern($phone_number, $pattern, $pattern_id);

                if ($result['result'] === true && !empty($this->OTP) ) {
                    $phone_number=(string)$phone_number;
                    $OTP=(string)$this->OTP;

                    Payamito_OTP::payamito_set_session($phone_number, $OTP);

                    return  $this->ajax_response(1, self::message(1));
                } else {

                    return  $this->ajax_response(-1, $result['message']);
                }
            } else {

                $messages = trim($options['text']);

                if (empty($messages)) {
                    return;
                }
                $messages_value = $this->set_value($messages, $options['number_of_code_otp']);

                $result = payamito_as()->send->Send($phone_number, $messages_value);

                if ($result === true) {

                    Payamito_OTP::payamito_set_session($phone_number, $this->OTP);
                    return  $this->ajax_response(1, self::message(1));
                } else {

                    return  $this->ajax_response(-1, $result['message']);
                }
            }
        }
       
        public function set_otp_pattern($pattern, $count = 4)
        {
            $send_pattern = [];
            foreach ($pattern as $item) {

                switch ($item['payamito_awesome_support_opt_tags']) {
                    case 'OTP':
                    case '{OTP}':
                        $this->OTP = Payamito_OTP::payamito_generate_otp($count);
                        $send_pattern[$item['payamito_awesome_support_otp_user_otp']] = $this->OTP;
                        break;
                    case 'site_name':
                    case '{site_name}':
                        $send_pattern[$item['payamito_awesome_support_otp_user_otp']] = get_bloginfo('name');
                        break;
                }
            }
            return $send_pattern;
        }

        public function set_value($text, $count = 4)
        {

            $tags = ['{site_name}', '{OTP}'];
            $value = [];

            foreach ($tags as  $tag) {

                switch ($tag) {
                    case "OTP":
                    case "{OTP}":
                        $this->OTP = Payamito_OTP::payamito_generate_otp($count);
                        array_push($value, $this->OTP);
                        break;
                    case "site_name":
                    case "{site_name}":
                        array_push($value, get_bloginfo('name'));
                        break;
                }
            }

            $message = str_replace($tags, $value, $text);

            return $message;
        }


        /**
         * ajax response message
         *
         * @access public
         * @since 1.0.0
         * @return array
         * @static
         */
        public static function message($key)
        {
            $messages = array(
                __('phone_number number is incorrect', 'payamito-edd'),
                __('OTP sent successfully', 'payamito-edd'),
                __('Failed to send OTP ', 'payamito-edd'),
                __('An unexpected error occurred. Please contact support ', 'payamito-edd'),
                __('Enter OTP number ', 'payamito-edd'),
                __(' OTP is Incorrect ', 'payamito-edd'),
            );
            return $messages[$key];
        }
        /**
         * ajax response
         *The response to the OTP request is given in Ajax
         * @access public
         * @since 1.0.0
         * @static
         */
        public  function  ajax_response(int $type = -1, $message, $redirect = null)
        {
            wp_send_json(array('e' => $type, 'message' => $message, "re" => $redirect));
            die;
        }
        /**
         * Register custom fields after the plugin is safely loaded.
         * products list custom filed 
         * requied
         */
        /**
         * Register the orders field
         *
         * @since  1.0.0
         * @return void
         */
        function fields()
        {
            if (Payamito_Awesome_Support_Functions::is_request('ajax')) {
                return;
            }
            global $payamito_awesome_support_otp_options;

            if ($payamito_awesome_support_otp_options['active'] != true) {

                return;
            }
            $phone_number = $this->get_user_phone_number($payamito_awesome_support_otp_options['meta_key']);
            if ($payamito_awesome_support_otp_options['once'] == true && !empty($phone_number)) {
                return;
            }

            $this->field_phone_number($payamito_awesome_support_otp_options);

            $this->field_otp($payamito_awesome_support_otp_options);
        }
        public function get_user_phone_number($meta_key)
        {
            $phone_number = get_user_meta(get_current_user_id(), $meta_key, true);
            if (empty($phone_number)) {
                $phone_number = get_user_meta(get_current_user_id(), 'payamito_phone_number', true);
            }
            return $phone_number;
        }

        public function field_phone_number($options)
        {
            global $payamito_awesome_support_otp_options;

            $default = get_user_meta(get_current_user_id(), $payamito_awesome_support_otp_options['meta_key'], true);

            $required = $options['force_enter'] == true ? true : false;

            $args = array(
                "title" => __('Phone number', 'payamito-awesome-support '),
                'field_type'            => 'number',
                'placeholder'           => "Phone number To Send Ticket",
                "show_column" => false,
                "filterable" => true,
                "default" => $default,
                "html5_pattern" => "^(?:0|\(?\+33\)?\s?|0033\s?)[1-79](?:[\.\-\s]?\d\d){4}$",
                'label'                 => __('Phone number ', 'payamito-awesome-support '),
                'taxo_hierarchical'     => true,
                'update_count_callback' => 'wpas_update_ticket_tag_terms_count',
                'required' => $required,
                "order" => 1
            );
            if (function_exists('wpas_add_custom_field')) {
                wpas_add_custom_field("payamito_awesome_support_phone_number", $args);
            }
            $args = array(
                'field_type'            => 'text',
                "extra_wrapper_css_classes" => "payamito-awesome-support-dispaly",
                "default" => wp_create_nonce("payamito_awesome_support"),
                'taxo_hierarchical'     => true,
                "sortable_column" => false,
                "filterable" => false,
                "show_column" => false,
                'readonly' => true,
                'save_callback' => false,
                'show_frontend_list' => false,
                'show_frontend_detail' => false,
            );
            if (function_exists('wpas_add_custom_field')) {
                wpas_add_custom_field("payamito_awesome_support_nonce", $args);
            }
        }

        public function field_otp($options)
        {
            global $payamito_awesome_support_otp_options;
            if ($options['force_otp'] != true) {
                return;
            }
            $args = array(
                "title" => __('OTP', 'payamito-awesome-support '),
                'field_type'            => 'text',
                'placeholder'           => "OTP",
                "sortable_column" => false,
                "filterable" => false,
                "show_column" => false,
                'label'                 => __('OTP ', 'payamito-awesome-support '),
                'taxo_hierarchical'     => true,
                'update_count_callback' => 'wpas_update_ticket_tag_terms_count',
                'required' => true,
                "order" => 2,
                'show_frontend_list' => false,
                'show_frontend_detail' => false,
                'save_callback' => false
            );
            if (function_exists('wpas_add_custom_field')) {
                wpas_add_custom_field("payamito_awesome_support_otp", $args);
            }
            $args = array(

                'field_type'            => 'text',
                "readonly" => true,
                "sortable_column" => false,
                "filterable" => false,
                "show_column" => false,
                "default" => __('Send OTP ', 'payamito-awesome-support '),
                "extra_field_css_classes" => "payamito-awesome-support-send-otp",
                'required' => true,
                "order" => 3,
                'show_frontend_list' => false,
                'show_frontend_detail' => false,
                'save_callback' => false
            );
            if (function_exists('wpas_add_custom_field')) {
                wpas_add_custom_field("payamito_awesome_support_send_otp", $args);
            }
            $args = array(
                'field_type'            => 'text',
                "extra_wrapper_css_classes" => "payamito-awesome-support-dispaly",
                "default" => $payamito_awesome_support_otp_options['again_send_time_otp'],
                "sortable_column" => false,
                "filterable" => false,
                "show_column" => false,
                'readonly' => true,
                'show_frontend_list' => false,
                'show_frontend_detail' => false,
                'save_callback' => false
            );
            if (function_exists('wpas_add_custom_field')) {
                wpas_add_custom_field("payamito_awesome_support_otp_time", $args);
            }
        }

        public function save_phone_number_field($ticket_id, $data)
        {
            if (Payamito_Awesome_Support_Functions::is_request('ajax')) {
                return;
            }

            if (!current_user_can("create_ticket")) {
                exit;
            }
            if (!count($this->trust)) {
                exit;
            }
            if (!$this->trust[$_REQUEST['wpas_payamito_awesome_support_phone_number']]) {
                exit;
            }
            global $payamito_awesome_support_otp_options;
            $meta_key = $payamito_awesome_support_otp_options['meta_key'];
            if (empty($meta_key)) {
                $meta_key = 'payamito_phone_number';
            }
            $phone_number = sanitize_text_field($_REQUEST['wpas_payamito_awesome_support_phone_number']);
            update_user_meta(get_current_user_id(), $meta_key, $phone_number);
            unset($_SESSION[$phone_number]);
            unset($_SESSION[$phone_number . 'T']);
        }

        /**
         * Maybe hide the field by setting the wrapper to display:none
         *
         * If an order number is found then we don't need to ask the client
         * to choose in the list, we pre select it and hide the field
         *
         * @since 1.0.6
         *
         * @param string $wrapper
         * @param array  $field
         * @param string $wrapper_class
         * @param string $wrapper_id
         *
         * @return string
         */
        public function maybe_hide_field_wrapper($wrapper, $field, $wrapper_class, $wrapper_id)
        {
            if ('order' !== $field['name']) {
                return $wrapper;
            }
            global $wp_query;

            $order_id = isset($wp_query->query['view-order']) ? $wp_query->query['view-order'] : '';

            if ($order_id) {
                $wrapper = str_replace('<div', '<div style="display:none;"', $wrapper);
            }
            return $wrapper;
        }

        public function submit_ticket()
        {
            global $payamito_awesome_support_otp_options;
            $slug = payamito_as()->slug;

            $phone_number = sanitize_text_field(payamito_to_english_number($_REQUEST['wpas_payamito_awesome_support_phone_number']));
            $OTP = sanitize_text_field(payamito_to_english_number($_REQUEST['wpas_payamito_awesome_support_otp']));

            if (!isset($_REQUEST['wpas_payamito_awesome_support_nonce']) || !wp_verify_nonce($_REQUEST['wpas_payamito_awesome_support_nonce'], $slug)) {

                wp_die("nonce is invalid");
            }
            $url = esc_url_raw($_POST['_wp_http_referer']);
            if ($payamito_awesome_support_otp_options['force_enter'] == true) {
                if (
                    !isset($_REQUEST['wpas_payamito_awesome_support_phone_number']) ||

                    empty($_REQUEST['wpas_payamito_awesome_support_phone_number'])  ||

                    !payamito_verify_moblie_number($_REQUEST['wpas_payamito_awesome_support_phone_number'])
                ) {
                    wpas_add_error('validation_issue', __("Please enter a valid phone number", 'payamito-awesome-support '));
                    wp_redirect($url);
                    exit;
                }
            }
            if ($payamito_awesome_support_otp_options['force_otp'] == true) {
                if (
                    !isset($_REQUEST['wpas_payamito_awesome_support_otp']) ||

                    empty($_REQUEST['wpas_payamito_awesome_support_otp']) ||

                    !is_numeric($_REQUEST['wpas_payamito_awesome_support_otp'])
                ) {
                    wpas_add_error('validation_issue', __("OTP not set", 'payamito-awesome-support '));
                    wp_redirect($url);
                    exit;
                }
                if (!payamito_verify_moblie_number($phone_number)) {

                    $this->trust[$phone_number] = false;
                    wpas_add_error('validation_issue', __("Phone number format is incorrect.  Correct sample 09121234567", 'payamito-awesome-support '));
                    wp_redirect($url);
                    exit;
                }
                if (!Payamito_OTP::payamito_validation_session($phone_number, $OTP)) {

                    $this->trust[$phone_number] = false;
                    wpas_add_error('validation_issue', __("OTP is incorrect", 'payamito-awesome-support '));
                    wp_redirect($url);
                    exit;
                }
            }
            $this->trust[$phone_number] = true;
            return;
        }

        public static  function check_once()
        {
            global $payamito_awesome_support_options;
            $phone_number = get_user_meta(get_current_user_id(), "payamito_awesome_support_phone_number", true);
            if ($phone_number == false) {
                return true;
            }
            if (!isset($payamito_awesome_support_options['once_get'])) {
                return false;
            }
            if ($payamito_awesome_support_options['once_get'] === true) {
                return true;
            }
        }
    }
endif;
