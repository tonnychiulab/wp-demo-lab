<?php

/**
 * Feature 2: Shortcodes Demo
 *
 * Demonstrates how to create custom shortcodes for use in posts and pages.
 */

// If this file is called directly, abort.
if (! defined('ABSPATH')) {
    exit;
}

class WP_Demo_Lab_Shortcodes_Demo
{

    /**
     * Initialize the class and define shortcodes.
     */
    public function __construct()
    {
        // Shortcode 1: [demo_button]
        add_shortcode('demo_button', array($this, 'shortcode_button'));

        // Shortcode 2: [demo_date]
        add_shortcode('demo_date', array($this, 'shortcode_date'));

        // Shortcode 3: [demo_user]
        add_shortcode('demo_user', array($this, 'shortcode_user'));
    }

    /**
     * 1. Button Shortcode
     * Usage: [demo_button link="https://example.com" text="Click Me" color="#2271b1"]
     */
    public function shortcode_button($atts = array())
    {
        // Normalize attributes
        $atts = shortcode_atts(
            array(
                'link'  => '#',
                'text'  => __('Button', 'wp-demo-lab'),
                'color' => '#2271b1',
            ),
            $atts,
            'demo_button'
        );

        // Sanitize output
        $link  = esc_url($atts['link']);
        $text  = esc_html($atts['text']);
        $color = sanitize_hex_color($atts['color']);

        // Return HTML
        return sprintf(
            '<a href="%s" style="display:inline-block; padding:10px 20px; background-color:%s; color:#fff; text-decoration:none; border-radius:4px;">%s</a>',
            $link,
            $color,
            $text
        );
    }

    /**
     * 2. Date Shortcode
     * Usage: [demo_date format="Y-m-d H:i:s"]
     */
    public function shortcode_date($atts = array())
    {
        $atts = shortcode_atts(
            array(
                'format' => get_option('date_format'),
            ),
            $atts,
            'demo_date'
        );

        return date_i18n($atts['format']);
    }

    /**
     * 3. User Shortcode
     * Usage: [demo_user field="display_name"]
     */
    public function shortcode_user($atts = array())
    {
        // Only run if user is logged in
        if (! is_user_logged_in()) {
            return __('Guest', 'wp-demo-lab');
        }

        $atts = shortcode_atts(
            array(
                'field' => 'display_name',
            ),
            $atts,
            'demo_user'
        );

        $current_user = wp_get_current_user();
        $field        = $atts['field'];

        // Whitelist allowed fields for security
        $allowed_fields = array('display_name', 'user_email', 'user_login', 'ID');

        if (in_array($field, $allowed_fields, true) && isset($current_user->$field)) {
            return esc_html($current_user->$field);
        }

        return '';
    }
}
