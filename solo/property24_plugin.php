<?php

/**
 * Plugin Name: Magner Properties Property 24 Plugin
 * Plugin URI: http://www.vlocitycommunications.com/magner_shortcode/
 * Description: This plugin retrieves PropCtl data using a Shortcode - [saleslist title="Any Title you want" images=1,2 or 3  align='left'/'right' mandate='all','Sales' or 'Rentals']. 
 * Version: 1.0
 * Author: Steve Akester Steve@VlocityCommunications.com
 * Author URI: http://www.vlocitycommunications.com
 * 
 * 
 * Version 1.1
 * 
 */

add_shortcode('saleslist', 'dotiavatar_function');

function dotiavatar_function($atts = [], $content = null, $tag = '') {
    include_once 'saleslisting.php';
    $displaystuff=displaylist($atts,$content,$tag);
     return $displaystuff ;
}