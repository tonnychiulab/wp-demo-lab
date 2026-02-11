<?php

/**
 * Feature 1: Hooks Demo
 *
 * Demonstrates how hooks (Actions & Filters) work by allowing users to toggle them on/off.
 */

// If this file is called directly, abort.
if (! defined('ABSPATH')) {
    exit;
}

class WP_Demo_Lab_Hooks_Demo
{
    /**
     * Initialize the class and set its properties.
     */
    public function __construct()
    {
        // 1. Add Settings Page
        add_action('admin_menu', array($this, 'add_plugin_page'));
        // 2. Register Settings
        add_action('admin_init', array($this, 'register_settings'));
        // 3. Enqueue Styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));

        // 4. Apply Hooks based on options
        $this->apply_hooks();
    }

    /**
     * Enqueue Admin Styles
     */
    public function enqueue_styles($hook)
    {
        if ('toplevel_page_wp-demo-lab' !== $hook) {
            return;
        }
        wp_enqueue_style('wp-demo-lab-admin', plugin_dir_url(dirname(__FILE__)) . 'assets/admin.css', array(), '1.0.0');
    }

    /**
     * Get Available Hooks Data
     * Centralized array for easier maintenance.
     */
    private function get_available_hooks()
    {
        return array(
            'filter_content' => array(
                'type' => 'filter',
                'hook' => 'the_content',
                'title' => __('Content Filter', 'wp-demo-lab'),
                'label' => __('Enable "Thank You" message', 'wp-demo-lab'),
                'description' => __('Appends a message to your post content immediately.', 'wp-demo-lab'),
                'code' => "add_filter('the_content', ...);",
                'custom_field' => array(
                    'key' => 'filter_content_text',
                    'label' => __('Message Text', 'wp-demo-lab'),
                    'default' => __('Thank you for reading this post!', 'wp-demo-lab'),
                )
            ),
            'action_footer' => array(
                'type' => 'action',
                'hook' => 'wp_footer',
                'title' => __('Footer Action', 'wp-demo-lab'),
                'label' => __('Add "Scroll Top" button', 'wp-demo-lab'),
                'description' => __('Injects HTML markup into the footer of your website.', 'wp-demo-lab'),
                'code' => "add_action('wp_footer', ...);",
                'custom_field' => array( // Optional: We can add text customization here too if needed
                    'key' => 'action_footer_text',
                    'label' => __('Button Label', 'wp-demo-lab'),
                    'default' => __('Top', 'wp-demo-lab'),
                )
            ),
            'action_admin_notice' => array(
                'type' => 'action',
                'hook' => 'admin_notices',
                'title' => __('Admin Notice Action', 'wp-demo-lab'),
                'label' => __('Show "Welcome" banner in Admin', 'wp-demo-lab'),
                'description' => __('Displays a custom persistent notice at the top of admin pages.', 'wp-demo-lab'),
                'code' => "add_action('admin_notices', ...);",
                'custom_field' => array(
                    'key' => 'action_admin_notice_text',
                    'label' => __('Notice Text', 'wp-demo-lab'),
                    'default' => __('Hello! This is a custom admin notice.', 'wp-demo-lab'),
                )
            ),
        );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        add_menu_page(
            'WP Demo Lab',
            'WP Demo Lab',
            'manage_options',
            'wp-demo-lab',
            array($this, 'create_admin_page'),
            'dashicons-lightbulb',
            65
        );
    }

    /**
     * Register and define the settings
     */
    public function register_settings()
    {
        register_setting(
            'wp_demo_lab_hooks_group',
            'wp_demo_lab_hooks',
            array(
                'sanitize_callback' => array($this, 'sanitize_hooks_options')
            )
        );
    }

    /**
     * Sanitize options
     */
    public function sanitize_hooks_options($input)
    {
        $new_input = array();
        $hooks = $this->get_available_hooks();

        foreach ($hooks as $id => $data) {
            // Sanitize checkbox (enable/disable)
            if (isset($input[$id])) {
                $new_input[$id] = absint($input[$id]);
            }
            // Sanitize custom text field
            if (isset($data['custom_field']) && isset($input[$data['custom_field']['key']])) {
                $new_input[$data['custom_field']['key']] = sanitize_text_field($input[$data['custom_field']['key']]);
            }
        }
        return $new_input;
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        $options = get_option('wp_demo_lab_hooks', array());
        $hooks = $this->get_available_hooks();
?>
        <div class="wrap">
            <h1><?php esc_html_e('WP Demo Lab: Hooks Experiment', 'wp-demo-lab'); ?></h1>
            <p><?php esc_html_e('Here you can enable distinct hooks to see how they affect WordPress behavior.', 'wp-demo-lab'); ?></p>

            <!-- JS for Tabs -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const tabs = document.querySelectorAll('.wpdl-tab-nav');
                    tabs.forEach(tab => {
                        tab.addEventListener('click', function() {
                            // Remove active class
                            document.querySelectorAll('.wpdl-tab-nav').forEach(t => t.classList.remove('active'));
                            document.querySelectorAll('.wpdl-hook-card').forEach(c => c.style.display = 'none');

                            // Add active class
                            this.classList.add('active');

                            // Filter cards
                            const filter = this.dataset.filter;
                            if (filter === 'all') {
                                document.querySelectorAll('.wpdl-hook-card').forEach(c => c.style.display = 'flex');
                            } else {
                                document.querySelectorAll('.wpdl-hook-card.' + filter).forEach(c => c.style.display = 'flex');
                            }
                        });
                    });
                });
            </script>

            <!-- Tabs Nav -->
            <div class="wpdl-tabs">
                <span class="wpdl-tab-nav active" data-filter="all"><?php esc_html_e('All', 'wp-demo-lab'); ?></span>
                <span class="wpdl-tab-nav" data-filter="type-filter"><?php esc_html_e('Filters', 'wp-demo-lab'); ?></span>
                <span class="wpdl-tab-nav" data-filter="type-action"><?php esc_html_e('Actions', 'wp-demo-lab'); ?></span>
            </div>

            <form method="post" action="options.php">
                <?php settings_fields('wp_demo_lab_hooks_group'); ?>

                <div class="wpdl-hooks-grid">
                    <?php foreach ($hooks as $id => $data) : ?>
                        <div class="wpdl-hook-card type-<?php echo esc_attr($data['type']); ?>">
                            <h3>
                                <?php echo esc_html($data['title']); ?>
                                <span class="wpdl-type-badge wpdl-type-<?php echo esc_attr($data['type']); ?>">
                                    <?php echo esc_html(strtoupper($data['type'])); ?>
                                </span>
                            </h3>

                            <div class="wpdl-card-content">
                                <code><?php echo esc_html($data['hook']); ?></code>
                                <p><?php echo esc_html($data['description']); ?></p>

                                <label>
                                    <input type="checkbox" name="wp_demo_lab_hooks[<?php echo esc_attr($id); ?>]" value="1" <?php checked(isset($options[$id])); ?> />
                                    <strong><?php echo esc_html($data['label']); ?></strong>
                                </label>

                                <?php if (isset($data['custom_field'])) :
                                    $field_key = $data['custom_field']['key'];
                                    $value = isset($options[$field_key]) ? $options[$field_key] : $data['custom_field']['default'];
                                ?>
                                    <div class="wpdl-custom-text">
                                        <label><?php echo esc_html($data['custom_field']['label']); ?>:</label>
                                        <input type="text" name="wp_demo_lab_hooks[<?php echo esc_attr($field_key); ?>]" value="<?php echo esc_attr($value); ?>" />
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php submit_button(); ?>
            </form>
        </div>
    <?php
    }

    /**
     * Apply the hooks if options are enabled
     */
    private function apply_hooks()
    {
        $options = get_option('wp_demo_lab_hooks', array());

        if (isset($options['filter_content'])) {
            add_filter('the_content', array($this, 'hook_the_content_callback'));
        }

        if (isset($options['action_footer'])) {
            add_action('wp_footer', array($this, 'hook_wp_footer_callback'));
        }

        if (isset($options['action_admin_notice'])) {
            add_action('admin_notices', array($this, 'hook_admin_notices_callback'));
        }
    }

    /**
     * Callback for 'the_content'
     */
    public function hook_the_content_callback($content)
    {
        if (is_singular() && is_main_query()) {
            $options = get_option('wp_demo_lab_hooks', array());
            $text = isset($options['filter_content_text']) && !empty($options['filter_content_text'])
                ? $options['filter_content_text']
                : __('Thank you for reading this post!', 'wp-demo-lab');

            $message = '<div style="background:#f0f0f1; padding:15px; margin-top:20px; border-left:4px solid #72aee6;">';
            $message .= sprintf(
                '<strong>[%s]</strong> %s',
                esc_html__('WP Demo Lab', 'wp-demo-lab'),
                esc_html($text)
            );
            $message .= '</div>';
            return $content . $message;
        }
        return $content;
    }

    /**
     * Callback for 'wp_footer'
     */
    public function hook_wp_footer_callback()
    {
        $options = get_option('wp_demo_lab_hooks', array());
        $text = isset($options['action_footer_text']) && !empty($options['action_footer_text'])
            ? $options['action_footer_text']
            : __('Top', 'wp-demo-lab');
    ?>
        <div style="position:fixed; bottom:20px; right:20px; z-index:9999;">
            <a href="#" style="background:#2271b1; color:white; padding:10px 15px; text-decoration:none; border-radius:5px; box-shadow:0 2px 5px rgba(0,0,0,0.2);">
                <?php printf('[%s] %s &uarr;', esc_html__('Demo', 'wp-demo-lab'), esc_html($text)); ?>
            </a>
        </div>
    <?php
    }

    /**
     * Callback for 'admin_notices'
     */
    public function hook_admin_notices_callback()
    {
        $options = get_option('wp_demo_lab_hooks', array());
        $text = isset($options['action_admin_notice_text']) && !empty($options['action_admin_notice_text'])
            ? $options['action_admin_notice_text']
            : __('Hello! This is a custom admin notice.', 'wp-demo-lab');
    ?>
        <div class="notice notice-info is-dismissible">
            <p><strong>[<?php esc_html_e('WP Demo Lab', 'wp-demo-lab'); ?>]</strong> <?php echo esc_html($text); ?></p>
        </div>
<?php
    }
}
