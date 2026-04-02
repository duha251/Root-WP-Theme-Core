<?php
/**
 * Search Form Template
 * 
 * This file renders the search form for the Mytheme theme.
 * It provides a customizable search interface for WordPress sites
 * using the Mytheme theme.
 * 
 * @package Mytheme
 * @subpackage Templates
 * @since 1.0.0
 * @version 1.0.0
 */
$template_file = ( $args['template'] ?? 'default' );
$template_path = "template-parts/searchform/{$template_file}";
if ( locate_template( $template_path.'.php' ) ) {
    mytheme_get_template( $template_path, [] );
} 
?>