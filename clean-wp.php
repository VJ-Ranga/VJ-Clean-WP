<?php
/**
 * Plugin Name: Clean WP
 * Plugin URI: https://yourwebsite.com/clean-wp
 * Description: Delete all posts, pages, and plugins with the click of a button.
 * Version: 2.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 */

// Add a button to the admin bar
add_action('admin_bar_menu', 'clean_wp_admin_bar_menu', 100);
function clean_wp_admin_bar_menu($wp_admin_bar) {
    $args = array(
        'id' => 'clean-wp-button',
        'title' => 'Clean WP',
        'href' => '#',
        'meta' => array(
            'class' => 'clean-wp-button',
            'onclick' => 'clean_wp(); return false;'
        )
    );
    $wp_admin_bar->add_node($args);
}

// Handle form submissions
add_action('wp_ajax_clean_wp', 'clean_wp_ajax_handler');
function clean_wp_ajax_handler() {
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
        if ($plugin_file != 'clean-wp2/clean-wp.php') {
            deactivate_plugins($plugin_file);
        }
    }

    // Delete all plugins
    $plugins = get_plugins();
    foreach ($plugins as $plugin_file => $plugin_data) {
        // Only delete plugins that are not active and not the Clean WP plugin
        if (!is_plugin_active($plugin_file) && $plugin_file != 'clean-wp2/clean-wp.php') {
            delete_plugins(array($plugin_file));
        }
    }
    
    // Return a success message
    wp_send_json_success('Clean WP completed successfully.');
}

// Auto refresh the page after cleanup
add_action('wp_ajax_clean_wp_refresh', 'clean_wp_ajax_refresh_handler');
add_action('wp_ajax_nopriv_clean_wp_refresh', 'clean_wp_ajax_refresh_handler');
function clean_wp_ajax_refresh_handler() {
    wp_send_json_success('Refreshed page.');
}

// Debugging statement to check if headers have already been sent
add_action('activated_plugin','clean_wp_check_headers_sent');
function clean_wp_check_headers_sent(){
    if(headers_sent()){
        error_log('Headers already sent in Clean WP plugin');
    }
}

?>

<script>
// Define the function to handle the Clean WP button click
function clean_wp() {
    if (confirm('Are you sure you want to delete all posts, pages, and plugins? This action cannot be undone.')) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert(JSON.parse(xhr.responseText).data);
                location.reload();
            }
            else {
                alert('Clean WP failed.');
            }
        };
        xhr.send('action=clean_wp');
    }
}
</script>
