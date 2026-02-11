<?php

/**
 * Feature 3: Icons Demo
 *
 * Displays a gallery of WordPress Dashicons with pagination and usage tools.
 */

// If this file is called directly, abort.
if (! defined('ABSPATH')) {
    exit;
}

class WP_Demo_Lab_Icons_Demo
{

    /**
     * Initialize the class.
     */
    public function __construct()
    {
        // Add Tool Menu
        add_action('admin_menu', array($this, 'add_tools_page'));
        // Enqueue Assets (only on our page)
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    /**
     * Add page to Tools menu
     */
    public function add_tools_page()
    {
        add_management_page(
            'WP Icons Gallery',
            'WP Icons',
            'manage_options',
            'wp-demo-lab-icons',
            array($this, 'render_icons_page')
        );
    }

    /**
     * Enqueue styles and scripts
     */
    public function enqueue_admin_assets($hook)
    {
        // Only load on our specific page
        if ('tools_page_wp-demo-lab-icons' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'wpdl-admin-style',
            WP_DEMO_LAB_URL . 'assets/css/admin-style.css',
            array(),
            WP_DEMO_LAB_VERSION
        );

        wp_enqueue_script(
            'wpdl-admin-script',
            WP_DEMO_LAB_URL . 'assets/js/admin-script.js',
            array(),
            WP_DEMO_LAB_VERSION,
            true
        );
    }

    /**
     * Render the admin page
     */
    public function render_icons_page()
    {
?>
        <div class="wrap">
            <h1><?php esc_html_e('WP Icons Gallery', 'wp-demo-lab'); ?></h1>
            <p><?php esc_html_e('Explore default WordPress Dashicons. Click an icon to copy its HTML code.', 'wp-demo-lab'); ?></p>

            <div class="wpdl-icons-controls">
                <button id="wpdl-prev" class="button"><?php esc_html_e('Previous', 'wp-demo-lab'); ?></button>
                <span id="wpdl-page-info"><?php esc_html_e('Page', 'wp-demo-lab'); ?> 1 <?php esc_html_e('of', 'wp-demo-lab'); ?> X</span>
                <button id="wpdl-next" class="button"><?php esc_html_e('Next', 'wp-demo-lab'); ?></button>
            </div>

            <div id="wpdl-icons-grid">
                <!-- Icons will be injected here via JS -->
            </div>
        </div>
<?php
    }
}
