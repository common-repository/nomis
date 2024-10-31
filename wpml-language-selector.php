<?php

$languages = icl_get_languages('orderby=id&order=asc&skip_missing=0');

if(strpos($_SERVER['REQUEST_URI'], 'property-en/') !== false)
{
	list($temp, $extraUrl) = explode('property-en/', $_SERVER['REQUEST_URI']);
}
if(strpos($_SERVER['REQUEST_URI'], 'property-nl/') !== false)
{
	list($temp, $extraUrl) = explode('property-nl/', $_SERVER['REQUEST_URI']);
}
?>
<div id="nomis-wpml-language-selector">
	<ul>
		<?php foreach ($languages as $language): ?>
		<li class="<?php echo $language['language_code']; ?>">
			<a href="<?php echo $language['url'] . (isset($extraUrl) ? $extraUrl : ''); ?>">
				<img src="http://static.annommx.com/images/flags/<?php echo $language['language_code']; ?>.png" alt="<?php echo $language['native_name']; ?>" />
			</a>
		</li>
		<?php endforeach; ?>
	</ul>
</div>