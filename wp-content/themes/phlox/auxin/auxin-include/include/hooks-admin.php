<?php
/**
 * Admin hooks
 *
 * 
 * @package    Auxin
 * @author     averta (c) 2014-2018
 * @link       http://averta.net
*/

function auxin_update_font_icons_list(){
    // parse and cache the list of fonts
    $fonts = Auxin()->Font_Icons;
    $fonts->update();
}
add_action( 'after_switch_theme', 'auxin_update_font_icons_list' );


// make the customizer avaialble while requesting via ajax
if ( defined('DOING_AJAX') && DOING_AJAX && version_compare( PHP_VERSION, '5.3.0', '>=') ){
    Auxin_Customizer::get_instance();
}


/*-----------------------------------------------------------------------------------*/
/*  Include the Welcome page
/*-----------------------------------------------------------------------------------*/

function auxin_register_theme_menu() {

    $root_menu_name = AUXIN_NO_BRAND ? __( 'Theme Setting', 'phlox') : THEME_NAME_I18N;
    $root_menu_name = apply_filters( 'auxin_theme_setting_menu_name', $root_menu_name );

    $welcome_root_slug = 'auxin-welcome';

    //

    add_theme_page(
        __('Welcome', 'phlox'),                    // [Title]    The title to be displayed on the corresponding page for this menu
        $root_menu_name,                                // [Text]     The text to be displayed for this actual menu item
        apply_filters( 'auxin_theme_welcome_capability', 'manage_options' ),
                                                        // [User]     Which type of users can see this menu
        $welcome_root_slug,                             // [ID/slug]  The unique ID - that is, the slug - for this menu item
        array( Auxin_About::get_instance(), 'render')   // [Callback] The name of the function to call when rendering the menu for this page
    );
    // @endif
}

add_action( 'admin_menu', 'auxin_register_theme_menu' );


/*------------------------------------------------------------------------*/

/**
 * Update the deprecated option ids
 */
function auxn_update_last_checked_version(){

    $last_checked_version = auxin_get_theme_mod( 'last_checked_version', '1.0.0' );

    if( version_compare( $last_checked_version, THEME_VERSION, '>=') ){
        return;
    }

    do_action( 'auxin_theme_updated', $last_checked_version );

    set_theme_mod( 'last_checked_version', THEME_VERSION );
}
add_action( 'auxin_loaded', 'auxn_update_last_checked_version' );


/**
 * Skip the notice for core plugin if skip btn clicked
 *
 * @return void
 */
function auxin_hide_core_plugin_notice() {

    if ( isset( $_GET['aux-hide-core-plugin-notice'] ) && isset( $_GET['_notice_nonce'] ) ) {
        if ( ! wp_verify_nonce( $_GET['_notice_nonce'], 'auxin_hide_notices_nonce' ) ) {
            wp_die( __( 'Authorization failed. Please refresh the page and try again.', 'phlox' ) );
        }
        set_transient( 'auxin_hide_core_plugin_notice', 1, 4 * YEAR_IN_SECONDS );
    }
}
add_action( 'wp_loaded', 'auxin_hide_core_plugin_notice' );


/**
 * Display a notice for installing theme core plugin
 *
 * @return void
 */
function auxin_core_plugin_notice(){
    if( defined( 'AUXELS_VERSION' ) || get_transient( 'auxin_hide_core_plugin_notice' ) ){
        return;
    }

    $current_screen = get_current_screen();
    if( isset( $current_screen->id ) && 'plugin-install' === $current_screen->id ){
        return;
    }

    $install_url = self_admin_url( 'plugin-install.php?s=phlox+core&tab=search&type=term' );
?>
    <div id="message" class="updated auxin-message">
        <p><?php _e( 'In order to import demo content and add more features to Phlox theme, please install <strong>Phlox Core Plugin</strong>.', 'phlox' ); ?></p>
        <p class="submit"><a href="<?php echo esc_url( $install_url ); ?>" class="button-primary"><?php _e( 'Install Core Plugin', 'phlox' ); ?></a> <a class="button-secondary skip" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'aux-hide-core-plugin-notice', 'install' ), 'auxin_hide_notices_nonce', '_notice_nonce' ) ); ?>"><?php _e( 'Skip', 'phlox' ); ?></a></p>
    </div>
<?php
}
add_action( 'admin_notices', 'auxin_core_plugin_notice' );
