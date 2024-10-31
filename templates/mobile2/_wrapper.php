<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
		<title><?php
		global $api;

		$blu = clone $api;
$blu->setPropertiesPerPage(1);
$blu->setRenderMode(false);
$tmpData = $blu->run(array(
	'action' => 'search',
));

$cur = current($tmpData['result']);
$title = !empty($cur) ? htmlentities($cur['broker'][0]['name'], ENT_COMPAT, 'utf-8') : '';

echo $title;
		?></title>
		
		<link rel="stylesheet" href="<?php echo WP_PLUGIN_URL; ?>/nomis/templates/mobile2/jquery.mobile-1.0a4.1.css" />
		<link rel="stylesheet" href="<?php echo WP_PLUGIN_URL; ?>/nomis/templates/mobile2/mobile.css"/>
		
		<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/nomis/templates/mobile2/jquery-1.5.2.min.js"></script>
		<script type="text/javascript">
			var _WIDTH;
			$(document).bind('mobileinit', function()
			{
				_WIDTH = $(window).width() - 30; // -= padding
			});
		</script>
		<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/nomis/templates/mobile2/jquery.mobile-1.0a4.1.js"></script>
		<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/nomis/templates/mobile2/jquery.lazyload.js"></script>
	</head>
	
	<body>
		<div data-role="page" data-theme="b"<?php
		if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest')
		{
			echo ' id="intro"';
		}
		?>>
			<div data-role="header" role="banner" data-position="fxed">
				<h1><?php echo $title; ?></h1>
				<a href="index.php?lang=<?php echo $locale; ?>" data-icon="home" data-iconpos="notext" data-direction="reverse" class="ui-btn-right">Home</a>
			</div>
			
			<div data-role="content" id="content">
			
		<?php

try
{
	echo $api->run($_GET);
}
catch (Nomis_Api_Exception $e)
{
	echo '<h2>Error occurred</h2>';
	echo '<p>' . $e->getMessage() . '</p>';
}

?>
			</div>
			
			<div data-role="footer" class="ui-bar">
				<h4><?php echo $title; ?>
					<a href="index.php?action=contact&amp;lang=<?php echo $_GET['lang']; ?>" data-role="button">Contact</a>
				</h4>
			</div><!-- /footer -->
		</div>
		<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/nomis/templates/mobile2/mobile.js"></script>
	</body>
</html>