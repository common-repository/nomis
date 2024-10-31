<?php

//If there are no entries in the db, this is the default array which determines which details are checked
//array(property, properties, random properties)

return array(
	'location' => array(
		'street' => array(1, 0, 0), // String
		'address' => array(0, 0, 0), // Street + number + addition
		'zipcode' => array(1, 0, 0), // String
		'district' => array(1, 0, 0), // String
		'subdistrict' => array(1, 0, 0), // String
		'city' => array(1, 0, 0), // String
		'location' => array(1, 0, 0) //String
	),
	'basic_information' => array(
		'property_type_2' => array(1, 1, 1),
		'property_type_3' => array(1, 0, 0),
		'building_type' => array(1, 0, 0),
		'buildyear' => array(1, 0, 0),
		'surface' => array(1, 0, 0),
		'surface_outdoor' => array(1, 0, 0),
	),
	'price' => array(
		'forsale_price' => array(1, 1, 1), // Int
		'forsale_status' => array(1, 0, 0), // String
		'forsale_condition' => array(1, 0, 0), // String
		'forsale_status' => array(1, 0, 0), // String
		'forsale_price_type' => array(1, 0, 0), // String
		'forsale_service_costs' => array(1, 0, 0),
	),
	'building' => array(
		'livinglayer' => array(1, 0, 0),
		'livinglayers' => array(1, 0, 0),
		'open_porch' => array(1, 0, 0),
//		'maintenance_indoor' => array(1, 0, 0), // grade 1/10 -> sterretjes ofzo
//		'maintenance_outdoor' => array(1, 0, 0), //grade 1/10
		'kitchen' => array(1, 0, 0),
		'bedrooms' => array(1, 0, 0),
		'bathrooms' => array(1, 0, 0),
		'livingrooms' => array(1, 0, 0),
		'seller' => array(1, 0, 0),
		'loft' => array(1, 0, 0),
		'roof_type' => array(1, 0, 0),
//		'heating' => array(1, 0, 0), // Speciaal veld, aanpassen in nomisDetails.func.php!
		'energy_label' => array(1, 0, 0),
//		'isolation' => array(1, 0, 0), // Speciaal veld, aanpassen in nomisDetails.func.php!
	),
	'outside_space' => array(
		'gardens' => array(1, 0, 0), // Speciaal veld, aanpassen in nomisDetails.func.php!
		'balconies' => array(1, 0, 0), // Speciaal veld, aanpassen in nomisDetails.func.php!
		'storages' => array(1, 0, 0), // Speciaal veld, aanpassen in nomisDetails.func.php!
		'parkings' => array(1, 0, 0), // Speciaal veld, aanpassen in nomisDetails.func.php!
	)
);