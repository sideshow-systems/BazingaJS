<?php
/**
 * Ajax PHP handler
 * 
 * This is just a demo to show you how it works!
 * If you are using a MVC framework like Zend Framework, you could use this code in your
 * own controllers. Be sure to set up the 'serverSide' path in your Java Script where you're
 * calling the BazingaJS plugin.
 */

// Get Bazinga php class
require 'Bazinga.php';

// Get url2validate from GET or POST (default is POST)
$url2validate = $_POST['url2validate'];

// Instantiate bazinga class and set url2validate
$bazinga = new Bazinga\Bazinga($url2validate);

// Trigger the validation and give it back to the client
echo json_encode($bazinga->validate());
?>