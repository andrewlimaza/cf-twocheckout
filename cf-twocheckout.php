<?php
/**
 * Plugin Name: Caldera Forms 2Checkout
 * Description: 2Checkout payment processor for Caldera Forms.
 * Plugin URI: https://yoohooplugins.com
 * Author: Yoohoo Plugins
 * Author URI: https://yoohooplugins.com
 * Version: 1.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cf-twocheckout
 * Domain Path: languages
 * Network: false
 *
 *
 * Caldera Forms 2Checkout is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Caldera Forms 2Checkout is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Caldera Forms 2Checkout. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */

defined( 'ABSPATH' ) or exit;

// define constants
define( 'CF_2CHECKOUT_PATH',  plugin_dir_path( __FILE__ ) );
define( 'CF_2CHECKOUT_URL',  plugin_dir_url( __FILE__ ) );
define( 'CF_2CHECKOUT_VER', '1.0' );

// Add language text domain
add_action( 'init', 'cf_2checkout_load_plugin_textdomain' );

// filter to add processor to regestered processors array
add_filter( 'caldera_forms_get_form_processors', 'cf_2checkout_register_processor' );

// Setup 2Checkout Redirect
add_action('caldera_forms_submit_start_processors', 'cf_2checkout_set_transient', 10, 3);

// Perform 2Checkout Redirect
add_filter('caldera_forms_submit_return_redirect', 'cf_2checkout_redirect_to_checkout', 10, 3);


// pull in the functions file
include CF_2CHECKOUT_PATH . 'includes/functions.php';
