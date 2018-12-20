<?php
/**
 * Include template functions
 *
 * 
 * @package    Auxin
 * @author     averta (c) 2014-2018
 * @link       http://averta.net
 */

// include the template parts only on frontend or during saving the page by siteorigin page builder
if( ! is_admin() || ! empty( $_POST['_sopanels_nonce'] ) || ! empty( $_REQUEST['_panelsnonce'] ) ){
    locate_template( AUXIN_INC . 'include/templates/templates-header.php', true, true );
    locate_template( AUXIN_INC . 'include/templates/templates-post.php'  , true, true );
    locate_template( AUXIN_INC . 'include/templates/templates-footer.php', true, true );
}