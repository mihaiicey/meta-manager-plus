<?php
/*
Plugin Name: Meta Manager Plus
Plugin URI: https://icey.dev
Description: Un plugin pentru a gestiona titlurile meta, descrierile și imaginile principale pentru toate paginile și postările individuale, cu compatibilitate RankMath.
Version: 1.0
Author: Icey Development
Author URI: https://icey.dev
License: GPL2
*/

// Add a menu in the admin panel
function mmp_add_admin_menu() {
    add_menu_page(
        'Meta Manager Plus',
        'Meta Manager',
        'manage_options',
        'meta-manager-plus',
        'mmp_settings_page',
        'dashicons-edit',
        20
    );
}
add_action('admin_menu', 'mmp_add_admin_menu');

// Display the settings page in the admin panel
function mmp_settings_page() {
    global $wpdb;

    // Check if an individual save button was clicked
    if (isset($_POST['mmp_save_single'])) {
        $post_id = intval($_POST['post_id']);
        $title = sanitize_text_field($_POST['meta_fields'][$post_id]['title']);
        $description = sanitize_text_field($_POST['meta_fields'][$post_id]['description']);
        $image = sanitize_text_field($_POST['meta_fields'][$post_id]['image']);

        update_post_meta($post_id, 'rank_math_title', $title);
        update_post_meta($post_id, 'rank_math_description', $description);
        update_post_meta($post_id, 'rank_math_facebook_image', $image);
        update_post_meta($post_id, 'rank_math_twitter_image', $image);

        echo '<div class="notice notice-success is-dismissible"><p>Meta fields for "' . get_the_title($post_id) . '" have been updated.</p></div>';
    }

    // Get all published pages and posts
    $pages = get_posts(array(
        'post_type' => array('page', 'post'),
        'post_status' => 'publish',
        'numberposts' => -1
    ));

    ?>
    <div class="wrap">
        <h1>Meta Manager Plus</h1>
        <table class="wp-list-table widefat fixed striped table-view-list">
            <thead>
                <tr>
                    <th>Page/Post Link</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Featured Image URL</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $page) : 
                    $current_title = get_post_meta($page->ID, 'rank_math_title', true);
                    $current_description = get_post_meta($page->ID, 'rank_math_description', true);
                    $current_image = get_post_meta($page->ID, 'rank_math_facebook_image', true);
                ?>
                <tr>
                    <form method="post" action="">
                        <td><a href="<?php echo get_permalink($page->ID); ?>" target="_blank"><?php echo $page->post_title; ?></a></td>
                        <td><input type="text" name="meta_fields[<?php echo $page->ID; ?>][title]" value="<?php echo esc_attr($current_title); ?>"></td>
                        <td><input type="text" name="meta_fields[<?php echo $page->ID; ?>][description]" value="<?php echo esc_attr($current_description); ?>"></td>
                        <td><input type="text" name="meta_fields[<?php echo $page->ID; ?>][image]" value="<?php echo esc_attr($current_image); ?>"></td>
                        <td>
                            <input type="hidden" name="post_id" value="<?php echo $page->ID; ?>">
                            <input type="submit" name="mmp_save_single" class="button-primary" value="Save">
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>
