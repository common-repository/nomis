<?php

return array(
	'basic_information' => array(
		'bog_hoofdbestemming' => array(1, 1, 0),
		'bog_nevenbestemmingen' => array(1, 0, 0),
		'street' => array(1, 0, 0), // String
		'address' => array(0, 0, 0), // Street + number + addition
		'zipcode' => array(1, 0, 0), // String
		'district' => array(1, 0, 0), // String
		'city' => array(1, 1, 1), // String
		'price' => array(1, 1, 1), // String
		'forsale_price' => array(1, 1, 1), // String
	),
	'bog_bouwgrond' => array(
		'bog_bouwgrond_bebouwingsmogelijkheid' => array(1, 0, 0),
		'bog_bouwgrond_bouwvolume_procentueel' => array(1, 0, 0),
		'bog_bouwgrond_bouwvolume_vierkante_meters' => array(1, 0, 0),
		'bog_bouwgrond_bouwvolume_bouwhoogte' => array(1, 0, 0),
		'bog_bouwgrond_units' => array(1, 0, 0),
	),
	'bog_company' => array(
		'bog_company_surface' => array(1, 0, 0),
		'bog_company_units_surface' => array(1, 0, 0),
		'bog_company_height' => array(1, 0, 0),
		'bog_company_floor_load' => array(1, 0, 0),
		'bog_company_vrije_overspanning' => array(1, 0, 0),
		'bog_company_luchtbehandelingen' => array(1, 0, 0),
		'bog_company_voorzieningen' => array(1, 0, 0),
		'bog_company_prijs' => array(1, 0, 0),
		'bog_company_btwtarief' => array(1, 0, 0),
		'bog_company_btwbelast' => array(1, 0, 0),
	),
	'bog_office' => array(
		'bog_office_surface' => array(1, 0, 0),
		'bog_office_floors' => array(1, 0, 0),
		'bog_office_units_from' => array(1, 0, 0),
		'bog_office_turnkey' => array(1, 0, 0),
		'bog_office_luchtbehandelingen' => array(1, 0, 0),
		'bog_office_opleveringsniveau' => array(1, 0, 0),
		'bog_office_prijs' => array(1, 0, 0),
		'bog_office_btwtarief' => array(1, 0, 0),
		'bog_office_btwbelast' => array(1, 0, 0),
	),
	'bog_terrein' => array(
		'bog_terrein_oppervlakte' => array(1, 0, 0),
		'bog_terrein_bouwhoogte' => array(1, 0, 0),
		'bog_terrein_uitbreiding_procentueel' => array(1, 0, 0),
		'bog_terrein_uitbreiding_vierkante_meters' => array(1, 0, 0),
		'bog_terrein_prijs' => array(1, 0, 0),
		'bog_terrein_btwtarief' => array(1, 0, 0),
		'bog_terrein_btwbelast' => array(1, 0, 0),
	),
	'bog_shop' => array(
		'bog_shop_surface' => array(1, 0, 0),
		'bog_shop_floors' => array(1, 0, 0),
		'bog_shop_shopkeepers_association_contribution' => array(1, 0, 0),
		'bog_shop_frontal_width' => array(1, 0, 0),
		'bog_shop_catering' => array(1, 0, 0),
		'bog_shop_verkoop_vloeroppervlakte' => array(1, 0, 0),
		'bog_shop_branchbeperking' => array(1, 0, 0),
		'bog_shop_prijs' => array(1, 0, 0),
		'bog_shop_btwtarief' => array(1, 0, 0),
		'bog_shop_btwbelast' => array(1, 0, 0),
		'bog_shop_personeel' => array(1, 0, 0),
		'bog_shop_welstandsklasse' => array(1, 0, 0),
	),
	'bog_horeca' => array(
		'bog_horeca_surface' => array(1, 0, 0),
		'bog_horeca_verkoop_vloer_oppervlakte' => array(1, 0, 0),
		'bog_horeca_verdieping' => array(1, 0, 0),
		'bog_horeca_regio' => array(1, 0, 0),
		'bog_horeca_soort' => array(1, 0, 0),
		'bog_horeca_concentratiegebied' => array(1, 0, 0),
		'bog_horeca_prijs' => array(1, 0, 0),
		'bog_horeca_btwtarief' => array(1, 0, 0),
		'bog_horeca_btwbelast' => array(1, 0, 0),
		'bog_horeca_personeel' => array(1, 0, 0),
		'bog_horeca_welstandsklasse' => array(1, 0, 0),
	),
	'overige' => array(
		'bog_garagebox' => array(1, 0, 0),
		'bog_praktijkruimte' => array(1, 0, 0),
		'bog_showroom' => array(1, 0, 0),
		'bog_location' => array(1, 0, 0),
	),
	'toegankelijkheid' => array(
		'bog_accessibility_trainstation' => array(1, 0, 0),
		'bog_accessibility_trainstop' => array(1, 0, 0),
		'bog_accessibility_tramstation' => array(1, 0, 0),
		'bog_accessibility_tramstop' => array(1, 0, 0),
		'bog_accessibility_busstation' => array(1, 0, 0),
		'bog_accessibility_busstop' => array(1, 0, 0),
		'bog_accessibility_metrostation' => array(1, 0, 0),
		'bog_accessibility_metrostop' => array(1, 0, 0),
		'bog_accessibility_highway' => array(1, 0, 0),
	),
	'voorzieningen' => array(
		'bog_voorzieningen_bank' => array(1, 0, 0),
		'bog_voorzieningen_bank_aantal' => array(1, 0, 0),
		'bog_voorzieningen_ontspanning' => array(1, 0, 0),
		'bog_voorzieningen_ontspanning_aantal' => array(1, 0, 0),
		'bog_voorzieningen_restaurant' => array(1, 0, 0),
		'bog_voorzieningen_restaurant_aantal' => array(1, 0, 0),
		'bog_voorzieningen_winkel' => array(1, 0, 0),
		'bog_voorzieningen_winkel_aantal' => array(1, 0, 0),
	),
	'parkeren' => array(
		'bog_parkeren_nietoverdekt_aantal' => array(1, 0, 0),
		'bog_parkeren_nietoverdekt_prijs' => array(1, 0, 0),
		'bog_parkeren_nietoverdekt_btwtarief' => array(1, 0, 0),
		'bog_parkeren_nietoverdekt_btwbelast' => array(1, 0, 0),
		'bog_parkeren_overdekt_aantal' => array(1, 0, 0),
		'bog_parkeren_overdekt_prijs' => array(1, 0, 0),
		'bog_parkeren_overdekt_btwtarief' => array(1, 0, 0),
		'bog_parkeren_overdekt_btwbelast' => array(1, 0, 0),
		'bog_parkeren_faciliteiten' => array(1, 0, 0),
	)
);