<?php
	$message = __( 'Apologies, we are busy updating our website.', THEME_DOMAIN ) ;
	$title   = __( 'Maintenance Mode Is Enable', THEME_DOMAIN ) ;
	wp_die( $message, $title );