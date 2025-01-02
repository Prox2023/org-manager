<?php
/**
 * Plugin Name: Organization Manager
 * Description: A React-powered WordPress plugin for organization management
 * Version: 1.0.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit;

define('org_manager_path', plugin_dir_path(__FILE__));
define('org_manager_url', plugin_dir_url(__FILE__));

// Register WP-CLI commands if available
if (defined('WP_CLI') && WP_CLI) {
    require_once org_manager_path . 'includes/CLI/GeneratorCommand.php';
    WP_CLI::add_command('org-manager', 'OrgManager\CLI\GeneratorCommand');
}

// Load Composer autoloader
require_once org_manager_path . 'vendor/autoload.php';

use OrgManager\Core\Main;

if (WP_DEBUG) {
    add_filter('rest_request_before_callbacks', function($response, $handler, $request) {
        error_log('REST Request: ' . $request->get_route());
        error_log('REST Method: ' . $request->get_method());
        error_log('REST Params: ' . print_r($request->get_params(), true));
        return $response;
    }, 10, 3);
}

/**
 * Run the Organization Manager plugin
 */
function run_org_manager(): void {
    $plugin = new Main();
    $plugin->init();
}

run_org_manager(); 