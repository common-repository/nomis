<?php

if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()
 || !ini_get('magic_quotes_sybase') || strtolower(ini_get('magic_quotes_sybase')) != 'off')
{
	$in = array(&$_GET, &$_POST, &$_COOKIE);
	while (list($k, $v) = each($in))
	{
		foreach ($v as $key => $val)
		{
			if (!is_array($val))
			{

				$in[$k][$key] = stripslashes($val);
				continue;
			}

			$in[] = & $in[$k][$key];
		}
	}

	unset($in);
}

$api = new NomisWordpressApi();

$nomisDetails = include dirname(__FILE__) . '/arrays/rent-details.array.php';
$nomisSaleDetails = include dirname(__FILE__) . '/arrays/sale-details.array.php';
$nomisBogDetails = include dirname(__FILE__) . '/arrays/bog-details.array.php';

$requiresDetails = array(
	'nomis_property_details',
	'nomis_properties_details',
	'nomis_random_properties_details'
);

$requiresSaleDetails = array(
	'nomis_property_sale_details'
);

$requiresBogDetails = array(
	'nomis_property_bog_details'
);

$nomisSearchCriteria = array(
	'minimum_price' => array(1, 1, 0), //(quick search, search, sale)
	'maximum_price' => array(1, 1, 0),
	'city' => array(1, 1, 1),
	'district' => array(0, 0, 0),
	'interior' => array(1, 1, 1),
	'bedrooms' => array(1, 1, 1),
	'available_at' => array(0, 0, 0),
	'garden' => array(0, 0, 0),
	'parking' => array(0, 0, 0),
	'balcony' => array(0, 0, 0),
	'elevator' => array(0, 0, 0),
	'surface' => array(0, 1, 1),
	'forsale' => array(0, 0, 0),
	'minimum_forsale_price' => array(0, 0, 1),
	'maximum_forsale_price' => array(0, 0, 1),
	'house_type' => array(0, 0, 0),
	'country' => array(0, 0, 0)
);

$requiresSearchCriteria = array(
	'nomis_quick_search_criteria',
	'nomis_search_criteria',
	'nomis_search_criteria_sale'
);

$requiresForm = array(
	'nomis_add_request_form',
	'nomis_rental_declaration_form'
);

$defaultMinPrices = '500,750,1000,1250,1500,2000,2500';
$defaultMaxPrices = '750,1000,1250,1500,2000,3000,4000';
$defaultMinPricesSale = '50000,100000,150000,200000,30000,500000';
$defaultMaxPricesSale = '100000,150000,200000,30000,500000,700000';
$defaultRequestForm = include dirname(__FILE__) . '/arrays/request-form.array.php';
$defaultRentalDeclarationForm = include dirname(__FILE__) . '/arrays/rental-declaration-form.array.php';
if (get_option('nomis_search_max_price_steps') == '') update_option('nomis_search_max_price_steps', $defaultMinPrices);
if (get_option('nomis_search_min_price_steps') == '') update_option('nomis_search_max_price_steps', $defaultMinPrices);
if (get_option('nomis_search_min_price_steps_sale') == '') update_option('nomis_search_min_price_steps_sale', $defaultMinPricesSale);
if (get_option('nomis_search_max_price_steps_sale') == '') update_option('nomis_search_max_price_steps_sale', $defaultMaxPricesSale);
if (get_option('nomis_quick_search_max_price_steps') == '') update_option('nomis_quick_search_max_price_steps', $defaultMinPrices);
if (get_option('nomis_quick_search_min_price_steps') == '') update_option('nomis_quick_search_min_price_steps', $defaultMinPrices);
if (get_option('nomis_property_layout') == '') update_option('nomis_property_layout', '1');
if (get_option('nomis_properties_order') == '') update_option('nomis_properties_order', 'price=asc');
if (get_option('nomis_add_request_form') == '') update_option('nomis_add_request_form', $defaultRequestForm);
if (get_option('nomis_rental_declaration_form') == '') update_option('nomis_rental_declaration_form', $defaultRentalDeclarationForm);

if (isset($_GET['clearcache']) && $_GET['clearcache'] == 'true')
{
	$api->clearCache();
}

if ($_POST['nomis-submit'] == 'Y')
{
	update_option('nomis_api_key', $_POST['api-key']);

	if (!empty($_POST['api-key-valid']))
	{
		foreach ($requiresDetails as $value)
		{
			$details = array();
			if (!empty($_POST[$value . '_order']))
			{
				foreach (explode(',', $_POST[$value . '_order']) as $detail)
				{
					if (array_key_exists($detail, $nomisDetails))
					{
						$details[$detail] = $_POST[$value . '_' . $detail] == '1' ? 1 : 0;
					}
				}
				update_option($value, $details);
			}
		}

		foreach ($requiresSaleDetails as $value)
		{
			if (!empty($_POST[$value . '_order']))
			{
				$insert = array();
				foreach (explode(',', $_POST[$value . '_order']) as $detail)
				{
					foreach ($_POST[$value] as $category => $categoryDetail)
					{
						$insert[$category] = $categoryDetail;
					}
				}
				update_option($value, $insert);
			}
		}

		foreach ($requiresBogDetails as $value)
		{
			if (!empty($_POST[$value . '_order']))
			{
				$insert = array();
				foreach (explode(',', $_POST[$value . '_order']) as $detail)
				{
					foreach ($_POST[$value] as $category => $categoryDetail)
					{
						$insert[$category] = $categoryDetail;
					}
				}
				update_option($value, $insert);
			}
		}

		foreach ($requiresSearchCriteria as $value)
		{
			if (!empty($_POST[$value]))
			{
				$criteria = array();
				foreach (explode(',', $_POST[$value]) as $criterium)
				{
					if (array_key_exists($criterium, $nomisSearchCriteria))
					{
						$criteria[$criterium] = $_POST[$value . '_' . $criterium] == '1' ? 1 : 0;
					}
				}
				update_option($value, $criteria);
			}
		}


		foreach ($requiresForm as $formName)
		{
			$formFields = array();

			if (isset($_POST[$formName . '_name']))
			{
				$i = 0;
				foreach ($_POST[$formName . '_name'] as $fieldName)
				{
					if (empty($fieldName))
					{
						continue;
					}

					if ($_POST[$formName . '_type'][$i] == 'select')
					{
						list($fieldName, $selectOptionsTmp) = explode('::', $fieldName);
						$selectOptionsTmp = explode(',', $selectOptionsTmp);

						$selectOptions = array();
						foreach ($selectOptionsTmp as $selectOption)
						{
							list($optionName, $optionLabel) = explode('=>', $selectOption);
							$selectOptions[$optionName] = $optionLabel;
						}
					}

					$formFields[$fieldName] = array(
						'options' => array(
							'label' => $_POST[$formName . '_label'][$i],
							'required' => $_POST[$formName . '_required'][$i] == '1' ? true : false,
							'type' => $_POST[$formName . '_type'][$i],
							'options' => isset($selectOptions) ? $selectOptions : '',
							'description' => $_POST[$formName . '_description'][$i]
						)
					);

					unset($selectOptions);
					$i ++;
				}
				update_option($formName, $formFields);
			}
		}

		update_option('nomis_google_analytics_key', $_POST['google_analytics_key']);
		update_option('nomis_property_back', $_POST['property_back']);
		update_option('nomis_property_contact', $_POST['property_contact']);
		update_option('nomis_property_print', $_POST['property_print']);
		update_option('nomis_property_addthis', $_POST['property_addthis']);
		update_option('nomis_property_mailtofriend', $_POST['property_mailtofriend']);
		update_option('nomis_property_googlemaps', $_POST['property_googlemaps']);
		update_option('nomis_property_streetview', $_POST['property_streetview']);
		update_option('nomis_property_contact_email', $_POST['property_contact_email']);
		update_option('nomis_property_layout', $_POST['property_layout']);
		update_option('nomis_property_different_sale_details', $_POST['property_different_sale_details']);

		update_option('nomis_search_display_labels', $_POST['search_display_labels']);
		update_option('nomis_search_min_price', $_POST['search_min_price']);
		update_option('nomis_search_max_price', $_POST['search_max_price']);
		update_option('nomis_search_city', $_POST['search_city']);
		update_option('nomis_search_district', $_POST['search_district']);
		update_option('nomis_search_interior', $_POST['search_interior']);
		update_option('nomis_search_bedrooms', $_POST['search_bedrooms']);
		update_option('nomis_search_available_at', $_POST['search_available_at']);
		update_option('nomis_search_min_price_steps', $_POST['search_min_price_steps']);
		update_option('nomis_search_max_price_steps', $_POST['search_max_price_steps']);
		update_option('nomis_search_min_price_steps_sale', $_POST['search_min_price_steps_sale']);
		update_option('nomis_search_max_price_steps_sale', $_POST['search_max_price_steps_sale']);
		update_option('nomis_search_garden', $_POST['search_garden']);
		update_option('nomis_search_balcony', $_POST['search_balcony']);
		update_option('nomis_search_elevator', $_POST['search_elevator']);
		update_option('nomis_search_parking', $_POST['search_parking']);

		update_option('nomis_quick_search_action', $_POST['quick_search_action']);
		update_option('nomis_quick_search_display_labels', $_POST['quick_search_display_labels']);
		update_option('nomis_quick_search_min_price', $_POST['quick_search_min_price']);
		update_option('nomis_quick_search_max_price', $_POST['quick_search_max_price']);
		update_option('nomis_quick_search_city', $_POST['quick_search_city']);
		update_option('nomis_quick_search_district', $_POST['quick_search_district']);
		update_option('nomis_quick_search_interior', $_POST['quick_search_interior']);
		update_option('nomis_quick_search_bedrooms', $_POST['quick_search_bedrooms']);
		update_option('nomis_quick_search_available_at', $_POST['quick_search_available_at']);
		update_option('nomis_quick_search_min_price_steps', $_POST['quick_search_min_price_steps']);
		update_option('nomis_quick_search_max_price_steps', $_POST['quick_search_max_price_steps']);
		update_option('nomis_quick_search_garden', $_POST['quick_search_garden']);
		update_option('nomis_quick_search_balcony', $_POST['quick_search_balcony']);
		update_option('nomis_quick_search_elevator', $_POST['quick_search_elevator']);
		update_option('nomis_quick_search_parking', $_POST['quick_search_parking']);

		update_option('nomis_random_properties_link', $_POST['random_properties_link']);
		update_option('nomis_random_properties_title', $_POST['random_properties_title']);
		update_option('nomis_random_properties_photo', $_POST['random_properties_photo']);

		update_option('nomis_disable_mobile', $_POST['disable_mobile']);

		update_option('nomis_properties_order', $_POST['properties_order_type'] . '=' . $_POST['properties_order_ascdesc']);
	}
}


// If there are no entries in the db for which details, use default array to set in db
foreach ($requiresDetails as $key => $value)
{
	if (!get_option($value))
	{
		$tmp = array();
		foreach ($nomisDetails as $detail => $checked)
		{
			$tmp[$detail] = $checked[$key];
		}
		update_option($value, $tmp);
	}
}

// If there are no entries in the db for the SALE details...
foreach ($requiresSaleDetails as $key => $value)
{
	if (!get_option($value))
	{
		$tmp = array();
		foreach ($nomisSaleDetails as $categoryName => $categoryDetails)
		{
			foreach ($categoryDetails as $detail => $checked)
			{
				$tmp[$categoryName][$detail] = $checked[$key];
			}
		}
		update_option($value, $tmp);
	}
}

// If there are no entries in the db for the BOG details...
foreach ($requiresBogDetails as $key => $value)
{
	if (!get_option($value))
	{
		$tmp = array();
		foreach ($nomisBogDetails as $categoryName => $categoryDetails)
		{
			foreach ($categoryDetails as $detail => $checked)
			{
				$tmp[$categoryName][$detail] = $checked[$key];
			}
		}
		update_option($value, $tmp);
	}
}

// Build values for sorting of the details (after sorting this is done by javascript)
$detailsOrder = array();
foreach ($requiresDetails as $value)
{
	foreach (get_option($value) as $detail => $checked)
	{
		$detailsOrder[$value][] = $detail;
	}
	$detailsOrder[$value] = implode(',', $detailsOrder[$value]);
}

// Build values for sorting of the SALE details (after sorting this is done by javascript)
$saleDetailsOrder = array();
foreach ($requiresSaleDetails as $value)
{
	foreach (get_option($value) as $categoryName => $categoryDetails)
	{
		foreach ($categoryDetails as $detail => $checked)
		{
			$saleDetailsOrder[$value][] = $detail;
		}
	}
	$saleDetailsOrder[$value] = implode(',', $saleDetailsOrder[$value]);
}

// Build values for sorting of the BOG details (after sorting this is done by javascript)
$bogDetailsOrder = array();
foreach ($requiresBogDetails as $value)
{
	foreach (get_option($value) as $categoryName => $categoryDetails)
	{
		foreach ($categoryDetails as $detail => $checked)
		{
			$bogDetailsOrder[$value][] = $detail;
		}
	}
	$bogDetailsOrder[$value] = implode(',', $bogDetailsOrder[$value]);
}

// If there are no entries in db for search criteria, use default array to set them in the db
foreach ($requiresSearchCriteria as $key => $value)
{
	if (!get_option($value))
	{
		$tmp = array();
		foreach ($nomisSearchCriteria as $detail => $checked)
		{
			$tmp[$detail] = $checked[$key];
		}
		update_option($value, $tmp);
	}
}

// Build values for sorting of the search criteria (after sorting this is done by javascript)
$searchCriteriaOrder = array();
foreach ($requiresSearchCriteria as $value)
{
	foreach (get_option($value) as $criterium => $checked)
	{
		$searchCriteriaOrder[$value][] = $criterium;
	}
	$searchCriteriaOrder[$value] = implode(',', $searchCriteriaOrder[$value]);
}

$htmlChecked = 'checked="checked" ';
$htmlSelected = 'selected="selected" ';

$libApi = new Nomis_Api(get_option('nomis_api_key'));
?>

<div class="wrap">
	<form method="post" action="" id="nomis-plugin-form">
	<input type="hidden" name="nomis-submit" value="Y" />

	<br />
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2><?php _e('Settings'); ?> | Nomis <input id="nomis-submit" class="button-primary" type="submit" value="<?php _e('Save changes', 'nomis'); ?>" /></h2>

	<div id="tabs">
		<script type="text/javascript">
			$(document).ready(function()
			{
				var first = true;
				$('#tabs').tabs({
					show: function(event, ui)
					{
						if (ui.index != 0 || first == false)
						{
							var scrollmem = $('body').scrollTop()
							window.location.hash = ui.panel.id;
							$('html,body').scrollTop(scrollmem);
						}
						first = false;
					}
				});

				$.address.change(function(ev)
				{
					if (ev.path != '' && ev.path != '/')
					{
						$('#tabs').tabs('select', ev.path);
					}
					else if (ev.path == '/')
					{
						first = true;
						$('#tabs').tabs('select', 0);
					}
				});
			});
		</script>
		<ul id="tabs-nav">
			<li><a href="#general"><?php _e('General', 'nomis'); ?></a></li>
			<?php if ($libApi->checkApiKey()): ?>
			<li><a href="#individual-property"><?php _e('Individual property', 'nomis'); ?></a></li>
			<li><a href="#search"><?php _e('Search', 'nomis'); ?></a></li>
			<li><a href="#quick-search-form"><?php _e('Quick search form', 'nomis'); ?></a></li>
			<li><a href="#searchresults"><?php _e('Searchresults', 'nomis'); ?></a></li>
			<li><a href="#random-properties"><?php _e('Random properties', 'nomis'); ?></a></li>
			<li><a href="#forms"><?php _e('Forms', 'nomis'); ?></a></li>
			<li><a href="#mobile"><?php _e('Mobile', 'nomis'); ?></a></li>
			<li><a href="#help"><?php _e('Help', 'nomis'); ?></a></li>
			<?php endif; ?>
		</ul>

		<div id="general">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label for="api-key"><?php _e('Api key', 'nomis'); ?></label>
					</th>
					<td>
						<?php $apiKey = get_option('nomis_api_key'); ?>
						<input style="width:300px" type="text" name="api-key" id="api-key" value="<?php echo $apiKey; ?>" />
						<?php if ($libApi->checkApiKey()): ?>
						<input name="api-key-valid" value="Y" type="hidden" />
						<span class="description green"><?php _e('Verified', 'nomis'); ?></span>
						<?php elseif (!$libApi->checkApiKey() && !empty($apiKey)): ?>
						<span class="description red"><?php _e('Incorrect', 'nomis'); ?></span>
						<?php endif; ?>
					</td>
				</tr>
				<?php if ($libApi->checkApiKey()): ?>
				<tr valign="top">
					<th scope="row">
						<label for="google_analytics_key">
							<?php _e('Google analytics key', 'nomis'); ?>
						</label>
					</th>
					<td>
						<input type="text" id="google_analytics_key" name="google_analytics_key" value="<?php echo get_option('nomis_google_analytics_key'); ?>" />
						<span class="description"><?php _e('Contact Anno MMX to obtain this key.', 'nomis'); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						&nbsp;
					</th>
					<td>
						<a href="?page=nomis-options&amp;clearcache=true"><?php _e('Clear cache', 'nomis'); ?></a>
					</td>
				</tr>
				<?php else: ?>
				<p><?php _e('You have to enter a valid API key to gain access to all the options.', 'nomis'); ?></p>
				<?php endif; ?>
			</table>
		</div>

		<?php if ($libApi->checkApiKey()): ?>

		<div id="individual-property">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<?php _e('Layout', 'nomis'); ?>
					</th>
					<td>
						<ul id="property-layout">
							<li>
								<input id="property-layout-1" type="radio" name="property_layout" value="1"<?php if (get_option('nomis_property_layout') == '1') echo ' checked="checked"'; ?> />
								<label for="property-layout-1">
									<img src="<?php echo WP_PLUGIN_URL; ?>/nomis/images/property_template_1.gif" alt="Layout 1" />
								</label>
							</li>
							<li>
								<input id="property-layout-2" type="radio" name="property_layout" value="2"<?php if (get_option('nomis_property_layout') == '2') echo ' checked="checked"'; ?> />
								<label for="property-layout-2">
									<img src="<?php echo WP_PLUGIN_URL; ?>/nomis/images/property_template_2.gif" alt="Layout 2" />
								</label>
							</li>
							<li>
								<input id="property-layout-3" type="radio" name="property_layout" value="3"<?php if (get_option('nomis_property_layout') == '3') echo ' checked="checked"'; ?> />
								<label for="property-layout-3">
									<img src="<?php echo WP_PLUGIN_URL; ?>/nomis/images/property_template_3.gif" alt="Layout 3" />
								</label>
							</li>
						</ul>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Actions', 'nomis'); ?></th>
					<td>
						<ul>
							<li>
								<input type="checkbox" name="property_back" id="property_back" value="1" <?php if (get_option('nomis_property_back') == '1') echo $htmlChecked; ?>/>
								<label for="property_back">
									<?php _e('Back to search results button', 'nomis'); ?>
								</label>
							</li>
							<li>
								<input type="checkbox" name="property_contact" id="property_contact" value="1" <?php if (get_option('nomis_property_contact') == '1') echo $htmlChecked; ?>/>
								<label for="property_contact">
									<?php _e('Contact us about this house button', 'nomis'); ?>
								</label>
							</li>
							<li>
								<input type="checkbox" name="property_addthis" id="property_addthis" value="1" <?php if (get_option('nomis_property_addthis') == '1') echo $htmlChecked; ?>/>
								<label for="property_addthis">
									<?php _e('AddThis button', 'nomis'); ?>
									<span class="description">(<?php _e('Will display buttons to share on Facebook, Hyves etc.', 'nomis'); ?>)</span>
								</label>
							</li>
							<li>
								<input type="checkbox" name="property_mailtofriend" id="property_mailtofriend" value="1" <?php if (get_option('nomis_property_mailtofriend') == '1') echo $htmlChecked; ?>/>
								<label for="property_mailtofriend">
									<?php _e('Mail to friend link', 'nomis'); ?>
								</label>
							</li>
							<li>
								<input type="checkbox" name="property_print" id="property_print" value="1" <?php if (get_option('nomis_property_print') == '1') echo $htmlChecked; ?>/>
								<label for="property_print">
									<?php _e('Print button', 'nomis'); ?>
								</label>
							</li>
						</ul>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="property_contact_email"><?php _e('Contact email', 'nomis'); ?></label>
					</th>
					<td>
						<input type="text" name="property_contact_email" id="property_contact_email" value="<?php echo get_option('nomis_property_contact_email'); ?>" />
						<span class="description"><?php _e('This emailaddress will be used for contact about property requests', 'nomis'); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Location', 'nomis'); ?></th>
					<td>
						<ul>
							<li>
								<input type="checkbox" name="property_googlemaps" id="googlemaps" value="1" <?php if (get_option('nomis_property_googlemaps') == '1') echo $htmlChecked; ?>/>
								<label for="googlemaps"><?php _e('Display Google Maps', 'nomis'); ?></label>
							</li>
							<li>
								<input type="checkbox" name="property_streetview" id="streetview" value="1" <?php if (get_option('nomis_property_streetview') == '1') echo $htmlChecked; ?>/>
								<label for="streetview"><?php _e('Display Google Streetview', 'nomis'); ?></label>
							</li>
						</ul>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label>
							<?php _e('Rent details display and order', 'nomis'); ?>
						</label>
					</th>
					<td>
						<input type="hidden" name="nomis_property_details_order" id="property-details-order" value="<?php echo $detailsOrder['nomis_property_details']; ?>" />
						<ul id="nomis-property-details" class="property-details details">
							<?php $i = 1; foreach (get_option('nomis_property_details') as $detail => $checked): ?>
							<li class="clearfix" id="<?php echo $detail . '_' . $i; ?>">
								<span class="ui-icon ui-icon-arrowthick-2-n-s sortable handle"></span>
								<span class="number"><?php echo $i; ?></span>
								<input type="checkbox" name="nomis_property_details_<?php echo $detail; ?>" id="nomis_property_details_<?php echo $detail; ?>" value="1" <?php echo $checked == 1 ? 'checked="checked" ' : ''; ?>/>
								<label for="nomis_property_details_<?php echo $detail; ?>">
									<?php echo _e(ucfirst(str_replace('_', ' ', $detail)), 'nomis'); ?>
								</label>
							</li>
							<?php $i ++; endforeach; ?>
						</ul>
					</td>
				</tr>
				<tr valign="top" id="sale-details">
					<th scope="row">
						<label>
							<?php _e('Properties for sale', 'nomis'); ?>
						</label>
					</th>
					<td>
						<input type="checkbox" name="property_different_sale_details" id="property_different_sale_details" value="1" <?php if (get_option('nomis_property_different_sale_details') == '1') echo $htmlChecked; ?>/>
						<label for="property_different_sale_details"><?php _e('Use different list of details for properties that are for sale', 'nomis'); ?> <span class="description">(<?php _e('The list of details below is used', 'nomis'); ?>)</span>.</label>
					</td>
				</tr>

				<tr valign="top" id="sale-details">
					<th scope="row">
						<label>
							<?php _e('Sale details display and order', 'nomis'); ?>
						</label>
					</th>
					<td>
						<input type="hidden" name="nomis_property_sale_details_order" id="property-sale-details-order" value="<?php echo $saleDetailsOrder['nomis_property_sale_details']; ?>" />
						<ul id="nomis-property-sale-details">
							<?php $i = 1; foreach (get_option('nomis_property_sale_details') as $categoryName => $categoryDetails): ?>
							<li id="<?php echo $categoryName; ?>" class="details-category">
								<h4>
									<input type="checkbox" class="uncheck-all" />
									<span class="ui-icon ui-icon-arrowthick-2-n-s sortable handle"></span>
									<?php _e(ucfirst(str_replace('_', ' ', $categoryName)), 'nomis'); ?>
								</h4>
								<ul>
									<?php foreach ($categoryDetails as $detail => $checked): ?>
									<li class="clearfix" id="<?php echo $detail . '_' . $i; ?>">
										<input type="hidden" name="nomis_property_sale_details[<?php echo $categoryName . '][' . $detail; ?>]" id="nomis_property_sale_details_<?php echo $detail; ?>" value="0" />
										<input type="checkbox" name="nomis_property_sale_details[<?php echo $categoryName . '][' . $detail; ?>]" id="nomis_property_sale_details_<?php echo $detail; ?>" value="1" <?php echo $checked == 1 ? 'checked="checked" ' : ''; ?>/>
										<span class="ui-icon ui-icon-arrowthick-2-n-s sortable handle"></span>
										<label for="nomis_property_sale_details_<?php echo $detail; ?>">
											<?php echo _e(ucfirst(str_replace('_', ' ', str_replace('forsale_', '', $detail))), 'nomis'); ?>
										</label>
									</li>
									<?php $i ++; endforeach; ?>
								</ul>
							</li>
							<?php endforeach; ?>
						</ul>
					</td>
				</tr>

				<tr valign="top" id="bog-details">
					<th scope="row">
						<label>
							<?php _e('BOG details display and order', 'nomis'); ?>
						</label>
					</th>
					<td>
						<input type="hidden" name="nomis_property_bog_details_order" id="property-bog-details-order" value="<?php echo $bogDetailsOrder['nomis_property_bog_details']; ?>" />
						<ul id="nomis-property-bog-details">
							<?php $i = 1; foreach (get_option('nomis_property_bog_details') as $categoryName => $categoryDetails): ?>
							<li id="<?php echo $categoryName; ?>" class="details-category">
								<h4>
									<input type="checkbox" class="uncheck-all" />
									<span class="ui-icon ui-icon-arrowthick-2-n-s sortable handle"></span>
									<?php _e(ucfirst(str_replace('_', ' ', $categoryName)), 'nomis'); ?>
								</h4>
								<ul>
									<?php foreach ($categoryDetails as $detail => $checked): ?>
									<li class="clearfix" id="<?php echo $detail . '_' . $i; ?>">
										<input type="hidden" name="nomis_property_bog_details[<?php echo $categoryName . '][' . $detail; ?>]" id="nomis_property_bog_details_<?php echo $detail; ?>" value="0" />
										<input type="checkbox" name="nomis_property_bog_details[<?php echo $categoryName . '][' . $detail; ?>]" id="nomis_property_bog_details_<?php echo $detail; ?>" value="1" <?php echo $checked == 1 ? 'checked="checked" ' : ''; ?>/>
										<span class="ui-icon ui-icon-arrowthick-2-n-s sortable handle"></span>
										<label for="nomis_property_bog_details_<?php echo $detail; ?>">
											<?php echo _e(ucfirst(str_replace('_', ' ', str_replace('bog_', '', $detail))), 'nomis'); ?>
										</label>
									</li>
									<?php $i ++; endforeach; ?>
								</ul>
							</li>
							<?php endforeach; ?>
						</ul>
					</td>
				</tr>
			</table>
		</div>

		<div id="search">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label>
							<?php _e('Search options', 'nomis'); ?>
						</label>
					</th>
					<td>
						<ul>
							<li>
								<input type="checkbox" name="search_display_labels" id="search_display_labels" value="1" <?php if (get_option('nomis_search_display_labels') == '1') echo $htmlChecked; ?> />
								<label for="search_display_labels"><?php _e('Display labels', 'nomis'); ?></label>
							</li>
						</ul>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Search criteria', 'nomis'); ?></th>
					<td>
						<ul class="search-criteria" id="search-criteria">
							<input type="hidden" id="nomis-search-criteria" name="nomis_search_criteria" value="<?php echo $searchCriteriaOrder['nomis_search_criteria']; ?>"/>
							<?php $i = 0; foreach (get_option('nomis_search_criteria') as $criterium => $checked): $i++; ?>
							<li class="clearfix" id="<?php echo $criterium . '_' . $i; ?>">
								<span class="ui-icon ui-icon-arrowthick-2-n-s sortable handle"></span>
								<span class="number"><?php echo $i; ?></span>
								<input type="checkbox" name="nomis_search_criteria_<?php echo $criterium; ?>" id="search_<?php echo $criterium; ?>" value="1" <?php echo $checked == 1 ? 'checked="checked" ' : ''; ?>/>
								<label for="search_<?php echo $criterium; ?>">
									<?php _e(ucfirst(str_replace('_', ' ', $criterium)), 'nomis'); ?>
								</label>
							</li>
							<?php endforeach; ?>
						</ul>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Minimum price steps', 'nomis'); ?></th>
					<td>
						<input type="text" name="search_min_price_steps" value="<?php echo get_option('nomis_search_min_price_steps'); ?>" />
						<span class="description"><?php _e('Enter prices separated by comma\'s', 'nomis'); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Maximum price steps', 'nomis'); ?></th>
					<td>
						<input type="text" name="search_max_price_steps" value="<?php echo get_option('nomis_search_max_price_steps'); ?>" />
						<span class="description"><?php _e('Enter prices separated by comma\'s', 'nomis'); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Search criteria sale', 'nomis'); ?><br /><span class="description">[nomis-properties type=&quot;sale&quot;]</span></th>
					<td>
						<ul class="search-criteria" id="search-criteria-sale">
							<input type="hidden" id="nomis-search-criteria-sale" name="nomis_search_criteria_sale" value="<?php echo $searchCriteriaOrder['nomis_search_criteria_sale']; ?>"/>
							<?php $i = 0; foreach (get_option('nomis_search_criteria_sale') as $criterium => $checked): $i++; ?>
							<li class="clearfix" id="<?php echo $criterium . '_' . $i; ?>">
								<span class="ui-icon ui-icon-arrowthick-2-n-s sortable handle"></span>
								<span class="number"><?php echo $i; ?></span>
								<input type="checkbox" name="nomis_search_criteria_sale_<?php echo $criterium; ?>" id="search_sale_<?php echo $criterium; ?>" value="1" <?php echo $checked == 1 ? 'checked="checked" ' : ''; ?>/>
								<label for="search_sale_<?php echo $criterium; ?>">
									<?php _e(ucfirst(str_replace('_', ' ', $criterium)), 'nomis'); ?>
								</label>
							</li>
							<?php endforeach; ?>
						</ul>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Minimum price steps sale', 'nomis'); ?></th>
					<td>
						<input type="text" name="search_min_price_steps_sale" value="<?php echo get_option('nomis_search_min_price_steps_sale'); ?>" />
						<span class="description"><?php _e('Enter prices separated by comma\'s', 'nomis'); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Maximum price steps sale', 'nomis'); ?></th>
					<td>
						<input type="text" name="search_max_price_steps_sale" value="<?php echo get_option('nomis_search_max_price_steps_sale'); ?>" />
						<span class="description"><?php _e('Enter prices separated by comma\'s', 'nomis'); ?></span>
					</td>
				</tr>
			</table>
		</div>

		<div id="quick-search-form">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label for="order">
							<?php _e('Search options', 'nomis'); ?>
						</label>
					</th>
					<td>
						<ul>
							<li>
								<input type="checkbox" name="quick_search_display_labels" id="quick_search_display_labels" value="1" <?php if (get_option('nomis_quick_search_display_labels') == '1') echo $htmlChecked; ?> />
								<label for="quick_search_display_labels"><?php _e('Display labels', 'nomis'); ?></label>
							</li>
						</ul>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="quick_search_action">
							<?php _e('Search results page id', 'nomis'); ?>
						</label>
					</th>
					<td>
						<input type="text" name="quick_search_action" id="quick_search_action" value="<?php echo get_option('nomis_quick_search_action'); ?>" />
						<span class="description"><?php _e('This is where the form action points to. Should be the id of the page where you use the shortcode [nomis-properties].', 'nomis'); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Search criteria', 'nomis'); ?></th>
					<td>
						<ul class="search-criteria" id="quick-search-criteria">
							<input type="hidden" id="nomis-quick-search-criteria" name="nomis_quick_search_criteria" value="<?php echo $searchCriteriaOrder['nomis_quick_search_criteria']; ?>"/>
							<?php $i = 0; foreach (get_option('nomis_quick_search_criteria') as $criterium => $checked): $i++; ?>
							<li class="clearfix" id="<?php echo $criterium . '_' . $i; ?>">
								<span class="ui-icon ui-icon-arrowthick-2-n-s sortable handle"></span>
								<span class="number"><?php echo $i; ?></span>
								<input type="checkbox" name="nomis_quick_search_criteria_<?php echo $criterium; ?>" id="quick_search_<?php echo $criterium; ?>" value="1" <?php echo $checked == 1 ? 'checked="checked" ' : ''; ?>/>
								<label for="quick_search_<?php echo $criterium; ?>">
									<?php _e(ucfirst(str_replace('_', ' ', $criterium)), 'nomis'); ?>
								</label>
							</li>
							<?php endforeach; ?>
						</ul>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Minimum price steps', 'nomis'); ?></th>
					<td>
						<input type="text" name="quick_search_min_price_steps" value="<?php echo get_option('nomis_quick_search_min_price_steps'); ?>" />
						<span class="description"><?php _e('Enter prices separated by comma\'s', 'nomis'); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Maximum price steps', 'nomis'); ?></th>
					<td>
						<input type="text" name="quick_search_max_price_steps" value="<?php echo get_option('nomis_quick_search_max_price_steps'); ?>" />
						<span class="description"><?php _e('Enter prices separated by comma\'s', 'nomis'); ?></span>
					</td>
				</tr>
			</table>
		</div>

		<div id="searchresults">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label for="api-key">
							<?php _e('Details display and order', 'nomis'); ?>
						</label>
					</th>
					<td>
						<input type="hidden" name="nomis_properties_details_order" id="properties-details-order" value="<?php echo $detailsOrder['nomis_properties_details']; ?>" />
						<ul id="nomis-properties-details" class="properties-details details">
							<?php $i = 1; foreach (get_option('nomis_properties_details') as $detail => $checked): ?>
							<li class="clearfix" id="<?php echo $detail . '_' . $i; ?>">
								<span class="ui-icon ui-icon-arrowthick-2-n-s sortable handle"></span>
								<span class="number"><?php echo $i; ?></span>
								<input type="checkbox" name="nomis_properties_details_<?php echo $detail; ?>" id="nomis_properties_details_<?php echo $detail; ?>" value="1" <?php echo $checked == 1 ? 'checked="checked" ' : ''; ?>/>
								<label for="nomis_properties_details_<?php echo $detail; ?>">
									<?php echo _e(ucfirst(str_replace('_', ' ', $detail)), 'nomis'); ?>
								</label>
							</li>
							<?php $i ++; endforeach; ?>
						</ul>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label>
							<?php _e('Order by', 'nomis'); ?>
						</label>
					</th>
					<td>
						<?php
						list($orderType, $orderAscdesc) = explode('=', get_option('nomis_properties_order'));
						?>
						<select name="properties_order_type">
							<option <?php echo $orderType == 'price' ? $htmlSelected : ''; ?> value="price"><?php _e('Price', 'nomis'); ?></option>
							<option <?php echo $orderType == 'date' ? $htmlSelected : ''; ?> value="date"><?php _e('Date', 'nomis'); ?></option>
							<option <?php echo $orderType == 'city' ? $htmlSelected : ''; ?> value="city"><?php _e('City', 'nomis'); ?></option>
							<option <?php echo $orderType == 'street' ? $htmlSelected : ''; ?> value="street"><?php _e('Street', 'nomis'); ?></option>
							<option <?php echo $orderType == 'district' ? $htmlSelected : ''; ?> value="district"><?php _e('District', 'nomis'); ?></option>
							<option <?php echo $orderType == 'bedrooms' ? $htmlSelected : ''; ?> value="bedrooms"><?php _e('Bedrooms', 'nomis'); ?></option>
							<option <?php echo $orderType == 'forsale_price' ? $htmlSelected : ''; ?> value="forsale_price"><?php _e('Saleprice', 'nomis'); ?></option>
							<option <?php echo $orderType == 'available' ? $htmlSelected : ''; ?> value="available"><?php _e('Availability', 'nomis'); ?></option>
							<option <?php echo $orderType == 'random' ? $htmlSelected : ''; ?> value="random"><?php _e('Random', 'nomis'); ?></option>
						</select>
						<select name="properties_order_ascdesc">
							<option <?php echo $orderAscdesc == 'asc' ? $htmlSelected : ''; ?> value="asc"><?php _e('Ascending', 'nomis'); ?></option>
							<option <?php echo $orderAscdesc == 'desc' ? $htmlSelected : ''; ?> value="desc"><?php _e('Descending', 'nomis'); ?></option>
						</select>
					</td>
				</tr>
			</table>
		</div>

		<div id="random-properties">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Options', 'nomis'); ?></th>
					<td>
						<ul>
							<li>
								<input type="checkbox" name="random_properties_link" id="random_properties_link" value="1" <?php if (get_option('nomis_random_properties_link') == '1') echo $htmlChecked; ?>/>
								<label for="random_properties_link"><?php _e('Display link', 'nomis'); ?></label>
							</li>
							<li>
								<input type="checkbox" name="random_properties_title" id="random_properties_title" value="1" <?php if (get_option('nomis_random_properties_title') == '1') echo $htmlChecked; ?>/>
								<label for="random_properties_title"><?php _e('Display title', 'nomis'); ?></label>
							</li>
							<li>
								<input type="checkbox" name="random_properties_photo" id="random_properties_photo" value="1" <?php if (get_option('nomis_random_properties_photo') == '1') echo $htmlChecked; ?>/>
								<label for="random_properties_photo"><?php _e('Display photo', 'nomis'); ?></label>
							</li>
						</ul>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<?php _e('Details display and order', 'nomis'); ?>
					</th>
					<td>
						<input type="hidden" name="nomis_random_properties_details_order" id="nomis-properties-random-details-order" value="<?php echo $detailsOrder['nomis_random_properties_details']; ?>" />
						<ul id="nomis-random-properties-details" class="nomis-random-properties-details details">
							<?php $i = 1; foreach (get_option('nomis_random_properties_details') as $detail => $checked): ?>
							<li class="clearfix" id="<?php echo $detail . '_' . $i; ?>">
								<span class="ui-icon ui-icon-arrowthick-2-n-s sortable handle"></span>
								<span class="number"><?php echo $i; ?></span>
								<input type="checkbox" name="nomis_random_properties_details_<?php echo $detail; ?>" id="nomis_random_properties_details_<?php echo $detail; ?>" value="1" <?php echo $checked == 1 ? 'checked="checked" ' : ''; ?>/>
								<label for="nomis_random_properties_details_<?php echo $detail; ?>">
									<?php echo _e(ucfirst(str_replace('_', ' ', $detail)), 'nomis'); ?>
								</label>
							</li>
							<?php $i ++; endforeach; ?>
						</ul>
					</td>
				</tr>
			</table>
		</div>

		<div id="forms">
			<p>
				<?php _e('Check out the documentation on forms first at'); ?> <a href="http://docs.annommx.com/?p=83">docs.annommx.com</a>.
			</p>
			<div id="form-field-template">
				<div class="form-field">
					<label><?php _e('Name', 'nomis'); ?></label>
					<input class="name" type="text" name="formdbname_name[]" value="name" />
					<label><?php _e('Label', 'nomis'); ?></label>
					<input class="label" type="text" name="formdbname_label[]" value="label" />
					<div class="clear"></div>
					<select name="formdbname_type[]" class="type">
						<option value="text">Text</option>
						<option value="select">Select</option>
						<option value="textarea">Textarea</option>
						<option value="checkbox">Checkbox</option>
					</select>
					<select name="formdbname_required[]" class="required">
						<option value="1"><?php _e('Required', 'nomis'); ?></option>
						<option value="0"><?php _e('Not required', 'nomis'); ?></option>
					</select>
					<label><?php _e('Description', 'nomis'); ?></label>
					<input class="description" type="text" name="formdbname_description[]" value="" />
					<span class="ui-icon ui-icon-closethick remove-form-field"></span>
					<div class="clear"></div>
				</div>
				<a href="javascript:;" class="new-form-field"><?php _e('New form field', 'nomis'); ?></a>
			</div>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Add request', 'nomis'); ?><br /><span class="description">[nomis-add-request]</span></th>
					<td id="request-form" rel="nomis_add_request_form">
						<?php nomisFormAdmin('nomis_add_request_form', 'request-form'); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Add rental declaration', 'nomis'); ?><br /><span class="description">[nomis-add-rental-declaration]</span></th>
					<td id="request-form" rel="nomis_rental_declaration_form">
						<?php nomisFormAdmin('nomis_rental_declaration_form', 'rental-declaration-form'); ?>
					</td>
				</tr>
			</table>
			<script type="text/javascript">
				$(document).ready(function()
				{
					$('#nomis-plugin-form').submit(function()
					{
						$('#form-field-template').remove();
					});
					$('.remove-form-field').live('click', function()
					{
						$(this).parent().next().remove();
						$(this).parent().remove();
					});
					$('.new-form-field').live('click', function()
					{
						var fieldTemplate = $('#form-field-template').clone();
						var inputs = fieldTemplate.find(':input');
						var clickLink = $(this);
						$.each(inputs, function(index, value)
						{
							$(value).attr('name', $(value).attr('name').replace('formdbname', clickLink.parent().parent().attr('rel')));
						});
						$(this).after(fieldTemplate.html());
					});
				});
			</script>
		</div>

		<div id="mobile">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">&nbsp;</th>
					<td id="request-form" rel="nomis_add_request_form">
						<input type="checkbox" id="disable-mobile" name="disable_mobile" value="1" <?php if (get_option('nomis_disable_mobile') == '1') echo $htmlChecked; ?> />
						<label for="disable-mobile"><?php echo __('Disable mobile version', 'nomis'); ?></label><br /><span class="description"><?php _e('When checked, the normal version of the website is also shown on mobile devices', 'nomis'); ?></span>
					</td>
				</tr>
			</table>
		</div>

		<div id="help">
			<h3><?php _e('Documentation', 'nomis'); ?></h3>
			<p>
				<a target="_blank" href="http://docs.annommx.com">docs.annommx.com</a>
			</p>
			<h3>Contact</h3>
			<p>
				<strong>Thomas Kuipers</strong><br />
				t.kuipers@annommx.com
			</p>
		</div>
		<?php endif; ?>

	</div>

	</form>

	<script src="<?php echo WP_PLUGIN_URL; ?>/nomis/javascript/nomis-admin.js" type="text/javascript"></script>

</div>