<?php
/*
Plugin Name: Movie widget
Description: A widget that displays the Movie/TV poster, overview and trailer.
Version:     1.0.2
Author:      Jason L
Text Domain: movieposterdisplay
License:     GPLv3 or later
 
Movie widget is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.
 
Movie widget is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Movie widget. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
*/

// Exit if accessed directly
if(!defined('ABSPATH')){
    exit;
}

// Load scripts
require_once(plugin_dir_path(__FILE__).'/includes/movieposterdisplay-scripts.php');

// Load class
require_once(plugin_dir_path(__FILE__).'/includes/movieposterdisplay-class.php');

//register widget
function register_movieposterdisplay(){
    register_widget('Movie_Poster_Display_Widget');
}

// Hook in function
add_action('widgets_init', 'register_movieposterdisplay');