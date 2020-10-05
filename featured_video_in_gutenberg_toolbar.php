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


/**
 * Add a meta box in the sidebar so users can add a featured video.
 * 
 * @return void
 */
function mgd_register_meta_boxes() {


    add_meta_box(
	'featured-video', 
     __( 'Featured Video', 'mgd' ), 
    'mgd_render_featured_video_fields',
    'post',
    'side' );
}
add_action( 'add_meta_boxes', 'mgd_register_meta_boxes' );

/**
 * Render HTML for the featured-video metabox.
 * 
 * Callback function for add_meta_box(). Echos the HTML directly. Does not return anything.
 * 
 * @return void
 */
function mgd_render_featured_video_fields( $object ) {
    
    /**
     * Store the meta keys for each field that is created so they can be saved later
     * @see hushlamb_save_post_class_meta()
     */
    
    global $mgd_meta_keys;
    $mgd_meta_keys[] = 'mgd_featured_video_url';

    wp_nonce_field( basename( __FILE__ ), 'mgd_post_class_nonce' );
    
    /**
     * Release date and catalog number
    */
    ?>
        <p>
            <label for="mgd-featured-video-url"><?php _e( "Vimeo or YouTube URL", 'mgd' ); ?></label>
            <br />
            <input class="widefat" type="text" name="mgd-featured-video-url" id="mgd-featured-video-url" value="<?php echo esc_attr( get_post_meta( $object->ID, 'mgd_featured_video_url', true ) ); ?>" size="30" />
        </p>
    <?php
}

    /**
     * Save metabox data.
     */
    
    function mgd_save_post_class_meta( $post_id, $post ) {
        
        //Save all the metadata by interating through the saved meta keys.
        $mgd_meta_keys = array();
        
        $mgd_meta_keys[] = 'mgd_featured_video_url';
        
        /* Verify the nonce before proceeding. */
        if ( !isset( $_POST['mgd_post_class_nonce'] ) || !wp_verify_nonce( $_POST['mgd_post_class_nonce'], basename( __FILE__ ) ) ) {
            return $post_id;
        }
        /* Get the post type object. */
        $post_type = get_post_type_object( $post->post_type );
        
        /* Check if the current user has permission to edit the post. */
        if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ) {
            return $post_id;
        }
        
        //Go through each of the meta keys registered to metaboxes and update them
        foreach( $mgd_meta_keys as $meta_key ) {
            $class_name = str_replace('_', '-', $meta_key);
            
            /* Get the posted data and sanitize it for use as an HTML class. */
            /**
             * @TODO Sanitize data, but allow hyperlinks to be stored in database
             */
            //$new_meta_value = ( isset( $_POST[ $class_name ] ) ? sanitize_html_class( $_POST[ $class_name ] ) : '' );
            $new_meta_value = $_POST[ $class_name ];
            
            /* Get the meta value of the custom field key. */
            $meta_value = get_post_meta( $post_id, $meta_key, true );
            
            /* If a new meta value was added and there was no previous value, add it. */
            if ( $new_meta_value && '' == $meta_value ) {
                
                add_post_meta( $post_id, $meta_key, $new_meta_value, true );
            
            /* If the new meta value does not match the old value, update it. */
            } elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
                
                update_post_meta( $post_id, $meta_key, $new_meta_value );
                
            /* If there is no new meta value but an old value exists, delete it. */
            } elseif ( '' == $new_meta_value && $meta_value ) {
                
                delete_post_meta( $post_id, $meta_key, $meta_value );
            }  
        }
        
        
    }

    add_action( 'save_post', 'mgd_save_post_class_meta', 10, 2 );