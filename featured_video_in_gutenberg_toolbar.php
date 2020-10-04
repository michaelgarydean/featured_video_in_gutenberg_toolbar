<?php
/**
 * Plugin Name:     Featured Video in Gutenberg Toolbar
 * Plugin URI:      https://github.com/mykedean/featured_video_in_gutenberg_toolbar.git
 * Description:     Add a Vimeo or YouTube link in the toolbar of the Gutenberg editor. Developer's can use the video link as a featured video for posts.
 * Author:          YOUR NAME HERE
 * Author URI:      michaeldean.ca
 * Text Domain:     featured_video_in_gutenberg_toolbar
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Featured_video_in_gutenberg_toolbar
 */

function sidebar_plugin_register() {
    wp_register_script(
        'plugin-sidebar-js',
        plugins_url( 'plugin-sidebar.js', __FILE__ ),
        array( 'wp-plugins', 'wp-edit-post', 'wp-element' )
    );
}
add_action( 'init', 'sidebar_plugin_register' );
 
function sidebar_plugin_script_enqueue() {
    wp_enqueue_script( 'plugin-sidebar-js' );
}
add_action( 'enqueue_block_editor_assets', 'sidebar_plugin_script_enqueue' );