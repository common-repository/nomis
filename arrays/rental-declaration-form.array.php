<?php
return array(
	'last_name' => array(
		'options' => array(
			'required' => true,
			'label' => 'Name'
		)
	),
	'address' => array(
		'options' => array(
			'required' => true
		)
	),
	'zipcode' => array(
		'options' => array(
			'required' => true
		)
	),
	'city' => array(
		'options' => array(
			'required' => true
		)
	),
	'telephone' => array(
		'options' => array(
			'required' => true,
		)
	),
	'mobile_telephone' => array(
		'options' => array(
			'label' => 'Mobile telephone'
		)
	),
	'emailaddress' => array(
		'options' => array(
			'required' => true,
			'reqex' => 'email'
		)
	),
	'house_address' => array(
		'options' => array(
			'required' => true,
			'label' => 'House address'
		)
	),
	'house_zipcode' => array(
		'options' => array(
			'required' => true
		)
	),
	'house_city' => array(
		'options' => array(
			'required' => true
		)
	),
	'house_bedrooms' => array(
		'options' => array(
			'required' => true,
			'type' => 'select',
			'options' => array(
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4'
			)
		)
	),
	'house_surface' => array(
		'options' => array(
			'regex' => 'number'
		)
	)
);