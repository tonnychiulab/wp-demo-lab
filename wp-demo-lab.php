<?php

/**
 * Plugin Name: WP Demo Lab
 * Plugin URI:  https://github.com/skill-md/wp-demo-lab
 * Description: A teaching plugin demonstrating Hooks, Shortcodes, and Core Icons.
 * Version:     1.0.0
 * Author:      WP Demo Lab Team
 * Author URI:  https://github.com/skill-md
 * License:     GPL-2.0+
 * Text Domain: wp-demo-lab
 * Requires at least: 6.9
 * Requires PHP:      8.3
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

// Define plugin constants
define('WP_DEMO_LAB_VERSION', '1.0.0');
define('WP_DEMO_LAB_PATH', plugin_dir_path(__FILE__));
define('WP_DEMO_LAB_URL', plugin_dir_url(__FILE__));

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
class WP_Demo_Lab
{

    /**
     * Loader for the plugin.
     *
     * @since    1.0.0
     */
    public function run()
    {
        // Load dependencies
        $this->load_dependencies();

        // Load Text Domain
        $this->load_textdomain();

        // Define admin hooks
        $this->define_admin_hooks();

        // Define public hooks
        $this->define_public_hooks();
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_textdomain()
    {
        load_plugin_textdomain(
            'wp-demo-lab',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for defining all actions that occur in the administration area.
         */
        require_once WP_DEMO_LAB_PATH . 'includes/class-hooks-demo.php';
        require_once WP_DEMO_LAB_PATH . 'includes/class-shortcodes-demo.php';
        require_once WP_DEMO_LAB_PATH . 'includes/class-icons-demo.php';
        require_once WP_DEMO_LAB_PATH . 'includes/class-gamification.php';
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $hooks_demo = new WP_Demo_Lab_Hooks_Demo();
        // add_action( 'admin_menu', array( $hooks_demo, 'add_plugin_admin_menu' ) );

        $icons_demo = new WP_Demo_Lab_Icons_Demo();
        $gamification = new WP_Demo_Lab_Gamification();
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $shortcodes_demo = new WP_Demo_Lab_Shortcodes_Demo();
        // add_shortcode( 'demo_button', array( $shortcodes_demo, 'shortcode_button' ) );
    }
}

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_wp_demo_lab()
{
    $plugin = new WP_Demo_Lab();
    $plugin->run();
}
run_wp_demo_lab();
