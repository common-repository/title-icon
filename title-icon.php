<?php
/*
Plugin Name: Title Icon
Plugin URI: http://blog.fleischer.hu/wordpress/title-icon/
Description: Displays a small icon or smiley in the title. Put <code>&lt;?php the_title_icon(); ?&gt;</code> in your template before the title.
Version: 0.3
Author: Gavriel Fleischer
Author URI: http://blog.fleischer.hu/gavriel/

Copyright 2009-2013 Gavriel Fleischer (flocsy@gmail.com)
This plugin is licensed under the terms of the GNU Public License, version 2.
*/

function title_icon_url2icon_filter($custom_field) {
	$img = $custom_field;
	if (preg_match('#\[img\](.+?)\[/img\]#i', $custom_field, $matches)) {
		$img = '<img src="'.$matches[1].'" alt ="" />';
	}
	return $img;
}

function title_icon_lmbbox_smileys($custom_field) {
	$title_icon = $custom_field;
	if (preg_match('#:([^:]+)_([^_:]+):#', $custom_field, $matches)) {
		$path = '/smiley/' . $matches[2] . '/' . $matches[1] . '.';
		$ext = file_exists(PLUGINDIR . '/../../' . $path . 'gif') ? 'gif' : 'png';
		$title_icon = '<img src="' . $path . $ext . '" alt ="" />';
	}
	return $title_icon;
}

function title_icon_the_title_filter($title, $post_id) {
#	global $id;
#	if (in_category('1') && $id) {
	if (in_the_loop()) {
		$title_icon = the_title_icon($post_id, false);
		return $title_icon . $title;
	} else {
		return $title;
	}
}

function the_title_icon($post_id, $echo = true) {
	$title_icon = '';
	$title_icon_arr = get_post_custom_values('title_icon', $post_id);
	if (!empty($title_icon_arr))
		$title_icon = $title_icon_arr[0];
	if (has_filter('title_icon')) {
		$title_icon = apply_filters('title_icon', $title_icon);
	}
	if ($echo)
		echo $title_icon;
	else
		return $title_icon;
}

add_filter('title_icon', 'title_icon_url2icon_filter', 10, 1);

//////////////////////////////////////////
// Integration with some Smiley Plugins:
//////////////////////////////////////////

// LMB^Box Smileys plugin
add_filter('title_icon', 'title_icon_lmbbox_smileys', 20, 1);
#if (function_exists('lmbbox_smileys_admin_convert')) {
#	add_filter('title_icon', 'lmbbox_smileys_admin_convert', 10, 1);
#}

// Smilies Themer
#if (isset($smilies_themer) && method_exists('smilies_themer', 'convert_smilies') /*is_callable(array('smilies_themer', 'convert_smilies'))*/) {
#	add_filter('title_icon', array(&$smilies_themer, 'convert_smilies'), 10, 1);
#}

// Tango Smileys Extended
if (function_exists('tse_switcher')) {
	add_filter('title_icon', 'tse_switcher', 30, 1);
}
#if (function_exists('tse_conditional')) {
#	add_filter('title_icon', 'tse_conditional', 10, 1);
#}

//////////////////////////////////////////////////////////////
// Add your own filter(s) here:
// add_filter('title_icon', 'my_title_icon_filter_function');
//////////////////////////////////////////////////////////////

// Leave this as the last line:
add_filter('the_title', 'title_icon_the_title_filter', 10, 2);
