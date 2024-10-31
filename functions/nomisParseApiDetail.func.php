<?php

function nomisParseApiDetail($key, $value, $replaceInKey = null)
{
	if ($replaceInKey !== null)
	{
		$key = preg_replace($replaceInKey, '', $key);
	}
	$key = __(ucfirst(str_replace('_', ' ', $key)), 'nomis');
	$value = is_array($value) ? implode(', ', $value) : $value;
	
	return array(
		$key,
		$value
	);
}