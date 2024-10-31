<?php
//If there are no entries in the db, this is the default array which determines which details are checked
//array(property, properties, random properties)

return array(
	'street' => array(1, 0, 0), // String
	'address' => array(0, 0, 0), // Street + number + addition
	'zipcode' => array(1, 0, 0), // String
	'district' => array(1, 0, 0), // String
	'subdistrict' => array(1, 0, 0), // String
	'city' => array(1, 0, 0), // String
	'price' => array(1, 1, 1), // Int
	'available_at' => array(1, 1, 1), // yyyy-mm-dd
	'interior' => array(1, 1, 1), // String
	'bedrooms' => array(1, 0, 0), // Int
	'toilets' => array(1, 0, 0), // Int
	'bathrooms' => array(1, 0, 0), // Int
	'elevator' => array(1, 0, 0), // String
	'flooring' => array(1, 0, 0), // String
	'surface' => array(1, 0, 0), // Int
	'kitchen' => array(1, 0, 0), // String
	'kitchen_fridge' => array(1, 0, 0), // 1 or 0
	'kitchen_freezer' => array(1, 0, 0), // 1 or 0
	'kitchen_dishwasher' => array(1, 0, 0), // 1 or 0
	'kitchen_furnace' => array(1, 0, 0), // String
	'kitchen_oven' => array(1, 0, 0), // 1 or 0
	'kitchen_microwave' => array(1, 0, 0), // 1 or 0
	'kitchen_microwave_combi' => array(1, 0, 0), // 1 or 0
	'kitchen_hood' => array(1, 0, 0), // 1 or 0
	'house_type' => array(1, 1, 1), // String
	'garden' => array(0, 0, 0), // String
	'balcony' => array(0, 0, 0), // String
	'roofterrace' => array(0, 0, 0), // String
	'parking' => array(1, 0, 0), // String
	'outside_space' => array(1, 0, 0) // String (garden, balcony, roofterrace)
);