<?php
/*
Plugin Name: Classic Image Button
Plugin URI: http://www.pluginspodcast.com/classic-image-button/
Description: <a href="http://www.pluginspodcast.com/classic-image-button/" target="_blank">Classic Image Button</a> restores the classic image button in the Visual Editor of Wordpress and optionally disables inline styling for images. A plugin from the <a href="http://www.pluginspodcast.com/" target="_blank">PluginsPodcast.com</a>.
Version: 1.0.2
Author: Angelo Mandato
Author URI: http://www.pluginspodcast.com/
Change Log:
	2009-02-03 - v1.0.0: Initial release of the Classic Image Button plugin
	2013-03-24 - v1.0.2: Still works with latest versions of WordPress!

Contributors:
	Angelo Mandato, CIO RawVoice and host of the PluginsPodcast.com - Plugin author
	
Copyright 2009 Angelo Mandato, host of the Plugins Podcast (http://www.pluginspodcast.com)

License: GPL (http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt)
*/

define('CLASSIC_IMAGE_BUTTON_VERSION', '1.0.1' );

function classic_image_button_admin_menu() {
	add_options_page('Classic Image Button Settings', 'Classic Image Button', 8, 'classic-image-button/classic_image_button.php', 'classic_image_button_admin_page');
}

function classic_image_button_admin_page()
{
	global $wp_version;
	
	$VersionDiff = version_compare($wp_version, 2.5);
	if( $VersionDiff < 0 )
		echo '<div class="error">Classic Image Button requires Wordpress version 2.5 or greater.</div>';
	
	
  if( isset($_POST[ 'Submit' ]) )
	{
		// Save the posted value in the database
		$Settings = $_POST['Settings'];
		
		// Wordpress adds slashes to everything, but since we're storing everything serialized, lets remove them...
		while( list($key,$value) = each($Settings) )
			$Settings[$key] = stripslashes($value);
		reset($Settings);
		
		// Update the settings in the database:
		update_option( 'classic_image_button',  $Settings);
					
?>
<div class="updated" style="margin-top: 10px; margin-bottom: 10px; line-height: 29px; font-size: 12px; border-width: 1px; border-style: solid; font-weight: bold;"><?php _e('Classic Image Button settings saved.'); ?></div>
<?php
  }
		
	// Get the general settings
	$Settings = get_option('classic_image_button');		

	if( !$Settings ) // If no general settings, lets pre-populate or copy from podpress
	{
		$Settings = array();
		$Settings['disable_inline_styling'] = 0;
	}
	
	// Format the data for printing in html
	while( list($key,$value) = each($Settings) )
		$General[$key] = htmlspecialchars($value);
	reset($Settings);
	
	// Now display the options editing screen
?>
<div class="wrap" id="classic_image_button_settings">

<form enctype="multipart/form-data" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">


<?php wp_nonce_field('update-options'); ?>

<h2><?php _e("Classic Image Button Settings"); ?></h2>

<table class="form-table">
<tr valign="top">
<th scope="row">
<?php _e("Disable Inline Styling"); ?></th> 
<td>
<select name="Settings[disable_inline_styling]" style="width: 80%;">
<?php
$options = array(0=>'No, do not disable inline styling (Wordpress default)', 1=>'Yes, disable inline styling');

while( list($value,$desc) = each($options) )
	echo "\t<option value=\"$value\"". ($Settings['disable_inline_styling'] ==$value?' selected':''). ">$desc</option>\n";
	
?>
</select>
</td>
</tr>
</table>
<br />


<p style="font-size: 85%; text-align: center;">
	<a href="http://www.pluginspodcast.com/classic-image-button/" title="Classic Image Button" target="_blank">Classic Image Button</a> <?php echo CLASSIC_IMAGE_BUTTON_VERSION; ?>
	by the <a href="http://www.pluginspodcast.com/" target="_blank">PluginsPodcast</a> &#8212; <a href="http://twitter.com/PluginsPodcast" target="_blank" title="Follow PluginsPodcast on Twitter">Follow us on Twitter</a>
</p>
<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes' ) ?>" />
</p>

</form>
</div>

<?php 
	}
	
	function classic_image_button_teeny_mce_buttons($buttons)
	{
		if( count($buttons) == 1 )
		{
			reset($buttons);
			$first = current($buttons);
			if( !strstr($first, ',image') && !in_array('image', $buttons) ) // If it is in neither one long string nor an array by itself...
				$buttons[] = 'image';
		}
		else if( !in_array('image', $buttons) )
		{
			$buttons[] = 'image';
		}
		
		return $buttons;
	}

	function classic_image_button_mce_buttons($buttons)
	{
		if( !in_array('image', $buttons) )
			$buttons[] = 'image';
		return $buttons;
	}
	
	function classic_image_button_mce_tiny_mce_before_init($initArray)
	{
		$Settings = get_option('classic_image_button');
		if( $Settings['disable_inline_styling'] == true )
			$initArray['inline_styles'] = false;
		return $initArray;
	}

	// Add the actions if we are in the admin area...
	if( is_admin() )
	{
		add_action('admin_menu', 'classic_image_button_admin_menu');
		
		add_filter('teeny_mce_buttons', 'classic_image_button_teeny_mce_buttons');
		add_filter('mce_buttons', 'classic_image_button_mce_buttons');
		add_filter('teeny_mce_before_init', 'classic_image_button_mce_tiny_mce_before_init');
		add_filter('tiny_mce_before_init', 'classic_image_button_mce_tiny_mce_before_init');
	}

	// eof
?>