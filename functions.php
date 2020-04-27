<?php
function my_theme_enqueue_styles() { 
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

// Enable {font-display: swap} as per
// https://deytah.io/blog/enable-font-swap-for-google-fonts-in-divi/
//
// Remove Divi's function
add_action( 'init', function () {
	remove_action( 'wp_enqueue_scripts', 'et_builder_preprint_font' );
});

// Add our modified font-swapper
function et_builder_preprint_font_replacement() {
	// Return if this is not a post or a page
	if ( ! is_singular() || ! et_core_use_google_fonts() ) {
		return;
	}

	$post_id = get_the_ID();

	$post_fonts_data = get_post_meta( $post_id, 'et_enqueued_post_fonts', true );

	// No need to proceed if the proper data is missing from the cache
	if ( ! is_array( $post_fonts_data ) || ! isset( $post_fonts_data['family'], $post_fonts_data['subset'] ) ) {
		return;
	}

	$fonts = $post_fonts_data[ 'family'];

	if ( ! $fonts ) {
		return;
	}

	$unique_subsets = $post_fonts_data[ 'subset'];
	$protocol       = is_ssl() ? 'https' : 'http';

	wp_enqueue_style( 'et-builder-googlefonts-cached', esc_url( add_query_arg( array(
		'family' => implode( '|', $fonts ) ,
		'subset' => implode( ',', $unique_subsets ),
		'display' => 'swap',
	), "$protocol://fonts.googleapis.com/css" ) ) );
}
add_action( 'wp_enqueue_scripts', 'et_builder_preprint_font_replacement', 10, 2 );
