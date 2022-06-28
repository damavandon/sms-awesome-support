<?php
// ═══════════════════════════ :هشدار: ═══════════════════════════

// ‫ تمامی حقوق مادی و معنوی این افزونه متعلق به سایت پیامیتو به آدرس payamito.com می باشد
// ‫ و هرگونه تغییر در سورس یا استفاده برای درگاهی غیراز پیامیتو ،
// ‫ قانوناً و شرعاً غیرمجاز و دارای پیگرد قانونی می باشد.

// © 2022 Payamito.com, Kian Dev Co. All rights reserved.

// ════════════════════════════════════════════════════════════════

?><?php

namespace Payamito\AwesomeSupport;

// don't call the file directly
if (!defined('ABSPATH')) {
    die();
}
if (!class_exists('Install')) :

    class Install
    {
        /**
         * Install PayamitoGravityForm.
         */
        public static function install()
        {
            if (!is_blog_installed()) {
                wp_die('WordPress is not already installed');
            }

            set_transient('payamito_as_installing', 'yes');

            self::update_version();
        }

        /**
         * Update Payamito_GF version to current.
         * @since  1.0.0
         */
        private static function update_version()
        {
            update_option('payamito_as_version', payamito_as()->version);
            self::set_core_version();
        }

        private static function set_core_version(){

            $core_version=get_option("payamito_core_version");
            $dir_name =self::get_fil_name(__DIR__);
            $file_name = basename(PAYAMITO_AWESOME_SUPPORT_PLUGIN_FILE);
            $update=[
                'version'=>payamito_as()->core_version,
                'absolute_path' => $dir_name . '/'.$file_name,
                'core_path'=>PAYAMITO_AWESOME_SUPPORT_COR_DIR,
            ];

            if($core_version===false){
               update_option("payamito_core_version",serialize($update));
            }
            else{
                $self_version=payamito_as()->core_version;
                $other_version=unserialize($core_version)['version'];

                if($self_version>$other_version){
                    update_option("payamito_core_version",serialize($update));
                }
            }
        }
        private static function get_fil_name($__DIR__)
        {
            $dir_name= basename(dirname($__DIR__, 1));

            if ($dir_name === 'plugins') {
                $dir_name = dirname(plugin_basename(__FILE__));
            }
            return $dir_name;
        }
    }
endif;
