<?php
/*
Plugin Name: Nomis
Plugin URI: http://www.annommx.com/
Description: Plugin for displaying your properties out of the backoffice Nomis. Only useful for customers of Anno MMX.
Version: 1.6.4
Author: Anno MMX
Author URI: http://www.annommx.com/
License: GPL2 
Tags: 1.6.4
*/

remove_action('wp_head', 'rel_canonical');

include 'lib/api.class.php';
include 'functions/nomisDetails.func.php';
include 'functions/nomisSearch.func.php';
include 'functions/nomisFormAdmin.func.php';
include 'functions/nomisTranslateForm.func.php';
include 'functions/nomisDetectMobile.func.php';
include 'functions/nomisTrMobile.func.php';
include 'functions/nomisParseApiDetail.func.php';
include 'functions/nomisBoolean.func.php';
include 'exclude-pages/exclude_pages.php';

class NomisWordpressApi
{
	private static $_nomisApi = null;
	private static $_pluginBase = null;
	private static $_title = null;

	public static function init()
	{
		if (self::$_nomisApi === null)
		{
			try
			{
				self::$_nomisApi = new Nomis_Api(get_option('nomis_api_key'));
				self::$_nomisApi->setUseOriginalHouseId(true);
//				self::$_nomisApi->debug(Nomis_Api::DEBUG_PRINT_RESPONSE | Nomis_Api::DEBUG_IGNORE_CACHE);//15 is aggressive
//				self::$_nomisApi->debug(15);
				self::$_nomisApi->setCacheTtl(900);

				$requestForm = get_option('nomis_add_request_form');
				if (is_array($requestForm))
				{
					self::$_nomisApi->setCustomRequestForm($requestForm);
				}

				$rentalDeclarationForm = get_option('nomis_rental_declaration_form');
				if (is_array($rentalDeclarationForm))
				{
					self::$_nomisApi->setCustomRentalDeclarationForm($rentalDeclarationForm);
				}				
			}
			catch (Exception $e)
			{
			}
		}

		if (self::$_pluginBase === null)
		{
			self::$_pluginBase = WP_PLUGIN_URL . '/nomis';
		}
	}

	private static function runNomisApi($input)
	{
		try
		{
			self::$_nomisApi->setLanguage(substr(get_bloginfo('language'), 0, 2));
			return self::$_nomisApi->run($input);
		}
		catch (Exception $e)
		{
			self::$_title = __('Something went wrong', 'nomis');
			return '<p>' . __('We are very sorry for the inconvenience. Please contact the owner of this website and we will have it fixed.', 'nomis') . '</p>';
		}
	}

	public function checkApiKey()
	{
		return self::$_nomisApi->checkApiKey();
	}

	public function clearCache()
	{
		foreach (self::$_nomisApi->getDefaultDatabaseFiles() as $cacheFile)
		{
			file_put_contents($cacheFile, '');
		}
	}

	public function properties($attr, $mobile)
	{
		if ($mobile == true)
		{
			self::$_nomisApi->templateFiles['search'] = 'templates/mobile/properties.phtml';
			self::$_nomisApi->setPropertiesPerPage(999999);
			return self::runNomisApi(array_merge(
				$_GET,
				array(
					'order' => 'price=asc',
					'action' => 'search'
			)));
		}

		extract(shortcode_atts(array(
			'num' => 999999,
			'template' => null,
			'forsale' => null,
			'cities' => null,
			'notcountry' => null,
			'country' => null,
			'division' => null
		), $attr));

		self::$_nomisApi->setPropertiesPerPage((int) $num);
		$_GET['action'] = 'search';

		if ($forsale == '0')
		{
			if (!isset($_GET['min-price']) || $_GET['min-price'] == 0)
			{
				$_GET['min-price'] = 1;
			}
		}
		elseif ($forsale == '1')
		{
			$_GET['for-sale'] = 1;
		}

		if ($cities !== null)
		{
			$_GET['city'] = $cities;
		}

		if ($country !== null)
		{
			$_GET['country'] = $country;
		}

		if ($notcountry !== null)
		{
			$_GET['not-country'] = $notcountry;
		}

		if ($division !== null)
		{
			$_GET['division'] = $division;
		}

		if (isset($_GET['forsale']) && $_GET['forsale'] == '0')
		{
			unset($_GET['forsale']);

			if (!isset($_GET['min-price']) || $_GET['min-price'] == '0')
			{
				$_GET['min-price'] = 1;
			}
		}

		$order = get_option('nomis_properties_order');
		$_GET['order'] = $order != '' ? $order : 'price=asc';

		if ($template !== null 
		 && file_exists(get_template_directory() . '/'. $template))
		{
			self::$_nomisApi->templateFiles['search'] = $template;
			$originalIncludeDir = self::$_nomisApi->setIncludeDir(get_template_directory());			
			$rs = self::runNomisApi($_GET);
			self::$_nomisApi->setIncludeDir($originalIncludeDir);
			return $rs;
		}
		else
		{
			self::$_nomisApi->templateFiles['search'] = 'templates/properties.phtml';
		}
		
		$rs = self::runNomisApi($_GET);

		return $rs;
	}

	public function randomProperties($attr)
	{
		extract(shortcode_atts(array(
			'num' => 3,
			'template' => null
		), $attr));

		self::$_nomisApi->setPropertiesPerPage((int) $num);
		
		if ($template !== null 
		 && file_exists(get_template_directory() . '/'. $template))
		{
			self::$_nomisApi->templateFiles['search'] = $template;
			$originalIncludeDir = self::$_nomisApi->setIncludeDir(get_template_directory());
			$rs = self::runNomisApi(array(
				'action' => 'search',
				'order' => 'random=asc'
			));
			self::$_nomisApi->setIncludeDir($originalIncludeDir);
			return $rs;
		}
		
		self::$_nomisApi->templateFiles['search'] = 'templates/random-properties.phtml';
		return self::runNomisApi(array(
			'action' => 'search',
			'order' => 'random=asc'
		));
	}

	public function propertiesMap($attr, $mobile = false)
	{
		self::$_nomisApi->setPropertiesPerPage(999999);

		if ($mobile == true)
		{
			self::$_nomisApi->templateFiles['search'] = 'templates/mobile/properties-map.phtml';
			return self::runNomisApi(array(
				'action' => 'search'
			));
		}
		
		self::$_nomisApi->templateFiles['search'] = 'templates/properties-map.phtml';
		return self::runNomisApi(array(
			'action' => 'search'
		));
	}

	public function searchForm($attr, $mobile = false)
	{
		if ($mobile == true)
		{
			self::$_nomisApi->templateFiles['search'] = 'templates/mobile/search.phtml';
			return self::runNomisApi($_GET);
		}

		extract(shortcode_atts(array(
			'template' => null,
			'type' => null
		), $attr));
		
		$_GET['action'] = 'search';
		
		self::$_nomisApi->templateFiles['search'] = 'templates/searchform.phtml';

		if ($type == 'sale')
		{
			self::$_nomisApi->templateFiles['search'] = 'templates/searchform-sale.phtml';
		}

		if ($template !== null 
		 && file_exists(get_template_directory() . '/'. $template))
		{
			self::$_nomisApi->templateFiles['search'] = $template;
			$originalIncludeDir = self::$_nomisApi->setIncludeDir(get_template_directory());
			$rs = self::runNomisApi($_GET);
			self::$_nomisApi->setIncludeDir($originalIncludeDir);
			return $rs;
		}		
		
		return self::runNomisApi($_GET);
	}

	public function quickSearch($attr)
	{
		extract(shortcode_atts(array(
			'template' => null
		), $attr));
		
		$_GET['action'] = 'search';
		
		if ($template !== null 
		 && file_exists(get_template_directory() . '/'. $template))
		{
			self::$_nomisApi->templateFiles['search'] = $template;
			$originalIncludeDir = self::$_nomisApi->setIncludeDir(get_template_directory());
			$rs = self::runNomisApi($_GET);
			self::$_nomisApi->setIncludeDir($originalIncludeDir);
			return $rs;
		}
		
		self::$_nomisApi->templateFiles['search'] = 'templates/quick-search.phtml';		
		return self::runNomisApi($_GET);
	}

	public function property($attr, $mobile = false, $print = false)
	{
		if ($mobile == true)
		{
			self::$_nomisApi->templateFiles['property'] = 'templates/mobile/property.phtml';
			return self::runNomisApi(array(
				'action' => 'property',
				'id' => $_GET['id']
			));
		}
		
		if ($print === true)
		{
			self::$_nomisApi->templateFiles['property'] = 'templates/property-print.phtml';
			return self::runNomisApi(array(
				'action' => 'property',
				'id' => $_GET['id']
			));
		}

		extract(shortcode_atts(array(
			'template' => null
		), $attr));

		global $wp_query;

		if (empty($wp_query->query_vars['propertyid']))
		{
			$wp_query->set_404();
			self::$_title = __('Something went wrong', 'nomis');
			return '<p>' . __('We are very sorry for the inconvenience. Please contact the owner of this website and we will have it fixed.', 'nomis') . '</p>';
		}

		if ($template !== null 
		 && file_exists(get_template_directory() . '/'. $template))
		{
			self::$_nomisApi->templateFiles['property'] = $template;
			$originalIncludeDir = self::$_nomisApi->setIncludeDir(get_template_directory());
			$rs = self::runNomisApi(array(
				'action' => 'property',
				'id' => $wp_query->query_vars['propertyid']
			));
			self::$_nomisApi->setIncludeDir($originalIncludeDir);
			return $rs;
		}

		return self::runNomisApi(array(
			'action' => 'property',
			'id' => $wp_query->query_vars['propertyid']
		));
	}

	public function addRentalDeclaration()
	{
		return self::runNomisApi(array(
			'action' => 'rental-declaration'
		));
	}

	public function addRequest()
	{
		return self::runNomisApi(array(
			'action' => 'add-single-request'
		));
	}

	public function translate()
	{
		load_plugin_textdomain('nomis', false, '/nomis/languages/');
	}

	public function addGoogleAnalytics()
	{
		?>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', '<?php echo get_option('nomis_google_analytics_key'); ?>']);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
		<?php
	}

	public function admin()
	{
		if (!current_user_can('manage_options'))
		{
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}

		include 'nomis-admin.php';
	}

	public function adminHead()
	{
		wp_enqueue_style('jquery-ui', self::$_pluginBase . '/stylesheets/jquery-ui-1.8.7.custom.css');
		wp_enqueue_style('nomis-admin', self::$_pluginBase . '/stylesheets/nomis-admin.css');
		if (!wp_script_is('jquery', 'done'))
		{
			wp_deregister_script('jquery-nomis');
			wp_register_script('jquery-nomis', 'http://static.annommx.com/javascript/jquery-1.4.4.min.js');
			wp_enqueue_script('jquery-nomis');
		}
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-easy-list-splitter', self::$_pluginBase . '/javascript/jquery.easyListSplitter.js', array('jquery'));
		wp_enqueue_script('jquery-address', 'http://static.annommx.com/javascript/jquery.address.js');
	}

	public function frontHead()
	{
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo self::$_pluginBase; ?>/stylesheets/nomis-frontend.css" />
		<link rel="stylesheet" type="text/css" href="http://static.annommx.com/stylesheets/jquery.fancybox.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo self::$_pluginBase; ?>/stylesheets/jquery-ui-1.8.7.custom.css" />
		<script type="text/javascript" src="http://static.annommx.com/javascript/jquery-1.4.4.min.js"></script>
		<script type="text/javascript" src="<?php echo self::$_pluginBase; ?>/javascript/jquery.fancybox.js"></script>
		<script type="text/javascript" src="<?php echo self::$_pluginBase; ?>/javascript/maps.js"></script>
		<?php
	}

	public function propertyTitle($title, $id = 0)
	{
		global $wp_query;

		if (!isset($wp_query->query_vars['propertyid']))
		{
			return $title;
		}

		self::$_nomisApi->setRenderMode(false);
		$house = self::runNomisApi(array(
			'action' => 'property',
			'id' => $wp_query->query_vars['propertyid']
		));
		self::$_nomisApi->setRenderMode(true);

		if (!empty(self::$_title) && get_the_ID() == $id)
		{
			return self::$_title;
		}

		if (isset($wp_query->query_vars['propertyid']))
		{			
			if (isset($house['property']) && get_the_ID() == $id)
			{
				return htmlentities($house['property']['street'] . ', ' . $house['property']['city'], ENT_QUOTES, 'UTF-8');
			}
		}
		return $title;
	}

	public function changeTitle()
	{
		add_filter('the_title', array('NomisWordpressApi', 'propertyTitle'), 10, 10);
	}

	public function propertyTitleTag($title)
	{
		if (stripos($title, 'property') !== 0)
		{
			return $title;
		}

		global $wp_query;

		self::$_nomisApi->setRenderMode(false);
		$house = self::runNomisApi(array(
			'action' => 'property',
			'id' => $wp_query->query_vars['propertyid']
		));
		self::$_nomisApi->setRenderMode(true);

		return htmlentities($house['property']['street'] . ', ' . $house['property']['city'] . ' | ', ENT_QUOTES, 'UTF-8');
	}

	public function adminActions()
	{
		add_options_page('Nomis', 'Nomis', 'manage_options', 'nomis-options', array('NomisWordpressApi', 'admin'));
	}

	public function insertRewriteRules($rules)
	{
		$newrules = array();
		$newrules['property/([^/]+)/([^/]+)/([^/]+)/?$'] = 'index.php?pagename=property&propertyid=$matches[3]';
//		$newrules['property/([^/]+)/([^/]+)/([^/]+)/\?a=(.*)$'] = 'index.php?pagename=property&propertyid=$matches[3]&a=$matches[4]';

		//WPML
		$newrules['property-en/([^/]+)/([^/]+)/([^/]+)/?$'] = 'index.php?pagename=property-en&propertyid=$matches[3]';
		$newrules['property-nl/([^/]+)/([^/]+)/([^/]+)/?$'] = 'index.php?pagename=property-nl&propertyid=$matches[3]';
		return array_merge($newrules, $rules);
	}

	// Remember to flush_rules() when adding rules
	public function flushRules()
	{
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	// Adding the id var so that WP recognizes it
	public function insertRewriteQueryVars($vars)
	{
		array_push($vars, 'propertyid');
		return $vars;
	}

	public function wpmlLanguageSelector()
	{
		ob_start();
		include dirname(__FILE__) . '/wpml-language-selector.php';
		$selector = ob_get_contents();
		ob_end_clean();
		return $selector;
	}

	public function contactform()
	{
		return self::runNomisApi(array(
			'action' => 'contact'
		));
	}
}

function tr(array $translate)
{
	/*
	 * echo _('en' => 'hello', 'nl' => 'hoi');
	 */
	foreach ($translate as $lang => $string)
	{
		$return = '<!--:' . $lang . '-->' . $string . '<!--:-->';
	}

	return __($return);
}


NomisWordpressApi::init();

if (isset($_GET['print-property']) && $_GET['print-property'] == true)
{
	$a = isset($_GET['a']) ? $_GET['a'] : 'index';
	$_GET['lang'] = isset($_GET['lang']) ? $_GET['lang'] : 'nl';

	if ($_GET['lang'] != 'nl') //nl is default language, for some strange reason you cant set it to nl or nl_NL
	{
		global $locale;
		$locale = $_GET['lang'];
	}

	load_plugin_textdomain('nomis', false, '/nomis/languages/');

	echo NomisWordpressApi::property('', false, true);

	die();
}

if (nomisDetectMobile(true, false) != false
 && get_option('nomis_disable_mobile') != '1')
{
	$a = isset($_GET['a']) ? $_GET['a'] : 'index';
	$_GET['lang'] = isset($_GET['lang']) ? $_GET['lang'] : 'nl_NL';

	if ($_GET['lang'] != 'nll') //nl is default language, for some strange reason you cant set it to nl or nl_NL
	{
		global $locale;
		$locale = $_GET['lang'];
	}
	
	load_plugin_textdomain('nomis', false, '/nomis/languages/');
	
	//*
	$api = new Nomis_Api(get_option('nomis_api_key'));
	$api->setLanguage(substr($locale, 0, 2));
	$api->setIncludeDir(dirname(__FILE__) . '/templates/mobile2');
	
	$api->setCustomRequestForm(array());
	$api->setCustomRentalDeclarationForm(array());
	
	$api->setPropertiesPerPage(1000);
	
	if (!isset($_GET['action']))
	{
		$api->templateFiles['search'] = 'templates/index.phtml';
	}
	else
	{
		$api->templateFiles['search'] = 'templates/search.phtml';
	}
	
	include 'templates/mobile2/_wrapper.php';
	
	die();
	//*/

	switch ($a)
	{
		case 'properties':
			echo NomisWordpressApi::properties('', true);
			break;

		case 'property':
			echo NomisWordpressApi::property('', true);
			break;

		case 'map':
			echo NomisWordpressApi::propertiesMap('', true);
			break;

		default:
			echo NomisWordpressApi::searchForm('', true);
			break;
	}
	die();
}

add_shortcode('nomis-properties', array('NomisWordpressApi', 'properties'));
add_shortcode('nomis-searchform', array('NomisWordpressApi', 'searchform'));
add_shortcode('nomis-property', array('NomisWordpressApi', 'property'));
add_shortcode('nomis-add-request', array('NomisWordpressApi', 'addRequest'));
add_shortcode('nomis-add-rental-declaration', array('NomisWordpressApi', 'addRentalDeclaration'));
add_shortcode('nomis-quick-search', array('NomisWordpressApi', 'quickSearch'));
add_shortcode('nomis-random-properties', array('NomisWordpressApi', 'randomProperties'));
add_shortcode('nomis-map', array('NomisWordpressApi', 'propertiesMap'));
add_shortcode('nomis-wpml-language-selector', array('NomisWordpressApi', 'wpmlLanguageSelector'));
add_shortcode('nomis-contactform', array('NomisWordpressApi', 'contactform'));

add_action('init', array('NomisWordpressApi', 'translate'));
add_action('wp_head', array('NomisWordpressApi', 'frontHead'));
add_action('wp_head', array('NomisWordpressApi', 'changeTitle'));
if (is_admin()) add_action('init', array('NomisWordpressApi', 'adminHead'));
add_action('sidebar_admin_page', array('NomisWordpressApi', 'admin'));
add_action('admin_menu', array('NomisWordpressApi', 'adminActions'));
if (get_option('nomis_google_analytics_key') != '') add_action('wp_head', array('NomisWordpressApi', 'addGoogleAnalytics'));

add_filter('init', array('NomisWordpressApi', 'flushRules'));
add_filter('init', array('NomisWordpressApi', 'translate'));
add_filter('rewrite_rules_array', array('NomisWordpressApi', 'insertRewriteRules'));
add_filter('query_vars', array('NomisWordpressApi', 'insertRewriteQueryVars'));
add_filter('wp_title', array('NomisWordpressApi', 'propertyTitleTag'));
