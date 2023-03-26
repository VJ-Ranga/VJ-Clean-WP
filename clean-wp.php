<?php
/**
 * Plugin Name: VJ Clean WP
 * Plugin URI: https://yourwebsite.com/clean-wp
 * Description: Delete all posts, pages, and plugins with the click of a button.
 * Version: 1.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 */

// Add a button to the admin bar
add_action('admin_bar_menu', 'vj_clean_wp_admin_bar_menu', 100);
function vj_clean_wp_admin_bar_menu($wp_admin_bar) {
    $args = array(
        'id' => 'clean-wp-button',
        'title' => 'VJ Clean WP',
        'href' => '#',
        'meta' => array(
            'class' => 'clean-wp-button',
            'onclick' => 'vj_clean_wp();'
        )
    );
    $wp_admin_bar->add_node($args);
}

// Add JavaScript to handle the button click
add_action('admin_enqueue_scripts', 'vj_clean_wp_enqueue_scripts');
function vj_clean_wp_enqueue_scripts() {
    wp_enqueue_script('clean-wp-script', plugin_dir_url(__FILE__) . 'clean-wp.js', array('jquery'), '1.0', true);
}

// Handle form submissions
add_action('wp_ajax_vj_clean_wp', 'vj_clean_wp_ajax_handler');
function vj_clean_wp_ajax_handler() {
    // Delete all posts
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'post_status' => 'any',
    );
    $posts = get_posts($args);
    foreach ($posts as $post) {
        wp_delete_post($post->ID, true);
    }

    // Delete all pages
    $args = array(
        'post_type' => 'page',
        'posts_per_page' => -1,
        'post_status' => 'any',
    );
    $pages = get_posts($args);
    foreach ($pages as $page) {
        wp_delete_post($page->ID, true);
    }

    // Deactivate all plugins except for the clean-wp plugin
    $plugins = get_plugins();
    foreach ($plugins as $plugin_file => $plugin_data) {
        if ($plugin_file != 'clean-wp/clean-wp.php') {
            deactivate_plugins($plugin_file);
        }
    }

    // Delete all plugins
    $plugins = get_plugins();
    foreach ($plugins as $plugin_file => $plugin_data) {
        // Only delete plugins that are not active and not the Clean WP plugin
        if (!is_plugin_active($plugin_file) && $plugin_file != 'clean-wp/clean-wp.php') {
            delete_plugins(array($plugin_file));
        }
    }

    // Delete all themes
    $all_themes = wp_get_themes();
    foreach ( $all_themes as $theme_key => $theme ) {
    if ( $theme->get_stylesheet() !== 'twentytwentythree' ) {
        delete_theme( $theme_key );
        }
    }

    // Return a success message
    wp_send_json_success('Clean WP completed successfully.');
}

// Auto refresh the page after cleanup
add_action('wp_ajax_vj_clean_wp_refresh', 'vj_clean_wp_ajax_refresh_handler');
function vj_clean_wp_ajax_refresh_handler() {
    wp_send_json_success('Refreshed page.');
}
