jQuery(document).ready(function() {
    // Define the function to handle the Clean WP button click
    function vj_clean_wp() {
        if (confirm('Are you sure you want to delete all posts, pages, and plugins? This action cannot be undone.')) {
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: { action: 'vj_clean_wp' },
                success: function(response) {
                    alert(response.data);
                }
            });
        }
    }

    // Get the AJAX URL from the PHP code
    var ajaxurl = '<?php echo esc_url( admin_url( "admin-ajax.php" ) ); ?>';

    // Add a click event listener to the Clean WP button
    jQuery('.clean-wp-button').on('click', function(event) {
        event.preventDefault();
        vj_clean_wp();
    });
});
