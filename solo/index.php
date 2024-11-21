<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="saleslisting.css">
</head>
<body>
    <?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include "saleslisting.php";
$atts=array(
            'title' => 'For Sale',
            'images' => 1,
            'align' => 'left',
            'mandate' =>'Sale',            
        );

echo displaylist($atts);

?>


</Body>
</html>