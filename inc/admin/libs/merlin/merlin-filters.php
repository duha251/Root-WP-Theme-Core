<?php
/**
 * Available filters for extending Merlin WP.
 *
 * @package   Merlin WP
 * @version   @@pkg.version
 * @link      https://merlinwp.com/
 * @author    Rich Tabor, from ThemeBeans.com & the team at ProteusThemes.com
 * @copyright Copyright (c) 2018, Merlin WP of Inventionn LLC
 * @license   Licensed GPLv3 for Open Source Use
 */


/**
 * Define the demo import files (remote files).
 *
 * To define imports, you just have to add the following code structure,
 * with your own values to your theme (using the 'merlin_import_files' filter).
 */
function mytheme_merlin_import_files() {
	return array(
		array(
			'import_file_name'             => 'Demo Import 1',
			'local_import_file'            => trailingslashit( get_template_directory() ) . 'merlin/demo-content.xml',
			'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'merlin/widgets.json',
			'local_import_customizer_file' => trailingslashit( get_template_directory() ) . 'merlin/customizer.dat',
			'local_import_redux'           => array(
				array(
					'file_path'   => trailingslashit( get_template_directory() ) . 'merlin/redux_options.json',
					'option_name' => 'redux_option_name',
				),
			),
			'import_preview_image_url'     => get_template_directory() . '/merlin/preview_import_image1.jpg',
			'import_notice'                => esc_html__( 'A special note for this import.', 'mytheme' ),
			'preview_url'                  => '//www.example.com/my-demo-1',
		)
	);
}
add_filter( 'merlin_import_files', 'mytheme_merlin_import_files' );
