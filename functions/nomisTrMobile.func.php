<?php

function nomisTrMobile($input)
{
	$translations = include dirname(__FILE__) . '/../arrays/mobile-translations.array.php';

	if (isset($translations[$input][$_GET['lang']]))
	{
		return $translations[$input][$_GET['lang']];
	}
	else
	{
		return $input;
	}
}