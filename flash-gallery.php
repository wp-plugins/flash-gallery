<?php
/*
Plugin Name: Flash Gallery
Plugin URI: www.ulfben.com
Description: use [flashgallery] shortcode to display an image wall/slideshow.
Version: 1.0
Author: Ulf Benjaminsson
Author URI: www.ulfben.com
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
*	thumbsize = 110 (size in pixels of thumbs)
* 	usescroll = true/false (scroll to change image)
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
		'rows' => 3, //fgr 
		'height' => '400px', //fgr
		'cats' => the_title('','',false),
		'size' => 'thumbnail',
		'thumbsize' => 110,
		'transparent' => false,
		'background' => FG_URL.'background.jpg',
		'logo' => FG_URL.'logo.png',
		'scaling' => 'fit',
		'exclude' => ''
	), $attr));
	$exclude = explode(',',$exclude);
	$id = intval($id);	
	$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby, 'post__not_in' => $exclude) );
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
	$gallery_id;		
	$current_gallery_title;
	$current_gallery_count;	
	$width = '100%';	
	$fgr = 'FG_'.$id; 		
	$categories = explode(FG_DELIMITER, trim($cats, FG_DELIMITER));		
	$gallerycount = count($categories);
	$wmode = ($transparent) ? "\n".$fgr.'.addParam("wmode", "transparent");' : '';	
	
	$flashgallery = '<p id="'.$fgr.'"><strong><a href="http://get.adobe.com/flashplayer/">Flash Player</a> is required to view this gallery!</strong></p>
	<!-- Flash Gallery 1.0, a WordPress plugin by ulfben. -->
	<script type="text/javascript">
		var '.$fgr.' = new SWFObject("'.FG_URL.FG_SWF.'", "FG", "'.$width.'", "'.$height.'", "8", "#000000");
		'.$fgr.'.addParam("allowFullScreen", "true");'.$wmode.'
		'.$fgr.'.addParam("scale", "noscale");		
		'.$fgr.'.addParam("menu", "false");		
		'.$fgr.'.addVariable("thumbsize", "'.$thumbsize.'");		
		'.$fgr.'.addVariable("gallerycount", "'.$gallerycount.'");	
		'.$fgr.'.addVariable("background", "'.$background.'");
		'.$fgr.'.addVariable("logourl", "'.$logo.'");
		'.$fgr.'.addVariable("scaling", "'.$scaling.'");
		'.$fgr.'.addVariable("rowcount", "'.$rows.'");
		'.$fgr.'.addVariable("basepath", "'.$basepath.'");'."\n";
	FG_set_current_Id_Title_Count($galleryc, $categories, $gallery_id, $current_gallery_title, $current_gallery_count);		
	$flashgallery .= $fgr.'.addVariable("'.$gallery_id.'", "'.$current_gallery_title.'_'.$current_gallery_count.'");'."\n";		
	foreach($attachments as $id => $attachment){		
		$url = str_replace($basepath, '', wp_get_attachment_url($id)); //original size		
		$thumb = wp_get_attachment_image_src($id, 'thumbnail');		
		$thumb = $thumb[0];
		$thumb = substr(strrchr($thumb, '/'), 1);		
		if(($count == $current_gallery_count) && $gallerycount > 1){		
			$galleryc++;		
			FG_set_current_Id_Title_Count($galleryc, $categories, $gallery_id, $current_gallery_title, $current_gallery_count);
			$flashgallery .= $fgr.'.addVariable("'.$gallery_id.'", "'.$current_gallery_title.'_'.$current_gallery_count.'");'."\n";	
			$count = 0;
		}else{
			$count++;		 
		}		
		//$flashgallery .= $fgr.'.addVariable("'.$gallery_id.'_tmb'.$count.'", "'.$thumb.'");'."\n";
		$flashgallery .= $fgr.'.addVariable("'.$galleryc.'_img'.$count.'", "'.$url.'?'.$thumb.'");'."\n";
		//$title = htmlspecialchars($attachment->post_title);		
		//post_excerpt == alt-text.
		$info = ($attachment->post_content) ? $attachment->post_content : $attachment->post_excerpt;	
		if($info){
			$flashgallery .= $fgr.'.addVariable("'.$galleryc.'_txt'.$count.'", "'.htmlspecialchars($info).'");'."\n";		
		}
	}	
	$flashgallery .= $fgr.'.write("'.$fgr.'");</script>';	
	return $flashgallery;
}

function FG_set_current_Id_Title_Count($galleryc, $categories, &$gallery_id, &$current_gallery_title, &$current_gallery_count){	
	$gallery_id = 'gallery'.$galleryc;	
	$name_and_count = explode('_', $categories[$galleryc]);
	$current_gallery_title = ($name_and_count[0]) ? $name_and_count[0] : the_title('','',false);	
	trim($current_gallery_title, FG_DELIMITER);
	$current_gallery_count = (is_numeric($name_and_count[1])) ? $name_and_count[1] : count($attachments);
}

function FG_js() {		
	wp_enqueue_script('swfobject_1.4.4', FG_SCRIPT_URL.'swfobject.js', false, '1.4.4');	
	wp_enqueue_script('swfaddress_2.3', FG_SCRIPT_URL.'swfaddress.js', false, '2.3');	
}
remove_shortcode('flashgallery');
add_shortcode('flashgallery', 'fgr_shortcode');	
add_action('wp_print_scripts', 'FG_js');	
?>