<?php
/** Plugin Name: Manage Upcoming Release
* Author: Olaf Parusel
* Description: Manage upcoming releases for your Site with Custom Post Type and display them easily via Shortcode.
* Version: 1.0.0
*/
// Register Custom Post Type Release
add_action( 'init', 'mur_custom_post_type_release' );
function mur_custom_post_type_release() {
	$labels = array(
		"name" => "Release",
		"singular_name" => "Release",
		"menu_name" => "Release",
		"all_items" => "All releases",
		"add_new" => "New release",
		"add_new_item" => "Add release",
		"edit" => "edit",
		"edit_item" => "Edit release",
		"new_item" => "New release",
		"view" => "view",
		"view_item" => "View release",
		"search_items" => "Search release",
		"not_found" => "Release not found",
		);

	$args = array(
		"labels" => $labels,
		"description" => "Manage Release Dates",
		"public" => true,
		"show_ui" => true,
		"has_archive" => false,
		"show_in_menu" => true,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array( "slug" => "release", "with_front" => true ),
		"query_var" => true,
		"menu_icon" => "dashicons-format-aside",		
		"supports" => array( "title", "thumbnail", "editor" ),			
		);
	register_post_type( "Release", $args );
}

require_once('metabox-datepicker.php');

if (class_exists('metabox_datepicker')){
  $event_start_date = new metabox_datepicker('release', 'release_date_field', 'Release Date', "Release Date");
}

function mur_stylesheet_register() {
	wp_register_style( 'mur', plugins_url( 'manage-upcoming-release/mur-plugin.css' ) );
	wp_enqueue_style( 'mur' );
}
add_action( 'wp_enqueue_scripts', 'mur_stylesheet_register' );


// Manage upcoming releases Shortcode
function mur_shortcode( $atts , $content = null , $post ) {
global $post;
$today = date('Ymd');
$release_date = get_post_meta( get_the_ID(), 'release_date_field', true );
	// Attributes
	extract( shortcode_atts(
		array(
			'posts' => '',
			'thumb' => '1',
			'thumb_width' => '200',
			'thumb_height' => '200',
		), $atts )
	);

	// Code
	$output .= '<div class="mur-wrapper">';
	$output .= '<table class="table table-striped">';
	
		$the_query = new WP_Query( array( 'post_type' => 'release', 'posts_per_page' => $posts, 'order' => 'ASC', 'orderby' => 'meta_value_num', 'meta_key' => 'release_date_field', 'meta_query' => array( array( 'key' => 'release_date_field', 'value' => $today, 'compare' => '>=', 'type' => 'DATE',)), ));
		while ( $the_query->have_posts() ):
			$the_query->the_post();
				$output .= '<tr>';
				$output .= 		'<td>';
				$output .= 			'<h4 class="mur-heading"><a href="' . get_the_permalink() .'">' . get_the_title() . '</a></h4>';
				$output .= 		'</td>';
				$output .= 		'<td>';
									$date = DateTime::createFromFormat('Ymd', get_post_meta( $post->ID, 'release_date_field', true ) );
				$output .= 			$date->format('d. F \'y');	
				$output .= 		'</td>';
			if ($thumb == '1') {
				$output .= 		'<td>';
				$output .=			get_the_post_thumbnail(get_the_ID(), array($thumb_width, $thumb_height), array( 'class' => 'center-block') );
				$output .= 		'</td>';
			}
			else {
				}
				$output .= '</tr>';
		endwhile;
		wp_reset_postdata();
	
	$output .= '</table>';
	$output .= '</div>';
	
	return $output;
}
add_shortcode( 'mur', 'mur_shortcode' );
add_filter('widget_text', 'do_shortcode');

// Load Template file for release Custom Post Type
function mur_template_cpt($content)
{
	global $post;
    if (is_singular('release')) {
        $content .= '<div class="mur-wrapper">';
		$content .= '<table class="table">';
		$content .= 	'<tr>';
		$content .= 		'<td>';
		$content .= 			'<h4 class="mur-heading">' . get_the_title() . '</h4>';
		$content .= 		'</td>';
		$content .= 		'<td>';
							$date = DateTime::createFromFormat('Ymd', get_post_meta( $post->ID, 'release_date_field', true ) );
		$content .= 		$date->format('d. F \'y');
		$content .= 		'</td>';
		$content .= 		'<td>';
		$content .=			get_the_post_thumbnail(get_the_ID(), 'thumbnail', array( 'class' => 'center-block') );
		$content .= 		'</td>';
		$content .= 	'</tr>';
		$content .= '</table>';
		$content .= '</div>';
    }

    return $content;
}
add_filter('the_content', 'mur_template_cpt');

// Add Quicktag to  HTML Editor
function mur_quicktag() {

	if ( wp_script_is( 'quicktags' ) ) {
	?>
	<script type="text/javascript">
	QTags.addButton( 'mur_shortcode', 'mur_shortcode', '[mur posts="-1" thumb="1" thumb_width="" thumb_height=""]', '', 'b', 'Manage Upcoming Release Shortcode', 1 );
	</script>
	<?php
	}
}
add_action( 'admin_print_footer_scripts', 'mur_quicktag' );
?>