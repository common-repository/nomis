<?php

function nomisBoolean($val)
{
	if ($val == '1')
	{
		return __('Yes', 'nomis');
	}
	
	return __('No', 'nomis');
}