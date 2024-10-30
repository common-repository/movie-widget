<?php
// add scripts
function mpd_add_scripts(){
    //Add main CSS
    wp_enqueue_style('mpd-main-style', plugins_url('../css/style.css', __FILE__));
    //Add main JS
    wp_enqueue_script('mpd-main-style', plugins_url('../js/main.js', __FILE__));

    // Add google Script
    wp_register_script('google', 'https://apis.google.com/js/platform.js');
    wp_enqueue_script('google');
}

add_action('wp_enqueue_scripts', 'mpd_add_scripts');