<?php
// ═══════════════════════════ :هشدار: ═══════════════════════════

// ‫ تمامی حقوق مادی و معنوی این افزونه متعلق به سایت پیامیتو به آدرس payamito.com می باشد
// ‫ و هرگونه تغییر در سورس یا استفاده برای درگاهی غیراز پیامیتو ،
// ‫ قانوناً و شرعاً غیرمجاز و دارای پیگرد قانونی می باشد.

// © 2022 Payamito.com, Kian Dev Co. All rights reserved.

// ════════════════════════════════════════════════════════════════

?><?php

namespace Payamito\AwesomeSupport\Core;

use Payamito\AwesomeSupport\Funtions\Payamito_Awesome_Support_Functions;


if (!defined('ABSPATH')) {

    die('Invalid request.');
}
if (!defined('WPINC')) {

    die;
}
if (!class_exists('Payamito_Awesome_Support_Core')) :

    class Payamito_Awesome_Support_Core
    {
        private static $_instance;

        public $ticket;

        public $ticket_id;

        public $reply_id;

        public $reply;

        public $options;

        // If the single instance hasn't been set, set it now.
        public static function init()
        {
            if (!self::$_instance) {

                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function __construct()
        {
            $this->hooks();
        }

        public function hooks()
        {
            $this->add_hooks();
        }

        public function add_hooks()
        {

            if (function_exists('wpas_insert_ticket')) {
                //hooks open ticket
                add_action('wpas_open_ticket_after',   [$this, 'open'], 11, 2);
            }
            if (function_exists('wpas_close_ticket')) {

                add_action('wpas_after_close_ticket', [$this, 'close'], 10, 3);
            }
            add_action('wpas_add_reply_after', [$this, 'reply'],  11, 2);

            add_action('wpas_ticket_assignee_changed', [$this, 'assignee_changed'], 11, 2);

            add_action('wpas_ticket_assigned', [$this, 'assigne'], 11, 2);

            add_action('save_post', [$this, 'status_updated'], 11, 3);
        }

        public function init_send($action)
        {
            $this->options = payamito_as()->functions::option_preparation(get_option('payamito_awesome_support_options'));

            if (!is_array($this->options) || $this->options['active'] == false) {
                return;
            }

            $this->admin($action, $this->options['administrator']);

            $this->agent($action, $this->options['agent']);

            $this->user($action,  $this->options['user']);
        }

        public function status_updated($post_id, $post, $updated)
        {

            if ($post->post_type != 'ticket') {
                return;
            }

            if ($updated === false) {
                return;
            }
            $old_status = get_post_meta($post_id, '_wpas_old_status', true);

            if ($old_status == $post->post_status) {
                return;
            }

            $this->get_ticket($post_id);

            $this->init_send($post->post_status);

            update_post_meta($post_id, '_wpas_old_status', $post->post_status);
        }
        public function user_is($type)
        {
            switch ($type) {
                case 'admin':
                    if (current_user_can('administrator')) {
                        return true;
                    } else {
                        return false;
                    }
                    break;
                case 'agent':
                    if (current_user_can('wpas_agent') && current_user_can('close_ticket')) {
                        return true;
                    } else {
                        return false;
                    }
                    break;
                case 'user':
                    if (current_user_can('close_ticket')) {
                        return true;
                    } else {
                        return false;
                    }
                    break;
            }
        }
        public function open($ticket_id, $data)
        {
            $this->get_ticket($ticket_id);

            $this->init_send('open');
        }

        public function close($ticket_id, $update, $user_id)
        {
            $this->get_ticket($ticket_id);

            $this->init_send('close');

            if ($this->user_is('admin')) {

                $this->init_send('close_by_admin');
            }
            if ($this->user_is('agent')) {

                $this->init_send('close_by_agent');
            }
            if ($this->user_is('user')) {
                
                $this->init_send('close_by_user');
            }


            if (Payamito_Awesome_Support_Functions::is_request('cron')) {
                $this->init_send('close_by_cron');
            }
        }
        public function assignee_changed($agent_id, $current)
        {
            #nex version
        }
        public function assigne($ticket_id)
        {
            $this->get_ticket($ticket_id);

            $this->init_send('assign');
        }


        public function reply($reply_id, $data)
        {
            $this->get_reply($reply_id);

            $this->init_send('reply');
        }

        public  function set_message($action, $option)
        {

            if ($option[$action . '_pattern_active']) {

                $pattern = $option[$action . '_pattern'];

                $pattern_id = trim($option[$action . "_pattern_id"]);

                if (is_array($pattern) && count($pattern) > 0 && is_numeric($pattern_id)) {

                    return array('type' => 1, 'message' => $pattern, 'pattern_id' => $pattern_id);
                } else {

                    return null;
                }
            } else {

                $text = trim($option[$action . '_text']);

                if ($text == '') {

                    return null;
                } else {
                    $message = $this->set_value($text);
                    return array('type' => 2, 'message' => $message);
                }
            }
        }

        public function set_value($text)
        {
            $tags = ['{ticket_id}', '{site_name}', '{agent_name}', '{agent_first_name}', '{agent_last_name}', '{agent_email}', '{client_name}', '{client_first_name}', '{client_last_name}', '{author_name}', '{author_first_name}', '{author_last_name}', '{author_email}', '{ticket_title}', '{ticket_link}', '{ticket_admin_link}', '{date}', '{admin_email}'];

            $value = [];

            foreach ($tags as  $tag) {

                array_push($value, $this->get_tag_value($tag));
            }

            $message = str_replace($tags, $value, $text);

            return $message;
        }

        public  function ready_send($action, $option)
        {
            if (!$option['active']) {

                return false;
            }

            if (!$option[$action]) {

                return false;
            }

            $message = $this->set_message($action, $option);

            if (is_null($message)) {

                return false;
            }

            return $message;
        }

        public  function set_pattern($pattern)
        {
            $send_pattern = [];

            foreach ($pattern as $index => $item) {

                $send_pattern[$item[1]] = $this->get_tag_value($item[0]);
            }
            return $send_pattern;
        }


        public function admin($action, $option)
        {
            $message = $this->ready_send($action, $option);

            if ($message === false) {

                return;
            }
            $phone_numbers = (array)$option['phone_number'];

            if (count($phone_numbers) == '0') {

                return;
            }

            foreach ($phone_numbers as $phone_number) {

                $this->start_send($message, $phone_number['admin_phone_number'],);
            }
        }

        public function agent($action, $option)
        {
            $message = $this->ready_send($action, $option);

            if ($message === false) {

                return;
            }
            $phone_number = get_user_meta(get_current_user_id(), $option['meta_key'], true);

            if ($phone_number == false || $phone_number == '') {

                return;
            }

            $this->start_send($message, $phone_number);
        }


        public function user($action, $option)
        {
            $message = $this->ready_send($action, $option);

            if ($message === false) {

                return;
            }
            $phone_number = get_user_meta(get_current_user_id(), $option['meta_key'], true);

            if ($phone_number == false || $phone_number == '') {

                return;
            }

            $this->start_send($message, $phone_number);
        }

        public function start_send($message, $phone_number)
        {
            switch ($message['type']) {

                case 1:
                    $send_pattern = $this->set_pattern($message['message']);

                    payamito_as()->send->Send_pattern($phone_number, $send_pattern, $message['pattern_id']);
                    break;

                case 2:

                    payamito_as()->send->Send($phone_number, $message['message']);
                    break;
            }
        }


        function is_multi_agent_active()
        {
            $options = $this->options;

            if (isset($options['multiple_agents_per_ticket']) && true === boolval($options['multiple_agents_per_ticket'])) {
                return true;
            }

            return false;
        }


        public function get_tag_value($tag)
        {
            /* Get the involved users' information */
            $agent_id = get_post_meta($this->ticket_id, '_wpas_assignee', true);

            // Fallback to the default assignee if for some reason there is no agent assigned
            if (empty($agent_id)) {

                $agent_id = wpas_get_option('assignee_default', 1);
            }

            $agent  = get_user_by('id',  $agent_id);

            if (is_object($agent)) {

                $agent->first_name = get_user_meta($agent->ID, 'first_name', true);
                $agent->last_name = get_user_meta($agent->ID, 'last_name', true);
            }

            $client = get_user_by('id', $this->ticket->post_author);

            if (is_object($client)) {
                $client->first_name = get_user_meta($client->ID, 'first_name', true);
                $client->last_name = get_user_meta($client->ID, 'last_name', true);
            }

            $author = get_user_by('id', $this->ticket->post_author);

            if (is_object($author)) {

                $author->first_name = get_user_meta($author->ID, 'first_name', true);
                $author->last_name = get_user_meta($author->ID, 'last_name', true);
            }

            /* Get the ticket links */
            $url_public  = get_permalink($this->ticket->ID);

            $url_private = add_query_arg(array('post' =>  $this->ticket_id, 'action' => 'edit'), admin_url('post.php'));

            /* Add the tag value in the current context */
            switch ($tag) {

                    /* Ticket ID */
                case 'ticket_id':
                case '{ticket_id}':
                    return $this->ticket->ID;
                    break;

                    /* Name of the website */
                case 'site_name':
                case '{site_name}':
                    return  get_bloginfo('name');
                    break;

                    /* Name of the agent assigned to this ticket */
                case 'agent_name':
                case '{agent_name}':
                    return !empty($agent) ? $agent->data->user_login : '';
                    break;

                case 'agent_first_name':
                case '{agent_first_name}':
                    return !empty($agent) ?  $agent->data->first_name : 'not found first name';
                    break;

                case 'agent_last_name':
                case '{agent_last_name}':
                    return !empty($agent) ?  $agent->data->last_name : 'not found last name';
                    break;

                    /* E-mail of the agent assigned to this ticket */
                case 'agent_email':
                case '{agent_email}':
                    return !empty($agent) ? $agent->data->user_email : '';
                    break;

                case 'client_first_name':
                case '{client_first_name}':
                    return !empty($client) ?  $client->data->first_name : 'not found first name';
                    break;

                case 'client_name':
                case '{client_name}':
                    return !empty($client) ? $client->data->user_login : '';
                    break;

                case 'client_last_name':
                case '{client_last_name}':
                    return !empty($client) ? $client->data->last_name : 'not found last name';
                    break;

                case 'client_email':
                case '{client_email}':
                    return !empty($client) ? $client->data->user_email : '';
                    break;

                case 'author_name':
                case '{author_name}':
                    return !empty($author) ? $author->data->user_login : '';
                    break;

                case 'author_first_name':
                case '{author_first_name}':
                    return !empty($author) ? $author->data->first_name : 'not found first name';
                    break;

                case 'author_last_name':
                case '{author_last_name}':
                    return !empty($author) ? $author->data->last_name : 'not found last name';
                    break;

                case 'author_email':
                case '{author_email}':
                    return !empty($author) ? $author->data->user_email : '';
                    break;

                case 'ticket_title':
                case '{ticket_title}':
                    return wp_strip_all_tags($this->ticket->post_title);
                    break;

                case 'ticket_link':
                case '{ticket_link}':
                    return  $url_public;
                    break;

                case 'ticket_url':
                case '{ticket_url}':
                    return $url_public;
                    break;

                case 'ticket_admin_link':
                case '{ticket_admin_link}':
                    return '<a href="' . $url_private . '">' . $url_private . '</a>';
                    break;

                case 'ticket_admin_url':
                case '{ticket_admin_url}':
                    return $url_private;
                    break;

                case 'admin_email':
                case '{admin_email}':
                    return date(get_option('date_format'));
                    break;

                case 'date':
                case '{date}':
                    return date(get_option('date_format'));
                    break;

                case 'admin_email':
                case '{admin_email}':
                    return get_bloginfo('admin_email');
                    break;
            }
        }
        /**
         * Get the post object for the ticket.
         *
         * @since  3.0.2
         * @return boolean|object The ticket object if there is a reply, false otherwise
         */
        public function get_ticket($ticket_id)
        {
            if ('ticket' !== get_post_type($ticket_id)) {
                return false;
            }
            $this->ticket = get_post($ticket_id);

            $this->ticket_id = $this->ticket->ID;

            return $this->ticket;
        }

        public function get_reply($reply_id)
        {
            if ('ticket_reply' !== get_post_type($reply_id)) {
                return false;
            }
            $this->reply = get_post($reply_id);

            $this->reply_id = $this->reply->ID;

            $this->ticket_id = $this->reply->post_parent;

            $this->get_ticket($this->ticket_id);

            return $this->reply;
        }
    }
endif;
