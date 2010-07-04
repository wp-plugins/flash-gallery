<?php
/*
Plugin Name: Flash Gallery
Plugin URI: http://wordpress.org/extend/plugins/flash-gallery/
Description: use [flashgallery] to turn galleries into interactive, full screen slideshows.
Version: 1.3
Author: Ulf Benjaminsson
Author URI: http://www.ulfben.com
License: GPL

The FLA sources are available in trunk.
*/
if(!defined('WP_CONTENT_URL')){
	define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
}
if(!defined('WP_CONTENT_DIR')){
	define('WP_CONTENT_DIR', ABSPATH.'wp-content');
}
if(!defined('WP_PLUGIN_URL')){
	define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
}
if(!defined('WP_PLUGIN_DIR')){
	define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
}
define('FG_DELIMITER', '%');
define('FG_URL', WP_PLUGIN_URL.'/flash-gallery/');
define('FG_SCRIPT_URL', FG_URL.'js/');
define('FG_SWF', 'zgallery.swf');

/**
*	This implementation of the [flashgallery] shortcode supports 
*	excluding images from the gallery: [gallery exclude='id1,id2']
assumes thumbnails are stored in the same folder as the large image.

* 	cats = "Album1_12%Album2_33%Album3_66" == three albums and their image count
*	height = "400px", "100%" etc.
* 	rows = "3" == number of rows in the gallery thumbnail view.
*	background = "http://www.test.com/background.jpg"
*	logo = http://www.test.com/logo.png
*   transparent = true/false (wmode)
*	scaling: "fill", "fit", "noscale" (default: fit)
*	thumbsize = 110 (size in pixels of thumbs) (deprecated. the gallery now autosenses size of thumbnails)
* 	usescroll = true/false (scroll to change image)
* 	showtitles = true/false (default false)
*   'allowdownload' = true/false (true)
*	'color' = '0xFF0099'
* 	
*/
function fgr_shortcode($attr){	
	global $post;	
	if(isset($attr['orderby'])){
		$attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
		if(!$attr['orderby']){
			unset($attr['orderby']);
		}
	}
	$pid = $post->ID;	
	if(!$pid && isset($_POST['submit']) && isset($_POST['previewID'])){	
		$pid = $_POST['previewID'];
	}	
	extract(shortcode_atts(array(
		'order' => 'ASC',
		'orderby' => 'menu_order', //menu_order ID
		'id' => $pid,
		'itemtag' => 'dl',
		'icontag' => 'dt',
		'link'	=> '',
		'captiontag' => 'dd',
		'columns' => 4, 
		'color' => '0xFF0099', //fgr
		'rows' => 3, //fgr
		'usescroll' => 'true', //fgr
		'showtitles' => 'false', //fgr
		'allowdownload' => 'true', //fgr
		'height' => '450px', //fgr
		'rowmajor' => 'false', //fgr
		'animate' => 'true', //fgr
		'cats' => '', //fgr - deprecated since 1.3. Use 'albums' instead.
		'albums' => the_title('','',false), //fgr
		'size' => 'thumbnail',
		'thumbsize' => 110, //fgr - deprecated. autodetected by gallery.
		'transparent' => false,
		'background' => FG_URL.'background.jpg', //fgr
		'logo' => FG_URL.'logo.png', //fgr
		'scaling' => 'fit', //fgr
		'exclude' => '',
		'numberposts' => -1
	), $attr));
	$exclude = explode(',',$exclude);
	$id = intval($id);	
	$global_id = $id;
	/* Arguments for get_children(). */
	$children = array(
		'post_parent' => $id, 
		'post_status' => 'inherit', 
		'post_type' => 'attachment', 
		'post_mime_type' => 'image', 
		'order' => $order, 
		'orderby' => $orderby, 
		'post__not_in' => $exclude, 
		'exclude' => "".$exclude, 
		'numberposts' => $numberposts
	);
	
	$attachments = get_children($children);
	if(empty($attachments)){
		return '';
	}
	if(is_feed()){
		$output = "\n";
		foreach($attachments as $id => $attachment){
			$output .= wp_get_attachment_link($id, $size, true)."\n";
		}
		return $output;
	} 		
	$count = -1; 
	$galleryc = 0;
	$basepath = get_option('siteurl');
	foreach($attachments as $id => $attachment){
		$s = wp_get_attachment_url($id); //assumes all attachments share the same URL
		$basepath = dirname($s);
		break;
	}	
	$gallery_id;		
	$current_gallery_title;
	$current_gallery_count;
	$albums = (isset($albums) && !empty($albums)) ? $albums : $cats;
	$width = '100%';	
	$fgr = 'FG_'.$id; 		
	$categories = explode(FG_DELIMITER, trim($albums, FG_DELIMITER));		
	$gallerycount = count($categories);
	$wmode = ($transparent) ? ',"wmode": "transparent"' : '';	
	if(!isset($content)){$content = '';}
	$noflash = apply_filters('post_gallery', $content, $attr);  //margin-bottom:-25px;
	$flashgallery = '<!-- Flash Gallery 1.3, a WordPress plugin by ulfben. -->
	<span class="fgr_container" id="container_'.$fgr.'">
		<span id="'.$fgr.'" class="fgr"></span>
	</span>
	<div class="fgr_noflash" style="display:none;">'.$noflash.'</div>	
	<script type="text/javascript">
	'.$fgr.'_config = { 
		"thumbsize":"'.$thumbsize.'",
		"gallerycount":"'.$gallerycount.'",
		"background":"'.$background.'",
		"logourl":"'.$logo.'",
		"scaling":"'.$scaling.'",
		"rowcount":"'.$rows.'",
		"animate":"'.$animate.'",
		"rowmajor":"'.$rowmajor.'",
		"basepath":"'.$basepath.'",
		"showtitles":"'.$showtitles.'",
		"usescroll":"'.$usescroll.'",
		"color":"'.$color.'",
		"allowdownload":"'.$allowdownload.'"
	};'."\n"; 
	FG_set_current_Id_Title_Count($galleryc, $categories, $gallery_id, $current_gallery_title, $current_gallery_count, $attachments);		
	$flashgallery .= $fgr.'_config["'.$gallery_id.'"] = "'. $current_gallery_title.'_'.$current_gallery_count .'";'."\n";		
	foreach($attachments as $id => $attachment){		
		$url = str_replace($basepath, '', wp_get_attachment_url($id)); //original size		
		$thumb = wp_get_attachment_image_src($id, 'thumbnail');		
		$thumb = $thumb[0];
		$thumb = substr(strrchr($thumb, '/'), 1);		
		if(($count == $current_gallery_count) && $gallerycount > 1){		
			$galleryc++;		
			FG_set_current_Id_Title_Count($galleryc, $categories, $gallery_id, $current_gallery_title, $current_gallery_count, $attachments);
			$flashgallery .= $fgr.'_config["'.$gallery_id.'"] = "'. $current_gallery_title.'_'.$current_gallery_count .'";'."\n";
			$count = 0;
		}else{
			$count++;		 
		}		
		$flashgallery .= $fgr.'_config["'.$galleryc.'_img'.$count.'"] = "'.$url.'?'.$thumb.'";'."\n";
		/*
		[post_content] => Description
		[post_title] => Filename
		[post_excerpt] => Caption */
		$info = ($attachment->post_content) ? $attachment->post_content : $attachment->post_excerpt;	
		if($info){
			$flashgallery .= $fgr.'_config["'.$galleryc.'_txt'.$count.'"] = "'.htmlspecialchars($info).'";'."\n";			
		}
	}		
$flashgallery .= '
	load'.$fgr.' = function(){
		swfobject.embedSWF("'.FG_URL.FG_SWF.'", "'.$fgr.'", "'.$width.'", "'.$height.'", "9",
			"'.FG_SCRIPT_URL.'expressinstall.swf'.'",'.$fgr.'_config,
			{"allowFullScreen":"true"'.$wmode.',"menu":"false","allowscriptacess":"always"}, 
			{"styleclass":"fgr"});
	};	
	unload'.$fgr.' = function(){
		swfobject.removeSWF("'.$fgr.'");
	};
	</script>
	<a id="gallery-toggle-'.$global_id.'" class="fgr-toggle" href="#" rel="'.$fgr.'" title="" style="font-size:smaller;display:block;text-align:right;">[Toggle Flash Gallery]</a>';	
	return $flashgallery;
}

function FG_set_current_Id_Title_Count($galleryc, $categories, &$gallery_id, &$current_gallery_title, &$current_gallery_count, &$attachments){	
	$gallery_id = 'gallery'.$galleryc;	
	$name_and_count = explode('_', $categories[$galleryc]);
	$current_gallery_title = ($name_and_count[0]) ? $name_and_count[0] : the_title('','',false);	
	trim($current_gallery_title, FG_DELIMITER);
	$current_gallery_count = (isset($name_and_count[1]) && is_numeric($name_and_count[1])) ? $name_and_count[1] : count($attachments);
}

function FG_js() {		
	wp_deregister_script('jquery');
	wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
	wp_enqueue_script('jquery', '', '', '', true ); //true == in footer. since wp 2.8
	wp_enqueue_script('swfobject', '', false, '2.2', true); 
	wp_enqueue_script('swfaddress_2.3', FG_SCRIPT_URL.'swfaddress.js', 'swfobject', '2.3', true);
	wp_enqueue_script('toggle_fgr', FG_SCRIPT_URL.'togglegallery.js', 'jquery', '1.0', true);		
}
remove_shortcode('flashgallery');
add_shortcode('flashgallery', 'fgr_shortcode');	
add_action('wp_print_scripts', 'FG_js');	
?>