<?php
/*
Plugin Name: Unlimited Marquee
Plugin URI: https://etookhan.com
Description: Unlimited Marquee is a WordPress plugin designed to enhance your website with interactive marquees. Easily create and customize marquees!
Version: 1.0.0
Requires at least: 6.0
Requires PHP: 5.7
Author: Muhammad Jamal
Author URI: https://etookhan.com
License: GPLv2 or later
Text Domain: Unlimited-Marquee
*/



//////////// 
// enqueuing JS script and CSS styles
function enqueue_color_picker() {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
}
add_action('admin_enqueue_scripts', 'enqueue_color_picker');


function unlimited_marquee_js() {
    // Enqueue first JavaScript file for non-admin
    wp_enqueue_script( 'unlimited-marquee-script', plugins_url( '/assets/js/marquee.js', __FILE__ ), array(), '1.0.0', true );

    // Enqueue second JavaScript file for non-admin
}
add_action( 'wp_enqueue_scripts', 'unlimited_marquee_js' );


function admin_unlimited_marquee_js() {
    // Enqueue first JavaScript file
    wp_enqueue_script( 'admin-theme-script', plugins_url( '/assets/js/unlimited-marquee.js', __FILE__ ), array(), '1.0.0', true );

    // Enqueue second JavaScript file
    wp_enqueue_script( 'admin-theme-script-2', plugins_url( '/assets/js/unlimited-marquee-textarea.js', __FILE__ ), array(), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'admin_unlimited_marquee_js' );


function unlimited_marquee_styles() {
    global $post;

    // Check if we are on the post edit or create screen
    if ( is_admin() && isset( $post ) && $post->post_type == 'marquee' ) {
        // Enqueue Tailwind CSS from CDN with version 2.2.19
        wp_enqueue_style( 'admin-tailwind', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css', array(), '2.2.19' );

        // Enqueue second CSS file with a version number
        wp_enqueue_style( 'admin-post-style', plugins_url( '/assets/css/post.css', __FILE__ ), array(), '1.0.0' );
    }
}
add_action( 'admin_enqueue_scripts', 'unlimited_marquee_styles' );





function theme_js_script() {
    // Enqueue the script with a version number
    wp_enqueue_script( 'theme-script', plugins_url( 'assets/js/unlimited-marquee.js', __FILE__ ), array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'theme_js_script' );

if ( is_admin() ) {
    add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'my_plugin_actions', 10, 4 );
}


function my_plugin_actions( $actions, $plugin_file, $plugin_data, $context ) {
  $website_url = get_site_url();
  $actions = array_merge( $actions, array(
    'show_marquees' => '<a href="' . $website_url . '/wp-admin/edit.php?post_type=marquee">Show Marquees</a>',
    'add_new_marquee' => '<a href="' . $website_url . '/wp-admin/post-new.php?post_type=marquee">Add New</a>',
	'marquee_default_setting' => '<a href="' . $website_url . '/wp-admin/edit.php?post_type=marquee&page=marquee_options">Default Setting</a>',
  ));
  return $actions;
}



function register_marquee_post_type() {
    $labels = array(
        'name'               => _x( 'Marquees', 'post type general name', 'ulmarquee' ),
        'singular_name'      => _x( 'Marquee', 'post type singular name', 'ulmarquee' ),
        'menu_name'          => _x( 'Marquees', 'admin menu', 'ulmarquee' ),
        'name_admin_bar'     => _x( 'Marquee', 'add new on admin bar', 'ulmarquee' ),
        'add_new'            => _x( 'Add New', 'marquee', 'ulmarquee' ),
        'add_new_item'       => __( 'Add New Marquee', 'ulmarquee' ),
        'new_item'           => __( 'New Marquee', 'ulmarquee' ),
        'edit_item'          => __( 'Edit Marquee', 'ulmarquee' ),
        'view_item'          => __( 'View Marquee', 'ulmarquee' ),
        'all_items'          => __( 'All Marquees', 'ulmarquee' ),
        
        'search_items'       => __( 'Search Marquees', 'ulmarquee' ),
        'parent_item_colon'  => __( 'Parent Marquees:', 'ulmarquee' ),
        'not_found'          => __( 'No marquees found.', 'ulmarquee' ),
        'not_found_in_trash' => __( 'No marquees found in Trash.', 'ulmarquee' )
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'marquee' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'supports'           => array( 'title', 'editor', 'thumbnail' ), // Customize as needed
        'menu_icon'          => 'dashicons-slides' // Icon name

    );

    register_post_type( 'marquee', $args );
}
add_action( 'init', 'register_marquee_post_type' );

function load_marquee_template( $template ) {
    if ( is_singular( 'marquee' ) ) {
        $template_path = plugin_dir_path( __FILE__ ) . 'Unlimited_marquee_single.php';

        if ( file_exists( $template_path ) ) {
            return $template_path;
        }
    }

    return $template;
}
add_filter( 'template_include', 'load_marquee_template' );



// Add meta box for text areas
function add_text_areas_meta_box() {
    add_meta_box(
        'text_areas_meta_box',
        'Text Areas',
        'render_text_areas_meta_box',
        'marquee',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'add_text_areas_meta_box' );

function render_text_areas_meta_box( $post ) {
    $text_areas = get_post_meta( $post->ID, 'text_areas', true );
    $text_links = get_post_meta( $post->ID, 'text_links', true );
    $link_targets = get_post_meta( $post->ID, 'link_targets', true );
    wp_nonce_field( basename( __FILE__ ), 'text_areas_nonce' );
    ?>
    <div id="text-areas-container" class="space-y-4">
        <?php if ( $text_areas ) :
            foreach ( $text_areas as $key => $text ) :
                $link = isset( $text_links[ $key ] ) ? $text_links[ $key ] : '';
                $target = isset( $link_targets[ $key ] ) ? $link_targets[ $key ] : '_self';
                ?>
               <div class="border border-gray-300 p-4 rounded-lg">
    <label for="text_<?php echo esc_attr( $key ); ?>" class="block mb-2">Text Area <?php echo esc_html( $key + 1 ); ?>:</label>
    <textarea name="text_areas[]" id="text_<?php echo esc_attr( $key ); ?>" rows="4" cols="50" class="block w-full border border-gray-300 rounded-md"><?php echo esc_textarea( $text ); ?></textarea>
    <label for="link_<?php echo esc_attr( $key ); ?>" class="block mb-2 mt-4">Link:</label>
    <input type="text" name="text_links[]" id="link_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $link ); ?>" class="block w-full border border-gray-300 rounded-md">
    <select name="link_targets[]" class="block w-full border border-gray-300 rounded-md mt-4">
        <option value="_self" <?php selected( $target, '_self' ); ?>>Open in Same Tab</option>
        <option value="_blank" <?php selected( $target, '_blank' ); ?>>Open in New Tab</option>
    </select>
    <button type="button" class="remove-text bg-red-500 text-white px-4 py-2 mt-4 rounded-lg hover:bg-red-600">Remove</button>
</div>

                <?php
            endforeach;
            else:
            ?>
	
           <div class="border border-gray-300 p-4 rounded-lg">
                    <label for="text_0" class="block mb-2">Text Area</label>
                    <textarea name="text_areas[]" id="text_0" rows="4" cols="50" class="block w-full border border-gray-300 rounded-md"></textarea>
                    <label for="link_0" class="block mb-2 mt-4">Link:</label>
                    <input type="text" name="text_links[]" id="link_0" class="block w-full border border-gray-300 rounded-md">
                    <select name="link_targets[]" class="block w-full border border-gray-300 rounded-md mt-4">
                        <option value="_self">Open in Same Tab</option>
                        <option value="_blank">Open in New Tab</option>
                    </select>
                    <button type="button" class="remove-text bg-red-500 text-white px-4 py-2 mt-4 rounded-lg hover:bg-red-600">Remove</button>
                </div>
            <?php
        endif;
        ?>
        <button type="button" id="add-text" class="button bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Add Text Area</button>
    </div>

    <?php
}

// Save meta box data
function save_text_areas_meta_box( $post_id ) {
    if ( ! isset( $_POST['text_areas_nonce'] ) || ! wp_verify_nonce( $_POST['text_areas_nonce'], basename( __FILE__ ) ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check if text areas are empty
    if ( empty( $_POST['text_areas'] ) ) {
        // If empty, delete the corresponding post meta data
        delete_post_meta( $post_id, 'text_areas' );
    } else {
        // If not empty, sanitize and save the text areas
        $text_areas = array_map( 'sanitize_text_field', $_POST['text_areas'] );
        update_post_meta( $post_id, 'text_areas', $text_areas );
    }

	
	
    // Save text links and link targets as before
    if ( isset( $_POST['text_links'] ) ) {
        $text_links = array_map( 'esc_url_raw', $_POST['text_links'] );
        update_post_meta( $post_id, 'text_links', $text_links );
    }

    if ( isset( $_POST['link_targets'] ) ) {
        $link_targets = array_map( 'sanitize_key', $_POST['link_targets'] );
        update_post_meta( $post_id, 'link_targets', $link_targets );
    }

    // Save icons with unique keys
}

add_action( 'save_post', 'save_text_areas_meta_box' );


// Customize admin styles to hide unnecessary elements



// Add meta box to custom post type 'Marquee'
function add_marquee_style_meta_box() {
    add_meta_box(
        'marquee_style_meta_box',
        'Style Form',
        'render_marquee_style_meta_box',
        'marquee',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_marquee_style_meta_box');


///////////////setting page content
//// Add menu item for the options page
function marquee_options_menu() {
    add_submenu_page(
        'edit.php?post_type=marquee', // parent menu slug
        'Marquee Default Settings',            // page title
        'Marquee Default Settings',            // menu title
        'manage_options',              // capability
        'marquee_options',             // menu slug
        'marquee_options_page'         // callback function to render the page
    );
}
add_action('admin_menu', 'marquee_options_menu');

// Register settings
function marquee_register_settings() {
    register_setting('marquee_options_group', 'marquee_default_options');
}
add_action('admin_init', 'marquee_register_settings');

// Render the options page
function marquee_options_page() {
    ?>
 <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <div class="wrap">
        <form method="post" action="options.php">
            <?php settings_fields('marquee_options_group'); ?>
            <?php $marquee_default_options = get_option('marquee_default_options'); ?>

            <!-- Add your form fields here -->
           <div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
		   <h1 style="color:white; background-color:black; text-align:center;">
			Set default settings for your marquees.
		</h1>
		<br>
        <!-- Background Color -->
        <div class="mb-4">
            <label for="background_color" class="block text-gray-700 font-bold">Background Color:</label>
<input type="text" id="background_color" class="color-field border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" name="marquee_default_options[background_color]" value="<?php echo isset($marquee_default_options['background_color']) ? esc_attr($marquee_default_options['background_color']) : '#ffffff'; ?>">
        </div>

        <!-- Text Color -->
        <div class="mb-4">
            <label for="text_color" class="block text-gray-700 font-bold">Text Color:</label>
<input type="text" id="text_color" class="color-field border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" name="marquee_default_options[text_color]" value="<?php echo isset($marquee_default_options['text_color']) ? esc_attr($marquee_default_options['text_color']) : '#000000'; ?>">
        </div>

        <!-- Text Hover Color -->
        <div class="mb-4">
            <label for="text_hover_color" class="block text-gray-700 font-bold">Text Hover Color:</label>
<input type="text" id="text_hover_color" class="color-field border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" name="marquee_default_options[text_hover_color]" value="<?php echo isset($marquee_default_options['text_hover_color']) ? esc_attr($marquee_default_options['text_hover_color']) : '#ff0000'; ?>">
        </div>

        <!-- Typography & Others -->
        <div class="mb-4">
            <label for="pause_on_hover" class="block text-gray-700 font-bold">Pause on Hover:</label>
<input type="checkbox" id="pause_on_hover" name="marquee_default_options[pause_on_hover]" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" <?php checked(isset($marquee_default_options['pause_on_hover']) ? $marquee_default_options['pause_on_hover'] : '', 'yes'); ?>>
        </div>

        <!-- Marquee Height -->
        <div class="mb-4">
            <label for="marquee_height" class="block text-gray-700 font-bold">Marquee Height (px):</label>
<input type="text" id="marquee_height" name="marquee_default_options[marquee_height]" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo isset($marquee_default_options['marquee_height']) ? esc_attr($marquee_default_options['marquee_height']) : ''; ?>">
        </div>

        <!-- Space Between Marquee Items -->
        <div class="mb-4">
            <label for="marquee_spacing" class="block text-gray-700 font-bold">Space Between Marquee Items (px):</label>
<input type="number" id="marquee_spacing" name="marquee_default_options[marquee_spacing]" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo isset($marquee_default_options['marquee_spacing']) ? esc_attr($marquee_default_options['marquee_spacing']) : ''; ?>">
        </div>

        <!-- Marquee Border -->
        <div class="mb-4">
            <label for="marquee_border" class="block text-gray-700 font-bold">Marquee Border (px):</label>
<input type="number" id="marquee_border" name="marquee_default_options[marquee_border]" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo isset($marquee_default_options['marquee_border']) ? esc_attr($marquee_default_options['marquee_border']) : ''; ?>">
        </div>

        <!-- Marquee Border Color -->
        <div class="mb-4">
            <label for="marquee_border_color" class="block text-gray-700 font-bold">Marquee Border Color:</label>
<input type="text" id="marquee_border_color" class="color-field border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" name="marquee_default_options[marquee_border_color]" value="<?php echo isset($marquee_default_options['marquee_border_color']) ? esc_attr($marquee_default_options['marquee_border_color']) : ''; ?>">
        </div>

        <!-- Marquee Border Radius -->
        <div class="mb-4">
            <label for="marquee_border_radius" class="block text-gray-700 font-bold">Marquee Border Radius(px):</label>
<input type="number" id="marquee_border_radius" name="marquee_default_options[marquee_border_radius]" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo isset($marquee_default_options['marquee_border_radius']) ? esc_attr($marquee_default_options['marquee_border_radius']) : ''; ?>">
        </div>

        <!-- Marquee Width -->
        <div class="mb-4">
            <label for="marquee_width" class="block text-gray-700 font-bold">Marquee Width (%):</label>
<input type="text" id="marquee_width" name="marquee_default_options[marquee_width]" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo isset($marquee_default_options['marquee_width']) ? esc_attr($marquee_default_options['marquee_width']) : ''; ?>">
        </div>

        <!-- Circle Color -->
        <div class="mb-4">
            <label for="circle_color" class="block text-gray-700 font-bold">Circle Color:</label>
<input type="text" id="circle_color" class="color-field border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" name="marquee_default_options[circle_color]" value="<?php echo isset($marquee_default_options['circle_color']) ? esc_attr($marquee_default_options['circle_color']) : ''; ?>">
        </div>

        <!-- Circle Width -->
        <div class="mb-4">
            <label for="circle_width" class="block text-gray-700 font-bold">Circle Width (px):</label>
<input type="number" id="circle_width" name="marquee_default_options[circle_width]" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo isset($marquee_default_options['circle_width']) ? esc_attr($marquee_default_options['circle_width']) : ''; ?>">
        </div>

        <!-- Circle Height -->
        <div class="mb-4">
            <label for="circle_height" class="block text-gray-700 font-bold">Circle Height (px):</label>
<input type="number" id="circle_height" name="marquee_default_options[circle_height]" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo isset($marquee_default_options['circle_height']) ? esc_attr($marquee_default_options['circle_height']) : ''; ?>">
        </div>

        <!-- Font Family -->
        <div class="mb-4">
            <label for="font_family" class="block text-gray-700 font-bold">Font Family:</label>
<input type="text" id="font_family" name="marquee_default_options[font_family]" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo isset($marquee_default_options['font_family']) ? esc_attr($marquee_default_options['font_family']) : ''; ?>">
        </div>

        <!-- Font Size -->
        <div class="mb-4">
            <label for="font_size" class="block text-gray-700 font-bold">Font Size (px):</label>
<input type="number" id="font_size" name="marquee_default_options[font_size]" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo isset($marquee_default_options['font_size']) ? esc_attr($marquee_default_options['font_size']) : ''; ?>">
        </div>

        <!-- Font Weight -->
        <div class="mb-4">
            <label for="font_weight" class="block text-gray-700 font-bold">Font Weight:</label>
<input type="number" id="font_weight" name="marquee_default_options[font_weight]" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo isset($marquee_default_options['font_weight']) ? esc_attr($marquee_default_options['font_weight']) : ''; ?>">
        </div>

        <!-- Font Direction -->
        <div class="mb-4">
            <label for="font_direction" class="block text-gray-700 font-bold">Font Direction:</label>
            <select id="font_direction" name="marquee_default_options[font_direction]" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out">
    <option value="right" <?php selected(isset($marquee_default_options['font_direction']) ? $marquee_default_options['font_direction'] : '', 'right'); ?>>Right</option>
    <option value="left" <?php selected(isset($marquee_default_options['font_direction']) ? $marquee_default_options['font_direction'] : '', 'left'); ?>>Left</option>
</select>
        </div>

        <!-- Scroll Delay -->
        <div class="mb-4">
            <label for="scroll_delay" class="block text-gray-700 font-bold">Scroll Delay:</label>
<input type="number" id="scroll_delay" name="marquee_default_options[scroll_delay]" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo isset($marquee_default_options['scroll_delay']) ? esc_attr($marquee_default_options['scroll_delay']) : ''; ?>">
        </div>

        <!-- Animation Speed -->
        <div class="mb-4">
            <label for="animation_speed" class="block text-gray-700 font-bold">Animation speed:</label>
<input type="number" id="animation_speed" name="marquee_default_options[animation_speed]" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo isset($marquee_default_options['animation_speed']) ? esc_attr($marquee_default_options['animation_speed']) : ''; ?>">
        </div>

        <!-- Rotate Marquee (degree) -->
        <div class="mb-4">
            <label for="marquee_degree" class="block text-gray-700 font-bold">Rotate Marquee (degree):</label>
<input type="number" id="marquee_degree" name="marquee_default_options[marquee_degree]" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo isset($marquee_default_options['marquee_degree']) ? esc_attr($marquee_default_options['marquee_degree']) : ''; ?>" max="360">
        </div>

        <!-- Show Marquee in these places -->
        <div class="mb-4">
            <label class="block text-gray-700 font-bold">Show Marquee in these places:</label>
			<br>
            <div class="flex items-center space-x-4">
                <!-- Add your checkbox inputs here -->
<input type="checkbox" id="show_marquee_hide" name="marquee_default_options[show_marquee][]" value="hide" <?php checked(isset($marquee_default_options['show_marquee']) && in_array('hide', $marquee_default_options['show_marquee'])); ?>>
<label for="show_marquee_hide" class="text-sm text-gray-600">Hide</label>

<input type="checkbox" id="show_marquee_before_header" name="marquee_default_options[show_marquee][]" value="before_header" <?php checked(isset($marquee_default_options['show_marquee']) && in_array('before_header', $marquee_default_options['show_marquee'])); ?>>
    <label for="show_marquee_before_header" class="text-sm text-gray-600">Before Header</label>

<input type="checkbox" id="show_marquee_after_header" name="marquee_default_options[show_marquee][]" value="after_header" <?php checked(isset($marquee_default_options['show_marquee']) && in_array('after_header', $marquee_default_options['show_marquee'])); ?>>
    <label for="show_marquee_after_header" class="text-sm text-gray-600">After Header/Body</label>

<input type="checkbox" id="show_marquee_before_footer" name="marquee_default_options[show_marquee][]" value="before_footer" <?php checked(isset($marquee_default_options['show_marquee']) && in_array('before_footer', $marquee_default_options['show_marquee'])); ?>>
    <label for="show_marquee_before_footer" class="text-sm text-gray-600">After Footer</label>

    
<input type="checkbox" id="show_marquee_inside_comments" name="marquee_default_options[show_marquee][]" value="inside_comments" <?php checked(isset($marquee_default_options['show_marquee']) && in_array('inside_comments', $marquee_default_options['show_marquee'])); ?>>
    <label for="show_marquee_inside_comments" class="text-sm text-gray-600">Inside Comments</label>
            </div>
			
        </div>

       
    </div>
</div>





            <!-- Add more fields as needed -->

            <input type="submit" class="button-primary" value="Save Settings">
        </form>
<p style="text-align: center;"><a href="https://etookhan.com">Made with ❤️ by Etookhan</a></p>
</div>
    <?php
}


// Render the meta box content
function render_marquee_style_meta_box($post) {
    // Add nonce field
    wp_nonce_field('marquee_style_nonce', 'marquee_style_meta_box_nonce');
    // Retrieve existing values for the fields
    $marquee_default_options = get_option('marquee_default_options');


    // Retrieve existing values for the fields
    $background_color = get_post_meta($post->ID, 'background_color', true);
$text_color = get_post_meta($post->ID, 'text_color', true);
$text_hover_color = get_post_meta($post->ID, 'text_hover_color', true);
$font_size = get_post_meta($post->ID, 'font_size', true);
$font_weight = get_post_meta($post->ID, 'font_weight', true);
$font_direction = get_post_meta($post->ID, 'font_direction', true);
$scroll_delay = get_post_meta($post->ID, 'scroll_delay', true);
$show_marquee = get_post_meta($post->ID, 'show_marquee', true);
$marquee_shortcode = get_post_meta($post->ID, 'marquee_shortcode', true);
$animation_speed = get_post_meta($post->ID, 'animation_speed', true);
$marquee_height = get_post_meta($post->ID, 'marquee_height', true);
$marquee_width = get_post_meta($post->ID, 'marquee_width', true);
$font_family = get_post_meta($post->ID, 'font_family', true);
$marquee_spacing = get_post_meta($post->ID, 'marquee_spacing', true);
$circle_color = get_post_meta($post->ID, 'circle_color', true);
$circle_width = get_post_meta($post->ID, 'circle_width', true);
$circle_height = get_post_meta($post->ID, 'circle_height', true);
$marquee_border_radius = get_post_meta($post->ID, 'marquee_border_radius', true);
$marquee_border_color = get_post_meta($post->ID, 'marquee_border_color', true);
$marquee_border = get_post_meta($post->ID, 'marquee_border', true);
$pause_on_hover = get_post_meta($post->ID, 'pause_on_hover', true);
$marquee_degree = get_post_meta($post->ID, 'marquee_degree', true);

$show_marquee_values = explode(',', $show_marquee); // Split the stored values back into an array

if (empty($background_color)) {
    $background_color = isset($marquee_default_options['background_color']) ? $marquee_default_options['background_color'] : '';
}
if (empty($text_color)) {
    $text_color = isset($marquee_default_options['text_color']) ? $marquee_default_options['text_color'] : '';
}
if (empty($text_hover_color)) {
    $text_hover_color = isset($marquee_default_options['text_hover_color']) ? $marquee_default_options['text_hover_color'] : '';
}
if (empty($font_size)) {
    $font_size = isset($marquee_default_options['font_size']) ? $marquee_default_options['font_size'] : '';
}
if (empty($font_weight)) {
    $font_weight = isset($marquee_default_options['font_weight']) ? $marquee_default_options['font_weight'] : '';
}
if (empty($font_direction)) {
    $font_direction = isset($marquee_default_options['font_direction']) ? $marquee_default_options['font_direction'] : '';
}
if (empty($scroll_delay)) {
    $scroll_delay = isset($marquee_default_options['scroll_delay']) ? $marquee_default_options['scroll_delay'] : '';
}
if (empty($show_marquee)) {
    $show_marquee = isset($marquee_default_options['show_marquee']) ? $marquee_default_options['show_marquee'] : '';
}
if (empty($marquee_shortcode)) {
    $marquee_shortcode = isset($marquee_default_options['marquee_shortcode']) ? $marquee_default_options['marquee_shortcode'] : '';
}
if (empty($animation_speed)) {
    $animation_speed = isset($marquee_default_options['animation_speed']) ? $marquee_default_options['animation_speed'] : '';
}
if (empty($marquee_height)) {
    $marquee_height = isset($marquee_default_options['marquee_height']) ? $marquee_default_options['marquee_height'] : '';
}
if (empty($marquee_width)) {
    $marquee_width = isset($marquee_default_options['marquee_width']) ? $marquee_default_options['marquee_width'] : '';
}
if (empty($font_family)) {
    $font_family = isset($marquee_default_options['font_family']) ? $marquee_default_options['font_family'] : '';
}
if (empty($marquee_spacing)) {
    $marquee_spacing = isset($marquee_default_options['marquee_spacing']) ? $marquee_default_options['marquee_spacing'] : '';
}
if (empty($circle_color)) {
    $circle_color = isset($marquee_default_options['circle_color']) ? $marquee_default_options['circle_color'] : '';
}
if (empty($circle_width)) {
    $circle_width = isset($marquee_default_options['circle_width']) ? $marquee_default_options['circle_width'] : '';
}
if (empty($circle_height)) {
    $circle_height = isset($marquee_default_options['circle_height']) ? $marquee_default_options['circle_height'] : '';
}
if (empty($marquee_border_radius)) {
    $marquee_border_radius = isset($marquee_default_options['marquee_border_radius']) ? $marquee_default_options['marquee_border_radius'] : '';
}
if (empty($marquee_border_color)) {
    $marquee_border_color = isset($marquee_default_options['marquee_border_color']) ? $marquee_default_options['marquee_border_color'] : '';
}
if (empty($marquee_border)) {
    $marquee_border = isset($marquee_default_options['marquee_border']) ? $marquee_default_options['marquee_border'] : '';
}
if (empty($pause_on_hover)) {
    $pause_on_hover = isset($marquee_default_options['pause_on_hover']) ? $marquee_default_options['pause_on_hover'] : '';
}
if (empty($marquee_degree)) {
    $marquee_degree = isset($marquee_default_options['marquee_degree']) ? $marquee_default_options['marquee_degree'] : '';
}




    
    ?>

   <!-- Background Color -->
<!-- Background Color -->

<!-- Background Color -->
<!-- Background Color -->
<link href="https://fonts.googleapis.com/css?family=Abel|Abril+Fatface|Acme|Alegreya|Alegreya+Sans|Anton|Archivo|Archivo+Black|Archivo+Narrow|Arimo|Arvo|Asap|Asap+Condensed|Bitter|Bowlby+One+SC|Bree+Serif|Cabin|Cairo|Catamaran|Crete+Round|Crimson+Text|Cuprum|Dancing+Script|Dosis|Droid+Sans|Droid+Serif|EB+Garamond|Exo|Exo+2|Faustina|Fira+Sans|Fjalla+One|Francois+One|Gloria+Hallelujah|Hind|Inconsolata|Indie+Flower|Josefin+Sans|Julee|Karla|Lato|Libre+Baskerville|Libre+Franklin|Lobster|Lora|Mada|Manuale|Maven+Pro|Merriweather|Merriweather+Sans|Montserrat|Montserrat+Subrayada|Mukta+Vaani|Muli|Noto+Sans|Noto+Serif|Nunito|Open+Sans|Open+Sans+Condensed:300|Oswald|Oxygen|PT+Sans|PT+Sans+Caption|PT+Sans+Narrow|PT+Serif|Pacifico|Passion+One|Pathway+Gothic+One|Play|Playfair+Display|Poppins|Questrial|Quicksand|Raleway|Roboto|Roboto+Condensed|Roboto+Mono|Roboto+Slab|Ropa+Sans|Rubik|Saira|Saira+Condensed|Saira+Extra+Condensed|Saira+Semi+Condensed|Sedgwick+Ave|Sedgwick+Ave+Display|Shadows+Into+Light|Signika|Slabo+27px|Source+Code+Pro|Source+Sans+Pro|Spectral|Titillium+Web|Ubuntu|Ubuntu+Condensed|Varela+Round|Vollkorn|Work+Sans|Yanone+Kaffeesatz|Zilla+Slab|Zilla+Slab+Highlight" rel="stylesheet">
<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Background Color -->
        <div class="mb-4">
            <label for="background_color" class="block text-gray-700 font-bold">Background Color:</label>
            <input type="text" id="background_color" class="color-field border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" name="background_color" value="<?php echo esc_attr($background_color); ?>">
        </div>

        <!-- Text Color -->
        <div class="mb-4">
            <label for="text_color" class="block text-gray-700 font-bold">Text Color:</label>
            <input type="text" id="text_color" class="color-field border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" name="text_color" value="<?php echo esc_attr($text_color); ?>">
        </div>

        <!-- Text Hover Color -->
        <div class="mb-4">
            <label for="text_hover_color" class="block text-gray-700 font-bold">Text Hover Color:</label>
            <input type="text" id="text_hover_color" class="color-field border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" name="text_hover_color" value="<?php echo esc_attr($text_hover_color); ?>">
        </div>

        <!-- Typography & Others -->
        <div class="mb-4">
            <label for="pause_on_hover" class="block text-gray-700 font-bold">Pause on Hover:</label>
            <input type="checkbox" id="pause_on_hover" name="pause_on_hover" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" <?php checked($pause_on_hover, 'yes'); ?>>
        </div>

        <!-- Marquee Height -->
        <div class="mb-4">
            <label for="marquee_height" class="block text-gray-700 font-bold">Marquee Height (px):</label>
            <input type="text" id="marquee_height" name="marquee_height" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo esc_attr($marquee_height); ?>">
        </div>

        <!-- Space Between Marquee Items -->
        <div class="mb-4">
            <label for="marquee_spacing" class="block text-gray-700 font-bold">Space Between Marquee Items (px):</label>
            <input type="number" id="marquee_spacing" name="marquee_spacing" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo esc_attr($marquee_spacing); ?>">
        </div>

        <!-- Marquee Border -->
        <div class="mb-4">
            <label for="marquee_border" class="block text-gray-700 font-bold">Marquee Border (px):</label>
            <input type="number" id="marquee_border" name="marquee_border" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo esc_attr($marquee_border); ?>">
        </div>

        <!-- Marquee Border Color -->
        <div class="mb-4">
            <label for="marquee_border_color" class="block text-gray-700 font-bold">Marquee Border Color:</label>
            <input type="text" id="marquee_border_color" class="color-field border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" name="marquee_border_color" value="<?php echo esc_attr($marquee_border_color); ?>">
        </div>

        <!-- Marquee Border Radius -->
        <div class="mb-4">
            <label for="marquee_border_radius" class="block text-gray-700 font-bold">Marquee Border Radius(px):</label>
            <input type="number" id="marquee_border_radius" name="marquee_border_radius" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo esc_attr($marquee_border_radius); ?>">
        </div>

        <!-- Marquee Width -->
        <div class="mb-4">
            <label for="marquee_width" class="block text-gray-700 font-bold">Marquee Width (%):</label>
            <input type="text" id="marquee_width" name="marquee_width" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo esc_attr($marquee_width); ?>">
        </div>

        <!-- Circle Color -->
        <div class="mb-4">
            <label for="circle_color" class="block text-gray-700 font-bold">Circle Color:</label>
            <input type="text" id="circle_color" class="color-field border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" name="circle_color" value="<?php echo esc_attr($circle_color); ?>">
        </div>

        <!-- Circle Width -->
        <div class="mb-4">
            <label for="circle_width" class="block text-gray-700 font-bold">Circle Width (px):</label>
            <input type="number" id="circle_width" name="circle_width" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo esc_attr($circle_width); ?>">
        </div>

        <!-- Circle Height -->
        <div class="mb-4">
            <label for="circle_height" class="block text-gray-700 font-bold">Circle Height (px):</label>
            <input type="number" id="circle_height" name="circle_height" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo esc_attr($circle_height); ?>">
        </div>

        <!-- Font Family -->
        <div class="mb-4">
            <label for="font_family" class="block text-gray-700 font-bold">Font Family:</label>
            <input type="text" id="font_family" name="font_family" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo esc_attr($font_family); ?>">
        </div>

        <!-- Font Size -->
        <div class="mb-4">
            <label for="font_size" class="block text-gray-700 font-bold">Font Size (px):</label>
            <input type="number" id="font_size" name="font_size" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo esc_attr($font_size); ?>">
        </div>

        <!-- Font Weight -->
        <div class="mb-4">
            <label for="font_weight" class="block text-gray-700 font-bold">Font Weight:</label>
            <input type="number" id="font_weight" name="font_weight" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo esc_attr($font_weight); ?>">
        </div>

        <!-- Font Direction -->
        <div class="mb-4">
            <label for="font_direction" class="block text-gray-700 font-bold">Font Direction:</label>
            <select id="font_direction" name="font_direction" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out">
                <option value="right" <?php selected($font_direction, 'right'); ?>>Right</option>
                <option value="left" <?php selected($font_direction, 'left'); ?>>Left</option>
            </select>
        </div>

        <!-- Scroll Delay -->
        <div class="mb-4">
            <label for="scroll_delay" class="block text-gray-700 font-bold">Scroll Delay:</label>
            <input type="number" id="scroll_delay" name="scroll_delay" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo esc_attr($scroll_delay); ?>">
        </div>

        <!-- Animation Speed -->
        <div class="mb-4">
            <label for="animation_speed" class="block text-gray-700 font-bold">Animation speed:</label>
            <input type="number" id="animation_speed" name="animation_speed" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo esc_attr($animation_speed); ?>">
        </div>

        <!-- Rotate Marquee (degree) -->
        <div class="mb-4">
            <label for="marquee_degree" class="block text-gray-700 font-bold">Rotate Marquee (degree):</label>
            <input type="number" id="marquee_degree" name="marquee_degree" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 transition duration-300 ease-in-out" value="<?php echo esc_attr($marquee_degree); ?>" max="360">
        </div>

        <!-- Show Marquee in these places -->
        <div class="mb-4">
            <label class="block text-gray-700 font-bold">Show Marquee in these places:</label>
            <div class="flex items-center space-x-4">
                <!-- Add your checkbox inputs here -->
				 <input type="checkbox" id="show_marquee_hide" name="show_marquee[]" value="hide" <?php checked(in_array('hide', $show_marquee_values)); ?>>
    <label for="show_marquee_hide" class="text-sm text-gray-600">Hide (deactivate marquee)</label>

    <input type="checkbox" id="show_marquee_before_header" name="show_marquee[]" value="before_header" <?php checked(in_array('before_header', $show_marquee_values)); ?>>
    <label for="show_marquee_before_header" class="text-sm text-gray-600">Before Header</label>


    <input type="checkbox" id="show_marquee_before_footer" name="show_marquee[]" value="before_footer" <?php checked(in_array('before_footer', $show_marquee_values)); ?>>
    <label for="show_marquee_before_footer" class="text-sm text-gray-600">After Footer</label>

    
    <input type="checkbox" id="show_marquee_inside_comments" name="show_marquee[]" value="inside_comments" <?php checked(in_array('inside_comments', $show_marquee_values)); ?>>
    <label for="show_marquee_inside_comments" class="text-sm text-gray-600">Inside Comments</label>
            </div>
        </div>

        <!-- Marquee Shortcode -->
        <table class="form-table">
            <tr>
                <th scope="row"><label for="marquee_shortcode"><?php print esc_html(
                    "Marquee Shortcode"
                ); ?></label></th>
                <td>
                    <input type="text" id="marquee_shortcode" name="marquee_shortcode" readonly value="[marquee id=&quot;<?php echo esc_attr(get_the_ID()); ?>&quot;]">
                    <button type="button" id="copy_shortcode_button" class="button">Copy Shortcode</button>
                </td>
            </tr>
        </table>
    </div>
</div>



    <?php
}

// Save meta box data
function save_marquee_style_meta_data($post_id) {
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // Check nonce for security
    if (!isset($_POST['marquee_style_meta_box_nonce']) || !wp_verify_nonce($_POST['marquee_style_meta_box_nonce'], 'marquee_style_nonce')) return;

    // Check user's permissions
    if (!current_user_can('edit_post', $post_id)) return;

    // Save background color
    if (isset($_POST['background_color'])) {
        update_post_meta($post_id, 'background_color', sanitize_hex_color($_POST['background_color']));
    }

    // Save text color
    if (isset($_POST['text_color'])) {
        update_post_meta($post_id, 'text_color', sanitize_hex_color($_POST['text_color']));
    }

 // Save text color
    if (isset($_POST['circle_color'])) {
        update_post_meta($post_id, 'circle_color', sanitize_hex_color($_POST['circle_color']));
    }
    
     if (isset($_POST['circle_width'])) {
        update_post_meta($post_id, 'circle_width', sanitize_text_field($_POST['circle_width']));
    }
     if (isset($_POST['circle_height'])) {
        update_post_meta($post_id, 'circle_height', sanitize_text_field($_POST['circle_height']));
    }
    // Save text hover color
    if (isset($_POST['text_hover_color'])) {
        update_post_meta($post_id, 'text_hover_color', sanitize_hex_color($_POST['text_hover_color']));
    }
    
    
    if (isset($_POST['marquee_border_color'])) {
        update_post_meta($post_id, 'marquee_border_color', sanitize_hex_color($_POST['marquee_border_color']));
    }

    // Save font size
    if (isset($_POST['font_size'])) {
        update_post_meta($post_id, 'font_size', sanitize_text_field($_POST['font_size']));
    }
    
	//marquee regree
	 if (isset($_POST['marquee_degree'])) {
        update_post_meta($post_id, 'marquee_degree', sanitize_text_field($_POST['marquee_degree']));
    }
	
  // Save pause on hover
$pause_on_hover = isset($_POST['pause_on_hover']) ? 'yes' : 'no';
update_post_meta($post_id, 'pause_on_hover', $pause_on_hover);

    
      if (isset($_POST['marquee_border'])) {
        update_post_meta($post_id, 'marquee_border', sanitize_text_field($_POST['marquee_border']));
    }
     if (isset($_POST['marquee_border_radius'])) {
        update_post_meta($post_id, 'marquee_border_radius', sanitize_text_field($_POST['marquee_border_radius']));
    }
    
    
    if (isset($_POST['circle_width'])) {
        update_post_meta($post_id, 'circle_width', sanitize_text_field($_POST['circle_width']));
    }
    
    if (isset($_POST['circle_height'])) {
        update_post_meta($post_id, 'circle_height', sanitize_text_field($_POST['circle_height']));
    }

    

    // Save font weight
    if (isset($_POST['font_weight'])) {
       // Save font weight
$font_weight = isset($_POST['font_weight']) ? sanitize_text_field($_POST['font_weight']) : '';
update_post_meta($post_id, 'font_weight', $font_weight);
    }

    // Save font direction
    if (isset($_POST['font_direction'])) {
        update_post_meta($post_id, 'font_direction', sanitize_text_field($_POST['font_direction']));
    }

    // Save scroll delay
    if (isset($_POST['scroll_delay'])) {
        update_post_meta($post_id, 'scroll_delay', sanitize_text_field($_POST['scroll_delay']));
    }

    // Save show marquee
// Save show marquee
$show_marquee_values = isset($_POST['show_marquee']) ? $_POST['show_marquee'] : array();
$show_marquee = implode(',', $show_marquee_values); // Combine all values into a comma-separated string
update_post_meta($post_id, 'show_marquee', $show_marquee); // Save the show_marquee meta value

// Retrieve saved values
$show_marquee_values = explode(',', $show_marquee); // Split the stored values back into an array

// Check if each option is checked
$show_marquee_hide_checked = in_array('hide', $show_marquee_values) ? 'checked' : '';
$show_marquee_before_header_checked = in_array('before_header', $show_marquee_values) ? 'checked' : '';
$show_marquee_after_header_checked = in_array('after_header', $show_marquee_values) ? 'checked' : '';
$show_marquee_before_footer_checked = in_array('before_footer', $show_marquee_values) ? 'checked' : '';
$show_marquee_after_footer_checked = in_array('after_footer', $show_marquee_values) ? 'checked' : '';
$show_marquee_inside_comments_checked = in_array('inside_comments', $show_marquee_values) ? 'checked' : '';
$show_marquee_inside_posts_checked = in_array('inside_posts', $show_marquee_values) ? 'checked' : '';





// Save animation speed
$animation_speed = isset($_POST['animation_speed']) ? sanitize_text_field($_POST['animation_speed']) : '';
update_post_meta($post_id, 'animation_speed', $animation_speed);



// Save marquee height
    if (isset($_POST['marquee_height'])) {
        update_post_meta($post_id, 'marquee_height', sanitize_text_field($_POST['marquee_height']));
    }

    // Save marquee width
    if (isset($_POST['marquee_width'])) {
        update_post_meta($post_id, 'marquee_width', sanitize_text_field($_POST['marquee_width']));
    }

    // Save marquee shortcode
    if (isset($_POST['marquee_shortcode'])) {
        update_post_meta($post_id, 'marquee_shortcode', sanitize_text_field($_POST['marquee_shortcode']));
    }
     if (isset($_POST['font_family'])) {
        update_post_meta($post_id, 'font_family', sanitize_text_field($_POST['font_family']));
    }
    if (isset($_POST['marquee_spacing'])) {
        update_post_meta($post_id, 'marquee_spacing', sanitize_text_field($_POST['marquee_spacing']));
    }

}
add_action('save_post', 'save_marquee_style_meta_data');

//////////////////registering a custom gloabl Unlimited marquee widget
function unlimited_marquee_widget($id) {
    // Get meta values
    $background_color = get_post_meta($id, 'background_color', true);
    $text_color = get_post_meta($id, 'text_color', true);
    $font_size = get_post_meta($id, 'font_size', true);
    $font_weight = get_post_meta($id, 'font_weight', true);
    $font_direction = get_post_meta($id, 'font_direction', true);
    $scroll_delay = get_post_meta($id, 'scroll_delay', true);
    $show_marquee = get_post_meta($id, 'show_marquee', true);
    $animation_speed = get_post_meta($id, 'animation_speed', true);
    $marquee_height = get_post_meta($id, 'marquee_height', true);
    $marquee_width = get_post_meta($id, 'marquee_width', true);
    $font_family = get_post_meta($id, 'font_family', true);
    $text_areas = get_post_meta($id, 'text_areas', true);
    $text_links = get_post_meta($id, 'text_links', true);
    $marquee_spacing = get_post_meta($id, 'marquee_spacing', true);
    $text_hover_color = get_post_meta($id, 'text_hover_color', true);
    $circle_color = get_post_meta($id, 'circle_color', true);
    $circle_width = get_post_meta($id, 'circle_width', true);
    $circle_height = get_post_meta($id, 'circle_height', true);
    $marquee_border = get_post_meta($id, 'marquee_border', true);
    $marquee_border_color = get_post_meta($id, 'marquee_border_color', true);
    $marquee_border_radius = get_post_meta($id, 'marquee_border_radius', true);
    $scroll_delay = get_post_meta($id, 'scroll_delay', true);
    $link_targets = get_post_meta($id, 'link_targets', true);
    $pause_on_hover = get_post_meta($id, 'pause_on_hover', true);
    $marquee_degree = get_post_meta($id, 'marquee_degree', true);
    $marquee_id = $id;

    // Prepare style attribute
    $style = "background-color: $background_color; color: $text_color; font-size: {$font_size}px; font-weight: $font_weight; direction: $font_direction; font-family: $font_family; transform: rotate({$marquee_degree}deg);";

    // Check if marquee should be shown
    if ($show_marquee == 'hide') {
        $output = '<div class="marquee-content-' . $marquee_id . '" style="display: none;">';
    } else {
        $output = '<div class="marquee-content-' . $marquee_id . '" style="display: block; width:' . $marquee_width . '%;">';
    }

    // Generate marquee content
    $output .= '<marquee id="myMarquee" class="marquee-text-' . $marquee_id . '" behavior="scroll" scrolldelay="' . $scroll_delay . '" direction="' . $font_direction . '" scrollamount="' . $animation_speed . '" style="' . $style . '" height="' . $marquee_height . 'px" width="' . $marquee_width . '%">';
    $link_style = "background-color: $background_color; color: $text_color; font-size: {$font_size}px; font-weight: $font_weight; direction: $font_direction; font-family: $font_family";

    // Loop through text areas and add to marquee
    if (!empty($text_areas) && is_array($text_areas)) {
        foreach ($text_areas as $index => $text_area) {
            if (is_array($text_links) && isset($text_links[$index])) {
                $link = esc_url($text_links[$index]);
            } else {
                $link = '';
            }
            if (is_array($link_targets)) {
                $target = implode(' ', $link_targets);
            } else {
                $target = esc_attr($link_targets);
            }
            
           $output .= '<link href="https://fonts.googleapis.com/css?family=Abel|Abril+Fatface|Acme|Alegreya|Alegreya+Sans|Anton|Archivo|Archivo+Black|Archivo+Narrow|Arimo|Arvo|Asap|Asap+Condensed|Bitter|Bowlby+One+SC|Bree+Serif|Cabin|Cairo|Catamaran|Crete+Round|Crimson+Text|Cuprum|Dancing+Script|Dosis|Droid+Sans|Droid+Serif|EB+Garamond|Exo|Exo+2|Faustina|Fira+Sans|Fjalla+One|Francois+One|Gloria+Hallelujah|Hind|Inconsolata|Indie+Flower|Josefin+Sans|Julee|Karla|Lato|Libre+Baskerville|Libre+Franklin|Lobster|Lora|Mada|Manuale|Maven+Pro|Merriweather|Merriweather+Sans|Montserrat|Montserrat+Subrayada|Mukta+Vaani|Muli|Noto+Sans|Noto+Serif|Nunito|Open+Sans|Open+Sans+Condensed:300|Oswald|Oxygen|PT+Sans|PT+Sans+Caption|PT+Sans+Narrow|PT+Serif|Pacifico|Passion+One|Pathway+Gothic+One|Play|Playfair+Display|Poppins|Questrial|Quicksand|Raleway|Roboto|Roboto+Condensed|Roboto+Mono|Roboto+Slab|Ropa+Sans|Rubik|Saira|Saira+Condensed|Saira+Extra+Condensed|Saira+Semi+Condensed|Sedgwick+Ave|Sedgwick+Ave+Display|Shadows+Into+Light|Signika|Slabo+27px|Source+Code+Pro|Source+Sans+Pro|Spectral|Titillium+Web|Ubuntu|Ubuntu+Condensed|Varela+Round|Vollkorn|Work+Sans|Yanone+Kaffeesatz|Zilla+Slab|Zilla+Slab+Highlight" rel="stylesheet">';
            $output .= '<a href="' . $link . '" style="' . $link_style . '" target="' . $target . '">' . esc_html($text_area) . '</a>';

            $output .= '<span class="marquee-circle" style="background-color:' . $circle_color . '; width:' . $circle_width . 'px; height:' . $circle_height . 'px;"></span>';
            // Check if the pause on hover option is set to 'yes'
if ($pause_on_hover === 'yes') {
    $output .= '<script>
document.querySelectorAll(".marquee-content-' . $marquee_id . ' marquee").forEach(function(marquee) {
    marquee.addEventListener("mouseenter", function() {
        this.stop();
    });

    marquee.addEventListener("mouseleave", function() {
        this.start();
    });
});
</script>';

}

        }
    }

    $output .= '</marquee></div>';

    // Close marquee and main-runtext
    $output .= '<style>
        .marquee-text-' . $marquee_id . ' {
            align-items: center;
            
            display: flex;
            border:solid ' . $marquee_border . 'px' . $marquee_border_color . ';
            border-radius:' . $marquee_border_radius . 'px;
        }
        .marquee-content-' . $marquee_id . ' a {
            text-decoration: none !important;
        }
        .marquee-content-' . $marquee_id . ' a {
            display: inline-block;
            margin-right: ' . $marquee_spacing . 'px;
        }
        .marquee-text-' . $marquee_id . ' a:hover {
            color: ' . $text_hover_color . ' !important;
        }
        .marquee-content-' . $marquee_id . ' .marquee-text-' . $marquee_id . ' a:hover {
            color: ' . $text_hover_color . ' !important;
        }
        .marquee-content-' . $marquee_id . ' .marquee-text-' . $marquee_id . ' a::before {
            content: "";
            display: inline-block;
            width:' . $circle_width . 'px; /* Diameter of the circle */
            height:' . $circle_height . 'px; /* Diameter of the circle */
            background-color:' . $circle_color . '; /* Color of the circle */
            border-radius: 50%; /* Makes the shape circular */
            margin-right: 5px; /* Adjust spacing between circle and text */
        }
    </style>';
         
    return $output;
}

/// Shortcode function for Marquee
function marquee_shortcode($atts) {
    // Shortcode attributes
    $atts = shortcode_atts(array(
        'id' => '',
    ), $atts);

    // Extract shortcode attributes
    extract($atts);

    // Initialize output variable
    $output = '';

    // Check if ID is provided
    if (!empty($id)) {
        // Check the post status
        $post_status = get_post_status($id);
        if ($post_status === 'publish') { // Only process if the post is published
            // Call the custom HTML widget function
            $output = unlimited_marquee_widget($id);
        }
    }

    return $output;
}
add_shortcode('marquee', 'marquee_shortcode');



// Enqueue block assets

function custom_before_header_text() {
    // Get all marquee posts
    $args = array(
        'post_type' => 'marquee',
        'posts_per_page' => -1,
    );
    $marquee_posts = new WP_Query($args);

    if ($marquee_posts->have_posts()) {
        while ($marquee_posts->have_posts()) {
            $marquee_posts->the_post();

            // Check if any marquee post has "before_header" option
            $show_marquee_values = get_post_meta(get_the_ID(), 'show_marquee', true);
            $show_marquee_values = explode(',', $show_marquee_values);

            if (in_array('before_header', $show_marquee_values) && !in_array('hide', $show_marquee_values)) {
                // Generate shortcode for this post ID
                $shortcode = '[marquee id="' . get_the_ID() . '"]';
                // Output the shortcode where needed
                echo do_shortcode($shortcode);
            }
        }
        wp_reset_postdata();
    }
}

add_action('wp_head', 'custom_before_header_text');




function custom_before_footer_text() {
    // Get all marquee posts
    $args = array(
        'post_type' => 'marquee',
        'posts_per_page' => -1,
    );
    $marquee_posts = new WP_Query($args);

    if ($marquee_posts->have_posts()) {
        while ($marquee_posts->have_posts()) {
            $marquee_posts->the_post();

            // Check if any marquee post has "before_header" option
            $show_marquee_values = get_post_meta(get_the_ID(), 'show_marquee', true);
            $show_marquee_values = explode(',', $show_marquee_values);

            if (in_array('before_footer', $show_marquee_values) && !in_array('hide', $show_marquee_values)) {
                // Generate shortcode for this post ID
                $shortcode = '[marquee id="' . get_the_ID() . '"]';
                // Output the shortcode where needed
                echo do_shortcode($shortcode);
            }
        }
        wp_reset_postdata();
    }
}

add_action('wp_footer', 'custom_before_footer_text');


function custom_before_comment_text() {
    // Get all marquee posts
    $args = array(
        'post_type' => 'marquee',
        'posts_per_page' => -1,
    );
    $marquee_posts = new WP_Query($args);

    if ($marquee_posts->have_posts()) {
        while ($marquee_posts->have_posts()) {
            $marquee_posts->the_post();

            // Check if any marquee post has "before_header" option
            $show_marquee_values = get_post_meta(get_the_ID(), 'show_marquee', true);
            $show_marquee_values = explode(',', $show_marquee_values);

            if (in_array('inside_comments', $show_marquee_values) && !in_array('hide', $show_marquee_values)) {
                // Generate shortcode for this post ID
                $shortcode = '[marquee id="' . get_the_ID() . '"]';
                // Output the shortcode where needed
                echo do_shortcode($shortcode);
            }
        }
        wp_reset_postdata();
    }
}

add_action('comment_form_before', 'custom_before_comment_text');
