<?php
/**
 * Plugin Name: Upcoming Events C.C. 't Aogje
 * Plugin URI: http://boldfocus.nl
 * Description: A plugin to show a list of upcoming events on the front-end.
 * Version: 1.0.1
 * Author: Richard van Ham
 * Author URI: http://boldfocus.nl
 */

/* Add jquery-ui-tdatepicker to the plugin*/
function uep_load_jquery_datepicker() {    
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
}
add_action( 'admin_enqueue_scripts', 'uep_load_jquery_datepicker' );


// function uep_admin_styles() {
       /*
        * It will be called only on your plugin admin page, enqueue our stylesheet here
        */
       // wp_enqueue_style( 'myPluginStylesheet' );
   // }

/**
 * Defining constants for later use
 */
define( 'ROOT', plugins_url( '', __FILE__ ) );
define( 'IMAGES', ROOT . '/img/' );
define( 'STYLES', ROOT . '/css/' );
define( 'SCRIPTS', ROOT . '/js/' );



/**
 * Registering custom post type for events
 */
function uep_custom_post_type() {
	$labels = array(
		'name'					=>	__( 'Events', 'uep' ),
		'singular_name'			=>	__( 'Event', 'uep' ),
		'add_new_item'			=>	__( 'Add New Event', 'uep' ),
		'all_items'				=>	__( 'All Events', 'uep' ),
		'edit_item'				=>	__( 'Edit Event', 'uep' ),
		'new_item'				=>	__( 'New Event', 'uep' ),
		'view_item'				=>	__( 'View Event', 'uep' ),
		'not_found'				=>	__( 'No Events Found', 'uep' ),
		'not_found_in_trash'	=>	__( 'No Events Found in Trash', 'uep' )
	);

	$supports = array(
		'title',
		'editor',
		'excerpt'
	);

	$args = array(
		'label'			=>	__( 'Events', 'uep' ),
		'labels'		=>	$labels,
		'description'	=>	__( 'A list of upcoming events', 'uep' ),
		'public'		=>	true,
		'show_in_menu'	=>	true,
		'menu_icon'		=>	IMAGES . 'event.svg',
		'has_archive'	=>	true,
		'rewrite'		=>	true,
		'supports'		=>	$supports
	);

	register_post_type( 'event', $args );
}
add_action( 'init', 'uep_custom_post_type' );


/**
 * Flushing rewrite rules on plugin activation/deactivation
 * for better working of permalink structure
 */
function uep_activation_deactivation() {
	uep_custom_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'uep_activation_deactivation' );



/**
 * Adding metabox for event information
 */
function uep_add_event_info_metabox() {
	add_meta_box(
		'uep-event-info-metabox',
		__( 'Event Info', 'uep' ),
		'uep_render_event_info_metabox',
		'event',
		'side',
		'core'
	);
}
add_action( 'add_meta_boxes', 'uep_add_event_info_metabox' );


/**
 * Rendering the metabox for event information
 * @param  object $post The post object
 */
function uep_render_event_info_metabox( $post ) {
	//generate a nonce field
	wp_nonce_field( basename( __FILE__ ), 'uep-event-info-nonce' );

	//get previously saved meta values (if any)
	$event_start_date = get_post_meta( $post->ID, 'event-start-date', true );
	$event_start_time = get_post_meta( $post->ID, 'event-start-time', true );
	$event_end_date = get_post_meta( $post->ID, 'event-end-date', true );
	$event_end_time = get_post_meta( $post->ID, 'event-end-time', true );
	$event_venue = get_post_meta( $post->ID, 'event-venue', true );

	//if there is previously saved value then retrieve it, else set it to the current time
	$event_start_date = ! empty( $event_start_date ) ? $event_start_date : time();
	// $event_start_time = ! empty( $event_start_time ) ? $event_start_time : time();

	//we assume that if the end date is not present, event ends on the same day
	$event_end_date = ! empty( $event_end_date ) ? $event_end_date : $event_start_date;

	?>
	<p> 
		<label for="uep-event-start-date"><?php _e( 'Event Start Date:', 'uep' ); ?></label>
		<input type="text" id="uep-event-start-date" name="uep-event-start-date" class="widefat uep-event-date-input" value="<?php echo date( 'F d, Y', $event_start_date ); ?>" placeholder="Format: February 18, 2014">
	</p>
	<p> 
		<label for="uep-event-start-time"><?php _e( 'Event start time:', 'uep' ); ?></label>
		<input type="text" id="uep-event-start-time" name="uep-event-start-time" class="widefat time" value="<?php echo date( 'H, i', $event_start_time ); ?>" placeholder="11:11">
	</p>
	<p>
		<label for="uep-event-end-date"><?php _e( 'Event End Date:', 'uep' ); ?></label>
		<input type="text" id="uep-event-end-date" name="uep-event-end-date" class="widefat uep-event-date-input" value="<?php echo date( 'F d, Y', $event_end_date ); ?>" placeholder="Format: February 18, 2014">
	</p>
	<p> 
		<label for="uep-event-end-time"><?php _e( 'Event end time:', 'uep' ); ?></label>
		<input type="text" id="uep-event-end-time" name="uep-event-end-time" class="widefat time" value="<?php echo date( 'H, i', $event_end_time ); ?>" placeholder="11:11">
	</p>
	<p>
		<label for="uep-event-venue"><?php _e( 'Event Venue:', 'uep' ); ?></label>
		<input type="text" id="uep-event-venue" name="uep-event-venue" class="widefat" value="<?php echo $event_venue; ?>" placeholder="eg. Times Square">
	</p>

	<?php
}


// add meta box for integration google maps

// add meta box to post type
function post_map() {
    add_meta_box( 
        'post_map_tab',
        __( 'Map', 'post_textdomain' ),
        'map_tab_box',
        'event' 
    );
}
add_action( 'add_meta_boxes', 'post_map' );


// print box content
function map_tab_box( $post ) {
    // Use nonce for verification
    // wp_nonce_field( plugin_basename( __FILE__ ), 'post_map_noncename' );
    // The actual fields for data entry
    ?>
    <p>
    	Voeg je adres toe<br />(This is to GeoCode Map the location using Google)
	</p>
        <p>
        	<label>Please enter country and update to preview Google Map</label>
        	<input name="geoecode_country" type="text" value="<?php echo get_post_meta($post->ID, 'geoecode_country', true); ?>" size="30" /></p>
        <div id="mapcanvas" sty></div>
    <?php
}


    function print_google_map_script() {

        // we could load conditionally by page if we want here
        global $post;
            $geoecode_country = get_post_meta($post->ID, 'geoecode_country', true);
        ?>

        <script type='text/javascript'>

              var geocoder;
              var map;
              var query = '<?php echo $geoecode_country; ?>';
              function initialize() {
                geocoder = new google.maps.Geocoder();
                var mapOptions = {
                  zoom:3,
                  mapTypeId: google.maps.MapTypeId.ROADMAP
                }
                map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
                codeAddress();
              }

              function codeAddress() {
                var address = query;
                geocoder.geocode( { 'address': address}, function(results, status) {
                  if (status == google.maps.GeocoderStatus.OK) {
                    map.setCenter(results[0].geometry.location);
                    var marker = new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location
                    });
                  } else {
                    alert('Geocode was not successful for the following reason: ' + status);
                  }
                });
              }

              function loadScript() {
                var script = document.createElement("script");
                script.type = "text/javascript";
                script.src = "http://maps.googleapis.com/maps/api/js?key=YOUR_API&sensor=false&callback=initialize";
                document.body.appendChild(script);
                }

              window.onload = loadScript;   

        </script>
        <?php

    }

     add_action('admin_head', 'print_google_map_script');



     



function my_init() {
	if (!is_admin()) {
		// comment out the next two lines to load the local copy of jQuery
		// wp_deregister_script('jquery'); 
		// wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js', false, '1.3.2'); 
		// wp_enqueue_script('jquery');
	}
}
add_action('init', 'my_init');


/**
 * Enqueueing scripts and styles in the admin
 * @param  int $hook Current page hook
 */
function uep_admin_script_style( $hook ) {
	global $post_type;

	if ( ( 'post.php' == $hook || 'post-new.php' == $hook ) && ( 'event' == $post_type ) ) {
		wp_enqueue_script(
			'upcoming-events',
			SCRIPTS . 'script.js',
			array( 'jquery', 'jquery-ui-datepicker' ),
			'1.0',
			true
		);

		// wp_enqueue_script( 'my_custom_script', SCRIPTS . 'jquery.timepicker.min.js' );
		wp_enqueue_script(
			'upcoming-events-time',
			SCRIPTS . 'jquery.timepicker.min.js'
		);

		wp_enqueue_style(
			'jquery-ui-calendar',
			STYLES . 'jquery-ui-1.10.4.custom.min.css',
			false,
			'1.10.4',
			'all'
		);
		wp_enqueue_style(
			'upcoming-events',
			STYLES . 'jquery.timepicker.css',
			false,
			'1.0',
			'all'
		);
	}
}
add_action( 'admin_enqueue_scripts', 'uep_admin_script_style' );




/**
 * Saving the event along with its meta values
 * @param  int $post_id The id of the current post
 */
function uep_save_event_info( $post_id ) {
	//checking if the post being saved is an 'event',
	//if not, then return
	if ( 'event' != $_POST['post_type'] ) {
		return;
	}

	//checking for the 'save' status
	$is_autosave = wp_is_post_autosave( $post_id );
	$is_revision = wp_is_post_revision( $post_id );
	$is_valid_nonce = ( isset( $_POST['uep-event-info-nonce'] ) && ( wp_verify_nonce( $_POST['uep-event-info-nonce'], basename( __FILE__ ) ) ) ) ? true : false;

	//exit depending on the save status or if the nonce is not valid
	if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
		return;
	}

	//checking for the values and performing necessary actions
	if ( isset( $_POST['uep-event-start-date'] ) ) {
		update_post_meta( $post_id, 'event-start-date', strtotime( $_POST['uep-event-start-date'] ) );
	}
	if ( isset( $_POST['uep-event-start-time'] ) ) {
		update_post_meta( $post_id, 'event-start-time', strtotime( $_POST['uep-event-start-time'] ) );
	}

	if ( isset( $_POST['uep-event-end-date'] ) ) {
		update_post_meta( $post_id, 'event-end-date', strtotime( $_POST['uep-event-end-date'] ) );
	}
	if ( isset( $_POST['uep-event-end-time'] ) ) {
		update_post_meta( $post_id, 'event-end-time', strtotime( $_POST['uep-event-end-time'] ) );
	}

	if ( isset( $_POST['uep-event-venue'] ) ) {
		update_post_meta( $post_id, 'event-venue', sanitize_text_field( $_POST['uep-event-venue'] ) );
	}
}
add_action( 'save_post', 'uep_save_event_info' );


/**
 * Custom columns head
 * @param  array $defaults The default columns in the post admin
 */
function uep_custom_columns_head( $defaults ) {
	unset( $defaults['date'] );

	$defaults['event_start_date'] = __( 'Start Date', 'uep' );
	$defaults['event_start_time'] = __( 'Start Time', 'uep' );
	$defaults['event_end_date'] = __( 'End Date', 'uep' );
	$defaults['event_venue'] = __( 'Venue', 'uep' );

	return $defaults;
}
add_filter( 'manage_edit-event_columns', 'uep_custom_columns_head', 10 );

/**
 * Custom columns content
 * @param  string 	$column_name The name of the current column
 * @param  int 		$post_id     The id of the current post
 */
function uep_custom_columns_content( $column_name, $post_id ) {
	if ( 'event_start_date' == $column_name ) {
		$start_date = get_post_meta( $post_id, 'event-start-date', true );
		echo date( 'F d, Y', $start_date );
	}

	if ( 'event_start_time' == $column_name ) {
		$start_time = get_post_meta( $post_id, 'event-start-time', true );
		echo date( 'H:i', $start_time );
	}

	if ( 'event_end_date' == $column_name ) {
		$end_date = get_post_meta( $post_id, 'event-end-date', true );
		echo date( 'F d, Y', $end_date );
	}

	if ( 'event_venue' == $column_name ) {
		$venue = get_post_meta( $post_id, 'event-venue', true );
		echo $venue;
	}
}
add_action( 'manage_event_posts_custom_column', 'uep_custom_columns_content', 10, 2 );


/**
 * Including the theme template page
 */


//Template fallback
add_action("template_redirect", 'my_theme_redirect');

function my_theme_redirect() {
    global $wp;
    $plugindir = dirname( __FILE__ );

    //A Specific Custom Post Type
    if ($wp->query_vars["post_type"] == 'event') {
        $templatefilename = 'single-event.php';
        if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
            $return_template = TEMPLATEPATH . '/' . $templatefilename;
        } else {
            $return_template = $plugindir . '/templates/' . $templatefilename;
        }
        do_theme_redirect($return_template);
    }
}

function do_theme_redirect($url) {
    global $post, $wp_query;
    if (have_posts()) {
        include($url);
        die();
    } else {
        $wp_query->is_404 = true;
    }
}