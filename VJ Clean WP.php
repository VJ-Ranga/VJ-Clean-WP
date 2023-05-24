<?php
/**
 * Plugin Name: VJ Clean WP
 * Description: A plugin to clean up your WordPress installation.
 * Version: 2.1
 * Author: VJRanga
 * Author URI: www.vjranga.com
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Add the settings menu
function vj_clean_wp_add_menu() {
    add_options_page(
        'VJ Clean WP Settings',
        'VJ Clean WP',
        'manage_options',
        'vj-clean-wp-settings',
        'vj_clean_wp_settings_page'
    );
}
add_action('admin_menu', 'vj_clean_wp_add_menu');

// Display the settings page
function vj_clean_wp_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Handle form submission
    if (isset($_POST['vj_clean_wp_submit'])) {
        $selected_tasks = isset($_POST['vj_clean_wp_tasks']) ? $_POST['vj_clean_wp_tasks'] : array();

        foreach ($selected_tasks as $task) {
            switch ($task) {
                case 'delete_all_posts':
                    vj_clean_wp_delete_posts();
                    break;
                case 'delete_all_pages':
                    vj_clean_wp_delete_pages();
                    break;
                case 'disable_delete_plugins':
                    vj_clean_wp_disable_delete_plugins();
                    break;
                case 'delete_all_comments':
                    vj_clean_wp_delete_comments();
                    break;
                case 'delete_all_media':
                    vj_clean_wp_delete_media();
                    break;
            }
        }
    }

    // Display the settings form
    ?>
    <div class="wrap">
        <h1>VJ Clean WP Settings</h1>
        <form method="POST" action="">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">Select Tasks:</th>
                        <td>
                            <label for="delete_all_posts">
                                <input type="checkbox" name="vj_clean_wp_tasks[]" value="delete_all_posts" id="delete_all_posts">
                                Delete All Posts
                            </label>
                            <br>
                            <label for="delete_all_pages">
                                <input type="checkbox" name="vj_clean_wp_tasks[]" value="delete_all_pages" id="delete_all_pages">
                                Delete All Pages
                            </label>
                            <br>
                            <label for="disable_delete_plugins">
                                <input type="checkbox" name="vj_clean_wp_tasks[]" value="disable_delete_plugins" id="disable_delete_plugins">
                                Disable and Delete Deactivated Plugins
                            </label>
                            <br>
                            <label for="delete_all_comments">
                                <input type="checkbox" name="vj_clean_wp_tasks[]" value="delete_all_comments" id="delete_all_comments">
                                Delete All Comments
                            </label>
                            <br>
                            <label for="delete_all_media">
                                <input type="checkbox" name="vj_clean_wp_tasks[]" value="delete_all_media" id="delete_all_media">
                                Delete All Media
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" name="vj_clean_wp_submit" class="button-primary" value="Execute Tasks">
            </p>
        </form>
    </div>
    <?php
}

// Task: Delete all posts
function vj_clean_wp_delete_posts() {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'post_status' => 'any',
    );
    $posts = get_posts($args);
    foreach ($posts as $post) {
        wp_delete_post($post->ID, true);
    }
}

// Task: Delete all pages
function vj_clean_wp_delete_pages() {
    $args = array(
        'post_type' => 'page',
        'posts_per_page' => -1,
        'post_status' => 'any',
    );
    $pages = get_posts($args);
    foreach ($pages as $page) {
        wp_delete_post($page->ID, true);
    }
}

// Task: Disable and Delete deactivated plugins
function vj_clean_wp_disable_delete_plugins() {
    $plugins = get_plugins();
    $active_plugins = get_option('active_plugins');

    foreach ($plugins as $plugin_path => $plugin) {
        if (!in_array($plugin_path, $active_plugins) && is_plugin_inactive($plugin_path)) {
            deactivate_plugins($plugin_path);
            delete_plugins(array($plugin_path));
        }
    }
}

// Task: Delete all comments
function vj_clean_wp_delete_comments() {
    $comments = get_comments();
    foreach ($comments as $comment) {
        wp_delete_comment($comment->comment_ID, true);
    }
}

// Task: Delete all media
function vj_clean_wp_delete_media() {
    $args = array(
        'post_type' => 'attachment',
        'posts_per_page' => -1,
    );
    $media = get_posts($args);
    foreach ($media as $attachment) {
        wp_delete_attachment($attachment->ID, true);
    }
}
