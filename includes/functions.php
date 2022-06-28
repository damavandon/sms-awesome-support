<?php
// ═══════════════════════════ :هشدار: ═══════════════════════════

// ‫ تمامی حقوق مادی و معنوی این افزونه متعلق به سایت پیامیتو به آدرس payamito.com می باشد
// ‫ و هرگونه تغییر در سورس یا استفاده برای درگاهی غیراز پیامیتو ،
// ‫ قانوناً و شرعاً غیرمجاز و دارای پیگرد قانونی می باشد.

// © 2022 Payamito.com, Kian Dev Co. All rights reserved.

// ════════════════════════════════════════════════════════════════

?><?php

// don't call the file directly
if (!defined('ABSPATH')) {
    die();
}

/**
 * Check if plugin is installed by getting all plugins from the plugins dir
 *
 * @param $plugin_slug
 * @since 1.0.0
 * @return bool
 */
if (!function_exists('payamito_check_plugin_installed')) {

    function payamito_check_plugin_installed($plugin_slug)
    {
        $installed_plugins = get_plugins();

        return array_key_exists($plugin_slug, $installed_plugins) || in_array($plugin_slug, $installed_plugins, true);
    }
}


/**
 * Check if plugin is installed
 * @param string $plugin_slug
 * @return bool
 * @since 1.0.0
 */
if (!function_exists('payamito_check_plugin_active')) {
    function payamito_check_plugin_active($plugin_slug): bool
    {
        if (is_plugin_active($plugin_slug)) {
            return true;
        }
        return false;
    }
}

if (!function_exists('payamito_as_load_core')) {

    function payamito_as_load_core()
    {
        $core = get_option("payamito_core_version");
        if ($core === false) {
            return PAYAMITO_AWESOME_SUPPORT_COR_DIR;
        }
        if (!function_exists('is_plugin_active')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        $core = unserialize($core);
        if (
            file_exists($core['core_path'])
             &&
            is_plugin_active($core['absolute_path'])
        ) {
            return $core['core_path'];
        } else {
            return PAYAMITO_AWESOME_SUPPORT_COR_DIR;
        }
        return PAYAMITO_AWESOME_SUPPORT_COR_DIR;
    }
}
