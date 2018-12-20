<?php




/*-----------------------------------------------------------------------------------*/
/*  Add shortcode button to tinymce
/*-----------------------------------------------------------------------------------*/

function auxin_register_shortcode_button( $buttons ) {
    array_push( $buttons, '|', 'phlox_shortcodes_button' );
    return $buttons;
}

/**
 * Add the shortcode button to TinyMCE
 *
 * @param array $plugin_array
 * @return array
 */
function auxin_add_elements_tinymce_plugin( $plugin_array ) {
    $wp_version = get_bloginfo( 'version' );

    $plugin_array['phlox_shortcodes_button'] = AUXELS_ADMIN_URL."/assets/js/tinymce/plugins/auxin-btns.js";

    return $plugin_array;
}


function auxels_init_shortcode_manager(){
    if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
        return;

    add_filter( 'mce_external_plugins', 'auxin_add_elements_tinymce_plugin' );
    add_filter( 'mce_buttons', 'auxin_register_shortcode_button' );
}
add_action("init", "auxels_init_shortcode_manager");


/*-----------------------------------------------------------------------------------*/
/*  Wizard admin notice
/*-----------------------------------------------------------------------------------*/

/**
 * Skip the notice for running the setup wizard
 *
 * @return void
 */
function auxels_hide_wizard_notice() {
    if ( isset( $_GET['auxels-hide-wizard-notice'] ) && isset( $_GET['_notice_nonce'] ) ) {
        if ( ! wp_verify_nonce( $_GET['_notice_nonce'], 'auxels_hide_notices_nonce' ) ) {
            wp_die( __( 'Authorization failed. Please refresh the page and try again.', 'auxin-elements' ) );
        }
        auxin_update_option( 'auxels_hide_wizard_notice', 1 );
    }
}
add_action( 'wp_loaded', 'auxels_hide_wizard_notice' );


/**
 * Display a notice for running the setup wizard
 *
 * @return void
 */
function auxels_wizard_notice(){
    if( auxin_get_option( 'auxels_hide_wizard_notice' ) ){
        return;
    }
?>
    <div id="message" class="updated auxin-message">
        <p><?php _e( '<strong>Welcome to Phlox</strong> &#8211; You can import demo content and install recommended plugins using setup wizard.', 'auxin-elements' ); ?></p>
        <p class="submit"><a href="<?php echo esc_url( Auxin_Wizard::get_instance()->get_page_link() ); ?>" class="button-primary"><?php _e( 'Run Setup Wizard', 'auxin-elements' ); ?></a> <a class="button-secondary skip" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'auxels-hide-wizard-notice', 'install' ), 'auxels_hide_notices_nonce', '_notice_nonce' ) ); ?>"><?php _e( 'Skip Setup', 'auxin-elements' ); ?></a></p>
    </div>
<?php
}

add_action( 'admin_notices', 'auxels_wizard_notice' );

/*-----------------------------------------------------------------------------------*/
/*  Add Editor styles
/*-----------------------------------------------------------------------------------*/

function auxin_register_mce_buttons_style(){
    wp_register_style('auxin_mce_buttons'  , AUXELS_ADMIN_URL. '/assets/css/editor.css', NULL, '1.1');
    wp_enqueue_style('auxin_mce_buttons');
}
add_action('admin_enqueue_scripts', 'auxin_register_mce_buttons_style');



/*-----------------------------------------------------------------------------------*/
/*  Adds demos tab in theme about (welcome) page
/*-----------------------------------------------------------------------------------*/

function auxin_welcome_add_section_demos( $sections ){

    $sections['demos'] = array(
        'label'       => __( 'Setup Wizard', 'auxin-elements' ),
        'description' => '',
        'url'         => Auxin_Wizard::get_instance()->get_page_link()
    );

    return $sections;
}

add_filter( 'auxin_admin_welcome_sections', 'auxin_welcome_add_section_demos', 60 );


/*-----------------------------------------------------------------------------------*/
/*  Adds install plugins tab in about (welcome) page
/*-----------------------------------------------------------------------------------*/

function auxin_welcome_add_section_install_plugins( $sections ){

    $sections['install_plugins'] = array(
        'label'       => esc_html__( 'Install Plugins', 'auxin-elements' ),
        'description' => '',
        'url'         => self_admin_url( 'admin.php?page=auxin-wizard&step=default_plugins' ), // optional
    );

    return $sections;
}

add_filter( 'auxin_admin_welcome_sections', 'auxin_welcome_add_section_install_plugins', 70 );

/*-----------------------------------------------------------------------------------*/
/*  Adds system status tab in theme about (welcome) page
/*-----------------------------------------------------------------------------------*/

function auxin_about_system_status( $sections ){

    $sections['status'] = array(
        'label'       => __( 'System Status', 'auxin-elements' ),
        'description' => __( 'The informaition about your WordPress installation which can be helpful for debugging or monitoring your website.', 'auxin-elements'),
        'callback'    => 'auxin_get_about_system_status'
    );

    return $sections;
}

add_filter( 'auxin_admin_welcome_sections', 'auxin_about_system_status', 100 );


/*-----------------------------------------------------------------------------------*/
/*  Adds feedback tab in theme about (welcome) page
/*-----------------------------------------------------------------------------------*/

function auxin_welcome_page_display_section_feedback(){
    // the previous rate of the client
    $previous_rate = auxin_get_option( 'user_rating' );

    $main_admin_page = ( defined( 'THEME_PRO' ) && THEME_PRO ) ? 'admin.php' : 'themes.php';
    $support_tab_url = admin_url( $main_admin_page .'?page=auxin-welcome&tab=support' );
    ?>

    <div class="changelog feature-section two-col feedback">

        <form class="aux-feedback-form" action="<?php echo admin_url( 'admin.php?page=auxin-welcome&tab=feedback'); ?>" method="post" >

            <div class="aux-rating-section">
                <h2 class="aux-featur"><?php _e('How likely are you to recommend Phlox to a friend?', 'auxin-elements' ); ?></h2>
                <div class="aux-theme-ratings">
                <?php
                    for( $i = 1; $i <= 5; $i++ ){
                        printf(
                            '<div class="aux-rate-cell"><input type="radio" name="theme_rate" id="theme-rating%1$s" value="%1$s" %2$s/><label class="rating" for="theme-rating%1$s">%1$s</label></div>',
                            $i, checked( $previous_rate, $i, false )
                        );
                    }
                ?>

                </div>
                <div class="aux-ratings-measure">
                    <p><?php _e( "Don't like it", 'auxin-elements' ); ?></p>
                    <p><?php _e( "Like it so much", 'auxin-elements' ); ?></p>
                </div>
            </div>

            <div class="aux-feedback-section aux-hide">
                <h2 class="aux-featur"><?php _e('Please explain why you gave this score (optional)', 'auxin-elements'); ?></h2>
                <h4 class="aux-featur feedback-subtitle">
                    <?php
                    printf( __( 'Please do not use this form to get support, in this case please check the %s help section %s', 'auxin-elements' ),
                           '<a href="' . $support_tab_url . '">', '</a>'  ); ?>
                </h4>
                <textarea placeholder="Enter your feedback here" rows="10" name="feedback" class="large-text"></textarea>
                <input type="text" placeholder="Email address (Optional)" name="email" class="text-input" />
                <?php wp_nonce_field( 'phlox_feedback' ); ?>

                <input type="submit" class="button button-primary aux-button" value="Submit feedback" />

                <div class="aux-sending-status">
                    <img  class="ajax-progress aux-hide" src="<?php echo AUXIN_URL; ?>/css/images/elements/saving.gif" />
                    <span class="ajax-response aux-hide" ><?php _e( 'Submitting your feedback ..', 'auxin-elements' ); ?></span>
                </div>

            </div>

            <?php auxin_send_feedback_mail(); ?>
        </form>
    </div>

    <?php
}

function auxin_welcome_add_section_feedback( $sections ){

    $sections['feedback'] = array(
        'label'       => __( 'Feedback', 'auxin-elements' ),
        'description' => sprintf(__( 'Please leave a feedback and help us to improve %s theme.', 'auxin-elements'), THEME_NAME_I18N ),
        'callback'    => 'auxin_welcome_page_display_section_feedback'
    );

    return $sections;
}

add_filter( 'auxin_admin_welcome_sections', 'auxin_welcome_add_section_feedback', 90 );

function auxin_send_feedback_mail(){
    if  ( ! ( ! isset( $_POST['phlox_feedback'] ) || ! wp_verify_nonce( $_POST['phlox_feedback'], 'feedback_send') ) ) {

        $email    = ! empty( $_POST["email"]    ) ? sanitize_email( $_POST["email"]  ) : 'Empty';
        $feedback = ! empty( $_POST["feedback"] ) ? esc_textarea( $_POST["feedback"] ) : '';

        if( $feedback ){
            wp_mail( 'info@averta.net', 'feedback from phlox dashboard', $feedback . chr(0x0D).chr(0x0A) . 'Email: ' . $email );
            $text = __( 'Thanks for your feedback', 'auxin-elements' );
        } else{
            $text = __('Please try again and fill up at least the feedback field.', 'auxin-elements');
        }

        printf('<p class="notification">%s</p>', $text);
    }
}

/*-----------------------------------------------------------------------------------*/
/*  Adds subtitle meta field to 'Title setting' tab
/*-----------------------------------------------------------------------------------*/

function auxin_add_metabox_field_to_title_setting_tab( $fields, $id, $type ){

    if( 'general-title' == $id ){
        array_splice(
            $fields,
            1, 0,
            array(
                array(
                    'title'         => __('Subtitle for Title Bar', 'auxin-elements'),
                    'description'   => __('Second Title for title bar (optional). Note: You have to enable "Display Title Bar Section" option in order to display the subtitle.', 'auxin-elements'),
                    'id'            => 'page_subtitle',
                    'type'          => 'editor',
                    'default'       => '',
                    'dependency'    => array(
                        array(
                             'id'      => 'aux_title_bar_show',
                             'value'   => array('default', 'yes'),
                             'operator'=> '=='
                        )
                    )
                )
            )
        );
    }

    return $fields;
}
add_filter( 'auxin_metabox_fields', 'auxin_add_metabox_field_to_title_setting_tab', 10, 3 );


/**
 * Display changelogs on welcome page
 *
 * @param  string  $theme_id  The theme ID that we intent to display the it's changelog
 * @return void
 */
function auxels_add_changelog_to_welcome_page( $theme_id ){


    if( function_exists('auxin_get_remote_changelog') ){

        // sanitize the theme id
        $theme_id = esc_sql( $theme_id );

        // get remote changelog
        if( false === $changelog_info = get_transient( "auxin_{$theme_id}_remote_changelog" ) ){

            $changelog = auxin_get_remote_changelog( $theme_id );

            if( is_wp_error( $changelog ) ){
                echo $changelog->get_error_message();
            } else {
                $changelog_info = json_decode( $changelog, true );
                set_transient( "auxin_{$theme_id}_remote_changelog", $changelog_info, 2 * HOUR_IN_SECONDS );
            }

        }

        // print the changelog
        if( $changelog_info && ! empty( $changelog_info['version'] ) && ! empty( $changelog_info['changelog'] ) ){
            echo '<a href="http://support.averta.net/en/changelog/'.$theme_id.'/" target="_blank"><h2 class="aux-featur">' . __( "What's New in version", 'auxin-elements') . ' '. $changelog_info['version'] . '</h2></a>';
            echo '<div class="welcome-changelog">';
            echo str_replace("\n", "<br />", $changelog_info['changelog'] );
            echo '</div>';
        }

    }

}

add_action( 'auxin_welcome_page_after_feature_section', 'auxels_add_changelog_to_welcome_page' );

/*-----------------------------------------------------------------------------------*/
/*  Adds Custom JavaScript meta field to 'Advanced setting' tab
/*-----------------------------------------------------------------------------------*/

function auxin_add_metabox_field_to_advanced_setting_tab( $fields, $id, $type ){

    if( 'general-advanced' == $id ){
        $fields[] = array(
            'title'         => __('Custom JavaScript Code', 'auxin-elements'),
            'description'   => __('Attention: The following custom JavaScript code will be applied ONLY to this page.', 'auxin-elements').'<br />'.
                               __('For defining global JavaScript roles, please use custom javaScript field on option panel.', 'auxin-elements' ),
            'id'            => 'aux_page_custom_js',
            'type'          => 'code',
            'mode'          => 'javascript',
            'default'       => ''
        );
    }
    return $fields;
}
add_filter( 'auxin_metabox_fields', 'auxin_add_metabox_field_to_advanced_setting_tab', 10, 3 );


/*-----------------------------------------------------------------------------------*/
/*  Removes install plugins submenu
/*-----------------------------------------------------------------------------------*/

function auxin_elements_remove_install_plugins_submenu(){
    remove_submenu_page( "themes.php", "tgmpa-install-plugins");
    global $submenu;
    $submenu['themes.php'][] = array( __('Install Plugins', 'auxin-elements' ), 'edit_theme_options', self_admin_url('admin.php?page=auxin-wizard&step=default_plugins') );
}
add_action( "admin_menu", "auxin_elements_remove_install_plugins_submenu", 14 );

/*-----------------------------------------------------------------------------------*/
/*  Define demo info list / for auxin-element plugin
/*-----------------------------------------------------------------------------------*/

/**
 * Retrieves the list of available demos for current theme
 *
 * @return array List of demos
 */
function auxels_add_to_demo_info_list( $default_demos ){

    $demos_list = array(
        'the-journey' => array(
            'id'            => 'the-journey',
            'title'         => __('The Journey', 'auxin-elements'),
            'desc'          => __('Create your awesome Journey Website using this demo as a starter. Best choice for adventure looks.', 'auxin-elements'),
            'preview_url'   => 'http://averta.net/phlox/demo/journey/',
            'thumb_url'     => AUXELS_URL . '/embeds/demos/journey-blog/banner.jpg',
            'file'          => AUXELS_DIR . '/embeds/demos/journey-blog/data.xml'
        ),
        'classic-blog' => array(
            'id'            => 'classic-blog',
            'title'         => __('Classic Blog', 'auxin-elements'),
            'desc'          => __('Create your classic good looking Blog using this demo as a starter. Best choice for a classic blogger.', 'auxin-elements'),
            'preview_url'   => 'http://averta.net/phlox/demo/classic-blog/',
            'thumb_url'     => AUXELS_URL . '/embeds/demos/classic-blog/banner.jpg',
            'file'          => AUXELS_DIR . '/embeds/demos/classic-blog/data.xml'
        ),
        'food-blog' => array(
            'id'            => 'food-blog',
            'title'         => __('Food Blog', 'auxin-elements'),
            'desc'          => __('Create your awesome Food Website using this demo as a starter. Best choice for restaurant looks.', 'auxin-elements'),
            'preview_url'   => 'http://averta.net/phlox/demo/food/',
            'thumb_url'     => AUXELS_URL . '/embeds/demos/food-blog/banner.jpg',
            'file'          => AUXELS_DIR . '/embeds/demos/food-blog/data.xml'
        ),
        'portfolio' => array(
            'id'            => 'portfolio',
            'title'         => __('Protfolio', 'auxin-elements'),
            'desc'          => __('A stunning demo for Phlox portfolio that represents your projects in a modern and stylish way.', 'auxin-elements'),
            'preview_url'   => 'http://averta.net/phlox/demo/portfolio/',
            'thumb_url'     => AUXELS_URL . '/embeds/demos/portfolio/banner.jpg',
            'file'          => AUXELS_DIR . '/embeds/demos/food-blog/data.xml'
        ),
        'default' => array(
            'id'            => 'default',
            'title'         => __('Default', 'auxin-elements'),
            'desc'          => __('An excellent example to get familiar with all available layouts, elements, shortcodes and other features of Phlox.', 'auxin-elements'),
            'preview_url'   => 'http://averta.net/phlox/demo/default/',
            'thumb_url'     => AUXELS_URL . '/embeds/demos/default/banner.jpg',
            'file'          => AUXELS_DIR . '/embeds/demos/default/data.xml'
        )
    );

    return array_merge( $default_demos, $demos_list );
}

add_filter( 'auxin_get_demo_info_list', 'auxels_add_to_demo_info_list' );

/*-----------------------------------------------------------------------------------*/
/*  Adding fallback for deprecated theme option name
/*-----------------------------------------------------------------------------------*/

function auxels_sync_deprecated_options(){

    $old_theme_options = get_option( THEME_ID . '_formatted_options' );
    if( false === $old_theme_options ){
        return;
    }

    $new_theme_options = get_option( THEME_ID . '_theme_options' );
    if( false === $new_theme_options ){
        update_option( THEME_ID . '_theme_options', $old_theme_options );
    }
}
add_action( 'admin_init', 'auxels_sync_deprecated_options' );

/*-----------------------------------------------------------------------------------*/
/*  Add post format related metafields to post
/*-----------------------------------------------------------------------------------*/

function auxels_add_post_metabox_models( $models ){

    // Load general metabox models
    include_once( 'metaboxes/metabox-fields-post-audio.php'   );
    include_once( 'metaboxes/metabox-fields-post-gallery.php' );
    include_once( 'metaboxes/metabox-fields-post-quote.php'   );
    include_once( 'metaboxes/metabox-fields-post-video.php'   );

    $models[] = array(
        'model'     => auxin_metabox_fields_post_gallery(),
        'priority'  => 20
    );

    $models[] = array(
        'model'     => auxin_metabox_fields_post_video(),
        'priority'  => 22
    );

    $models[] = array(
        'model'     => auxin_metabox_fields_post_audio(),
        'priority'  => 24
    );

    $models[] = array(
        'model'     => auxin_metabox_fields_post_quote(),
        'priority'  => 26
    );

    $models[] = array(
        'model'     => auxin_metabox_fields_general_advanced(),
        'priority'  => 36
    );

    return $models;
}

add_filter( 'auxin_admin_metabox_models_post', 'auxels_add_post_metabox_models' );

/*-----------------------------------------------------------------------------------*/
/*  Add advanced metafields to page
/*-----------------------------------------------------------------------------------*/

function auxels_add_page_metabox_models( $models ){

    include_once( 'metaboxes/metabox-fields-general-top-header.php');
    include_once( 'metaboxes/metabox-fields-general-header.php');
    include_once( 'metaboxes/metabox-fields-general-footer.php');

    $models[] = array(
        'model'     => auxin_metabox_fields_general_top_header(),
        'priority'  => 13
    );

    $models[] = array(
        'model'     => auxin_metabox_fields_general_header(),
        'priority'  => 16
    );

    $models[] = array(
        'model'     => auxin_metabox_fields_general_footer(),
        'priority'  => 20
    );

    $models[] = array(
        'model'     => auxin_metabox_fields_general_advanced(),
        'priority'  => 36
    );


    return $models;
}

add_filter( 'auxin_admin_metabox_models_page', 'auxels_add_page_metabox_models' );

/*-----------------------------------------------------------------------------------*/
/*  Add theme tab in siteorigin page builder
/*-----------------------------------------------------------------------------------*/

function auxin_add_widget_tabs($tabs) {
    $tabs[] = array(
        'title'  => THEME_NAME,
        'filter' => array(
            'groups' => array('auxin')
        )
    );

    if (isset($tabs['recommended'])){
        unset($tabs['recommended']);
    }


    return $tabs;
}
add_filter( 'siteorigin_panels_widget_dialog_tabs', 'auxin_add_widget_tabs', 20 );

// =============================================================================

function auxin_admin_footer_text( $footer_text ) {

    // the admin pages that we intent to display theme footer text on
    $admin_pages = array(
        'toplevel_page_auxin',
        'appearance_page_auxin',
        'toplevel_page_auxin-welcome',
        'appearance_page_auxin-welcome',
        'page',
        'post',
        'widgets',
        'dashboard',
        'edit-post',
        'edit-page',
        'edit-portfolio',
        'edit-faq',
        'edit-product'
    );

    if( ! ( function_exists('auxin_is_theme_admin_page') && auxin_is_theme_admin_page( $admin_pages ) ) ){
        return $footer_text;
    }

    if( defined('THEME_PRO') && THEME_PRO ){
        $welcome_tab_url  = self_admin_url( 'admin.php?page=auxin-welcome&tab=' );
        $setup_wizard_url = self_admin_url( 'admin.php?page=auxin-wizard' );
    } else {
        $welcome_tab_url  = self_admin_url( 'themes.php?page=auxin-welcome&tab=' );
        $setup_wizard_url = self_admin_url( 'themes.php?page=auxin-wizard' );
    }


    $auxin_text = sprintf(
        __( 'Quick access to %s %sdashboard%s, %ssetup wizard%s, %soptions%s, %ssupport%s and %sfeedback%s page.', 'auxin-elements' )
        ,
        '<strong>' . THEME_NAME_I18N . '</strong>',
        '<a href="'. esc_url( $welcome_tab_url .'features' ) .'" title="'. sprintf( esc_attr__( '%s theme version %s', 'auxin-elements' ), THEME_NAME_I18N, THEME_VERSION ) .'" >',
        '</a>',
        '<a href="'. esc_url( $setup_wizard_url ) .'" title="'. __('Theme Setup Wizard', 'auxin-elements' ) .'" >',
        '</a>',
        '<a href="'. esc_url( self_admin_url( 'customize.php?url=' ) . $welcome_tab_url .'features' ) .'" title="'. __('Theme Customizer', 'auxin-elements' ) .'" >',
        '</a>',
        '<a href="'. esc_url( $welcome_tab_url .'support' ) .'">',
        '</a>',
        '<a href="'. esc_url( $welcome_tab_url .'feedback' ) .'">',
        '</a>'
    );

    return '<span id="footer-thankyou">' . $auxin_text . '</span>';
}
add_filter( 'admin_footer_text',  'auxin_admin_footer_text' );




/*-----------------------------------------------------------------------------------*/
/*  Dashboard "Right Now" modification
/*-----------------------------------------------------------------------------------*/

function auxin_add_2_rightnow_bottom() {
    $p_theme = auxin_get_main_theme();

    echo '<span style="line-height:1.5em;">';
    printf(
        __( 'You are using %1$s theme version %2$s.', 'auxin-elements'),
        '<strong>'. $p_theme->display('Name'). '</strong>',
        '<strong>'. $p_theme->display('Version'). '</strong>'
    );
    if( ( ! defined( 'THEME_PRO' ) && THEME_PRO ) ){
        printf(
            __( 'Please support us to continue this project by rating it %3$s', 'auxin-elements' ),
            '<a href="https://wordpress.org/support/theme/phlox/reviews/#new-post" target="_blank">★★★★★</a>'
        );
    }
    echo '</span>';
}

add_action( 'rightnow_end', 'auxin_add_2_rightnow_bottom' );

/*-----------------------------------------------------------------------------------*/
/*  Assign menus on start or after demo import
/*-----------------------------------------------------------------------------------*/

/**
 * Automatically assigns the appropriate menus to menu locations
 * Known Locations:
 *  - header-primary  : There should be a menu with the word "Primary" Or "Mega" in its name
 *  - header-secondary: There should be a menu with the word "Secondary" in its name
 *  - footer          : There should be a menu with the word "Footer" in its name
 *
 * @return bool         True if at least one menu was assigned, false otherwise
 */
function auxin_assign_default_menus(){

    $assinged = false;
    $locations = get_theme_mod('nav_menu_locations');
    $nav_menus = wp_get_nav_menus();

    foreach ( $nav_menus as $nav_menu ) {
        $menu_name = strtolower( $nav_menu->name );

        if( empty( $locations['header-secondary'] ) && preg_match( '(secondary)', $menu_name ) ){
            $locations['header-secondary'] = $nav_menu->term_id;
            $assinged = true;
        } elseif( empty( $locations['header-primary'] ) && preg_match( '(primary|mega|header)', $menu_name ) ){
            $locations['header-primary'] = $nav_menu->term_id;
            $assinged = true;
        } elseif( empty( $locations['footer'] ) && preg_match( '(footer)', $menu_name ) ){
            $locations['footer'] = $nav_menu->term_id;
            $assinged = true;
        }
    }

    set_theme_mod( 'nav_menu_locations', $locations );
    return $assinged;
}

add_action( 'after_switch_theme', 'auxin_assign_default_menus' ); // triggers when theme will be actived, WP 3.3
add_action( 'import_end', 'auxin_assign_default_menus' ); // triggers when the theme data was imported

/*-----------------------------------------------------------------------------------*/
/*  Remove any script tag fromt custom js (if user used them in the script content)
/*-----------------------------------------------------------------------------------*/

/**
 * Strip <script> tags
 *
 * @param  string $js_string  The custom js string
 * @return string             The sanitized custom js code
 */
function auxels_strip_script_tags_from_custom_js( $js_string ){
    if ( false !== stripos( $js_string, '</script>' ) ) {
        $js_string = str_replace( array( "<script>", "</script>" ), array('', ''), $js_string );
    }
    return $js_string;
}
add_filter( 'auxin_custom_js_string', 'auxels_strip_script_tags_from_custom_js' );

/*-----------------------------------------------------------------------------------*/
/*  Remove any style tag fromt custom css (if user used them in the style content)
/*-----------------------------------------------------------------------------------*/

/**
 * Strip <style> tags
 *
 * @param  string $css_string  The custom css string
 * @return string             The sanitized custom css code
 */
function auxels_strip_style_tags_from_custom_css( $css_string ){
    if ( false !== stripos( $css_string, '</style>' ) ) {
        $css_string = str_replace( array( "<style>", "</style>" ), array('', ''), $css_string );
    }
    return $css_string;
}
add_filter( 'auxin_custom_css_string', 'auxels_strip_style_tags_from_custom_css' );

/*-----------------------------------------------------------------------------------*/

/**
 * Recreate custom css and js files after updating auxin plugins
 *
 * @param  $flush  Whether to flush rewrite rules after plugin update or not
 * @return void
 */
function auxels_update_custom_js_css_file_on_auxin_plugin_update( $flush = true ){
    auxin_save_custom_js();
    auxin_save_custom_css();
    if( $flush )
        flush_rewrite_rules();
}
add_action( "auxin_plugin_updated", "auxels_update_custom_js_css_file_on_auxin_plugin_update" );


/**
 * Triggers an action after plugin was updated to new version.
 *
 * @return void
 */
function auxels_after_plugin_update(){
    if( AUXELS_VERSION !== get_transient( 'auxin_' . AUXELS_SLUG . '_version' ) ){
        set_transient( 'auxin_' . AUXELS_SLUG . '_version', AUXELS_VERSION, MONTH_IN_SECONDS );

        do_action( 'auxin_plugin_updated', false, AUXELS_SLUG, AUXELS_VERSION, AUXELS_BASE_NAME );
    }
}
add_action( "admin_init", "auxels_after_plugin_update");


/**
 * Disable the query monitor on vc frontend editor
 *
 * @return bool                Whether to displatche the debug report or not
 */
function auxin_disable_query_monitor_on_vc_fronteditor( $debug_enabled ){
    return ( function_exists( 'vc_is_frontend_editor' ) && vc_is_frontend_editor() ) ? false : $debug_enabled;
}
add_filter( 'qm/dispatch/ajax', "auxin_disable_query_monitor_on_vc_fronteditor" );
add_filter( 'qm/dispatch/html', "auxin_disable_query_monitor_on_vc_fronteditor" );


function auxin_meida_setting_requires_modification(){
    echo '<div class="aux-admin-error notice notice-warning notice-large">';
    _e( 'Please make sure the image aspect ratio for all image sizes are the same.', 'auxin-elements' );
    echo '</div>';
}

/**
 *
 *
 * @return void
 */
function auxels_after_media_setting_updated(){

    $image_sizes = array('thumbnail', 'medium', 'medium_large', 'large');
    $same_ratio = true;
    $ratio = '';

    foreach ( $image_sizes as $image_size ) {
        $width = get_option( $image_size. '_size_w' );

        if( $height = get_option( $image_size. '_size_h' ) ){
            if( ! empty( $ratio ) && $ratio != ( $width / $height ) ){
                $same_ratio = false;
                break;
            }
            $ratio = $width / $height;
        }

    }

    if( $same_ratio && $ratio ){
        if( ! get_option( 'medium_large_size_h') ){
            update_option( 'medium_large_size_h', get_option( 'medium_large_size_w' ) * $ratio );
        }
        set_theme_mod( 'auxin_wp_image_sizes_ratio', $ratio );
    } elseif( ! $same_ratio ) {
        add_action( 'admin_notices', 'auxin_meida_setting_requires_modification' );
    }

}

add_action( "load-options-media.php", "auxels_after_media_setting_updated");
add_action( "auxin_plugin_updated"  , "auxels_after_media_setting_updated" );


/*-----------------------------------------------------------------------------------*/
/*  Adds Custom Footer Metafields to 'Layout Options' tab
/*-----------------------------------------------------------------------------------*/

function auxin_add_metabox_field_to_layout_setting_tab( $fields, $id, $type ){

    if( 'layout-options' == $id ){

        $fields[] = array(
            'title'       => __('Footer Brand Image', 'auxin-elements'),
            'description' => __('This image appears as site brand image on footer section.', 'auxin-elements'),
            'id'          => 'page_secondary_logo_image',
            'section'     => 'footer-section-footer',
            'dependency'  => array(
                array(
                     'id'      => 'page_show_footer',
                     'value'   => array('yes', 'default'),
                     'operator'=> '=='
                )
            ),
            'type'        => 'image'
        );
    }

    return $fields;
}
add_filter( 'auxin_metabox_fields', 'auxin_add_metabox_field_to_layout_setting_tab', 10, 3 );
