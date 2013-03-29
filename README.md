BazingaJS
=========

Validate your current HTML page quickly!

If you have ever wanted to validate your current page you are working on quickly then BazingaJS is here to help you optimize your validation workflow.

Demo
----
Check out the Demo pages!

* <http://bazingajs.sideshow-systems.de/demo/demo_page_1.html>
* <http://bazingajs.sideshow-systems.de/demo/demo_page_2.html>
* <http://bazingajs.sideshow-systems.de/demo/demo_page_3.html>


Usage
-----
This is mainly a jQuery plugin and you'll use it something like this:

```javascript
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" />
<script type="text/javascript" src="path/to/jquery.bazinga_v0.1.js" />
<script type="text/javascript">
	$(document).ready(function() {
		$(document).bazinga({
			serverSide: 'path/to/serverside/ajax.php',
			validateOnLoad: true
		});
	});
</script>
```

You will also need a php file you could reach via ajax. This could be a controller if you use MVC. Your serverside script looks like this:

```php
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
```

More information is coming soon!
