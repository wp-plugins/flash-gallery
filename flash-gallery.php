<?php
/*
Plugin Name: Flash Gallery
Plugin URI: http://wordpress.org/extend/plugins/flash-gallery/
Description: use [flashgallery] to turn galleries into flash image walls.
Version: 1.3.3
Author: Ulf Benjaminsson
Author URI: http://www.ulfben.com
License: GPL

The FLA sources are available in the development version: http://wordpress.org/extend/plugins/flash-gallery/download/
Documentation: http://wordpress.org/extend/plugins/flash-gallery/faq/

Trunk: 
Added "allowfullscreen"-parameter
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

function fgr_shortcode($attr){	
	global $post;
	if(!in_the_loop()){return '';}
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
		'hidetoggle' => false, //fgr
		'allowfullscreen' => true, //fgr
		'delay' => 3, //fgr
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
		'thumbsize' => get_option('thumbnail_size_w'), //fgr - deprecated. autodetected by gallery.
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
		$s = wp_get_attachment_url($id);
		$basepath = dirname($s);
		break;
	}	
	$gallery_id;		
	$current_gallery_title;
	$current_gallery_count;
	$albums = (isset($albums) && !empty($albums)) ? $albums : $cats;
	$width = '100%';
	if(!$allowfullscreen || strtolower($allowfullscreen) == 'false'){$allowfullscreen = 'false';}else{$allowfullscreen = 'true';}	
	$fgr = 'FG_'.$id; 		
	$categories = explode(FG_DELIMITER, trim($albums, FG_DELIMITER));		
	$gallerycount = count($categories);
	$wmode = ($transparent) ? ',"wmode": "transparent"' : '';	
	if(!isset($content)){$content = '';}
	$noflash = apply_filters('post_gallery', $content, $attr);
	$flashgallery = '<!-- Flash Gallery 1.3.3, a WordPress plugin by ulfben. -->
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
		"delay":"'.$delay.'",
		"rowmajor":"'.$rowmajor.'",
		"basepath":"'.$basepath.'",
		"showtitles":"'.$showtitles.'",
		"usescroll":"'.$usescroll.'",
		"color":"'.$color.'",
		"allowdownload":"'.$allowdownload.'",
		"allowfullscreen":"'.$allowfullscreen.'"
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
			$flashgallery .= $fgr.'_config["'.$galleryc.'_txt'.$count.'"] = "'.rawurlencode($info).'";'."\n";			
		}
	}	
$flashgallery .= '
	load'.$fgr.' = function(){
		swfobject.embedSWF("'.FG_URL.FG_SWF.'", "'.$fgr.'", "'.$width.'", "'.$height.'", "9",
			"'.FG_SCRIPT_URL.'expressinstall.swf'.'",'.$fgr.'_config,
			{"allowFullScreen":"'.$allowfullscreen.'"'.$wmode.',"menu":"false","allowscriptacess":"always"}, 
			{"styleclass":"fgr"});
	};	
	unload'.$fgr.' = function(){
		swfobject.removeSWF("'.$fgr.'");
	};
	</script>';
	if(!$hidetoggle){
		$flashgallery .= '<a id="gallery-toggle-'.$global_id.'" class="fgr-toggle" href="#" rel="'.$fgr.'" title="" style="font-size:smaller;display:block;text-align:right;">[Toggle Flash Gallery]</a>';	
	}
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
if(!is_admin()){
	add_action('wp_print_scripts', 'FG_js');	
}		
?>