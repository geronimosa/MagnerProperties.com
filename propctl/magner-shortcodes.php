<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


add_shortcode('saleslist', 'dotiavatar_function');

function dotiavatar_function() {
    include_once 'saleslisting.php';
    $displaystuff=displaylist();
     return $displaystuff ;
}
