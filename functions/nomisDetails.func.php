<?php
/*
 * nomisDetail for key and value of detail
 * nomisDetails for generating list of details for properties for rent
 * nomisSaleDetails for generating list of details for properties for sale
 */

function nomisDetail($house, $detail)
{	
	switch ($detail)
	{
		case 'price':
		case 'forsale_price':
			$key = __('Price', 'nomis');
			if (!empty($house['forsale_price']))
			{
				$value = '€ ' . number_format($house['forsale_price'], 0, '.', '.') . ',-';
			}
			elseif (!empty($house['price']))
			{
				$value = '€ ' . number_format($house['price'], 0, '.', '.') . ',- (' . __($house['price_inc'] == '1' ? 'inclusive' : 'exclusive', 'nomis') . ')' . ($house['forrent_type'] == 403 ? ' ' . __('per year') : '');
			}
			break;

		case 'forsale_service_costs':
			$key = __('Service costs', 'nomis');
			$value = '€ ' . number_format($house['price'], 0, '.', '.') . ',-';
			break;

		case 'available_at':
			$key = __('Availability', 'nomis');
			list($year, $month, $day) = explode('-', $house['available_at']);
			$value = strtotime($house['available_at']) < time() ? __('Direct', 'nomis') : __('From', 'nomis') . ' ' . $day . '-' . $month . '-' . $year;
			break;

		case 'surface':
		case 'surface_outdoor':
			$key = __(ucfirst(str_replace('_', ' ', $detail)), 'nomis');
			$value = $house[$detail] . ' m²';
			break;

		case 'address':
			$key = __('Address', 'nomis');
			$value = $house['street'] . ' ' . $house['number'] . $house['addition'];
			break;

		case 'forsale_status':
			$key = __('Status', 'nomis');
			$value = $house['forsale_status'];
			break;

		case 'forsale_condition':
			$key = __('Condition', 'nomis');
			$value = $house['forsale_condition'];
			break;

		case 'forsale_price_type':
			$key = __('Price type', 'nomis');
			$value = $house['forsale_price_type'];
			break;

		case 'forsale_service_costs':
			$key = __('Service costs', 'nomis');
			$value = $house['forsale_service_costs'];
			break;

		case 'buildyear':
			$key = __('Buildyear', 'nomis');
			$value = $house['buildyear'] == 0 ? '-' : $house['buildyear'];
			break;

		case 'kitchen_fridge':
		case 'kitchen_freezer':
		case 'kitchen_dishwasher':
		case 'kitchen_oven':
		case 'kitchen_microwave':
		case 'kitchen_microwave_combi':
		case 'kitchen_hood':
			$key = __(ucfirst(str_replace('_', ' ', str_replace('kitchen_', '', $detail))), 'nomis');
			$value = $house[$detail] == '1' ? __('Yes', 'nomis') : __('No', 'nomis');
			break;

		case 'kitchen_furnace':
			$key = __('Furnace', 'nomis');
			$value = $house[$detail];
			break;

		case 'outside_space':
			$key = __('Outside space', 'nomis');
			$outsidespace = array();

			if (stripos($house['garden'], 'none') === false
			 && stripos($house['garden'], 'geen') === false
			 && !empty($house['garden']))
			{
				$outsidespace[] = __('garden', 'nomis');
			}
			if (stripos($house['balcony'], 'none') === false
			 && stripos($house['balcony'], 'geen') === false
			 && !empty($house['balcony']))
			{
				$outsidespace[] = __('balcony', 'nomis');
			}
			if (stripos($house['roofterrace'], 'no') === false
			 && stripos($house['roofterrace'], 'nee') === false
			 && !empty($house['roofterrace']))
			{
				$outsidespace[] = __('roofterrace', 'nomis');
			}

			if (empty($outsidespace))
			{
				$value =  __('No', 'nomis');
			}
			else
			{
				$value = __('Yes', 'nomis') . ', ' . implode($outsidespace, ', ');
			}

			break;
		
		case 'bog_bouwgrond_bouwvolume_procentueel':
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_bouwgrond_/');
			$value = $value . '%';
			break;
		
		case 'bog_bouwgrond_bouwvolume_vierkante_meters':
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_bouwgrond_/');
			$value = $value . ' m²';
			break;
		
		case 'bog_bouwgrond_bouwvolume_bouwhoogte':
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_bouwgrond_/');
			$value = $value . ' m';
			break;
			
		case 'bog_bouwgrond_bebouwingsmogelijkheid':
		case 'bog_bouwgrond_units':
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_bouwgrond_/');
			break;
		
		case 'bog_company_surface': 
		case 'bog_company_units_surface': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_company_/');
			$value = $value . ' m²';
			break;
		
		case 'bog_company_height': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_company_/');
			$value = $value . ' m';
			break;
		
		case 'bog_company_floor_load': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_company_/');
			$value = $value . ' kg/m²';
			break;
		
		case 'bog_company_prijs': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_company_/');
			$value = '€ ' . $value . ',-';
			break;
		 
		case 'bog_company_btwtarief': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_company_/');
			$value = $value . '%';
			break;
		
		case 'bog_company_btwbelast': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_company_/');
			$value = nomisBoolean($value);
			break;
		
		case 'bog_company': 
		case 'bog_company_vrije_overspanning': 
		case 'bog_company_luchtbehandelingen': 
		case 'bog_company_voorzieningen': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_company_/');
			break;
		
		case 'bog_office_btwbelast': 
		case 'bog_office_turnkey': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_office_/');
			$value = nomisBoolean($value);
			break;
		
		case 'bog_office': 
		case 'bog_office_surface': 
		case 'bog_office_floors': 
		case 'bog_office_units_from': 
		case 'bog_office_luchtbehandelingen': 
		case 'bog_office_opleveringsniveau': 
		case 'bog_office_prijs': 
		case 'bog_office_btwtarief': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_office_/');
			break;
		
		case 'bog_terrein_oppervlakte': 
		case 'bog_terrein_uitbreiding_vierkante_meters': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_terrein_/');
			$value = $value . ' m²';
			break;
				
		case 'bog_terrein_bouwhoogte': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_terrein_/');
			$value = $value . ' m';
			break;
		
		case 'bog_terrein_uitbreiding_procentueel': 
		case 'bog_terrein_btwtarief': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_terrein_/');
			$value = $value . '%';
			break;
		
		case 'bog_terrein_prijs': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_terrein_/');
			$value = '€ ' . $value . ',-';
			break;
		
		case 'bog_terrein': 
		case 'bog_terrein_btwbelast': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_terrein_/');
			$value = nomisBoolean($value);
			break;
		
		case 'bog_shop_surface': 
		case 'bog_shop_verkoop_vloeroppervlakte': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_shop_/');
			$value = $value . ' m²';
			break;
		
		case 'bog_shop_frontal_width': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_shop_/');
			$value = $value . ' m';
			break;
		
		case 'bog_shop_prijs': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_shop_/');
			$value = '€ ' . $value . ',-';
			break;
		
		case 'bog_shop_btwtarief': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_shop_/');
			$value = $value . '%';
			break;
		
		case 'bog_shop_btwbelast': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_shop_/');
			$value = nomisBoolean($value);
			break;
		
		case 'bog_shop': 
		case 'bog_shop_floors': 
		case 'bog_shop_shopkeepers_association_contribution': 
		case 'bog_shop_catering': 
		case 'bog_shop_branchbeperking': 
		case 'bog_shop_personeel': 
		case 'bog_shop_welstandsklasse': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_shop_/');
			break;
		
		case 'bog_horeca_surface': 
		case 'bog_horeca_verkoop_vloer_oppervlakte': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_shop_/');
			$value = $value . ' m²';
			break;
		
		case 'bog_horeca_prijs': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_shop_/');
			$value = '€ ' . $value . ',-';
			break;
		
		case 'bog_horeca_btwtarief': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_shop_/');
			$value = $value . '%';
			break;		
		
		case 'bog_horeca_btwbelast': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_shop_/');
			$value = nomisBoolean($value);
			break;
		
		case 'bog_horeca': 
		case 'bog_horeca_verdieping': 
		case 'bog_horeca_regio': 
		case 'bog_horeca_soort': 
		case 'bog_horeca_concentratiegebied': 
		case 'bog_horeca_personeel': 
		case 'bog_horeca_welstandsklasse': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_horeca_/');
			break;		
		
		case 'bog_accessibility_trainstation': 
		case 'bog_accessibility_trainstop': 
		case 'bog_accessibility_tramstation': 
		case 'bog_accessibility_tramstop': 
		case 'bog_accessibility_busstation': 
		case 'bog_accessibility_busstop': 
		case 'bog_accessibility_metrostation': 
		case 'bog_accessibility_metrostop': 
		case 'bog_accessibility_highway': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_accessibility_/');
			break;		
		
		case 'bog_voorzieningen_bank': 
		case 'bog_voorzieningen_bank_aantal': 
		case 'bog_voorzieningen_ontspanning': 
		case 'bog_voorzieningen_ontspanning_aantal': 
		case 'bog_voorzieningen_restaurant': 
		case 'bog_voorzieningen_restaurant_aantal': 
		case 'bog_voorzieningen_winkel': 
		case 'bog_voorzieningen_winkel_aantal': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_voorzieningen_/');
			break;	
		
		case 'bog_parkeren_overdekt_btwbelast': 
		case 'bog_parkeren_nietoverdekt_btwbelast': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_parkeren_/');
			$value = nomisBoolean($value);
			break;			
		
		case 'bog_parkeren_nietoverdekt_aantal': 
		case 'bog_parkeren_nietoverdekt_prijs': 
		case 'bog_parkeren_nietoverdekt_btwtarief': 
		case 'bog_parkeren_overdekt_aantal': 
		case 'bog_parkeren_overdekt_prijs': 
		case 'bog_parkeren_overdekt_btwtarief': 
		case 'bog_parkeren_faciliteiten': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_parkeren_/');
			break;	
		
		case 'bog_garagebox': 
		case 'bog_praktijkruimte': 
		case 'bog_showroom': 
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_/');
			$value = nomisBoolean($value);
			break;	
		
		default:
			list($key, $value) = nomisParseApiDetail($detail, $house[$detail], '/^bog_/');
			break;
	}

	return array(
		'key' => $key,
		'value' => $value
	);
}

function nomisDetails($house, $details)
{
	if (!empty($details)):
		foreach ($details as $detail => $checked):
			if (empty($house[$detail]) && $detail != 'address' && $detail != 'outside_space' && $detail != 'price'
			 || $checked == 0) continue;

			$rs = nomisDetail($house, $detail);
			?>
			<div class="item <?php echo $detail; ?>">
				<p class="key"><?php echo htmlentities($rs['key'], ENT_QUOTES, 'UTF-8'); ?></p>
				<p class="value"><?php echo htmlentities($rs['value'], ENT_QUOTES, 'UTF-8'); ?></p>
			</div>
		<?php
		endforeach;
	endif;
}

//backwards compatibility
function nomisSaleDetails($house, $detailGroups) 
{
	nomisGroupDetails($house, $detailGroups);
}

function nomisGroupDetails($house, $detailGroups)
{
	$bogDetails = include ABSPATH . '/wp-content/plugins/nomis/arrays/bog-details.array.php';
	
	foreach ($detailGroups as $detailGroup => $details):
		
		if (in_array($detailGroup, array(
			'bog_bouwgrond',
			'bog_company',
			'bog_office',
			'bog_terrein',
			'bog_shop',
			'bog_horeca'
		 ))
		 && isset($bogDetails[$detailGroup]))
		{
			if ($house[$detailGroup] != '1')
			{
				continue;
			}
		}
		?>
		<div class="detailgroup">
			<h3><?php echo htmlentities(__(ucfirst(str_replace('_', ' ', $detailGroup)), 'nomis'), ENT_QUOTES, 'UTF-8'); ?></h3>
			<?php
			foreach ($details as $detail => $checked):
				if (empty($house[$detail]) && $detail != 'address' && $detail != 'outside_space' && $detail != 'price'
				 || $checked == 0) continue;
				$rs = nomisDetail($house, $detail);
				?>
				<div class="item <?php echo $detail; ?>">
					<p class="key" title="<?php echo $detail; ?>"><?php echo htmlentities($rs['key'], ENT_QUOTES, 'UTF-8'); ?> </p>
					<p class="value"><?php echo htmlentities($rs['value'], ENT_QUOTES, 'UTF-8'); ?>&nbsp;</p>
				</div>
			<?php
			endforeach;
			?>
			<div class="clear"></div>
		</div>
		<?php
	endforeach;
}