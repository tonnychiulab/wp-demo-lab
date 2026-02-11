<?php

/**
 * Hooks Adventure: Level Data
 * 
 * Returns an array of levels for the gamification engine.
 * Users can edit this file to modify questions or add new levels.
 */

if (! defined('ABSPATH')) {
    exit;
}

return array(
    'level_1' => array(
        'id'          => 'level_1',
        'title'       => __('Level 1: The Coat Rack', 'wp-demo-lab'),
        'description' => __('Understand the difference between Action and Filter.', 'wp-demo-lab'),
        'icon'        => 'dashicons-location',
        'story'       => sprintf(
            '<p>%s</p><ul><li><strong>%s</strong> %s</li><li><strong>%s</strong> %s</li></ul><p>%s</p>',
            __('Imagine WordPress execution is like walking down a long hallway with many Coat Racks on the walls.', 'wp-demo-lab'),
            __('Action (Do it):', 'wp-demo-lab'),
            __('You stop at a Coat Rack and hang something new on it (like a coat, a hat, or a custom message). You DO NOT change the hallway, you just ADD to it.', 'wp-demo-lab'),
            __('Filter (Change it):', 'wp-demo-lab'),
            __('You put on a pair of magic glasses (Tinted Lens). Everything you see passes through these glasses and gets changed (filtered) before it reaches your eyes.', 'wp-demo-lab'),
            __('So: Actions ADD things. Filters CHANGE things.', 'wp-demo-lab')
        ),
        'challenge'   => array(
            'type'     => 'quiz',
            'question' => __('You want to add a "Copyright 2026" message to the bottom of your website footer. Which hook type should you use?', 'wp-demo-lab'),
            'options'  => array(
                'filter' => __('Filter (Change the footer)', 'wp-demo-lab'),
                'action' => __('Action (Add to the coat rack)', 'wp-demo-lab'),
            ),
            'correct'  => 'action',
            'reward'   => array(
                'xp'    => 100,
                'badge' => 'dashicons-lightbulb', // Unlocks the Lightbulb icon
            ),
        ),
    ),
);
