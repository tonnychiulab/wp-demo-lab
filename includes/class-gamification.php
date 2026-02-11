<?php

/**
 * Class WP_Demo_Lab_Gamification
 * 
 * Handles the "Hooks Adventure" game logic, admin page, and progress tracking.
 */

if (! defined('ABSPATH')) {
    exit;
}

class WP_Demo_Lab_Gamification
{

    /**
     * Initialize the class.
     */
    public function __construct()
    {
        // Register Admin Hook
        add_action('admin_menu', array($this, 'add_gamification_page'));

        // Register AJAX endpoints
        add_action('wp_ajax_wpdl_check_answer', array($this, 'check_answer'));
    }

    /**
     * Add "Hooks Adventure" to the admin menu.
     */
    public function add_gamification_page()
    {
        add_submenu_page(
            'wp-demo-lab',                 // Parent slug
            __('Hooks Adventure', 'wp-demo-lab'), // Page title
            __('Hooks Adventure', 'wp-demo-lab'), // Menu title
            'manage_options',              // Capability
            'wp-demo-lab-game',            // Menu slug
            array($this, 'render_game_page') // Callback
        );
    }

    /**
     * Render the Game Map Page.
     */
    public function render_game_page()
    {
        // Get all levels
        $levels = include WP_DEMO_LAB_PATH . 'includes/gamification/levels.php';

        // Get user progress
        $user_id = get_current_user_id();
        $progress = get_user_meta($user_id, 'wpdl_game_progress', true) ?: array();
        $current_xp = get_user_meta($user_id, 'wpdl_game_xp', true) ?: 0;

?>
        <div class="wrap">
            <h1><?php esc_html_e('Hooks Adventure', 'wp-demo-lab'); ?> 🗺️</h1>
            <p><?php printf(esc_html__('Total XP: %s', 'wp-demo-lab'), '<strong>' . intval($current_xp) . '</strong>'); ?></p>

            <div class="wpdl-game-map">
                <?php foreach ($levels as $level_id => $level) :
                    $is_completed = in_array($level_id, $progress);
                    $is_locked = false; // Logic to lock levels can be added here
                    $status_class = $is_completed ? 'completed' : ($is_locked ? 'locked' : 'active');
                ?>
                    <div class="wpdl-level-node <?php echo esc_attr($status_class); ?>" data-level="<?php echo esc_attr($level_id); ?>">
                        <div class="wpdl-node-icon">
                            <span class="dashicons <?php echo esc_attr($level['icon']); ?>"></span>
                        </div>
                        <div class="wpdl-node-info">
                            <h3><?php echo esc_html($level['title']); ?></h3>
                            <p><?php echo esc_html($level['description']); ?></p>
                        </div>
                        <?php if (! $is_locked) : ?>
                            <button class="button button-primary wpdl-start-level"
                                data-level="<?php echo esc_attr($level_id); ?>"
                                data-story="<?php echo esc_attr($level['story']); ?>"
                                data-question="<?php echo esc_attr($level['challenge']['question']); ?>"
                                data-options="<?php echo esc_attr(json_encode($level['challenge']['options'])); ?>">
                                <?php $is_completed ? esc_html_e('Replay', 'wp-demo-lab') : esc_html_e('Start Adventure', 'wp-demo-lab'); ?>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Game Modal (Hidden by default) -->
            <div id="wpdl-game-modal" style="display:none;">
                <div class="wpdl-modal-content">
                    <span class="wpdl-close">&times;</span>
                    <h2 id="wpdl-modal-title"></h2>
                    <div id="wpdl-modal-story"></div>
                    <div id="wpdl-modal-challenge">
                        <h3><?php esc_html_e('Challenge', 'wp-demo-lab'); ?></h3>
                        <p id="wpdl-challenge-question"></p>
                        <div id="wpdl-challenge-options"></div>
                        <button id="wpdl-check-answer" class="button button-primary button-large"><?php esc_html_e('Check Answer', 'wp-demo-lab'); ?></button>
                    </div>
                    <div id="wpdl-modal-feedback" style="display:none; margin-top:15px; padding:10px; border-radius:5px;"></div>
                </div>
            </div>
        </div>

        <style>
            /* Simple Inline CSS for Prototype */
            .wpdl-game-map {
                display: flex;
                flex-direction: column;
                gap: 20px;
                max-width: 600px;
                margin-top: 20px;
            }

            .wpdl-level-node {
                background: #fff;
                padding: 20px;
                border: 1px solid #ccd0d4;
                border-radius: 8px;
                display: flex;
                align-items: center;
                gap: 15px;
                transition: transform 0.2s;
            }

            .wpdl-level-node:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }

            .wpdl-level-node.completed {
                border-left: 5px solid #46b450;
            }

            .wpdl-node-icon .dashicons {
                font-size: 32px;
                width: 32px;
                height: 32px;
                color: #2271b1;
            }

            /* Modal */
            #wpdl-game-modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                z-index: 10000;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .wpdl-modal-content {
                background: #fff;
                padding: 30px;
                border-radius: 10px;
                width: 90%;
                max-width: 500px;
                position: relative;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            }

            .wpdl-close {
                position: absolute;
                top: 10px;
                right: 15px;
                font-size: 24px;
                cursor: pointer;
            }

            #wpdl-challenge-options label {
                display: block;
                padding: 10px;
                border: 2px solid #eee;
                margin-bottom: 10px;
                border-radius: 5px;
                cursor: pointer;
            }

            #wpdl-challenge-options label:hover {
                background: #f0f6fc;
                border-color: #2271b1;
            }

            #wpdl-challenge-options input {
                margin-right: 10px;
            }
        </style>

        <script>
            jQuery(document).ready(function($) {
                // Open Modal
                $('.wpdl-start-level').on('click', function() {
                    var levelId = $(this).data('level');
                    var story = $(this).data('story');
                    var question = $(this).data('question');
                    var options = $(this).data('options');

                    $('#wpdl-modal-title').text('Level: ' + levelId.replace('_', ' ').toUpperCase());
                    $('#wpdl-modal-story').html(story);
                    $('#wpdl-challenge-question').text(question);

                    var optionsHtml = '';
                    $.each(options, function(key, val) {
                        optionsHtml += '<label><input type="radio" name="wpdl_answer" value="' + key + '"> ' + val + '</label>';
                    });
                    $('#wpdl-challenge-options').html(optionsHtml);
                    $('#wpdl-modal-feedback').hide();
                    $('#wpdl-check-answer').data('level', levelId).show();

                    $('#wpdl-game-modal').fadeIn();
                });

                // Close Modal
                $('.wpdl-close').on('click', function() {
                    $('#wpdl-game-modal').fadeOut();
                });

                // Check Answer
                $('#wpdl-check-answer').on('click', function() {
                    var levelId = $(this).data('level');
                    var selected = $('input[name="wpdl_answer"]:checked').val();

                    if (!selected) {
                        alert('<?php esc_html_e('Please select an answer!', 'wp-demo-lab'); ?>');
                        return;
                    }

                    $.post(ajaxurl, {
                        action: 'wpdl_check_answer',
                        level_id: levelId,
                        answer: selected,
                        nonce: '<?php echo wp_create_nonce('wpdl_game_nonce'); ?>'
                    }, function(response) {
                        if (response.success) {
                            $('#wpdl-modal-feedback').html('✅ ' + response.data.message).css('background', '#d4edda').css('color', '#155724').show();
                            $('#wpdl-check-answer').hide();
                            // Reload page to show progress (simple version)
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            $('#wpdl-modal-feedback').html('❌ ' + response.data.message).css('background', '#f8d7da').css('color', '#721c24').show();
                        }
                    });
                });
            });
        </script>
<?php
    }

    /**
     * AJAX Handler: Check Answer
     */
    public function check_answer()
    {
        check_ajax_referer('wpdl_game_nonce', 'nonce');

        $level_id = sanitize_text_field($_POST['level_id']);
        $answer   = sanitize_text_field($_POST['answer']);
        $user_id  = get_current_user_id();

        // Load levels logic
        $levels = include WP_DEMO_LAB_PATH . 'includes/gamification/levels.php';

        if (! isset($levels[$level_id])) {
            wp_send_json_error(array('message' => __('Level not found.', 'wp-demo-lab')));
        }

        $level = $levels[$level_id];

        if ($answer === $level['challenge']['correct']) {
            // Save Progress
            $progress = get_user_meta($user_id, 'wpdl_game_progress', true) ?: array();
            if (! in_array($level_id, $progress)) {
                $progress[] = $level_id;
                update_user_meta($user_id, 'wpdl_game_progress', $progress);

                // Add XP
                $current_xp = get_user_meta($user_id, 'wpdl_game_xp', true) ?: 0;
                update_user_meta($user_id, 'wpdl_game_xp', $current_xp + $level['challenge']['reward']['xp']);
            }

            wp_send_json_success(array('message' => __('Correct! XP Awarded!', 'wp-demo-lab')));
        } else {
            wp_send_json_error(array('message' => __('Incorrect. Try again!', 'wp-demo-lab')));
        }
    }
}
