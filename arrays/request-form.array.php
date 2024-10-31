<?php

return array (
	'last_name' => array(
		'options' => array(
			'required' => true,
			'label' => '[:en]Name[:nl]Naam'
		)
	),
	'company' => array(),
	'telephone' => array(),
	'emailaddress' => array(
		'options' => array(
			'regex' => 'email',
			'label' => '[:en]Emailaddress[:nl]Emailadres'
		)
	),
	'interior' => array(
		'options' => array(
			'type' => 'select',
			'label' => '[:en]Interior[:nl]Interieur',
			'options' => array(
				'' => '',
				13 => '[:en]Unfurnished[:nl]Ongemeubileerd',
				14 => '[:en]Furnished[:nl]Gemeubileerd',
				15 => '[:en]Bare[:nl]Helemaal leeg'
			)
		)
	),
	'bedrooms' => array(
		'options' => array(
			'type' => 'select',
			'label' => '[:en]Bedrooms[:nl]Slaapkamers',
			'options' => array(
				'' => '',
				'1' => '1+',
				'2' => '2+',
				'3' => '3+',
				'4' => '4+'
			)
		)
	),
	'city' => array(),
	'district' => array(),
	'commencing_date' => array(
		'options' => array(
			'description' => '[:en]Commencing date in dd/mm/yyyy[:nl]Begindatum in dd/mm/yyyy',
			'label' => '[:en]When do you want to rent[:nl]Wanneer wilt u huren',
			'class' => 'regex',
			'regex' => 'date'
		)
	),
	'max_price' => array(
		'options' => array(
			'regex' => 'number',
			'label' => '[:en]Maximum price[:nl]Maximum prijs'
		)
	),
	'other_wishes' => array(
		'options' => array(
			'label' => '[:en]Other wishes[:nl]Andere wensen',
			'type' => 'textarea'
		)
	)
);