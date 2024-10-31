<?php

//does not work on localhost if a symlink from the svn trunk is being used, wp-load is trying to be accessed from the trunk
include realpath(dirname(__FILE__) . '/../../../wp-load.php');

$_POST['add_contact'] = true;
$_POST['last_name'] = $_POST['name'];
$_POST['emailaddress'] = $_POST['email'];
$_POST['subject'] = 'Interesse in ' . $_POST['id'];
if (!isset($_POST['telephone'])) $_POST['telephone'] = '';
$_POST['message'] = 'Ik ben geinteresseerd in woning ' . $_POST['id'] . ' aan de ' . $_POST['street'] . ' in ' . $_POST['city'] . ".\n-----------\n" . $_POST['message'];

$api = new Nomis_Api(get_option('nomis_api_key'));
$api->run(array('action' => 'contact'));

?>

<div id="contact-about-house-success">
	<p><?php _e('Thanks for your contact request, we will be in touch with you as soon as possible!', 'nomis'); ?></p>
	<input type="button" class="nomis-fancybox-close-button" value="<?php _e('Close', 'nomis'); ?>" />
</div>

<script type="text/javascript">
	$('.nomis-fancybox-close-button').click(function()
	{
		$.fancybox.close();
	});
</script>