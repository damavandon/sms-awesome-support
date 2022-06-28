<?php
// ═══════════════════════════ :هشدار: ═══════════════════════════

// ‫ تمامی حقوق مادی و معنوی این افزونه متعلق به سایت پیامیتو به آدرس payamito.com می باشد
// ‫ و هرگونه تغییر در سورس یا استفاده برای درگاهی غیراز پیامیتو ،
// ‫ قانوناً و شرعاً غیرمجاز و دارای پیگرد قانونی می باشد.

// © 2022 Payamito.com, Kian Dev Co. All rights reserved.

// ════════════════════════════════════════════════════════════════

?><?php

	namespace Payamito\AwesomeSupport\Options;

	/**
	 * Register an options panel.
	 *
	 * @package Payamito
	 */
	// Exit if accessed directly
	if (!defined('ABSPATH')) {
		exit;
	}

	class  Payamito_Awesome_Support_Options
	{
		/**
		 * Holds the options panel controller.
		 *
		 * @var object
		 */
		protected $panel;

		public $actiones;
		/**
		 * Get things started.
		 */
		public function __construct()
		{
			add_filter('payamito_add_section', [$this, 'register_settings'], 1);

			add_action('admin_footer', [$this, "print_tags"]);
		}

		public function register_settings($section)
		{

			$awesome_support_sms_settings = array(
				'title'  => esc_html__('Awesome Support', 'payamito-awesome-support'),
				'fields' => array(
					array(
						'id'            => 'payamito_awesome_support_otp',
						'type'          => 'accordion',
						'title'   => esc_html__('OTP', 'payamito-awesome-support'),
						'validate' => [$this, 'otp_validate_pattern'],
						'accordions'    => array(

							array(
								'title'   => esc_html__('OTP', 'payamito-awesome-support'),
								'fields'    => array(
									array(
										'id'    => 'payamito_awesome_support_otp_active',
										'type'  => 'switcher',
										'title'  => esc_html__('Active', 'payamito-awesome-support'),
									),
									array(
										'id'      => 'payamito_awesome_support_otp_meta_key',
										'title'    => esc_html__('Meta key', 'payamito-awesome-support'),
										'desc'    => esc_html__('Save the user‍‍‍‍‍‍‍ phone number number after confirming it in this meta key', 'payamito-awesome-support'),
										'type'    => 'text',
										'dependency' => array("payamito_awesome_support_otp_active", '==', 'true'),
									),
								    array(
					                'type'       => 'notice',
					                'style'      => 'warning',
									'content'    => esc_html__( '"توجه" توصیه می شود به صورت پیشفرض ارسال با پترن را انتخاب نمایید و در صورتیکه با این بخش آشنایی ندارید لطفا با پشتیبانی پیامیتو در ارتباط باشید', 'payamito-awesome-support' ),
									'dependency' => array("payamito_awesome_support_otp_active", '==', 'true'),
					                'class' => 'pattern_background',
								    ),
									array(
										'id'    => 'payamito_awesome_support_otp_active_p',
										'type'  => 'switcher',
										'title'      => payamito_dynamic_text('pattern_active_title'),
										'desc'       => payamito_dynamic_text('pattern_active_desc'),
										'help'       => payamito_dynamic_text('pattern_active_help'),
										'dependency' => array("payamito_awesome_support_otp_active", '==', 'true'),
										'class' => 'pattern_background',


									),
									array(
										'id'   => 'payamito_awesome_support_otp_p',
										'type'    => 'text',
										'title'      => payamito_dynamic_text('pattern_ID_title'),
										'desc'       => payamito_dynamic_text('pattern_ID_desc'),
										'help'       => payamito_dynamic_text('pattern_ID_help'),
										'dependency' => array("payamito_awesome_support_otp_active|payamito_awesome_support_otp_active_p", '==|==', 'true|true'),
										'class' => 'pattern_background',

									),
									array(
										'id'     => 'payamito_awesome_support_otp_repeater',
										'type'   => 'repeater',
										'title'      => payamito_dynamic_text('pattern_Variable_title'),
										'desc'       => payamito_dynamic_text('pattern_Variable_desc'),
										'help'       => payamito_dynamic_text('pattern_Variable_help'),
										'class' => 'pattern_background',
										'max' => '2',
										'dependency' => array("payamito_awesome_support_otp_active|payamito_awesome_support_otp_active_p", '==|==', 'true|true'),
										'fields' => array(
											array(
												'id'   => 'payamito_awesome_support_opt_tags',
												'placeholder' =>  esc_html__("Tags", "payamito-awesome-support"),
												'class' => 'pattern_background',
												'type' => 'select',
												'options' =>
												array(
													"{OTP}" => esc_html__('OTP', 'payamito-awesome-support'),
													"{site_name}" => esc_html__('Wordpress title', 'payamito-awesome-support'),
													'class' => 'pattern_background',
												)
											),
											array(
												'id'    => 'payamito_awesome_support_otp_user_otp',
												'type'  => 'number',
												'placeholder' =>  esc_html__("Your tag", "payamito-awesome-support"),
												'class' => 'pattern_background',
												'default' => '0',

											),
										)
									),
									array(
										'id'   => 'payamito_awesome_support_otp_sms',
										'title'      => payamito_dynamic_text('send_content_title'),
										'desc'       => payamito_dynamic_text('send_content_desc'),
										'help'       => payamito_dynamic_text('send_content_help'),
										'default' => esc_html__('کاربر گرامی کد تایید ثبت نام {OTP} می باشد. ', 'payamito-awesome-support'),

										'class' => 'pattern_background',
										'type' => 'textarea',
										'dependency' => array("payamito_awesome_support_otp_active|payamito_awesome_support_otp_active_p", '==|!=', 'true|true'),
									),
									array(
										'id'   => 'user_add_phone_number_field_enter',
										'title' => esc_html__('Force enter phone number  ', 'payamito-awesome-support'),
										'desc' => esc_html__('Force users to Enter phone number   ', 'payamito-awesome-support'),
										'dependency' => array("payamito_awesome_support_otp_active", '==', 'true'),
										'type' => 'switcher',
									),
									array(
										'id'   => 'user_add_phone_number_field_force_OTP',
										'title' => esc_html__('Forcing OTP ', 'payamito-awesome-support'),
										'desc' => esc_html__('Force users to phone number confirmation ', 'payamito-awesome-support'),
										'dependency' => array("payamito_awesome_support_otp_active", '==', 'true'),
										'type' => 'switcher',
									),


									array(
										'id'   => 'payamito_awesome_support_number_of_code',
										'title' => esc_html__('Number of OTP code', 'payamito-awesome-support'),
										'desc' => esc_html__('Number of OTP code that you want send for user', 'payamito-awesome-support'),
										'type' => 'select',
										'dependency' => array("payamito_awesome_support_otp_active", '==', 'true'),
										'options' => apply_filters("payamito_awesome_support_again_send_number", array(
											"4" => "4",
											"5" => "5",
											"6" => "6",
											"7" => "7",
											"8" => "8",
											"9" => "9",
											"10" => "10",
										)),
									),
									array(
										'id'   => 'payamito_awesome_support_again_send_time',
										'title' => esc_html__('Send Again', 'payamito-awesome-support'),
										'desc' => esc_html__('When you want the user to re-request OTP.', 'payamito-awesome-support'),
										'type' => 'select',
										'dependency' => array("payamito_awesome_support_otp_active", '==', 'true'),
										'options' => apply_filters("payamito_awesome_support_again_send_time", array(
											"30" => "30",
											"60" => "60",
											"90" => "90",
											"120" => "120",
											"300" => "300",
										)),
									),

									array(
										'id'    => 'payamito_awesome_support_otp_once',
										'type'  => 'switcher',
										'title'  => esc_html__('Once', 'payamito-awesome-support'),
										'dependency' => array("payamito_awesome_support_otp_active", '==', 'true'),
										'desc' => esc_html__('If the user confirms his phone number number once, he does not need to confirm his phone number number again in the next tickets', 'payamito-awesome-support'),

									),
								)
							)
						)
					),
					array(
						'id'            => 'payamito_awesome_support',
						'type'          => 'tabbed',
						'title'  => esc_html__('Message', 'payamito-awesome-support'),
						'tabs'      => $this->tabs(),
					),
				)
			);
			array_push($section, $awesome_support_sms_settings);

			return $section;
		}

		public function tabs()
		{
			$tabs = [];
			array_push($tabs, $this->admin_tab());

			array_push($tabs, $this->agent_tab());

			array_push($tabs, $this->user_tab());

			return apply_filters('payamito_awesome_support_tabs', $tabs);
		}

		public function admin_tab()
		{
			$admin_tab = array(
				'title'     => esc_html__('Admin', 'payamito-awesome-support'),
				'fields'    => array(
					array(
						'type'    => 'heading',
						'content' => esc_html__('Administrator Message', 'payamito-awesome-support'),
					),

					array(
						'id'   => 'administrator_active',
						'title' => esc_html__('Active', 'payamito-awesome-support'),
						'desc' => esc_html__('Are you want send sms to admin ', 'payamito-awesome-support'),
						'type' => 'switcher',
					),
					$this->option_get_admin_phone_number()
				),
			);
			$actions = payamito_as()->functions::actions();

			$flag = false;
			foreach ($actions as  $action) {

				$key = key($action);

				if ($key == 'processing' && $flag === false) {

					array_push($admin_tab['fields'], $this->add_header(array("administrator_active", '==', 'true')));

					$flag = true;
				}
				if (isset($action['close_by_admin'])) {
					continue;
				}
				array_push($admin_tab['fields'], $this->set_action_field('administrator', $action));
			}

			return apply_filters('awesome_support_payamito_admin_tab', $admin_tab);
		}

		public function user_tab()
		{
			$user_tab = array(
				'title'     => esc_html__('User', 'payamito-awesome-support'),
				'fields'    => array(
					array(
						'type'    => 'heading',
						'content' => esc_html__('User Ticket', 'payamito-awesome-support'),
					),
					array(
						'id'   => 'user_active',
						'title' => esc_html__('Active', 'payamito-awesome-support'),
						'desc' => esc_html__('Are you want send sms to user ', 'payamito-awesome-support'),
						'type' => 'switcher',
					),
					array(
						'id'      => 'user_meta_key',
						'title'    => esc_html__('Meta keys', 'payamito-awesome-support'),
						'desc'    => esc_html__('User meta key
					 plugin', 'payamito-awesome-support'),
						'type'    => 'select',
						'attributes'  => array(
							'style"' => "min-width:120px  !important ;width:120px "
						),
						'chosen' => true,
						'multiple' => false,
						'dependency' => array("user_active", '==', 'true'),
						'options' => payamito_as()->functions::get_meta_keys(),
						'settings' => array(
							'search_contains' => true,
						),
					),
				),
			);
			$actions = payamito_as()->functions::actions();

			$flag = false;
			foreach ($actions as  $action) {

				$key = key($action);

				if ($key == 'processing' && $flag === false) {

					array_push($user_tab['fields'], $this->add_header(array("user_active", '==', 'true')));

					$flag = true;
				}
				if (isset($action['close_by_user'])) {
					continue;
				}
				array_push($user_tab['fields'], $this->set_action_field('user', $action));
			}
			return apply_filters('awesome-support_payamito_user_tab', $user_tab);
		}
		public function agent_tab()
		{

			$agent_tab = array(
				'title'     => esc_html__('Agent', 'payamito-awesome-support'),
				'fields'    => array(
					array(
						'type'    => 'heading',
						'content' => esc_html__('Agent Ticket', 'payamito-awesome-support'),
					),
					array(
						'id'   => 'agent_active',
						'title' => esc_html__('Active', 'payamito-awesome-support'),
						'desc' => esc_html__('Are you want send sms to agent ', 'payamito-awesome-support'),
						'type' => 'switcher',
					),
					array(
						'id'      => 'agent_meta_key',
						'title'    => esc_html__('Meta keys', 'payamito-awesome-support'),
						'desc'    => esc_html__('Agent meta key
				 plugin', 'payamito-awesome-support'),
						'type'    => 'select',
						'attributes'  => array(
							'style"' => "min-width:120px  !important ;width:120px "
						),
						'chosen' => true,
						'multiple' => false,
						'dependency' => array("agent_active", '==', 'true'),
						'options' => payamito_as()->functions::get_meta_keys(),
						'settings' => array(
							'search_contains' => true,
						),
					),
				),
			);
			$actions = payamito_as()->functions::actions();

			$flag = false;
			foreach ($actions as  $action) {

				$key = key($action);

				if ($key == 'processing' && $flag === false) {

					array_push($agent_tab['fields'], $this->add_header(array("agent_active", '==', 'true')));

					$flag = true;
				}
				if (isset($action['close_by_agent'])) {
					continue;
				}
				array_push($agent_tab['fields'], $this->set_action_field('agent', $action));
			}
			return apply_filters('awesome-support_payamito_agent_tab', $agent_tab);
		}

		public function add_header($dependency)
		{
			return array(
				'type'    => 'heading',
				'content' =>  esc_html__('SMS change status', 'payamito-awesome-support'),
				'dependency' => $dependency
			);
		}
		public function get_tags()
		{
			$tags = [
				"ticket_id" => esc_html__(" Ticket id", 'payamito-awesome-support'),
				"site_name" => esc_html__("Site name", 'payamito-awesome-support'),
				"agent_name" =>  esc_html__("Agent name", 'payamito-awesome-support'),
				"agent_first_name" =>  esc_html__("  Agent first name", 'payamito-awesome-support'),
				"agent_last_name" =>  esc_html__(" Agent last name", 'payamito-awesome-support'),
				"client_name" =>  esc_html__(" User name", 'payamito-awesome-support'),
				"client_first_name" =>  esc_html__(" User first name", 'payamito-awesome-support'),
				"client_last_name" =>  esc_html__(" User last name", 'payamito-awesome-support'),
				"client_email" =>  esc_html__(" User email", 'payamito-awesome-support'),
				"author_name" =>  esc_html__("Author name", 'payamito-awesome-support'),
				"author_first_name" => esc_html__("Author first name", 'payamito-awesome-support'),
				"author_last_name" =>  esc_html__(" Author last name", 'payamito-awesome-support'),
				"author_email" =>  esc_html__("Author email", 'payamito-awesome-support'),
				"ticket_link" =>   esc_html__("  Ticket link", 'payamito-awesome-support'),
				"ticket_url" =>   esc_html__(" Ticket url", 'payamito-awesome-support'),
				"ticket_admin_link" =>   esc_html__("Ticket admin link", 'payamito-awesome-support'),
				"ticket_admin_url" =>   esc_html__("Ticket admin url", 'payamito-awesome-support'),
				"date" =>   esc_html__(" Date", 'payamito-awesome-support'),
				"admin_email" =>   esc_html__(" Admin email", 'payamito-awesome-support'),
			];
			ksort($tags, SORT_STRING);
			return $tags;
		}

		/**
		 * print tags for modal
		 *
		 */
		public function print_tags()
		{

			if (!isset($_REQUEST['page']) ||  $_REQUEST['page'] != 'payamito') {
				return;
			}

			$tags = "<div id='payamito-awesome-support-modal' class='modal ' >";
			$tags .= "<div>";

			$tags .= "<div  class='payamito-tags-modal'><p class='payamito-tag-modal' > {ticket_id}</p>";
			$tags .= "<span>" . esc_html__('Ticket id', 'payamito-awesome-support') . "</span></div>";

			$tags .= "<div  class='payamito-tags-modal'><p class='payamito-tag-modal' > {site_name}</p>";
			$tags .= "<span>" . esc_html__('Site name', 'payamito-awesome-support') . "</span></div>";

			$tags .= "<div  class='payamito-tags-modal'><p class='payamito-tag-modal' > {agent_first_name}</p>";
			$tags .= "<span>" . esc_html__('Agent first name', 'payamito-awesome-support') . "</span></div>";

			$tags .= "<div  class='payamito-tags-modal'><p class='payamito-tag-modal' > {agent_last_name}</p>";
			$tags .= "<span>" . esc_html__('Agent last name', 'payamito-awesome-support') . "</span></div>";

			$tags .= "<div  class='payamito-tags-modal'><p class='payamito-tag-modal' > {client_name}</p>";
			$tags .= "<span>" . esc_html__('Client Name', 'payamito-awesome-support') . "</span></div>";

			$tags .= "<div  class='payamito-tags-modal'><p class='payamito-tag-modal' > {client_first_name}</p>";
			$tags .= "<span>" . esc_html__('Client first name', 'payamito-awesome-support') . "</span></div>";

			$tags .= "<div  class='payamito-tags-modal'><p class='payamito-tag-modal' > {client_last_name}</p>";
			$tags .= "<span>" . esc_html__('Client last name', 'payamito-awesome-support') . "</span></div>";

			$tags .= "<div  class='payamito-tags-modal'><p class='payamito-tag-modal' > {client_email}</p>";
			$tags .= "<span>" . esc_html__('Client email', 'payamito-awesome-support') . "</span></div>";

			$tags .= "<div  class='payamito-tags-modal'><p class='payamito-tag-modal' > {author_name}</p>";
			$tags .= "<span>" . esc_html__('Author name', 'payamito-awesome-support') . "</span></div>";

			$tags .= "<div  class='payamito-tags-modal'><p class='payamito-tag-modal' > {author_first_name}</p>";
			$tags .= "<span>" . esc_html__('Author first name', 'payamito-awesome-support') . "</span></div>";

			$tags .= "<div  class='payamito-tags-modal'><p class='payamito-tag-modal' > {author_last_name}</p>";
			$tags .= "<span>" . esc_html__('Author last name', 'payamito-awesome-support') . "</span></div>";

			$tags .= "<div  class='payamito-tags-modal'><p class='payamito-tag-modal' > {author_email}</p>";
			$tags .= "<span>" . esc_html__('Author email', 'payamito-awesome-support') . "</span></div>";

			$tags .= "<div  class='payamito-tags-modal'><p class='payamito-tag-modal' > {ticket_link}</p>";
			$tags .= "<span>" . esc_html__('Ticket link', 'payamito-awesome-support') . "</span></div>";

			$tags .= "<div  class='payamito-tags-modal'><p class='payamito-tag-modal' > {ticket_url}</p>";
			$tags .= "<span>" . esc_html__('Ticket url', 'payamito-awesome-support') . "</span></div>";

			$tags .= "<div  class='payamito-tags-modal'><p class='payamito-tag-modal' > {date}</p>";
			$tags .= "<span>" . esc_html__('Date', 'payamito-awesome-support') . "</span></div>";

			$tags .= "<div  class='payamito-tags-modal'><p class='payamito-tag-modal' > {admin_email}</p>";
			$tags .= "<span>" . esc_html__('Admin email', 'payamito-awesome-support') . "</span></div>";

			$tags .= '</div>';
			echo $tags;
		}


		public  function option_set_pattern($user_type, $action)
		{
			return array(
				'id'     => $user_type . '_' . $action . '_pattern_ticket',
				'type'   => 'repeater',
				'title'      => payamito_dynamic_text('pattern_Variable_title'),
				'desc'       => payamito_dynamic_text('pattern_Variable_desc'),
				'help'       => payamito_dynamic_text('pattern_Variable_help'),
				'class' => "awesome-support-payamito-repeater pattern_background",
				'dependency' => array($user_type . "_" . $action . "_active_p", '==', 'true'),
				'fields' => array(
					array(
						'id'          => 0,
						'type'        => 'select',
						'placeholder' =>  esc_html__("Select tag", "payamito-awesome-support"),
						'class' => 'pattern_background',
						'options'     => $this->get_tags(),
					),
					array(
						'id'    => 1,
						'type'  => 'number',
						'placeholder' =>  esc_html__("Your tag", "payamito-awesome-support"),
						'default' => '0',
					),
				)
			);
		}
		public function option_get_admin_phone_number()
		{
			return array(
				'id'     => 'admin_phone_number_repeater',
				'type'   => 'repeater',
				'title' => esc_html__("phone number", "payamito-awesome-support"),
				'max' => '20',

				'dependency' => array("administrator_active", '==', 'true'),
				'fields' => array(
					array(
						'id'    => 'admin_phone_number',
						'type'  => 'text',
						'placeholder' => esc_html__("Phone number ", "payamito-awesome-support"),
						'class' => 'awesome-support-payamito-phone-number ',
						'attributes'  => array(
							'type'      => 'tel',
							'maxlength' => 11,
							'minlength' => 11,
							"pattern" => "[0-9]{3}-[0-9]{3}-[0-9]{4}"
						),
					),
				),
			);
		}

		public function set_action_field($user_type, $action)
		{
			$title = "";
			$slug = "";
			$active = __("Active", "payamito-awesome-support");

			foreach ($action as $index => $ac) {
				$title = (string)$ac;
				$slug = (string)$index;
			}
			if (is_array($user_type) || is_array($slug)) {

				return [];
			}
			return	array(
				'id'            => $user_type . '_' . $slug . '_accordion',
				'type'          => 'accordion',
				'dependency' => array($user_type . "_active", '==', 'true'),
				'accordions'    => array(
					array(
						'title'     => esc_html__(ucfirst($title), 'payamito-awesome-support'),
						'fields'    => array(
							array(
								'id'   => $user_type . "_" . $slug . "_active",
								'title' => ucfirst($title) . " " . $active,
								'type' => 'switcher'
							),
							array(
					            'type'       => 'notice',
					            'style'      => 'warning',
					            'content'    => esc_html__( '"توجه" توصیه می شود به صورت پیشفرض ارسال با پترن را انتخاب نمایید و در صورتیکه با این بخش آشنایی ندارید لطفا با پشتیبانی پیامیتو در ارتباط باشید', 'payamito-awesome-support' ),
					            'class' => 'pattern_background',
							),
							array(

								'id'    => 	$user_type . "_" . $slug . "_active_p",
								'type'  => 'switcher',
								'title'      => payamito_dynamic_text('pattern_active_title'),
								'desc'       => payamito_dynamic_text('pattern_active_desc'),
								'help'       => payamito_dynamic_text('pattern_active_help'),
								'class' => 'pattern_background',
							),
							array(

								'id'   => 	$user_type . "_" . $slug . "_p",
								'type'    => 'text',
								'title'      => payamito_dynamic_text('pattern_ID_title'),
								'desc'       => payamito_dynamic_text('pattern_ID_desc'),
								'help'       => payamito_dynamic_text('pattern_ID_help'),
								'class' => 'pattern_background',
								'dependency' => array($user_type . "_" . $slug . "_active_p", '==', 'true'),
							),
							$this->option_set_pattern($user_type, $slug),
							array(
								'id'   => $user_type . "_" . $slug . "_text",
								'title'      => payamito_dynamic_text('send_content_title'),
								'desc'       => payamito_dynamic_text('send_content_desc'),
								'help'       => payamito_dynamic_text('send_content_help'),
								'default' => esc_html__('کاربر گرامی به تیکت شما با شماره {ticket_id} پاسخ دادیم. ', 'payamito-awesome-support'),
								'class' => 'pattern_background',
								'type' => 'textarea',
								'dependency' => array($user_type . "_" . $slug . "_active_p", '!=', 'true'),
							),
							array(
								'type'     => 'callback',
								'dependency' => array($user_type . "_" . $slug . "_active_p", '!=', 'true'),
								'function' => [$this, 'payamito_awesome_support_print_tags'],
							),
						)
					),
				)
			);
		}

		public	function payamito_awesome_support_print_tags()
		{
			echo "<h3 class='payamito-tags payamito-awesome-support-open-modal' >" . esc_html__('Tags', 'payamito-awesome-support') . "</h3>";
		}
		public  function  otp_validate_pattern($value)
		{

			if (!is_array($value) || count($value) == '0') {
				return;
			}
			if ($value['payamito_awesome_support_otp_active'] != '1') {
				return;
			}
			if (empty($value['payamito_awesome_support_otp_meta_key'])) {

				return esc_html__('Please enter a metakey to save user phone number', 'payamito-awesome-support');
			}
			if ($value['payamito_awesome_support_otp_active_p'] == '1' && empty($value['payamito_awesome_support_otp_p'])) {

				return esc_html__('If you use pattern to send OTP sms pattern id can not is empty', 'payamito-awesome-support');
			}
			if ($value['payamito_awesome_support_otp_active_p'] == '1' && !is_numeric($value['payamito_awesome_support_otp_p'])) {

				return esc_html__('OTP pattern id must be a number', 'payamito-awesome-support');
			}
			if ($value['payamito_awesome_support_otp_active_p'] == '1' && strlen($value['payamito_awesome_support_otp_p']) < 4) {

				return esc_html__('OTP pattern id must be at least 4 characters must be a number', 'payamito-awesome-support');
			}
			if ($value['payamito_awesome_support_otp_active_p'] == '1' && !isset($value['payamito_awesome_support_otp_repeater'])) {

				return esc_html__('If you use pattern to send OTP sms. Please create a pattern for sending SMS', 'payamito-awesome-support');
			}
			foreach ($value['payamito_awesome_support_otp_repeater'] as $pattern) {

				if (empty($pattern['payamito_awesome_support_opt_tags'] || $pattern['payamito_awesome_support_otp_user_otp'])) {

					return esc_html__('You created a pattern for sending OTP but did not specify a value .Please select a valid value from the drop-down box', 'payamito-awesome-support');
				}
			}
		}
	}
