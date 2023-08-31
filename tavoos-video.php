<?php

/*
Plugin Name: Tavoos Video
Description: This plugin can put tavoos VAST code to your site.
Version: 1.0
Author: Alireza Sadi
Author URI: https://github.com/alireza-sadi
Text Domain: tavoos-video
*/


// Enqueue the script
function tavoos_enqueue_scripts() {
    // wp_enqueue_script('tavoos-player', 'https://player.tavoos.net/jwplayer.js?v=1.0', array(), null, false);
	// load_plugin_textdomain('tavoos-video', false, dirname(plugin_basename(__FILE__)).'/languages');
}
add_action('wp_enqueue_scripts', 'tavoos_enqueue_scripts');


// Shortcode to embed Tavoos video player
function tavoos_video_shortcode($atts) {
    $atts = shortcode_atts(array(
        'url' => '',
    ), $atts);

    $video_url = esc_url($atts['url']);
    $vast_url = get_option('tavoos_video_vast_url', ''); // Get the VAST URL from options

    if ($video_url) {
        ob_start(); ?>
        <div id='tavoos-vplyr'>
            <video src="<?php echo $video_url; ?>"></video>
        </div>
        <script type="text/javascript">
            tavoos_init_player(
                'tavoos-vplyr',
                'posterUrl',
                [
                    {
                        "file": "<?php echo $video_url; ?>",
                        "type": "mp4",
                        "label": "720p"
                    },
                ],
                '<?php echo $vast_url; ?>'
            );
        </script>
        <?php
        return ob_get_clean();
    } else {
        return __('Please provide a valid video URL.','tavoos-video');
    }
}
add_shortcode('tavoos_video_player', 'tavoos_video_shortcode');

// Admin menu for VAST URL option
function tavoos_video_settings_menu() {
    add_options_page(
		__('Tavoos Video Settings','tavoos-video'), 
		__('Tavoos Video Settings','tavoos-video'), 
		'manage_options',
		'tavoos-video-settings',
		'tavoos_video_settings_page');
}
add_action('admin_menu', 'tavoos_video_settings_menu');

// VAST URL option page
function tavoos_video_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['tavoos_video_vast_url'])) {
        update_option('tavoos_video_vast_url', esc_url($_POST['tavoos_video_vast_url']));
        echo '<div class="notice notice-success"><p>'.__('VAST URL updated successfully','tavoos-video').'.</p></div>';
    }

    $vast_url = get_option('tavoos_video_vast_url', '');
    ?>
    <div class="wrap">
        <h1><?php __('Tavoos Video Settings','tavoos-video');?></h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="tavoos_video_vast_url"><?php __('Default VAST URL','tavoos-video');?></label></th>
                    <td>
                        <input type="text" name="tavoos_video_vast_url" id="tavoos_video_vast_url" value="<?php echo $vast_url; ?>" class="regular-text">
                        <p class="description"><?php __('Enter the default VAST URL to be used in all shortcodes.','tavoos-video');?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
		<h3><?php __('Shortcode Help:','tavoos-video');?></h3>
		<p> <?php __('In the "Default VAST URL" field, enter the VAST URL that you want to use as the default for all video shortcodes. This URL will be used when a shortcode does not explicitly specify a VAST URL.In your posts or pages, use the [tavoos_video_player] shortcode to embed videos. The shortcode supports the url attribute for specifying the video URL. for example:[tavoos_video_player url="VIDEO_URL_HERE" vast="VAST_URL_HERE"]','tavoos-video'); ?> </p>
    </div>
    <?php
}
