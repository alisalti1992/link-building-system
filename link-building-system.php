<?php
/**
 * Plugin Name: Move Ahead Media Link Building System
 * Plugin URI: https://github.com/moveaheadmedia/link-building-system
 * Description: Use to manage our Link building clients and link building resources easily and more effectively to make it easier for everyone to manage the clients and the resources in hand.
 * Version: 1.0
 * Author: AliSal
 * Text Domain: link-building-system
 * Author URI: https://github.com/moveaheadmedia/
 * Move Ahead Media Link Building System is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Move Ahead Media Link Building System is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Move Ahead Media Link Building System. If not, see <http://www.gnu.org/licenses/>.
 */
namespace MAM;

use MAM\Plugin\Init;
use ORM;


/**
 * Prevent direct access
 */
defined('ABSPATH') or die('</3');


global $lbs_version;
$lbs_version = '1.0';

/**
 * Require once the Composer Autoload
 */
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

/**
 * Initialize and run all the core classes of the plugin
 */
if (class_exists('MAM\Plugin\Init')) {
    global $wpdb;
    ORM::configure('mysql:host='.DB_HOST.';dbname=' . DB_NAME);
    ORM::configure('username', DB_USER);
    ORM::configure('password', DB_PASSWORD);
    ORM::configure('id_column_overrides', array(
        $wpdb->base_prefix . 'lbs_orders' => 'order_number',
        $wpdb->base_prefix . 'lbs_providers' => 'id',
        $wpdb->base_prefix . 'lbs_resources' => 'resource_id'
    ));

    Init::register_services();
}

/**
 * The code that runs during plugin activation
 * Create database tables if they don't exist
 */
register_activation_hook(__FILE__, function () {
    global $wpdb;
    global $lbs_version;
    $charset_collate = $wpdb->get_charset_collate();
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $table_name = $wpdb->base_prefix . 'lbs_orders';
    $sql = "";
    dbDelta($sql);

    $table_name = $wpdb->base_prefix . 'lbs_resources';
    $sql = "";
    dbDelta($sql);

    $table_name = $wpdb->base_prefix . 'lbs_providers';
    $sql = "";
    dbDelta($sql);

    add_option( 'lbs_version', $lbs_version );
});

