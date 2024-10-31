<?php

if (file_exists('./../../../wp-load.php'))
{
	include './../../../wp-load.php';
}
else
{
	function _e($input, $domain)
	{
		echo $input;
	}

	function __($input, $domain)
	{
		return $input;
	}
}

$subject = $_POST['name-sender'] . ' ' . __('thought you might be interested in this house', 'nomis');
$message = __('Dear', 'nomis') . ' ' . $_POST['name-recipient'] . ",<br><br> " . $_POST['name-sender'] . ' (' . $_POST['email-sender'] . ') ' . __('thought you might be interested in this house at the', 'nomis') . ' <a href="' . $_POST['link'] . '">' . $_POST['street'] . ' in ' . $_POST['city'] . "</a><br><br>" . nl2br($_POST['message']);
$headers = 'FROM: ' . $_POST['name-sender'] . ' <' . $_POST['email-sender'] . '>' . "\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8";

if (@mail($_POST['email-recipient'], $subject, $message, $headers)): ?>

	<div id="mail-to-friend-success">
		<p><?php _e('You have emailed your friend a link to this house!', 'nomis'); ?></p>
		<input type="button" class="nomis-fancybox-close-button" value="<?php _e('Close', 'nomis'); ?>" />
	</div>

<?php else: ?>

	<div id="mail-to-friend-error">
		<p><?php _e('We apologize for the inconvenience, but something went wrong. Please try to contact your friend in another way.', 'nomis'); ?></p>
		<input type="button" class="nomis-fancybox-close-button" value="<?php _e('Close', 'nomis'); ?>" />
	</div>

<?php endif; ?>

<script type="text/javascript">
	$('.nomis-fancybox-close-button').click(function()
	{
		$.fancybox.close();
	});
</script>