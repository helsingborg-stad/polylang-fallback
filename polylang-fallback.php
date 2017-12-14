<?php

/**
 * Plugin Name:       Polylang Fallback
 * Plugin URI:        (#plugin_url#)
 * Description:       Automatic fallback to selected language(s) if a translation dosen't exist on current language. Without doing a redirect of the page.
 * Version:           1.0.0
 * Author:            Sebastian Thulin
 * Author URI:        (#plugin_author_url#)
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       polylang-fallback
 * Domain Path:       /languages
 */

 // Protect agains direct file access
if (! defined('WPINC')) {
    die;
}

define('PPLFALLBACK_PATH', plugin_dir_path(__FILE__));
define('PPLFALLBACK_URL', plugins_url('', __FILE__));
define('PPLFALLBACK_TEMPLATE_PATH', PPLFALLBACK_PATH . 'templates/');

load_plugin_textdomain('polylang-fallback', false, plugin_basename(dirname(__FILE__)) . '/languages');

require_once PPLFALLBACK_PATH . 'source/php/Vendor/Psr4ClassLoader.php';
require_once PPLFALLBACK_PATH . 'Public.php';

// Instantiate and register the autoloader
$loader = new pplfallback\Vendor\Psr4ClassLoader();
$loader->addPrefix('pplfallback', PPLFALLBACK_PATH);
$loader->addPrefix('pplfallback', PPLFALLBACK_PATH . 'source/php/');
$loader->register();

// Start application
new pplfallback\App();
