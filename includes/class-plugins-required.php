<?php
// ═══════════════════════════ :هشدار: ═══════════════════════════

// ‫ تمامی حقوق مادی و معنوی این افزونه متعلق به سایت پیامیتو به آدرس payamito.com می باشد
// ‫ و هرگونه تغییر در سورس یا استفاده برای درگاهی غیراز پیامیتو ،
// ‫ قانوناً و شرعاً غیرمجاز و دارای پیگرد قانونی می باشد.

// © 2022 Payamito.com, Kian Dev Co. All rights reserved.

// ════════════════════════════════════════════════════════════════

?><?php

namespace Payamito\AwesomeSupport\Required;

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}
if (!class_exists('Payamito_Awesome_Support_Required')) {

	class Payamito_Awesome_Support_Required
	{
		public $id;
		public $parent;
		public $slug;

		function __construct()
		{
			add_action('tgmpa_register', [$this, 'required_plugins']);

			if (class_exists('TGM_Plugin_Activation')) {

				$this->id = 'payamitoawesomesupport';

				$this->slug = 'payimito_awesomesupport';

				$this->parent = 'plugins.php';
			}
		}

		public function required_plugins()
		{
			if(!function_exists('tgmpa')){
				
				return;
			}

			/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
			$plugins = array(
				array(
					'name'      => 'Awesome Support',
					'slug'      => "awesome-support",
					'force_activation' => true,
					'required'  => true,
					'version'            => '6.0.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
				),
			);
			$config = array(
				'id'           => $this->id,              // Unique ID for hashing notices for multiple instances of TGMPA.
				'default_path' => '',                      // Default absolute path to bundled plugins.
				//'menu'         => $this->slug, // Menu slug.
				//'parent_slug'  => $this->parent,            // Parent menu slug.
				'capability'   => 'install_plugins',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
				'has_notices'  => true,                    // Show admin notices or not.
				'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
				'is_automatic' => false,                   // Automatically activate plugins after installation or not.
				'dismiss_msg'  => __(' Plugin Payamito:Awesome Support requires the installation of Awesome Support  ', 'payamito-awesome-support'),

			);

			tgmpa($plugins, $config);
		}
	}
}
