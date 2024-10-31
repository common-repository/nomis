<?php

/**
 * Nomis properties app
 * 2.3.2
 *  - Negative country search (not in nl)
 *
 * 2.3.1
 *  - checkApiKey returns false on empty key
 *
 * 2.3.0
 *  - This version number fetches details for properties for sale
 *  - Search countries
 *
 * 2.2.2
 *  - Search inclusive on prices
 *
 * 2.2.1
 *  - Get default database files function for clear cache functionality
 *
 * 2.2.0
 *  - Support for completely customized request and declaration form
 *  - Get all available house types
 *
 * 2.1.1
 *  - default sort is price
 *
 * 2.1.0
 *  - Send the clients buildnumber to the server
 *  - Somewhat more advanced debug methods
 *  - Added extra template option (quicksearch)
 *
 * 2.004
 *  - Search on forsale prices
 *
 * 2.003
 *  - Sort by price
 *  - Emailaddress in contactform required
 *
 * 2.002
 *  - Support for extra fields in single request form
 *
 * 2.001
 *  - When property is deleted from backoffice, its still on the website [FIXED]
 *  - Major performance improvement while fetching the new data
 *
 * 2.000
 * - Incremental database updates (much, much, much, much, much, much & much faster -- it's even worth a 2.0 release :-)
 * - Date format regex
 * - Option to use the original house id @see 1.007, now enabled by default
 * - Sort random
 * - Related properties can be random (within criteria that is)
 *
 * 1.011
 *  - Order by date added, city, district, street, availability, bedrooms and forsale price
 *
 * 1.010
 *  - Search rent/sale
 *
 * 1.009
 *  - Search on surface
 *
 * 1.008
 *  - Option to get the current include dir
 *
 * 1.007
 *  - Option to use the original house id
 *
 * 1.006
 *  - Cache the API-key check for multiple calls
 *
 * 1.005
 *  - Get company email address
 *  - Order search results
 *  - Add request to single broker
 *
 * 1.004
 *  - Rental declaration form
 *  - All available districts are available in extraData
 *  - Search functionality for available_at, parking, garden, balcony, elevator, districts
 *  - Search changed to using continue; instead of break 2;
 *
 * 1.003
 *  - Added option to check if the API-key is valid
 *
 * 1.002:
 *  - Added CMS getters
 *  - Contact page
 *
 * 1.001:
 *  - Added support for custom directories
 *  - Added Nomis_View::shorten
 *  - Added some getters for additional information outside the API-class
 *
 * 1.000: Initial
 */

class Nomis_Api
{
	/**
	 * Build number
	 */
	const BUILD_NUMBER = '2.3.0';

	/**
	 * Cache time to live
	 * 60 * 60 * 24	== 86400	a day
	 * 60 * 60		== 3600		an hour
	 * 60 * 15		== 900		quarter of an hour
	 * 60			== 60		gone in sixty seconds
	 * 6			== 6		six seconds
	 */
	const CACHE_TTL_DAY = 86400;
	const CACHE_TTL_HOUR = 3600;
	const CACHE_TTL_QUARTER = 900;

	const DEFAULT_CACHE_TTL = 900;

	const CACHE_TTL_MIN = 900;

	/**
	 * Default debug modus
	 * 1 -> enable error reporting
	 * 2 -> ignore cache
	 * 4 -> print response from server
	 * 8 -> after printing, kill the script
	 */
	const DEBUG_ERROR_REPORTING = 1;
	const DEBUG_IGNORE_CACHE = 2;
	const DEBUG_PRINT_RESPONSE = 4;
	const DEBUG_PRINT_RESPONSE_DIE = 8;
	const DEBUG_MODUS_ALL = 15;

	const DEFAULT_DEBUG_MODUS = 0; /* Debug purposes */
	protected static $_debugModus = self::DEFAULT_DEBUG_MODUS;

	/**
	 * Default language
	 */
	const DEFAULT_LANGUAGE = 'en';

	/**
	 * Default 'use house id'
	 * Use the number of the houses or the generated md5
	 */
	const DEFAULT_USE_ORIGINAL_HOUSE_ID = true;

	/**
	 * Default database files
	 */
	const DEFAULT_DATABASE_FILE_NL = 'cache/data-nl.db';
	const DEFAULT_DATABASE_FILE_EN = 'cache/data.db';
	const DEFAULT_DATABASE_PAGE_NL = 'cache/pages-nl.db';
	const DEFAULT_DATABASE_PAGE_EN = 'cache/pages.db';

	/**
	 *
	 */
	const DEFAULT_ENABLE_PAGES = false;

	/**
	 * Possible options
	 */
	const ACTION_GET_PROPERTIES = 'getproperties';
	const ACTION_ADD_REQUEST = 'add-request';
	const ACTION_GET_PAGES = 'get-pages';
	const ACTION_CONTACT = 'contact';
	const ACTION_CHECK_API_KEY = 'check-api-key';
	const ACTION_ADD_RENTAL_DECLARATION = 'add-rental-declaration';
	const ACTION_GET_COMPANY_EMAILADDRESS = 'get-company-emailaddress';
	const ACTION_ADD_SINGLE_REQUEST = 'add-single-request';

	/**
	 * Default properties per page
	 */
	const PROPERTIES_PER_PAGE = 10;
	const DEFAULT_RANDOM_RELATED_PROPERTIES = true;

	/**
	 * API-server details
	 */
	const API_HOST = 'http://api.annommx.com/index.php';
	const API_PORT = 80;
	const API_VERSION = 2;

	/**
	 * Date format for the request pages
	 */
//	const DATE_FORMAT_REGEX = '/^\d{1,2}-\d{1,2}-\d{4}$/';
	const DATE_FORMAT_REGEX = '/^(0?[1-9]|[12][0-9]|3[01])-(0?[1-9]|1[012])-(19|20)\d\d$/';
	const DATE_FORMAT_HUMAN_READABLE = 'dd-mm-yyyy';

	const REQUEST_COOKIE_NAME = 'nomis_request_values';

	private $_databaseFile;
	private $_databaseFileTouched = false;
	private $_apiKey;
	private $_language;
	private $_cacheTtl;
	private $_render;
	private $_propertiesPerPage;
	private $_randomRelatedProperties;
	private $_includeDir;
	private $_useOriginalHouseId;

	private static $_customRequestForm;
	private static $_customRentalDeclarationForm;

	/**
	 * Is the pages module loaded
	 * @var boolean
	 */
	private $_enablePages;

	/**
	 * Path to the database file for the pages
	 * @var string
	 */
	private $_databasePage;

	/**
	 * Is the page database file 'touched'
	 * @var boolean
	 */
	private $_databasePageTouched = false;

	/**
	 * Additional outside information
	 */
	private $_searchResults;
	private $_propertyInformation;
	private $_pageInformation;

	public $templateFiles = array(
		'search' => 'templates/search.phtml',
		'property' => 'templates/property.phtml',
		'request' => 'templates/request.phtml', // lead
		'_field' => 'templates/_field.phtml',
		'page' => 'templates/page.phtml',
		'contact' => 'templates/contact.phtml',
		'rental-declaration' => 'templates/rental-declaration.phtml',
		'add-single-request' => 'templates/single-request.phtml', // request directly to broker
		'quicksearch' => 'templates/quicksearch.phtml'
	);

	/**
	 * HTTP-response codes
	 */
	protected static $messages = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',

        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',  // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',

        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );

	/**
	 * Create an API-object for communicating with the API
	 * @param string $apiKey The API-key to identify yourself to the API-server
	 * @param boolean load the pages module
	 */
	public function __construct($apiKey, $enablePages = self::DEFAULT_ENABLE_PAGES, $debugModus = null)//, $databaseFile = self::DEFAULT_DATABASE_FILE_EN)
	{
		if ($debugModus !== null)
		{
			self::debug($debugModus);
		}

		if (self::debug() & self::DEBUG_ERROR_REPORTING)
		{
			error_reporting(E_ALL);
			@ini_set('display_errors', '1');
		}

		$this->_apiKey = $apiKey;
		$this->_enablePages = (bool) $enablePages;

		// make the quotes disappear

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

		// $this->setDatabaseFile($databaseFile);

		// some defaults
		$this->setUseOriginalHouseId();
		$this->setLanguage();
		$this->setCacheTtl();
		$this->setRenderMode();
		$this->setPropertiesPerPage();
		$this->setRandomRelatedProperties();
		$this->setIncludeDir();
	}

	public function getDefaultDatabaseFiles()
	{
		return array(
			'data-nl' => realpath(dirname(__FILE__) . '/../' . self::DEFAULT_DATABASE_FILE_NL),
			'data-en' => realpath(dirname(__FILE__) . '/../' . self::DEFAULT_DATABASE_FILE_EN),
			'page-nl' => realpath(dirname(__FILE__) . '/../' . self::DEFAULT_DATABASE_PAGE_NL),
			'page-en' => realpath(dirname(__FILE__) . '/../' . self::DEFAULT_DATABASE_PAGE_EN)
		);
	}

	public static function debug($level = null)
	{
		if ($level !== null)
		{
			self::$_debugModus = $level;
		}

		return self::$_debugModus;
	}

	public function checkApiKey()
	{
		if (empty($this->_apiKey))
		{
			return false;
		}

		static $_checkApiKey = null;

		if ($_checkApiKey === null)
		{
			$rs = $this->_fetchData(self::ACTION_CHECK_API_KEY);

			$_checkApiKey = !empty($rs['result']) && $rs['result'] == '1';
		}

		return $_checkApiKey;
	}

	/**
	 * Set whether to use the original house id
	 * @param bool $useHouseId
	 * @return Nomis_Api provide fluent interface
	 */
	public function setUseOriginalHouseId($useHouseId = self::DEFAULT_USE_ORIGINAL_HOUSE_ID)
	{
		$this->_useOriginalHouseId = (bool) $useHouseId;

		return $this;
	}

	/**
	 * Set the language to be used
	 * @param string $language
	 * @return Nomis_Api fluent interface
	 */
	public function setLanguage($language = self::DEFAULT_LANGUAGE)
	{
		$this->_language = $language;

		switch ($this->_language)
		{
			case 'nl':
				$this->setDatabaseFile(self::DEFAULT_DATABASE_FILE_NL);

				if ($this->_enablePages)
				{
					$this->setPageDatabase(self::DEFAULT_DATABASE_PAGE_NL);
				}
				break;

			case 'en':
				$this->setDatabaseFile(self::DEFAULT_DATABASE_FILE_EN);

				if ($this->_enablePages)
				{
					$this->setPageDatabase(self::DEFAULT_DATABASE_PAGE_EN);
				}
				break;
		}

		return $this;
	}

	public function setDatabaseFile($databaseFile = self::DEFAULT_DATABASE_FILE_EN)
	{
		$this->_databaseFile = realpath(dirname(__FILE__) . '/../' . $databaseFile);

		if ($this->_databaseFile === false)
		{
			if (touch($databaseFile))
			{
				$this->_databaseFileTouched = true;
				$this->_databaseFile = realpath($databaseFile);
			}
			else
			{
				throw new Nomis_Api_Exception('Invalid database file');
			}
		}

		if (!is_writeable($this->_databaseFile))
		{
			throw new Nomis_Api_Exception('Database is not writable');
		}

		return $this;
	}

	/**
	 * Set the database file for the pages
	 * @param type $databaseFile
	 * @since 1.002
	 * @return Nomis_Api provide a fluent interface
	 */
	public function setPageDatabase($databaseFile = self::DEFAULT_DATABASE_PAGE_EN)
	{
		if (!$this->_enablePages)
		{
			throw new Nomis_Api_Exception('Pages not enabled');
		}

		$this->_databasePage = realpath(dirname(__FILE__) . '/../' . $databaseFile);

		if ($this->_databasePage === false)
		{
			if (touch($databaseFile))
			{
				$this->_databasePageTouched = true;
				$this->_databasePage = realpath($databaseFile);
			}
			else
			{
				throw new Nomis_Api_Exception('Invalid page database file');
			}
		}

		if (!is_writeable($this->_databasePage))
		{
			throw new Nomis_Api_Exception('Page database is not writable');
		}

		return $this;
	}

	public function setCacheTtl($seconds = self::DEFAULT_CACHE_TTL)
	{
		if ($seconds < self::CACHE_TTL_MIN)
		{
			throw new Nomis_Api_Exception('Invalid cache TTL');
		}

		$this->_cacheTtl = $seconds;

		return $this;
	}

	public function setRenderMode($render = true)
	{
		$this->_render = (bool) $render;

		return $this;
	}

	public function setPropertiesPerPage($ppp = self::PROPERTIES_PER_PAGE)
	{
		$this->_propertiesPerPage = (int) $ppp;

		return $this;
	}

	public function setRandomRelatedProperties($random = self::DEFAULT_RANDOM_RELATED_PROPERTIES)
	{
		$this->_randomRelatedProperties = (bool) $random;

		return $this;
	}

	public function setCustomRequestForm(array $form)
	{
		self::$_customRequestForm = $form;

		return $this;
	}

	public function setCustomRentalDeclarationForm(array $form)
	{
		self::$_customRentalDeclarationForm = $form;

		return $this;
	}

	/**
	 * Set the directory where to find the templates
	 * @since 1.001
	 * @param string new include directory
	 * @return old include dir
	 */
	public function setIncludeDir($dir = null)
	{
		$oldIncludeDir = $this->_includeDir;

		if ($dir === null)
		{
			$dir = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
		}
		else
		{
			$dir = realpath($dir);

			if ($dir === false)
			{
				throw new Nomis_Api_Exception('Invalid directory');
			}
		}

		$this->_includeDir = $dir . DIRECTORY_SEPARATOR;

		return $oldIncludeDir;
	}

	public function getIncludeDir()
	{
		return $this->_includeDir;
	}

	public function getPropertyInformation()
	{
		return $this->_propertyInformation;
	}

	public function getSearchResults()
	{
		return $this->_searchResults;
	}

	public function getPageInformation()
	{
		return $this->_pageInformation;
	}

	public function run($input)
	{
		$contents = '';
		if (isset($input['action']))
		{
			switch ($input['action'])
			{
				case 'search':
					$contents = $this->_render(
						$this->templateFiles['search'],
						$this->_search($input)
					);
					break;

				case 'property':
					$contents = $this->_render(
						$this->templateFiles['property'],
						$this->_getProperty($input)
					);
					break;

				case 'property-request':
					$contents = $this->_render(
						$this->templateFiles['request'],
						$this->_processRequest($input)
					);
					break;

				case 'request':
					$contents = $this->_render(
						$this->templateFiles['request'],
						$this->_processRequest($input)
					);
					break;

				case 'rental-declaration':
					$contents = $this->_render(
						$this->templateFiles['rental-declaration'],
						$this->_processRentalDeclaration($input)
					);
					break;

				case 'all-cities':
					$contents = $this->_getAllCities();
					break;

				case 'page':
					if (!$this->_enablePages)
					{
						throw new Nomis_Api_Exception('Pages not enabled');
					}

					$contents = $this->_render(
						$this->templateFiles['page'],
						$this->_page($input)
					);
					break;

				case 'add-single-request':
					$contents = $this->_render(
						$this->templateFiles['add-single-request'],
						$this->_addSingleRequest($input)
					);
					break;

				case 'contact':
					$contents = $this->_render(
						$this->templateFiles['contact'],
						$this->_contact($input)
					);
					break;

				case 'quicksearch':
					$contents = $this->_render(
						$this->templateFiles['quicksearch'],
						$this->_search($input)
					);
					break;

				default:
					throw new Nomis_Api_Exception('Invalid page');
					break;
			}
		}
		else
		{
			$contents = $this->_render(
				$this->templateFiles['search']
			);
		}

		return $contents;
	}

	private function _render($template, $data = null)
	{
		if ($this->_render)
		{
			$view = new Nomis_View(
				$this->_includeDir,
				$template,
				$data,
				array(
					'cities' => $this->_getAllCities(),
					'districts' => $this->_getAllDistricts(),
					'house_types' => $this->_getAllHouseTypes(),
					'countries' => $this->_getAllCountries()
				)
			);

			ob_start();
			$view->display();
			$contents = ob_get_contents();
			ob_end_clean();
		}
		else
		{
			$contents = $data;
		}

		return $contents;
	}

	private function _getAllCities()
	{
		$cities = array();
		$tmp = array();

		$houses = $this->_getAllProperties();

		foreach ($houses['result'] as $house)
		{
			if (!empty($house['city']) && !in_array(strtolower($house['city']), $tmp))
			{
				$tmp[] = strtolower($house['city']);
				$cities[] = $house['city'];
			}
		}

		natcasesort($cities);

		return $cities;
	}

	private function _getAllDistricts()
	{
		$districts = array();
		$tmp = array();

		$houses = $this->_getAllProperties();

		foreach ($houses['result'] as $house)
		{
			if (!empty($house['district']) && !in_array(strtolower($house['district']), $tmp))
			{
				$tmp[] = strtolower($house['district']);
				$districts[] = $house['district'];
			}
		}

		natcasesort($districts);

		return $districts;
	}

	private function _getAllCountries()
	{
		$countries = array();
		$tmp = array();

		$houses = $this->_getAllProperties();

		foreach ($houses['result'] as $house)
		{
			if (!empty($house['country']) && !in_array(strtolower($house['country']), $tmp))
			{
				$tmp[] = strtolower($house['country']);
				$countries[] = $house['country'];
			}
		}

		natcasesort($countries);

		return $countries;
	}

	private function _getAllHouseTypes()
	{
		$houseTypes = array();
		$tmp = array();

		$houses = $this->_getAllProperties();

		foreach ($houses['result'] as $house)
		{
			if (!empty($house['house_type']) && !in_array(strtolower($house['house_type']), $houseTypes))
			{
				$tmp[] = strtolower($house['house_type']);
				$houseTypes[] = $house['house_type'];
			}
		}

		natcasesort($houseTypes);

		return $houseTypes;
	}

	private function _search($values)
	{
		$houses = $this->_getAllProperties();
		$houses = $houses['result'];

		$fields = array(
			'min-price',
			'max-price'
		);

		$search = array();

		$cities = array();
		if (!empty($values['city']))
		{
			$cities = array_map('strtolower', array_map('trim', explode(',', $values['city'])));
		}

		$districts = array();
		if (!empty($values['district']))
		{
			$districts = array_map('strtolower', array_map('trim', explode(',', $values['district'])));
		}

		$countries = array();
		if (!empty($values['country']))
		{
			$countries = array_map('strtolower', array_map('trim', explode(',', $values['country'])));
		}

		$notCountries = array();
		if (!empty($values['not-country']))
		{
			$notCountries = array_map('strtolower', array_map('trim', explode(',', $values['not-country'])));
		}

		$rs = array();
		foreach ($houses as $id => &$house)
		{
			foreach ($values as $index => $value)
			{
				switch ($index)
				{
					case 'for-sale':
						if (!empty($value)) $search['for-sale'] = $value;
						if (ctype_digit((string) $value) && $house['forsale'] != (string) $value)
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'min-price':
						if (!empty($value)) $search['min-price'] = $value;
						if (ctype_digit((string) $value) && $house['price'] < $value)
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'max-price':
						if (!empty($value)) $search['max-price'] = $value;
						if (ctype_digit((string) $value) && $house['price'] > $value)
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'forsale-min-price':
						if (!empty($value)) $search['forsale-min-price'] = $value;
						if (ctype_digit((string) $value) && $house['forsale_price'] < $value)
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'forsale-max-price':
						if (!empty($value)) $search['forsale-max-price'] = $value;
						if (ctype_digit((string) $value) && $house['forsale_price'] > $value)
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'city':
						if (!empty($value)) $search['city'] = $value;
						if (!empty($value) && !in_array(strtolower($house['city']), $cities))// != strtolower($value))
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'country':
						if (!empty($value)) $search['country'] = $value;
						if (!empty($value) && !in_array(strtolower($house['country']), $countries))// != strtolower($value))
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'not-country':
						if (!empty($value)) $search['not-country'] = $value;
						if (!empty($value) && in_array(strtolower($house['country']), $notCountries))// != strtolower($value))
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'division':
						if (!empty($value)) $search['division'] = $value;
						if (!empty($value) && $house['division'] != $value)// != strtolower($value))
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'district':
						if (!empty($value)) $search['district'] = $value;
						if (!empty($value) && !in_array(strtolower($house['district']), $districts))// != strtolower($value))
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'house_type':
						if (!empty($value)) $search['house_type'] = $value;
						if (!empty($value) && stripos($house['house_type'], $value) !== false
						 || !empty($value) && empty($house['house_type']))
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'interior':
						if (!empty($value)) $search['interior'] = $value;
						if (!empty($value) && (stripos($house['interior'], $value) === false
						 || strtolower($house['interior']) == 'unfurnished' && strtolower($value) == 'furnished'))
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'bedrooms':
						if (!empty($value)) $search['bedrooms'] = $value;
						if (ctype_digit((string) $value) && $house['bedrooms'] < $value)
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'available_at':
						if (!empty($value)) $search['available_at'] = $value;
						if (!empty($value) && strtotime($value) < strtotime($house['available_at']))
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'parking':
						if (!empty($value)) $search['parking'] = $value;
						if (!empty($value) && stripos($house['parking'], $value) !== false
						 || !empty($value) && empty($house['parking']))
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'garden':
						if (!empty($value)) $search['garden'] = $value;
						if (!empty($value) && stripos($house['garden'], $value) !== false
						 || !empty($value) && empty($house['garden']))
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'balcony':
						if (!empty($value)) $search['balcony'] = $value;
						if (!empty($value) && stripos($house['balcony'], $value) !== false
						 || !empty($value) && empty($house['balcony']))
						{
							unset($houses[$id]);
							continue;
						}
						break;
					case 'elevator':
						if (!empty($value)) $search['elevator'] = $value;
						if (!empty($value) && $house['elevator'] != $value)
						{
							unset($houses[$id]);
							continue;
						}
						break;

					case 'surface':
						if (!empty($value)) $search['surface'] = $value;
						if (ctype_digit((string) $value) && $house['surface'] < $value)
						{
							unset($houses[$id]);
							continue;
						}
						break;
				}
			}
		}

		if (!empty($values['order']) && preg_match('~^(price|date|city|street|district|bedrooms|forsale_price|available|random)((,|=)(asc|desc))?$~i', $values['order'], $match))
		{
			switch ($match[1])
			{
				case 'price':
					uasort($houses, array('self', '_priceSort'));
					break;

				case 'date':
					uasort($houses, array('self', '_dateSort'));
					break;

				case 'city':
					uasort($houses, array('self', '_citySort'));
					break;

				case 'district':
					uasort($houses, array('self', '_districtSort'));
					break;

				case 'street':
					uasort($houses, array('self', '_streetSort'));
					break;

				case 'bedrooms':
					uasort($houses, array('self', '_bedroomsSort'));
					break;

				case 'available':
					uasort($houses, array('self', '_availableSort'));
					break;

				case 'forsale_price':
					uasort($houses, array('self', '_forsalePriceSort'));
					break;

				case 'random':
					shuffle($houses);
					break;
			}

			if (isset($match[4]) && $match[4] == 'desc')
			{
				$houses = array_reverse($houses, true);
			}
		}
		else
		{
			uasort($houses, array('self', '_priceSort'));
		}

		$totalHouses = count($houses);

		$data = array_chunk($houses, $this->_propertiesPerPage, true);

		$page = 1;
		if (!empty($values['page']) && ctype_digit((string) $values['page']) && $values['page'] > 0 && $values['page'] <= count($data))
		{
			$search['page'] = $page = $values['page'];
		}

		return $this->_searchResults = array(
			'search' => $search,
			'page' => $page,
			'total_pages' => count($data),
			'total' => $totalHouses,
			'result' => empty($data[$page - 1]) ? array() : $data[$page - 1]
		);

		return $houses;
	}

	private static function _priceSort($one, $two)
	{
		if ($one['price'] < $two['price']) return -1;
		if ($one['price'] == $two['price']) return 0;
		if ($one['price'] > $two['price']) return 1;
	}

	private static function _dateSort($one, $two)
	{
		$oneTime = strtotime($one['registration_date']);
		$twoTime = strtotime($two['registration_date']);

		if ($oneTime < $twoTime) return -1;
		if ($oneTime == $twoTime) return 0;
		if ($oneTime > $twoTime) return 1;
	}

	private static function _citySort($one, $two)
	{
		return strcasecmp($one['city'], $two['city']);
	}

	private static function _streetSort($one, $two)
	{
		return strcasecmp($one['street'], $two['street']);
	}

	private static function _districtSort($one, $two)
	{
		return strcasecmp($one['district'], $two['district']);
	}

	private static function _bedroomsSort($one, $two)
	{
		if ($one['bedrooms'] < $two['bedrooms']) return -1;
		if ($one['bedrooms'] == $two['bedrooms']) return 0;
		if ($one['bedrooms'] > $two['bedrooms']) return 1;
	}

	private static function _availableSort($one, $two)
	{
		$oneTime = strtotime($one['available_at_start']);
		$twoTime = strtotime($two['available_at_start']);

		if ($oneTime < $twoTime) return -1;
		if ($oneTime == $twoTime) return 0;
		if ($oneTime > $twoTime) return 1;
	}

	private static function _forsalePriceSort($one, $two)
	{
		if ($one['forsale_price'] < $two['forsale_price']) return -1;
		if ($one['forsale_price'] == $two['forsale_price']) return 0;
		if ($one['forsale_price'] > $two['forsale_price']) return 1;
	}

	private function _getProperty($input)
	{
		$properties = $this->_getAllProperties();

		if ($this->_randomRelatedProperties)
		{
			$search = $this->_search(array_merge($input, array(
				'order' => 'random'
			)));
		}
		else
		{
			$search = $this->_search($input);
		}

		$property = null;

		if (isset($input['id']) && !empty($properties['result'][$input['id']]))
		{
			$property = $properties['result'][$input['id']];
			$property['id'] = $input['id'];
		}

		$this->_propertyInformation = $property;
		return array(
			'search' => $search,
			'property' => $property
		);
	}

	private function _page($input)
	{
		$pages = $this->_getAllPages();
		$page = null;

		if (isset($input['id']) && !empty($pages['result'][$input['id']]))
		{
			$page = $pages['result'][$input['id']];
		}

		$this->_pageInformation = $page;
		return $page;
	}

	private function _contact($input)
	{
		$contact = array(
			'form' => array(
				'last_name' => array(
					'options' => array(
						'required' => true,
						'label' => 'Name'
					)
				),
				'emailaddress' => array(
					'options' => array(
						'label' => 'Email address',
						'regex' => 'email',
						'required' => true
					)
				),
				'telephone' => array(),
				'subject' => array(),
				'message' => array()
			)
		);

		$contact['error'] = false;

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_contact']))
		{
			if ($this->_validateForm($contact))
			{
				$tmp = $_POST;
				$tmp['name'] = $tmp['last_name'];

				$rs = $this->_fetchData(self::ACTION_CONTACT, $tmp);

				// var_dump($rs);

				if ($rs['result'] == '1')
				{
					$contact['success'] = true;
				}
			}
		}

		return array(
			'contact' => $contact
		);
	}

	/**
	 *
	 * @param type $input
	 * @return type
	 */
	private function _processRequest($input)
	{
		$otherIntel = $this->_getProperty($input);

		$request = array(
			'form' => array(
//				'title' => array(
//					'options' => array(
//						'required' => true
//					)
//				),
				'last_name' => array(
					'options' => array(
						'required' => true
					)
				),
				'last_name_prefix' => array(
					'options' => array(
						'type' => 'hidden'
					)
				),
				'first_name' => array(),
				'company' => array(),
				'telephone' => array(
					'options' => array(
						'required' => true,
					)
				),
				'emailaddress' => array(
					'options' => array(
						'required' => true,
						'label' => 'Emailaddress',
						'regex' => 'email'
					)
				)
			)
		);

		if ($otherIntel['property'] === null)
		{
			$request['form'] += array(
				'max_price' => array(
					'options' => array(
						'label' => 'Budget',
						'required' => true,
						'regex' => 'number',
						'type' => 'select',
						'options' => array(
							'' => '---',
							500 => 500,
							1000 => 1000,
							1500 => 1500,
							2000 => 2000,
							2500 => 2500,
							3000 => 3000,
							3500 => 3500,
							4000 => 4000
						)
					)
				),
				'interior' => array(
					'options' => array(
						'required' => true,
						'type' => 'select',
						'options' => array(
							'' => '',
							13 => 'Unfurnished',
							14 => 'Furnished',
							15 => 'Bare'
						)
					)
				),
				'bedrooms' => array(
					'options' => array(
						'required' => true,
						'type' => 'select',
						'options' => array(
							'' => '',
							'1' => '1+',
							'2' => '2+',
							'3' => '3+',
							'4' => '4+'
						)
					)
				),
				'city' => array(
					'options' => array(
						'required' => true
					)
				)
			);
		}

		$request['form'] += array(
			'commencing_date' => array(
				'options' => array(
					'description' => 'Commencing date in ' . self::DATE_FORMAT_HUMAN_READABLE,
					'label' => 'When do you want to rent',
					'class' => 'date',
					'regex' => 'date'
				)
			),
			'other_wishes' => array(
				'options' => array(
					'label' => 'Other wishes'
				)
			)
		);

		$request['error'] = false;

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_request']))
		{
			if (!headers_sent())
			{
				setcookie(self::REQUEST_COOKIE_NAME, base64_encode(serialize($_POST)), 0, '/', '.' . $_SERVER['HTTP_HOST'], false, true);
			}

			if ($this->_validateForm($request))
			{
				$ids = array();
				if ($otherIntel['property'] !== null)
				{
					foreach ($otherIntel['property']['broker'] as $broker)
					{
						$ids[] = $broker['house_id'];
					}
				}

				$rs = $this->_fetchData(self::ACTION_ADD_REQUEST, array_merge($_POST, array(
					'house_id' => $ids,
				)));

				// var_dump($rs);

				if ($rs['result'] == '1')
				{
					$request['success'] = true;
				}
			}
		}
		else
		{
			if (isset($_COOKIE[self::REQUEST_COOKIE_NAME]))
			{
				$tmp = base64_decode($_COOKIE[self::REQUEST_COOKIE_NAME]);

				if ($tmp !== false)
				{
					$level = error_reporting(0);
					$tmp = unserialize($tmp);
					error_reporting($level);

					if ($tmp !== false)
					{
						$newForm = array();
						foreach ($request['form'] as $name => $info)
						{
							$info['info'] = array();
							if (!empty($tmp[$name]))
							{
								$info['info'] = array(
									'value' => $tmp[$name]
								);
							}
							$newForm[$name] = $info;
						}

						$request['form'] = $newForm;
					}
				}
			}
		}

		return array_merge($otherIntel, array(
			'request' => $request
		));
	}

	private function _addSingleRequest($input)
	{
		$request = array(
			'form' => array(
				'last_name' => array(
					'options' => array(
						'required' => true,
						'label' => 'Name'
					)
				),
				'company' => array(),
				'telephone' => array(),
				'emailaddress' => array(
					'options' => array(
						'regex' => 'email'
					)
				),
				'interior' => array(
					'options' => array(
						'type' => 'select',
						'options' => array(
							'' => '',
							13 => 'Unfurnished',
							14 => 'Furnished',
							15 => 'Bare'
						)
					)
				),
				'bedrooms' => array(
					'options' => array(
						'type' => 'select',
						'options' => array(
							'' => '',
							'1' => '1+',
							'2' => '2+',
							'3' => '3+',
							'4' => '4+'
						)
					)
				),
				'city' => array(
					// 'required' => true
				),
				'district' => array(),
				'commencing_date' => array(
					'options' => array(
						'description' => 'Commencing date in ' . self::DATE_FORMAT_HUMAN_READABLE,
						'label' => 'When do you want to rent',
						'class' => 'regex',
						'regex' => 'date'
					)
				),
				'max_price' => array(
					'options' => array(
						// 'required' => true,
						'regex' => 'number'
					)
				),
				'other_wishes' => array()
			)
		);

		if (!empty(self::$_customRequestForm))
		{
			$request['form'] = self::$_customRequestForm;
		}

		$request['error'] = false;

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_single_request']))
		{
			if ($this->_validateForm($request))
			{
				unset($_POST['add_single_request']);

				$tmp = $_POST;
				$tmp['name'] = $tmp['last_name'];

				$rs = $this->_fetchData(self::ACTION_ADD_SINGLE_REQUEST, $tmp);

				if (!empty($rs['result']))
				{
					$request['success'] = true;
				}
			}
		}

		return $request;
	}

	private function _processRentalDeclaration($input)
	{
		$addDeclaration = array(
			'form' => array(
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
							'' => '',
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
				),
			)
		);

		if (!empty(self::$_customRentalDeclarationForm))
		{
			$addDeclaration['form'] = self::$_customRentalDeclarationForm;
		}

		$addDeclaration['error'] = false;

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_rental_declaration']))
		{
			if ($this->_validateForm($addDeclaration))
			{
				$tmp = $_POST;
				$tmp['name'] = $tmp['last_name'];

				$rs = $this->_fetchData(self::ACTION_ADD_RENTAL_DECLARATION, $tmp);

				if (!empty($rs['result']))
				{
					$addDeclaration['success'] = true;
					$addDeclaration['filename'] = $rs['result'];
				}
			}
		}

		return $addDeclaration;
	}

	private function _validateForm(&$form)
	{
		$error = false;

		foreach ($form['form'] as $name => &$element)
		{
			if (!isset($element['info']))
			{
				$element['info'] = array();
			}

			$element['info']['value'] = @$_POST[$name];

			if (!empty($element['options']['required']) && empty($_POST[$name]))
			{
				if (!isset($element['info']))
				{
					$element['info'] = array();
				}

				if (empty($element['info']['errors']))
				{
					$element['info']['errors'] = 'Field is required';
					$error = true;
				}
			}

			if (!empty($element['options']['regex']))
			{
				$errorMessage = '';

				if (!empty($_POST[$name]))
				{
					$huh = (array) $element['options']['regex'];
					foreach ($huh as $regex)
					{
						switch ($element['options']['regex'])
						{
							case 'email':
								if (!filter_var($_POST[$name], FILTER_VALIDATE_EMAIL))
								{
									$errorMessage = 'Invalid emailaddress';
								}
								break 2;

							case 'number':
								if (!ctype_digit((string) $_POST[$name]))
								{
									$errorMessage = 'Invalid integer';
								}
								break 2;

							case 'date':
								if (!preg_match(self::DATE_FORMAT_REGEX, $_POST[$name]))
								{
									$errorMessage = 'Invalid date format, "' . self::DATE_FORMAT_HUMAN_READABLE . '" required';
								}
								break 2;
						}
					}

					if (!empty($errorMessage))
					{
						if (empty($element['info']['errors']))
						{
							$element['info']['errors'] = $errorMessage;
							$error = true;
						}
					}
				}
			}

			if (!empty($element['options']['type']) && $element['options']['type'] == 'select' && !empty($_POST[$name]) && !isset($element['options']['options'][$_POST[$name]]))
			{
				$element['info']['value'] = '';

				if (empty($element['info']['errors']))
				{
					$element['info']['errors'] = 'Field not present in list';
					$error = true;
				}
			}
		}

		return !$error;

		foreach ($form['form'] as $name => &$element)
		{
			$element['info']['value'] = @$_POST[$name];

			if (!empty($element['options']['required']) && empty($_POST[$name]))
			{
				if (!isset($element['info']))
				{
					$element['info'] = array();
				}

				if (empty($element['info']['errors']))
				{
					$element['info']['errors'] = 'Field is required';
					$form['error'] = true;
				}
			}

			if (!empty($element['options']['regex']))
			{
				$error = '';

				if (!empty($_POST[$name]))
				{
					$huh = (array) $element['options']['regex'];
					foreach ($huh as $regex)
					{
						switch ($element['options']['regex'])
						{
							case 'email':
								if (!filter_var($_POST[$name], FILTER_VALIDATE_EMAIL))
								{
									$error = 'Invalid email address';
								}
								break 2;

							case 'number':
								if (!ctype_digit((string) $_POST[$name]))
								{
									$error = 'Value must be a number';
								}
								break 2;
						}
					}

					if (!empty($error))
					{
						if (!isset($element['info']))
						{
							$element['info'] = array();
						}

						if (empty($element['info']['errors']))
						{
							$element['info']['errors'] = $error;
							$form['error'] = true;
						}
					}
				}
			}

			if (!empty($element['options']['type']) && !empty($_POST[$name]) && !isset($element['options']['options'][$_POST[$name]]))
			{
				$element['info']['value'] = '';

				if (!isset($element['info']))
				{
					$element['info'] = array();
				}

				if (empty($element['info']['errors']))
				{
					$element['info']['errors'] = 'Field not present in list';
					$form['error'] = true;
				}
			}
		}

		return $form;
	}

	private function _getAllProperties()
	{
		static $_dataFetched = false;

		if (!$_dataFetched && (self::debug() & self::DEBUG_IGNORE_CACHE
		 || !file_exists($this->_databaseFile)
		 || filemtime($this->_databaseFile) + $this->_cacheTtl < time()
		 || $this->_databaseFileTouched || filesize($this->_databaseFile) == 0))
		{
			$currentData = array('result' => array());
			if (file_exists($this->_databaseFile) && filesize($this->_databaseFile) > 0)
			{
				$currentData = unserialize(file_get_contents($this->_databaseFile));

				if ($currentData === false)
				{
					$currentData = array('result' => array());
				}
			}

			$lastModified = 0;
			$currentIds = array();
			if (file_exists($this->_databaseFile) && filesize($this->_databaseFile) > 0)
			{
				$lastModified = date('c', filemtime($this->_databaseFile));
				$tmp = unserialize(file_get_contents($this->_databaseFile));
				foreach ($tmp['result'] as $house)
				{
					$currentIds[] = $house['house_id'];
				}
			}

			$newData = $this->_fetchData(self::ACTION_GET_PROPERTIES, array(
				'last_modified' => $lastModified,
				'current_ids' => implode(';', $currentIds)
			));

			$_dataFetched = true;

			if (isset($newData['result']))
			{
				$data = array(
					'result' => array_merge($currentData['result'], $newData['result']['properties'])
				);

				$currentIds = explode(';', $newData['result']['current-properties']);
				foreach (array_keys($data['result']) as $key)
				{
					if (!in_array($data['result'][$key]['house_id'], $currentIds))
					{
						unset($data['result'][$key]);
					}
				}

				if (!@file_put_contents($this->_databaseFile, serialize($data)))
				{
					throw new Nomis_Api_Exception('Database not writable');
				}
			}
			else
			{
				throw new Nomis_Api_Exception('Could not fetch data');
			}
		}
		else
		{
			$data = unserialize(file_get_contents($this->_databaseFile));
		}


		if ($this->_useOriginalHouseId)
		{
			$rs = array();

			foreach ($data['result'] as $house)
			{
				$house['id'] = $house['house_id'];
				$rs[$house['id']]  = $house;
			}

			$data['result'] = $rs;
		}

		return $data;
	}

	private function _getAllPages()
	{
		if (!file_exists($this->_databasePage)
		 || filemtime($this->_databasePage) + $this->_cacheTtl < time()
		 || $this->_databasePageTouched || filesize($this->_databasePage) == 0)
		{
			$data = $this->_fetchData(self::ACTION_GET_PAGES);

			if (isset($data['result']))
			{
				if (!@file_put_contents($this->_databasePage, serialize($data)))
				{
					throw new Nomis_Api_Exception('Database not writable');
				}

				return $data;
			}
			else
			{
				throw new Nomis_Api_Exception('Could not fetch data');
			}
		}
		else
		{
			return unserialize(file_get_contents($this->_databasePage));
		}
	}

	public function getCompanyEmailaddress()
	{
		$rs = $this->_fetchData(self::ACTION_GET_COMPANY_EMAILADDRESS);

		return $rs['result'];
	}

	private function _fetchData($action, $data = array())
	{
		$data = http_build_query(array_merge($data, array(
			'key' => $this->_apiKey,
			'lang' => $this->_language,
			'action' => $action,
			'version' => self::API_VERSION,
			'client-version' => self::BUILD_NUMBER
		)), '', '&');

		$urlInfo = parse_url(self::API_HOST);

		$fp = @fsockopen($urlInfo['host'], self::API_PORT, $errno, $errstr);
		$buf = '';

		if (!$fp)
		{
			throw new Nomis_Api_Exception('Could not connect to API-server');
		}

		@fputs($fp, "POST {$urlInfo['path']} HTTP/1.0\n");
		@fputs($fp, "Host: " . $urlInfo['host'] . "\n");
		@fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
		@fputs($fp, "Content-length: " . strlen($data) . "\n");
		@fputs($fp, "Connection: close\n\n");
		@fputs($fp, $data);

		while (!feof($fp))
		{
			$buf .= fgets($fp, 128);
		}

		fclose($fp);

		if (empty($buf))
		{
			throw new Nomis_Api_Exception('Zero-sized reply');
		}
		else
		{
			preg_match("|^HTTP/[\d\.x]+ (\d+)|", $buf, $m);

			if (isset($m[1]))
			{
				$restype = floor((int) $m[1] / 100);
	            if ($restype == 4 || $restype == 5)
				{
		            throw new Nomis_Api_Exception('Request unsuccessful');
		        }
	        }

			list($headers, $body) = preg_split("/(\r?\n){2}/", $buf, 2);
		}

		if (self::debug() & self::DEBUG_PRINT_RESPONSE)
		{
			print_r($body);

			if (self::debug() & self::DEBUG_PRINT_RESPONSE_DIE)
			{
				exit;
			}
		}

		// try to decode response
		if (function_exists('json_decode'))
		{
			$data = json_decode($body, true);
		}
		else
		{
			throw new Nomis_Api_Exception('API: could not process request');
		}

		if (!empty($data['error']) && $action != self::ACTION_CHECK_API_KEY)
		{
			throw new Nomis_Api_Exception('API: ' . $data['error']);
		}

		return $data;
	}
}

class Nomis_View
{
	private $_includeDir;
	private $_template;
	private $_data;
	private $_extraData;

	public function __construct($includeDir, $template, $data = null, $extraData = array())
	{
		$this->_includeDir = $includeDir;
		$this->_template = $template;
		$this->_data = $data;
		$this->_extraData = $extraData;
	}

	public function display()
	{
		$data = $this->_data;
		$templateFile = $this->_includeDir . $this->_template;
		include $templateFile;
	}

	public function __get($key)
	{

	}

	public function __set($key, $value)
	{

	}

	public function getAllCities()
	{
		return empty($this->_extraData['cities']) ? array() : $this->_extraData['cities'];
	}

	public function getAllDistricts()
	{
		return empty($this->_extraData['districts']) ? array() : $this->_extraData['districts'];
	}

	public function getAllCountries()
	{
		return empty($this->_extraData['countries']) ? array() : $this->_extraData['countries'];
	}

	public function getAllHouseTypes()
	{
		return empty($this->_extraData['house_types']) ? array() : $this->_extraData['house_types'];
	}

	public function formText($name, $info = array(), $options = array())
	{
		$label = !empty($options['label']) ? $options['label'] : ucfirst(strtolower(str_replace('_', ' ', $name)));
		ob_start();
		include $this->_includeDir . 'templates/_field.phtml';
		$rs = ob_get_contents();
		ob_end_clean();

		return $rs;
	}

	public function html($string, $quote_style = ENT_COMPAT, $charset = 'utf-8', $double_encode = true)
	{
		return htmlentities($string, $quote_style, $charset);//, $double_encode);
	}

	public function shorten($haystack, $length = 20, $needle = array('.', '!', '?'), $endString = '...')
    {
		$needle = (array) $needle;
		$i = 0;
		$count = 0;
		$short = '';
		$open = array();
		$continue = true;

		while ($i < strlen($haystack) && $continue)
		{
		    if ($haystack{$i} == '<')
		    {
				$tmp = substr($haystack, $i, strpos($haystack, '>', $i + 1) - $i + 1);

				if ($tmp{1} != '/' && substr($tmp, -2, 1) != '/')
				{
					$open[] = substr($tmp, 1, strlen($tmp) - 2);
				}
				else
				{
					array_pop($open);
				}

				$short .= $tmp;

				$i += strlen($tmp);
		    }
		    elseif (substr($haystack, $i, 7) == '[break]')
		    {
				$continue = false;
		    }
		    elseif (in_array($haystack{$i}, $needle) && $count > $length)
		    {
				$short .= $haystack{$i};
				$continue = false;
		    }
		    else
		    {
				$short .= $haystack{$i};
				++$count;
				++$i;
		    }
		}

		while (count($open) != 0)
		{
		    $short .= '</' . array_pop($open) . '>';
		}

		return $short;
	}

	public function seo($str, $replace = '-')
	{
		if (is_string($str) && strlen($str) > 0)
		{
			$str = html_entity_decode(preg_replace(
				'~&([a-z])(tilde|elig|zlig|cedil|acute|uml|grave|circ|slash|ring);~i',
				'$1',
				$this->html($str)
			));

			return strtolower(str_replace(
				' ',
				$replace,
				trim(preg_replace('~(?:\s|[^a-z0-9_])+~i', ' ', $str))
			));
		}

		return '';
	}
}

class Nomis_Form
{

}

class Nomis_Api_Exception extends Exception {}
