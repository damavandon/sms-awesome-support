<?php
// ═══════════════════════════ :هشدار: ═══════════════════════════

// ‫ تمامی حقوق مادی و معنوی این افزونه متعلق به سایت پیامیتو به آدرس payamito.com می باشد
// ‫ و هرگونه تغییر در سورس یا استفاده برای درگاهی غیراز پیامیتو ،
// ‫ قانوناً و شرعاً غیرمجاز و دارای پیگرد قانونی می باشد.

// © 2022 Payamito.com, Kian Dev Co. All rights reserved.

// ════════════════════════════════════════════════════════════════

?><?php

/**
 * @package   Payamito
 * @link      https://payamito.com/
 *
 * Plugin Name:       Payamito:Awesome Support 
 * Plugin URI:        https://payamito.com/
 * Description:       This plugin gives you the ability to send SMS 
 * Version:           1.2.0
 * Author:            Payamito
 * Author URI:        https://payamito.com/
 * Text Domain:       payamito-awesome-support        
 * Domain Path:       /languages
 * Requires PHP: 7.0
 */

//require_once __DIR__.'/add-license-header.php';
if (!defined('ABSPATH')) {

	die('Invalid request.');
}
if (!defined('WPINC')) {

	die('Invalid request.');
}

/**
 * main class Payamito_Awesome_Support
 *
 * @since    1.0.0
 */
if (!class_exists('Payamito_Awesome_Support')) :

	final class Payamito_Awesome_Support
	{

		public $textdomain = " payamito-awesome-support";
		/**
		 * Instance of this loader class.
		 *
		 * @since    1.0.0
		 * @var      object
		 */
		protected static $instance = null;
		/**
		 * Return an instance of this class.
		 *
		 * @since     1.0.0
		 * @return    object    A single instance of this class.
		 */
		/**
		 * Required version of the core.
		 *
		 * The minimum version of the core that's required
		 * to properly run this addon. If the minimum version
		 * requirement isn't met an error message is displayed
		 * and the addon isn't registered.
		 *
		 * @since  1.0.0
		 * @var    string
		 */
		protected $version_required = '6.0.0';

		/**
		 * Required version of PHP.
		 *
		 * Follow WordPress latest requirements and require
		 * PHP version 5.4 at least.
		 * 
		 * @var string
		 */
		protected $php_version_required = '7.0.0';

		/**
		 * Plugin slug.
		 *
		 * @since  1.0.0
		 * @var    string
		 */
		public static  $slug = 'payamito_as';

		public $core_version = '2.0.0';

		public $version = '1.2.0';
		/**
		 * Possible error message.
		 * 
		 * @var null|WP_Error
		 */
		protected $error = null;

		/**
		 * functions container
		 * 
		 * @var object
		 */
		public $functions;

		/**
		 * options container
		 * 
		 * @var object
		 */
		public $options;

		/**
		 * awesome support container
		 * 
		 * @var object
		 */
		public $awesome_support;

		/**
		 * send container
		 * 
		 * @var object
		 */
		public $send;

		/**
		 * plugin name container
		 * 
		 * @var object
		 */
		public $plugin_name = ' Payamito Awesome Support ';

		/**
		 * Payamito_Awesome_Support constructor
		 */
		public function __construct()
		{
			register_activation_hook(__FILE__, [$this, 'activate']);
			register_deactivation_hook(__FILE__, [$this, 'deactivate']);
		}
		public function activate()
		{

			do_action("payamito_as_activate");
			
			require_once PAYAMITO_AWESOME_SUPPORT_PATH . '/includes/functions.php';
			require_once PAYAMITO_AWESOME_SUPPORT_PATH . '/includes/class-install.php';

			Payamito\AwesomeSupport\Install::install();
			require_once PAYAMITO_AWESOME_SUPPORT_PATH . 'includes/core/payamito-core/includes/class-payamito-activator.php';
			Payamito_Activator::activate();
		}

		public function deactivate()
		{

			do_action("payamito_as_deactivate");
			require_once PAYAMITO_AWESOME_SUPPORT_PATH . 'includes/core/payamito-core/includes/class-payamito-deactivator.php';
			Payamito_Deactivator::deactivate();
		}
		// If the single instance hasn't been set, set it now.
		public static function get_instance()
		{
			if (null == self::$instance) {

				self::$instance = new self;
			}
			self::$instance->declare_constants();

			self::$instance->include_files();

			self::$instance->load_tgm_object();

			self::$instance->init();

			return self::$instance;
		}

		public function load_tgm_object()
		{

			new Payamito\AwesomeSupport\Required\Payamito_Awesome_Support_Required;
		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function __clone()
		{
			_doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'payamito-awesome-support'), '1.0.0');
		}

		/**
		 * Disable unserializing of the class
		 *
		 * Attempting to wakeup an FES instance will throw a doing it wrong notice.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __wakeup()
		{
			_doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'payamito-awesome-support'), '1.0.0');
		}
		/**
		 * Declare addon constants
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function declare_constants()
		{
			if (!defined('PAYAMITO_AWESOME_SUPPORT_PLUGIN_FILE')) {

				define('PAYAMITO_AWESOME_SUPPORT_PLUGIN_FILE', __FILE__);
			}
			if (!defined('PAYAMITO_AWESOME_SUPPORT_VERSION')) {
				define('PAYAMITO_AWESOME_SUPPORT_VERSION', '1.2.0');
			}
			if (!defined('PAYAMITO_AWESOME_SUPPORT_URL')) {
				define('PAYAMITO_AWESOME_SUPPORT_URL', trailingslashit(plugin_dir_url(__FILE__)));
			}
			if (!defined('PAYAMITO_AWESOME_SUPPORT_PATH')) {
				define('PAYAMITO_AWESOME_SUPPORT_PATH', trailingslashit(plugin_dir_path(__FILE__)));
			}
			if (!defined('PAYAMITO_AWESOME_SUPPORT_ROOT')) {
				define('PAYAMITO_AWESOME_SUPPORT_ROOT', trailingslashit(dirname(plugin_basename(__FILE__))));
			}
			if (!defined('PAYAMITO_AWESOME_SUPPORT_CORE_VER')) {
				define('PAYAMITO_AWESOME_SUPPORT_CORE_VER', '2.0.0');
			}
			if (!defined('PAYAMITO_AWESOME_SUPPORT_COR_DIR')) {

				define('PAYAMITO_AWESOME_SUPPORT_COR_DIR', PAYAMITO_AWESOME_SUPPORT_PATH . 'includes/core/payamito-core');
			}
		}


		/**
		 * Initialize the addon.
		 *
		 * This method is the one running the checks and
		 * registering the addon to the core.
		 *
		 * @since  0.1.0
		 * @return boolean Whether or not the addon was registered
		 */
		public function init()
		{
			$this->get_options();

			$this->init_classes();

			$this->add_action();


			if (!$this->is_php_version_enough()) {

				wp_die(__('Minimum php required version 7.0.0 or higher is required', 'payamito-awesome-support'));
			}

			if (!$this->is_version_compatible()) {

				$this->add_error(sprintf(__('%s requires Awesome Support version %s or greater. Please update the awesome support plugin first.', 'payamito-awesome-support'), $this->plugin_name, $this->version_required));
			}

			if (is_a($this->error, 'WP_Error')) {

				add_action('admin_notices', array($this, 'display_error'), 10, 0);

				return false;
			}

			return true;
		}
		/**
		 * Get options
		 *
		 *create a global variable containe options
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_options()
		{
			global $payamito_awesome_support_otp_options;

			$options = get_option('payamito_awesome_support_otp_options');
			if ($options == false) {

				return $payamito_awesome_support_otp_options['active'] = false;
			}
			return	$payamito_awesome_support_otp_options = $options;
		}

		/**
		 * register addon awesome support
		 * @since  1.0.0
		 * @return void
		 */
		public function register_addon()
		{
			return  self::$slug;
		}

		/**
		 * Initialize the actions  .
		 *
		 *@param 0 param
		 * @since 1.0
		 * @return void
		 */
		public function add_action()
		{
			add_action('kianfr_' . 'payamito' . '_save_before', [self::$instance, 'option_save'], 10, 1);
			// Load the plugin translation.
			add_action('plugins_loaded', array($this, 'localization_setup'), 1);

			add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
			add_action("admin_init",["PAS_Updater","init"]);
		}

		public function admin_enqueue_scripts()
		{

			wp_enqueue_script("payamito-awesome-support-admin-js",  PAYAMITO_AWESOME_SUPPORT_URL . "/includes/admin/assets/js/admin-app.js", array('jquery'), false, true);
		}

		/**
		 * Check if awesome support is active.
		 *
		 * Checks if the awesome support is plugin is listed in the active
		 * plugins in the WordPress database.
		 *
		 * @since  1.0.0
		 * @return boolean Whether or not the core is active
		 */
		protected function is_awesome_support_active()
		{
			if (in_array('awesome-support/awesome-support.php', apply_filters('active_plugins', get_option('active_plugins')))) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Initialize plugin for localization
		 *
		 * @uses load_plugin_textdomain()
		 */
		public function localization_setup()
		{
			load_plugin_textdomain('payamito-awesome-support', false, dirname(plugin_basename(__FILE__)) . '/languages/');
		}



		/**
		 * Return true if the core is not active so that this message won't show.
		 * We already have the error saying the plugin is disabled, no need to add this one.
		 */
		protected function is_version_compatible()
		{

			if (!$this->is_awesome_support_active()) {

				return true;
			}

			if (empty($this->version_required)) {

				return true;
			}

			if (!defined('WPAS_VERSION')) {

				return false;
			}

			if (version_compare(WPAS_VERSION, $this->version_required, '<')) {

				return false;
			}

			return true;
		}

		/**
		 * Add error.
		 *
		 * Add a new error to the WP_Error object
		 * and create the object if it doesn't exist yet.
		 *
		 * @since  1.0.0
		 * @param string $message Error message to add
		 * @return void
		 */
		public function add_error($message)
		{

			if (!is_object($this->error) || !is_a($this->error, 'WP_Error')) {

				$this->error = new WP_Error();
			}

			$this->error->add('addon_error', $message);
		}

		/**
		 * Display error.
		 *
		 * Get all the error messages and display them
		 * in the admin notices.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function display_error(): void
		{
			if (!is_a($this->error, 'WP_Error')) {
				return;
			}
			$message = $this->error->get_error_messages(); ?>
			<div class="error">
				<p>
					<?php
					if (count($message) > 1) {
						echo '<ul>';
						foreach ($message as $msg) {
							echo "<li>$msg</li>";
						}
						echo '</li>';
					} else {
						echo $message[0];
					}
					?>
				</p>
			</div>
<?php
		}

		/**
		 * Check if the version of PHP is compatible with this addon.
		 *
		 * @since  1.0.0
		 * @return boolean
		 */
		protected function is_php_version_enough()
		{
			/**
			 * No version set, we assume everything is fine.
			 */
			if (empty($this->php_version_required)) {

				return true;
			}

			if (version_compare(phpversion(), $this->php_version_required, '<')) {

				return false;
			}

			return true;
		}
		/**
		 * Save Plugin options .
		 *
		 * Save all options  in external row in data base form payamito options   .
		 *@param 1 param
		 * @since 1.0
		 * @return void
		 */
		public function  option_save($options)
		{
			$user_type = $this->functions::user_type();

			$init = [];

			$statuses = payamito_as()->functions::status();

			$actions = array_merge($this->functions::actions(), $statuses);

			foreach ($actions as $action) {
				$slug = key($action);
				foreach ($user_type as $type) {

					if (isset($options['payamito_awesome_support'][$type . "_" . $slug . "_accordion"])) {

						array_push($init, $options['payamito_awesome_support'][$type . "_" . $slug . "_accordion"]);
					}
					if (isset($options['payamito_awesome_support'][$type . "_" . $slug . "_accordion"])) {

						unset($options['payamito_awesome_support'][$type . "_" . $slug . "_accordion"]);
					}
				}
			}
			foreach ($init as $ini) {

				foreach ($ini as $index => $in) {

					$options['payamito_awesome_support'][$index] = $in;
				}
			}
			$this->otp_option_save($options['payamito_awesome_support_otp']);

			update_option('payamito_awesome_support_options', $options['payamito_awesome_support']);
		}

		/**
		 * Save OTP options .
		 * Save all otp options in external row in data base form payamito options   .
		 *@param 1 param
		 * @since 1.0
		 * @return void
		 */
		public  function otp_option_save($options)
		{
			$init = [];
			if (isset($options['payamito_awesome_support_otp_active']) && $options['payamito_awesome_support_otp_active'] == '1') {

				$init['active'] = true;

				if (isset($options['payamito_awesome_support_otp_active_p']) && $options['payamito_awesome_support_otp_active_p'] == '1') {
					$init['pattern_active'] = true;

					$init['pattern_id'] = $options['payamito_awesome_support_otp_p'];

					$init['pattern'] = $options['payamito_awesome_support_otp_repeater'];
				} else {

					$init['text'] = $options['payamito_awesome_support_otp_sms'];
				}
			} else {

				$init['active'] = false;
			}
			$init['force_enter'] = $options['user_add_phone_number_field_enter'];

			$init['force_otp'] = $options['user_add_phone_number_field_force_OTP'];

			$init['number_of_code_otp'] = $options['payamito_awesome_support_number_of_code'];

			$init['again_send_time_otp'] = $options['payamito_awesome_support_again_send_time'];

			$init['once'] = $options['payamito_awesome_support_otp_once'];

			$init['meta_key'] = $options['payamito_awesome_support_otp_meta_key'];

			update_option('payamito_awesome_support_otp_options', $init);
		}


		/**
		 * Load the addon.
		 *
		 * Include all necessary files and instantiate the addon.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function include_files()
		{
			require_once PAYAMITO_AWESOME_SUPPORT_PATH . 'includes/functions.php';
			require_once PAYAMITO_AWESOME_SUPPORT_PATH . 'includes/class-updater.php';

			require_once PAYAMITO_AWESOME_SUPPORT_PATH . 'includes/lib/class-tgm-plugin-activation.php';

			require_once PAYAMITO_AWESOME_SUPPORT_PATH . 'includes/class-plugins-required.php';

			require_once PAYAMITO_AWESOME_SUPPORT_PATH . 'includes/class-functions.php';

			require_once PAYAMITO_AWESOME_SUPPORT_PATH . 'includes/gateway/api/class-send.php';

			require_once PAYAMITO_AWESOME_SUPPORT_PATH . 'includes/class-awesome-support.php';

			require_once PAYAMITO_AWESOME_SUPPORT_PATH . 'includes/tags.php';

			require_once PAYAMITO_AWESOME_SUPPORT_PATH . 'includes/admin/class-settings.php';

			require_once PAYAMITO_AWESOME_SUPPORT_PATH . 'includes/class-awesome-support-field.php';
		}

		/**
		 * Load the addon.
		 *
		 * Include all necessary files and instantiate the addon.
		 *
		 * @since  1.0.0
		 * @return void
		 */

		public function init_classes()
		{

			if (!class_exists('Awesome_Support')) {

				return;
			}
			$this->load_core();
			$this->functions = new Payamito\AwesomeSupport\Funtions\Payamito_Awesome_Support_Functions;

			$this->options = new Payamito\AwesomeSupport\Options\Payamito_Awesome_Support_Options;

			$this->awesome_support = Payamito\AwesomeSupport\Core\Payamito_Awesome_Support_Core::init();

			$this->send = new Payamito\AwesomeSupport\Send\Payamito_Awesome_Support_Send;

			Payamito\AwesomeSupport\Field\Payamito_Awesome_Support_Field::get_instance();
		}

		public function load_core()
		{

			if (!class_exists('Payamito') && !function_exists("run_payamito")) {

				require_once payamito_as_load_core().'/payamito.php';
				run_payamito();
			}
		}
	}

endif;

function payamito_as()
{
	return  Payamito_Awesome_Support::get_instance();
}
payamito_as();
