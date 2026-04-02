<?php
/**
 * The header for our theme
 *
 * Displays all of the <head> section and everything up to <main>
 *
 * @package Mytheme
 * @author Case-Themes
 * @link https://mytheme.casethemes.net
 * @since 1.0.0
 */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    
    <link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_uri() ); ?>" type="text/css" />
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <?php do_action( 'mytheme_before_main_content' ); ?>