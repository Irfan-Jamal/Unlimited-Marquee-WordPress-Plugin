<?php
// Enable error reporting and display all errors
get_header();

/*
Template Name: Marquee
Template Post Type: marquee
*/

// Check if accessed directly
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Get the current post ID
$post_id = get_the_ID();

// Check if it's a Marquee post type
if ($post->post_type === 'marquee' && is_singular('marquee')) {
    // Include the file where the global function is registered
   // Include the file where the function is defined
$path_to_file = plugin_dir_path( __FILE__ ) . 'Unlimited_marquee.php';
if ( file_exists( $path_to_file ) ) {
    require_once $path_to_file;

    // Check if the function exists before calling it
   if ( function_exists( 'unlimited_marquee_widget' ) ) {
    // Call the function
    $output = unlimited_marquee_widget( $post_id );

    // Define the list of allowed HTML tags and attributes
    $allowed_tags = array(
        'div' => array(
            'class' => array(),
            'style' => array(),
        ),
        'marquee' => array(
            'class' => array(),
            'behavior' => array(),
            'scrolldelay' => array(),
            'direction' => array(),
            'scrollamount' => array(),
            'style' => array(),
            'height' => array(),
            'width' => array(),
        ),
		
		'style' => array(
        'type' => true, // Allow the type attribute
        'media' => true, // Allow the media attribute
        'scoped' => true, // Allow the scoped attribute
    ),
        'link' => array(
            'href' => array(),
            'rel' => array(),
            'type' => array(),
        ),
        'a' => array(
            'href' => array(),
            'style' => array(),
            'target' => array(),
        ),
        'script' => array(
            'src' => array(),
        ),
        'span' => array(
            'class' => array(),
            'style' => array(),
        ),
    );

    // Output the result with allowed HTML tags and attributes
    echo wp_kses( $output, $allowed_tags );
} else {
    echo 'Error: unlimited_marquee_widget function is not defined.';
}

} else {
    echo 'Error: File Unlimited_marquee.php not found.';
}


    // Check if ID is provided
    if (!empty($post_id)) {
        // Output for debugging

        // Call the custom HTML widget function
        $output = unlimited_marquee_widget($post_id);

        // Output the widget content
}}
get_footer();
