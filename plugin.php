<?php
/**
 * Callout Boxes Plugin
 *
 * @package    callout-boxes
 * @author     David Skinner <djbskinner@icloud.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Plugin Name: Callout Boxes
 * Description: Use responsives callout boxes with shortcodes.
 * Version: 2.1.0
 * Author: David Skinner
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

/*  Copyright 2022 David Skinner (email: djbskinner@icloud.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Styles enqueue
 *
 * @since 1.0.0
 */
function callout_boxes_styles() {
	//$options = get_option( 'cob_options' );

	wp_enqueue_style( 'callout-boxes', plugins_url( 'css/callout-boxes.css', __FILE__ ), array(), '1.4', 'all' );
	wp_enqueue_style( 'cob-fontawesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0', 'all' );
}
add_action( 'wp_enqueue_scripts', 'callout_boxes_styles' );

/**
 * Admin styles enqueue
 *
 * @since 1.3.0
 */
function callout_boxes_admin_styles() {
	wp_enqueue_style( 'callout-boxes-tinymce', plugins_url( 'css/callout-boxes-tinymce.css', __FILE__ ) );
}
add_action( 'admin_enqueue_scripts', 'callout_boxes_admin_styles' );

/*********************************************************************
 * SHORTCODES
*********************************************************************/

/**
 * Callout Shortcode
 * 
 * [callout type=the-box-type size=the-icon-size]the content[/callout]
 *
 * @since 2.0.1
 *
 * @param array $atts Shortcode atts.
 * @param string $content Shortcode content.
 * @return string Shortcode HTML.
 */
function callout_boxes_output( array $atts, string $content = null) {
    $atts = shortcode_atts( array(
        'type'      => 'info',      // set type attr and defaults
		'size'      => 'normal',    // set size attr and defaults
		'icon-size' => '',			// same as above - from simple alert boxes
		'element'	=> ''			// From Documentor
    ), $atts );
    
    /* ALERT SHORTCODE */
    if($atts['icon-size'] != '')
    {
        switch ($atts['type'])
        {
            case 'success':
                $atts['type'] = 'info';
                break;
                    
            case 'info':
                $atts['type'] = 'tips';
                break;

            case 'warning':
                $atts['type'] = 'note';
                break;

            case 'danger':
                $atts['type'] = 'warn';
                break;
        }

        $atts['size'] = $atts['icon-size'];
    }

    /* DOCUMETOR SHORTCODE */
    if($atts['element'] == 'callout')
    {
        switch ($atts['type'])
        {
            case 'note':
                $atts['type'] = 'info';
                break;
                    
            case 'message':
                $atts['type'] = 'tips';
                break;

            case 'warning':
                $atts['type'] = 'note';
                break;

            case 'error':
                $atts['type'] = 'warn';
                break;
        }
    }

	//$options = get_option( 'cob_options' );
	$classes = array();
	$classes[] = $atts['type'];
	$classes[] = $atts['size'];

	ob_start();
	?>
	<div class="callout-box <?php foreach( $classes as $class ) { echo 'cob_' . $class . ' '; }?>">
		<?php echo $atts['text']; echo do_shortcode( $content ); ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'callout', 'callout_boxes_output' );
add_shortcode( 'alert', 'callout_boxes_output' );
add_shortcode( 'docembed', 'callout_boxes_output' );


/*********************************************************************
 * FILTERS
*********************************************************************/

/**
 * Filters the content to remove any extra paragraph or break tags
 * caused by shortcodes.
 *
 * @since 1.3.0
 *
 * @param string $content  String of HTML content.
 * @return string $content Amended string of HTML content.
 */
function empty_paragraph_fix( $content ) {

    $array = array(
        '<p>['    => '[',
        ']</p>'   => ']',
        ']<br />' => ']'
    );
    return strtr( $content, $array );

}
add_filter( 'the_content', 'empty_paragraph_fix' );


/*********************************************************************
 * TINYMCE
*********************************************************************/

/**
 * Register TinyMCE plugin
 *
 * @since 1.2.0
 */
function callout_boxes_tinymce() {
    add_filter( 'mce_buttons', 'cob_add_tinymce_button' );
    add_filter( 'mce_external_plugins', 'cob_add_tinymce_plugin' );
}
add_action( 'admin_init', 'callout_boxes_tinymce' );

/**
 * Add TinyMCE button
 *
 * @since 1.2.0
 */
function cob_add_tinymce_button( $buttons ) {
    array_push( $buttons, 'callout_box_button_key' );
    return $buttons;
}

/**
 * Add TinyMCE plugin
 *
 * @since 1.2.0
 *
 * @param array $plugin_array TinyMCE plugins list
 */
function cob_add_tinymce_plugin( $plugin_array ) {
    $plugin_array['callout_boxes'] = plugins_url( 'js/tinymce-plugin.js', __FILE__ );
    return $plugin_array;
}
