<?php

if (!defined('_PS_VERSION_'))
	exit;

require 'helpers.php';

/**
 * This is the weather module main class
 */
class Weather extends Module
{
	use Helpers;

	/**
	 * The constructor class is the first method to
	 * be called when the module is loaded by PrestaShop
	 * here are defined most of the details of the module.
	 */
	function __construct()
	{
		$this->name = 'Weather';
	    $this->tab = 'front_office_features';
	    $this->version = '1.0.0';
	    $this->author = 'Christian Kuri';
	    $this->need_instance = 0;
	    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
	    $this->bootstrap = true;

	    parent::__construct();

	    $this->displayName = $this->l('Weather');
    	$this->description = $this->l('Shows the weather at the zip code.');
    	$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

    	if (!Configuration::get('WEATHER'))
    		$this->warning = $this->l('No name provided');
	}

	/**
	 * When the store administrator installs  
	 * the module this is the method that is called.
	 * First install its dependencies, then the database,then
	 * it register the footer hook and assign the defaul values.
	 * 
	 * @return boolean
	 */
	public function install()
	{
		return parent::install() && $this->installDb() && $this->registerHook('footer') && $this->defaultValues();
	}

	/**
	 * When the store administrator uninstalls  
	 * the module this is the method that's called.
	 * First uninstall its dependencies, then uninstall
	 * the database and deletes the configuration values.
	 * 
	 * @return boolean
	 */
	public function uninstall()
	{
		return parent::uninstall() && $this->uninstallDb() && Configuration::deleteByName('WEATHER');
	}

	/**
	 * This method displays the configuration link, its
	 * content is loaded when the configuration page loads.
	 * Displays a form and when its submited, validates the data,
	 * show any twrown error or save the values in the configuration.
	 */
    public function getContent()
	{
	    $output = null;
	 
	    if (Tools::isSubmit('submit'.$this->name))
	    {
	    	$weather = array();
	        $weather['api'] = strval(Tools::getValue('api'));
	        $weather['zip'] = strval(Tools::getValue('zip'));
	        $weather['cache'] = strval(Tools::getValue('cache'));
	        if (empty($weather['api']) || empty($weather['zip']))
	            $output .= $this->displayError($this->l('Invalid Configuration value'));
	        if (!Validate::isZipCodeFormat($weather['zip']))
	        	$output .= $this->displayError($this->l('The zip code is invalid'));
	        if (!is_numeric($weather['cache']))
	        	$output .= $this->displayError($this->l('The cache value should be a number in munuts'));
	        else
	        {
	            Configuration::updateValue('WEATHER', serialize($weather));
	            $output .= $this->displayConfirmation($this->l('Settings updated'));
	        }
	    }
	    return $output.$this->displayForm();
	}

	/**
	 * This is the method that is called to
	 * display a form in the getContent method.
	 * First gets the default language, then create
	 * the form fiels and assign the HelperForm setting
	 * 
	 * @return HelperForm
	 */
	public function displayForm()
	{
	    // Get default language
	    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	     
	    // Fields of the form
	    $fields_form[0]['form'] = array(
	        'legend' => array(
	            'title' => $this->l('Settings'),
	        ),
	        'input' => array(
	            array(
	                'type' => 'text',
	                'label' => $this->l('Api key'),
	                'name' => 'api',
	                'desc' => $this->l('Introduce your api key (You need to visit https://openweathermap.org to get it)'),
	                'size' => 50,
	                'required' => true
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('Zip code'),
	                'name' => 'zip',
	                'desc' => $this->l('Introduce the zip code of your office'),
	                'size' => 5,
	                'required' => true
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('Time in minuts'),
	                'name' => 'cache',
	                'desc' => $this->l('Introduce the time that the weather data should be stored in cache (more time means less api calls, set 0 if you dont want cache)'),
	                'size' => 5,
	                'required' => true
	            )
	        ),
	        'submit' => array(
	            'title' => $this->l('Save'),
	            'class' => 'btn btn-default pull-right'
	        )
	    );

	    // Creates an instance of the HelperForm class.
	    $helper = new HelperForm();
	     
	    // Module, token and currentIndex
	    $helper->module = $this;
	    $helper->name_controller = $this->name;
	    $helper->token = Tools::getAdminTokenLite('AdminModules');
	    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
	     
	    // Language
	    $helper->default_form_language = $default_lang;
	    $helper->allow_employee_form_lang = $default_lang;
	     
	    // Title and toolbar
	    $helper->title = $this->displayName;
	    $helper->show_toolbar = true;
	    $helper->toolbar_scroll = true;
	    $helper->submit_action = 'submit'.$this->name;
	    $helper->toolbar_btn = array(
	        'save' =>
	        array(
	            'desc' => $this->l('Save'),
	            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
	            '&token='.Tools::getAdminTokenLite('AdminModules'),
	        ),
	        'back' => array(
	            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
	            'desc' => $this->l('Back to list')
	        )
	    );

	    // Load current value
	    $helper->fields_value['api'] = $this->getValue('api');
	    $helper->fields_value['zip'] = $this->getValue('zip');
	    $helper->fields_value['cache'] = $this->getValue('cache');
	    
	    // return the form
	    return $helper->generateForm($fields_form);
	}

	/**
	 * This method hooks the module to the footer hook.
	 * First calls the weatherData method and stores the 
	 * data in a variable called $data, then with the assign
	 * method assigns the template's name variable with a value.
	 * 
	 * @return Display file weather.tpl
	 */
	public function hookDisplayFooter()
	{
		
		$data = $this->weatherData();

		$this->context->smarty->assign(
		    array(
		        'city' => $data->name,
		        'wind' => $data->wind->speed,
		        'weather' => $data->weather[0]->main,
		        'temp' => $data->main->temp
		    )
		);
		return $this->display(__FILE__, 'weather.tpl');
	}

	/**
	 * This method makes the call to the open weather api.
	 * First stores the url in the $url variable, then crates a
	 * curl resource with it, then returns the transfer as a string
	 * which accepts json and stores it in $response, then closes the curl.
	 * 
	 * @return object $response
	 */
    protected function callApi()
    {
        $url = 'http://api.openweathermap.org/data/2.5/weather?zip=' . $this->getValue('zip') . ',us&appid=' . $this->getValue('api') . '&units=imperial';

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER , array('Accept: application/json'));

		$response = curl_exec($curl);

		curl_close($curl);

		return json_decode($response);
    }

    /**
     * This method is called in the hookDisplayFooter method.
     * First assigns the zip value to $zip then checks if there
     * is any stored data in the database, if not calls the method 
     * callApi, removes the old data from the database and stores new data.
     * 
     * @return object $data
     */
    protected function weatherData()
    {
    	$zip = $this->getValue('zip');
    	
    	if($data = $this->getData($zip))
    		return $data;

    	if($data = $this->callApi()){
    		$this->removeOldData();
    		$this->setData($zip, $data);
    		return $data;
    	}
    }
}