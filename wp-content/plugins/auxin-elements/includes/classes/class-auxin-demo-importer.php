<?php
/**
 * Demo Importer for auxin framework
 *
 * 
 * @package    Auxin
 * @license    LICENSE.txt
 * @author     
 * @link       http://averta.net/phlox/
 * @copyright  (c) 2010-2018 
*/

// no direct access allowed
if ( ! defined('ABSPATH') )  exit;

class Auxin_Demo_Importer {

    /**
     * Instance of this class.
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Return an instance of this class.
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    function __construct() {

        add_action( 'wp_ajax_auxin_demo_data'   , array( $this, 'import') );

    }

    /**
     * Main Import
     *
     *
     * @return  JSON
     */
    public function import() {

        $demo_ID = $_POST['ID'];

        if ( ! wp_verify_nonce( $_POST['verify'], 'aux-import-demo-' . $demo_ID ) ) {
            // This nonce is not valid.
            wp_send_json_error( 'Invalid Nonce' );
        }

        $data = $this->parse( 'http://averta.net/phlox/wordpress-theme/demo/api/?demo=' . $demo_ID, $demo_ID );

        if( ! empty( $data ) ) {

            $get_options = $_POST['options'];

            foreach ($get_options as $key => $value) {
                $option[ $value['name'] ] = $value['value'];
            }

            $status = array();

            if ( $option['import'] == 'complete' ) {

                if( isset( $option['import-media'] ) && isset( $data['medias'] ) ){
                    $status[] = $this->import_medias( $data['medias'] );
                }
                if( isset( $data['content'] ) ){
                    $status[] = $this->import_posts( $data['content'] );
                }
                if( isset( $data['auxin_options'] ) ){
                    $status[] = $this->import_options( $data['auxin_options'], $data['site_options'], $data['theme_mods'] );
                }
                if( isset( $data['menus'] ) ){
                    $status[] = $this->import_menus( $data['menus'] );
                }
                if( isset( $data['widgets'] ) && isset( $data['widgets_data'] ) ){
                    $status[] = $this->import_widgets( $data['widgets'], $data['widgets_data'] );
                }   
                if( isset( $data['masterslider'] ) && isset( $data['masterslider']['sliders'] ) ){
                    $status[] = $this->import_sliders( $data['masterslider']['sliders'] );
                }             

            } else {

                if( isset( $option['medias'] ) && isset( $data['medias'] ) ){
                    $status[] = $this->import_medias( $data['medias'] );
                }
                if( isset( $option['posts'] ) && isset( $data['content'] ) ){
                    $status[] = $this->import_posts( $data['content'] );
                }
                if( isset( $option['options'] ) && isset( $data['auxin_options'] ) ){
                    $status[] = $this->import_options( $data['auxin_options'], $data['site_options'], $data['theme_mods'] );
                }
                if( isset( $option['menus'] ) && isset( $data['menus'] ) ){
                    $status[] = $this->import_menus( $data['menus'] );
                }
                if( isset( $option['widgets'] ) && isset( $data['widgets'] ) && isset( $data['widgets_data'] ) ){
                    $status[] = $this->import_widgets( $data['widgets'], $data['widgets_data'] );
                }
            }

            wp_send_json_success( $status );

        } else {
            wp_send_json_error( 'No Data Receives' );

        }

    }

    /**
     * Parse url
     *
     * @param   String $url
     *
     * @return  Array
     */
    public function parse( $url, $id ) {
        //Get JSON
        $request    = wp_remote_get( $url );
        //If the remote request fails, wp_remote_get() will return a WP_Error
        if( is_wp_error( $request ) || ! current_user_can( 'import' ) ){
            wp_send_json_error( 'Remote Request Fails' );
        }

        //proceed to retrieving the data
        $body       = wp_remote_retrieve_body( $request );
        // Check for error
        if ( is_wp_error( $body ) ) {
            wp_send_json_error( 'Retrieve Body Fails' );
        }

        //translate the JSON into Array
        return json_decode( $body, true );
    }

    // Importers
    // =====================================================================

    /**
     * Import options ( Customizer & Site Options )
     *
     * @param   array $auxin_options
     * @param   array $site_options
     * @param   array $theme_mods
     *
     * @return  String
     */
    public function import_options( array $auxin_options, array $site_options, array $theme_mods ) {

        $auxin_custom_images    = $this->get_options_by_type( 'image' );

        foreach ( $auxin_options as $auxin_key => $auxin_value ) {
            if ( in_array( $auxin_key, $auxin_custom_images ) && ! empty( $auxin_value ) ) {
                // This line is for changing the old attachment ID with new one.
                $auxin_value    = $this->get_attachment_id( 'auxin_import_id', $auxin_value );
            }
            // Update exclusive auxin options
            auxin_update_option( $auxin_key , $auxin_value);
        }

        foreach ( $site_options as $site_key => $site_value ) {
            // If option value is empty, continue...
            if ( empty( $site_value ) ) continue;
            // Else change some values :)
            if( $site_key === 'page_on_front' || $site_key === 'page_for_posts' ) {
                // Retrieves page object given its title.
                $page           = get_page_by_title( $site_value );
                // Set $site_value to page ID
                $site_value     = $page->ID;
            }
            // Finally update options :)
            update_option( $site_key, $site_value );
        }

        foreach ( $theme_mods as $theme_mods_key => $theme_mods_value ) {
            // Start theme mods loop:
            if( $theme_mods_key === 'custom_logo' ) {
                // This line is for changing the old attachment ID with new one.
                $theme_mods_value = $this->get_attachment_id( 'auxin_import_id', $theme_mods_value );
            }
            // Update theme mods
            set_theme_mod( $theme_mods_key , $theme_mods_value);
        }

        // Stores css content in custom css file
        auxin_save_custom_css();
        // Stores JavaScript content in custom js file
        auxin_save_custom_js();

        return 'options dones';

    }

    /**
     * Import widgets data
     *
     * @param   array $widgets
     * @param   array $widgets_data
     *
     * @return  String
     */
    public function import_widgets( array $widgets, array $widgets_data ) {

        if ( ! function_exists( 'wp_get_sidebars_widgets' ) ) { 
            require_once ABSPATH . WPINC . '/widgets.php'; 
        }
        $default_widgets    = wp_get_sidebars_widgets();
        // Import widgets
        foreach (  $widgets as $key => $value ) {
            if ( ! array_key_exists( $key, $default_widgets) ) continue;
            $default_widgets[$key]  = $value;
        }
        // Replace new widgets with old ones.
        wp_set_sidebars_widgets( $default_widgets );        

        // Import widgets data
        foreach ( $widgets_data as $data_key => $data_values ) {

            foreach ( $data_values as $counter => $options ) {
                // This line is for changing the old attachment ID with new one.
                if( isset( $options['about_image'] ) ) {
                    $data_values[$counter]['about_image'] = $this->get_attachment_id( 'auxin_import_id', $options['about_image'] );
                }

            }
            // Finally update widgets data.
            update_option( $data_key, $data_values );
        }   

        return 'widgets dones';

    }

    /**
     * Import menus data
     *
     * @param   array $args
     *
     * @return  Boolean
     */
    public function import_menus( array $args ) {

        global $wp_rewrite;

        foreach ($args as $menu_name => $menu_data) {

            $menu_exists = wp_get_nav_menu_object( $menu_name );

            // If it doesn't exist, let's create it.
            if( ! $menu_exists ) {

                $menu_id = wp_create_nav_menu( $menu_name );

                if( is_wp_error( $menu_id ) ) return 'menus failed!';

                // Create menu items
                foreach ( $menu_data['items'] as $item_key => $item_value ) {
                    //Keep 'menu-meta' in a variable
                    $meta_data = $item_value['menu-meta'];
                    $post_name = isset( $item_value['menu-item-object-id'] ) ? $item_value['menu-item-object-id'] : '';
                    //remove Non-standard items from nav_menu input array
                    unset( $item_value['menu-meta']             );
                    unset( $item_value['menu-item-attr-title']  );
                    unset( $item_value['menu-item-classes']     );
                    unset( $item_value['menu-item-description'] );
                    unset( $item_value['menu-item-object-id']   );
                    unset( $item_value['menu-item-url']         );               

                    $item_id    = wp_update_nav_menu_item( $menu_id, 0, $item_value );

                    if ( is_wp_error( $item_id ) ) {
                        continue;
                    }

                    //Add 'meta-data' options for menu items
                    foreach ($meta_data as $meta_key => $meta_value) {

                        switch ( $meta_key ) {
                            case '_menu_item_object_id':
                                // Create a flag transient
                                set_transient( 'auxin_menu_item_old_parent_id_' . $meta_value, $item_id, 3600 );
                                // Change exporter's object ID value
                                switch ( $item_value['menu-item-type'] ) {
                                    case 'post_type':
                                        $get_page       = get_page_by_title( $post_name, 'OBJECT', $item_value['menu-item-object'] );
                                        $meta_value     = $get_page->ID;
                                        break;
                                    case 'taxonomy':
                                        $get_term       = get_term_by( 'name', $post_name, $item_value['menu-item-object'] );
                                        $meta_value     = (int) $get_term->term_id;
                                        break;
                                    
                                    default:
                                        $meta_value     = null;
                                }
                                break;

                            case '_menu_item_menu_item_parent':
                                if( (int) $meta_value != 0 ) {
                                    $meta_value     = get_transient( 'auxin_menu_item_old_parent_id_' . $meta_value );
                                }
                                break;
                            case '_menu_item_url':
                                if( ! empty( $meta_value ) ) {
                                    $meta_value     = str_replace( "{{demo_home_url}}", get_site_url(), $meta_value );
                                }
                                break;
                        }

                        update_post_meta( $item_id, $meta_key, $meta_value );
                    }
                }

                // Putting up menu locations on theme_mods_phlox
                $locations = get_theme_mod( 'nav_menu_locations' );
                foreach ( $menu_data['location'] as $location_id => $location_name ) {
                    $locations[$location_name] = $menu_id;
                }
                set_theme_mod( 'nav_menu_locations', $locations );

            }

        }

        // Change permalink structure
        $wp_rewrite->set_permalink_structure('/%postname%/');
        // Automatic flushing of the WordPress rewrite rules
        $wp_rewrite->flush_rules();        

        return 'menus dones';

    }


    /**
     * Import posts data
     *
     * @param   array $args
     *
     * @return  String
     */
    public function import_posts( array $args ) {

        foreach ($args as $slug => $post) {

            $title      = sanitize_text_field( $post['post_title'] ); // remove any junk
            $post_type  = $post['post_type'];
            $get_page   = get_page_by_title( $title, 'OBJECT', $post_type );

            // If the post already exists or there is no post_type, then continue loop...
            if ( ! post_type_exists( $post_type ) || ! empty( $get_page ) ) {
                continue;
            }

            $content    = base64_decode( $post['post_content'] );
            $author_id  = get_current_user_id();

            $post_id = wp_insert_post(
                array(
                    'post_title'        => $title,
                    'post_content'      => $content,
                    'post_excerpt'      => $post['post_excerpt'],
                    'post_date'         => $post['post_date'],
                    'post_password'     => $post['post_password'],
                    'post_parent'       => $post['post_parent'],
                    'post_type'         => $post_type,
                    'post_author'       => $author_id,
                    'post_status'       => 'publish',
                )
            );

            if ( ! is_wp_error( $post_id ) ) {

                //Check post terms existence
                if ( ! empty( $post['post_terms'] ) ){
                    // Start adding post terms
                    foreach ( $post['post_terms'] as $tax => $term ) {

                        if( $tax === 'post_format' ) {
                            // Get post_format key value
                            $term = array_keys( $term );
                            // Set post format (Video, Audio, Gallery, ...)
                            set_post_format( $post_id , $term[0] );

                        } else {

                            // If taxonomy not exists, then continue loop...
                            if( ! taxonomy_exists( $tax ) ){
                                continue;
                            }

                            $add_these_terms = array();

                            foreach ($term as $key => $value) {

                                $term               = term_exists( $key, $tax );

                                // If the taxonomy doesn't exist, then we create it
                                if ( ! $term ) {

                                    // Get parent term
                                    $parent_term    = $value != "0" ? get_term_by( 'name', $value, $tax ) : (object) array( 'term_id' => "0" );
                                    $parent_term_ID = (int) $parent_term->term_id;
                                    $term_args      = $parent_term_ID ? array( 'parent' => $parent_term_ID ) : array();

                                    $term = wp_insert_term(
                                        $key,
                                        $tax,
                                        $term_args
                                    );

                                    if ( is_wp_error( $term ) ) {
                                        continue;
                                    }

                                }

                                $add_these_terms[]  = $term['term_id'];
                            }

                            // Add post terms
                            wp_set_post_terms( $post_id, $add_these_terms, $tax );
                        }

                    }

                }

                if ( ! empty( $post['post_meta'] ) ){
                    // Add post meta data
                    foreach ( $post['post_meta'] as $meta_key => $meta_value ) {
                        // Unserialize when data is serialized
                        $meta_value = maybe_unserialize( $meta_value );

                        switch ( $meta_key ) {
                            case '_panels_data_preview':
                            case 'panels_data'  :
                                $auxin_custom_images    = $this->get_widget_by_type( array('attach_image', 'attach_images', 'aux_select_video', 'aux_select_audio') );
                                foreach ( $meta_value['widgets'] as $widgets_key => $widgets_value ) {
                                    foreach ($widgets_value as $panel_key => $panel_value) {
                                        if ( in_array( $panel_key, $auxin_custom_images ) && ! empty( $panel_key ) ) {
                                            // This line is for changing the old attachment ID with new one.
                                            if( strpos( $panel_value, ',' ) !== false ) {
                                                $panel_value    = explode( ",", $panel_value );
                                                $gallery_widget = array();
                                                foreach ( $panel_value as $gallery_key => $gallery_value ) {
                                                    $get_new_attachment     = $this->get_attachment_id( 'auxin_import_id', $gallery_value );
                                                    if ( $get_new_attachment ) {
                                                        $gallery_widget[]   = $get_new_attachment;
                                                    }
                                                }
                                                $panel_value = implode( ",", $gallery_widget );
                                            } else {
                                                $panel_value = $this->get_attachment_id( 'auxin_import_id', $panel_value );
                                            }

                                            $meta_value['widgets'][$widgets_key][$panel_key]   = $panel_value;
                                        }
                                    }
                                }
                                break;
                            case '_thumbnail_id' :
                            case '_thumbnail_id2':
                            case '_format_audio_attachment':
                            case '_format_video_attachment':
                            case '_format_video_attachment_poster':
                            case '_format_gallery_type':
                            case 'aux_custom_bg_image':
                            case 'aux_title_bar_bg_image':
                            case 'aux_title_bar_bg_video_mp4':
                            case 'aux_title_bar_bg_video_ogg':
                            case 'aux_title_bar_bg_video_webm':
                                if( strpos( $meta_value, ',' ) !== false ) {
                                    $meta_value     = explode( ",", $meta_value );
                                    $gallery_widget = array();
                                    foreach ( $meta_value as $gallery_key => $gallery_value ) {
                                        $get_new_attachment     = $this->get_attachment_id( 'auxin_import_id', $gallery_value );
                                        if ( $get_new_attachment ) {
                                            $gallery_widget[]   = $get_new_attachment;
                                        }
                                    }
                                    $meta_value = implode( ",", $gallery_widget );
                                } else {
                                    $meta_value = $this->get_attachment_id( 'auxin_import_id', $meta_value );
                                }                            
                                break;
                        }

                        update_post_meta( $post_id, $meta_key, $meta_value );
                    }
                }

                if ( ! empty( $post['comments'] ) ){
                    // Add post comments
                    foreach ( $post['comments'] as $comment_key => $comment_values ) {
                        $comment_values['comment_post_ID']      = $post_id;
                        $comment_old_ID                         = $comment_values['comment_ID'];

                        if ( $comment_values['comment_parent'] != 0 ) {
                            $comment_values['comment_parent']   = get_transient( 'auxin_comment_new_comment_id_' . $comment_values['comment_parent'] );
                        }
                        
                        unset( $comment_values['comment_ID'] );
                        $comment_ID = wp_insert_comment( $comment_values );
                        if ( is_wp_error( $comment_ID ) ) {
                            continue;
                        } else {
                            set_transient( 'auxin_comment_new_comment_id_' . $comment_old_ID, $comment_ID, 3600 );
                        }
                    }
                }

                //Add auxin meta flag
                add_post_meta( $post_id,  'auxin_import_post', 'demo' );

                if( $post['post_thumb'] != "" ){
                    /* Get Attachment ID */
                    $attachment_id    = $this->get_attachment_id( 'auxin_import_id', $post['post_thumb'] );

                    if ( $attachment_id ) {
                        set_post_thumbnail( $post_id, $attachment_id );
                    }

                }

                // Trash the default WordPress Post, "Hello World," which has an ID of '1'.
                wp_trash_post( 1 );

            } else {

                return 'posts faild!';

            }

        }

        return 'posts dones';

    }


    /**
     * Import media data
     *
     * @param   array $args
     *
     * @return  String
     */
    public function import_medias( array $args ) {

        foreach ( $args as $import_id => $import_url ) {
            $path = isset( $import_url['path'] ) ? $import_url['path'] : '';
            $import = $this->insert_attachment( $import_id, $import_url['url'], $path );

        }

        return 'medias dones';

    }

    // Custom Functionalities
    // =====================================================================

    /**
     * Get options (ID) by type
     *
     * @param   string  $type
     * @param   array   $output
     *
     * @return  array | empty array
     */
    public function get_options_by_type( $type, $output = array() ) {

        $get_options    = auxin_get_defined_options();

        foreach ( $get_options['fields'] as $key => $value ) {
            if ( ! array_search(  $type, $value ) ) {
                continue;
            }
            $output[]   = $value['id'];
        }

        return $output;
    }

    /**
     * Get page builder (param_name) by type
     *
     * @param   string  $type
     * @param   array   $output
     *
     * @return  array | empty array
     */
    public function get_widget_by_type( array $type, $output = array() ) {

        $get_widgets    = Auxin_Widget_Shortcode_Map::get_instance()->get_master_array();

        foreach ( $get_widgets as $key => $value ) {
            foreach ( $value['params'] as $params_key => $params_value ) {
                if ( ! in_array( $params_value['type'], $type ) ) {
                    continue;
                }
                $output[]   = $params_value['param_name'];
            }
        }

        return $output;
    }

    /**
     * Get the attachment ID
     *
     * @param   string $key
     * @param   string $value
     *
     * @return  ID | false
     */
    public function get_attachment_id( $key, $value ) {

        global $wpdb;

        $meta       =   $wpdb->get_results( "
                            SELECT * 
                            FROM $wpdb->postmeta 
                            WHERE 
                            meta_key='".$key."' 
                            AND 
                            meta_value='".$value."' 
                            OR
                            meta_key='auxin_attachment_has_duplicate_".$value."'                      
                        ");

        if ( is_array($meta) && !empty($meta) && isset($meta[0]) ) {
            $meta   =   $meta[0];
        }

        if ( is_object( $meta ) ) {
            return $meta->post_id;
        } else {
            return null;
        }

    }

    /**
     * Get the attachment ID by PATHINFO_BASENAME
     *
     * @param   string $path
     *
     * @return  ID | false
     */
    public function get_attachment_id_by_basename( $path ) {

        global $wpdb;

        $post       =   $wpdb->get_results( "
                            SELECT *
                            FROM $wpdb->posts
                            WHERE
                            guid LIKE '%".$path."%'
                        ");                   

        if ( is_array($post) && !empty($post) && isset($post[0]) ) {
            $post   =   $post[0];
        }

        if ( is_object( $post ) ) {
            return $post->ID;
        } else {
            return null;
        }

    }

    /**
     * Insert attachment from url
     *
     * @param   integer $import_id
     * @param   string  $url
     * @param   integer $post_id
     *
     * @return  Integer
     */
    public function insert_attachment( $import_id, $url, $path = '', $post_id = 0 ) {

        // Check if media exist then get out
        if ( $this->attachment_exist( pathinfo( $url, PATHINFO_BASENAME ) ) ) {
            // Add meta data for duplicated videos
            if( pathinfo( $url, PATHINFO_FILENAME ) == "video" ) {
                $imported_id    = $this->get_attachment_id_by_basename( pathinfo( $url, PATHINFO_BASENAME ) );
                update_post_meta( $imported_id, 'auxin_attachment_has_duplicate_' . $import_id , $import_id );
            }
            
            return;
        }

        if ( ! function_exists('media_sideload_image') ) {
            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            require_once(ABSPATH . "wp-admin" . '/includes/file.php');
            require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        }

         $file_array                = array();
         $file_array['name']        = basename( $url );
         // Download file to temp location.
         $file_array['tmp_name']    = download_url( $url );
         // If error storing temporarily, return the error.
         if ( is_wp_error( $file_array['tmp_name'] ) ) {
                 return;
         }

        $overrides = array( 'test_form' => false );

        $time = current_time( 'mysql' );

        $date = explode( '/', $path );
        $year = isset( $date[0] ) ? $date[0] : date("Y");
        $month = isset( $date[1] ) ? $date[1] : date("n");

        if ( ! empty( $path ) ) {
            $time = date( "Y-m-d H:i:s", mktime( date("H"), date("i"), date("s"), $month, date("j"), $year ) );
        } elseif ( $post = get_post( $post_id ) ) {
                if ( substr( $post->post_date, 0, 4 ) > 0 )
                        $time = $post->post_date;
        }

        $file = wp_handle_sideload( $file_array, $overrides, $time );

        if ( isset( $file['error'] ) ) {
            return new WP_Error( 'upload_error', $file['error'] );
        }

        $url = $file['url'];
        $type = $file['type'];
        $file = $file['file'];
        $title = preg_replace('/\.[^.]+$/', '', basename($file));
        $content = '';

        // Use image exif/iptc data for title and caption defaults if possible.
        if ( $image_meta = wp_read_image_metadata( $file ) ) {
            if ( trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) ) {
                $title = $image_meta['title'];
            }
            if ( trim( $image_meta['caption'] ) ) {
                $content = $image_meta['caption'];
            }
        }

        if ( isset( $desc ) ) {
            $title = $desc;
        }

        // Construct the attachment array.
        $attachment = array(
            'post_mime_type' => $type,
            'guid' => $url,
            'post_parent' => $post_id,
            'post_title' => $title,
            'post_content' => $content,
        );

        // This should never be set as it would then overwrite an existing attachment.
        unset( $attachment['ID'] );

        // Save the attachment metadata
        $attach_id = wp_insert_attachment($attachment, $file, $post_id);
        
        if ( !is_wp_error($attach_id) ) {
            wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $file ) );
        }        

        //Add auxin meta flag on attachment
        update_post_meta( $attach_id, 'auxin_import_id', $import_id );

        return $attach_id;

    }

    /**
     * Check media existence
     *
     * @param   string $filename
     *
     * @return  boolean
     */
    public function attachment_exist( $filename ) {

        global $wpdb;

        return $wpdb->get_var( "
            SELECT COUNT(*)
            FROM
            $wpdb->posts    AS p,
            $wpdb->postmeta AS m
            WHERE
            p.ID = m.post_ID
            AND p.post_type = 'attachment'
            AND m.meta_key  = 'auxin_import_id'
            AND p.guid LIKE '%/".$filename."%'
        " );

    }

    public function import_sliders( $sliders ) {

        if ( ! class_exists( 'MSP_DB' ) ) {
            return false;
        }

        $ms_db = new MSP_DB;

        foreach ( $sliders as $slider ) {
            
            if ( isset( $slider['ID'] ) ) {
                unset( $slider['ID'] );
            }

            $ms_db->add_slider( $slider );

        }

    }


}//End class
