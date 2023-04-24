<?php
/*
Plugin Name: ACS Shipping
Plugin URI:
Description: Address Validation, Price Calculation, Create Voucher, Tracking, Receipt List, Delete Voucher
Version: 1.0.20
Author: Fresh Voltage
Author URI: https://freshvoltage.com/
*/

if (!defined('ABSPATH'))
	{
	exit;
	}

date_default_timezone_set('Europe/Athens');
require_once ("nusoap/lib/nusoap.php");

require_once ("nusoap/lib/class.wsdlcache.php");

// require_once("acs_shipping_class.php");

abstract class WS_SOAP

	{
	protected $ws_host;
	protected $ws_username;
	protected $ws_password;
	protected $ws_service;
	protected $ws_method;
	protected $ws_params;
	public

	function ws_call()
		{
		$data = array(
			'status' => '0',
			'title' => 'Completed',
			'response' => ''
		);
		$cache = new nusoap_wsdlcache(WC()->plugin_path() . '/../wc_acs_shipping_method/inc/tmp', 86400);
		$wsdl = $cache->get($this->ws_host . $this->ws_service);
		if (is_null($wsdl))
			{
			$wsdl = new wsdl($this->ws_host . $this->ws_service, '', '', '', '', 5);
			$cache->put($wsdl);
			}

		$client = new nusoap_client($wsdl, 'wsdl', '', '', '', '');
		$client->setCredentials($this->ws_username, $this->ws_password, 'basic');
		$client->soap_defencoding = 'UTF-8';
		$client->decode_utf8 = false;
		if ($client->getError())
			{
			$data = array(
				'status' => '1',
				'title' => 'Connection error',
				'response' => $client->getError()
			);
			}
		  else
			{
			$data['response'] = $this->ws_params ? $client->call($this->ws_method, $this->ws_params) : $client->call($this->ws_method);
			if ($client->getError())
				{
				$data = array(
					'status' => '1',
					'title' => 'Method error',
					'response' => $client->getError()
				);
				}
			}

		// echo '<pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>';

		return $data;
		}
	}

final class WS_SOAP_ACS extends WS_SOAP

	{
	public

	function __construct($ws_host = null, $ws_username = null, $ws_password = null, $companyId = null, $companyPass = null, $username = null, $password = null)
		{
		$this->ws_host = isset($ws_host) ? $ws_host : 'https://services.acscourier.net';

		// $this->ws_username				= isset($ws_username) 	? $ws_username 	: '';
		// $this->ws_password				= isset($ws_password) 	? $ws_password 	: '';

		$this->ws_params['companyId'] = isset($companyId) ? $companyId : 'demo';
		$this->ws_params['companyPass'] = isset($companyPass) ? $companyPass : 'demo';
		$this->ws_params['username'] = isset($username) ? $username : 'demo';
		$this->ws_params['password'] = isset($password) ? $password : 'demo';
		}

	/*
	**
	* Validates address
	*
	* Method: validateAddress
	*/
	public

	function validateAddress($params)
		{
		$this->ws_service = '/ACS-AddressValidationNew-portlet/api/axis/Plugin_ACSAddressValidation_ACSAddressService?wsdl';
		$this->ws_method = 'validateAddress';
		$this->ws_params['address'] = isset($params['address']) ? $params['address'] : 'ΜΙΑΟΥΛΗ 16, 15344 ΓΕΡΑΚΑΣ';
		$this->ws_params['lang'] = isset($params['lang']) ? $params['lang'] : 'GR';
		try
			{
			$res = parent::ws_call();
			if ($res['status'] == '0')
				{
				foreach($res['response'][0] as $k => $v)
					{
					$data[$k] = $v;
					}
				return $data;
				}

			return $res['response'];

			}

		catch(Exception $e)
			{
			return 'Μη διαθέσιμη ' . $e->getMessage();
			}
				

		}

	/*
	**
	* Validates address by ZIP Code
	*
	* Method: validateAddress
	*/
	public

	function findByZipCode($params)
		{
		$this->ws_service = '/ACS-AddressValidationNew-portlet/api/axis/Plugin_ACSAddressValidation_ACSAreaService?wsdl';
		$this->ws_method = 'getByZipCode';
		$this->ws_params['zip_code'] = $params['postcode'];
		$this->ws_params['only_dp'] = false;
		try
			{
			$res = parent::ws_call();
			if ($res['status'] == '0')
				{
				foreach($res['response'][0] as $k => $v)
					{
					$data[$k] = $v;
					}

				return $data;
				}

			return $res['response'];
			}

		catch(Exception $e)
			{
			return 'Μη διαθέσιμη ' . $e->getMessage();
			}
		}

	/*
	**
	* Calculate Price
	*
	* Methods: getPrice
	*/
	public

	function getPrice($params)
		{

		// die($params['products']);

		$this->ws_service = '/ACSPriceCalculation-portlet/api/axis/Plugin_ACSPriceCalculation_ACSPriceService?wsdl';
		$this->ws_method = 'getPrice';
		$this->ws_params['customerId'] = isset($params['customerId']) ? $params['customerId'] : '2ΑΘ999999';
		$this->ws_params['st_from'] = isset($params['st_from']) ? $params['st_from'] : 'ΑΘ';
		$this->ws_params['st_to'] = isset($params['st_to']) ? $params['st_to'] : '';
		$this->ws_params['varos'] = isset($params['varos']) ? $params['varos'] : 2.00;
		$this->ws_params['date_par'] = isset($params['date_par']) ? $params['date_par'] : '';
		$this->ws_params['products'] = isset($params['products']) ? $params['products'] : '';
		$this->ws_params['xrewsh'] = isset($params['xrewsh']) ? $params['xrewsh'] : 2;
		$this->ws_params['zone'] = isset($params['zone']) ? $params['zone'] : '';
		$this->ws_params['asf_poso'] = isset($params['asf_poso']) ? $params['asf_poso'] : 0.00;
		$res = parent::ws_call();
		if ($res['status'] == '0')
			{
			foreach($res['response'] as $k => $v)
				{
				$data[$k] = $v;

			    	if ($k === 'errorMsg' && $v !=='')   {  
				        echo '<script>console.log("Λάθος ACS:' .  $k . ' -'. $v . '")</script>';
				        wc_add_notice('Λάθος ACS Courrier: ' . $v, 'error');
				    } else {
				       // echo '<script>console.log("ACS:' .  $params['customerId'] . '")</script>';
				    }
				
				}
	   
          	return $data;

			}
	
			
		$this->extra_notice = $res['status'];
		return $res['response'];
		}

	/*
	**
	* Create Voucher
	*
	* Methods: createVoucher
	*/
	public

	function createVoucher($params)
		{
		$this->ws_service = '/ACSCreateVoucher-portlet/axis/Plugin_ACSCreateVoucher_ACSVoucherService?wsdl';
		$this->ws_method = 'createVoucher';
		$this->ws_params['diakDateParal'] = isset($params['diakDateParal']) ? $params['diakDateParal'] : '2018-01-01';
		$this->ws_params['diakApostoleas'] = isset($params['diakApostoleas']) ? $params['diakApostoleas'] : '';
		$this->ws_params['diakParalhpthsOnoma'] = isset($params['diakParalhpthsOnoma']) ? $params['diakParalhpthsOnoma'] : 'demo';
		$this->ws_params['diakParalhpthsDieth'] = isset($params['diakParalhpthsDieth']) ? $params['diakParalhpthsDieth'] : 'demo';
		$this->ws_params['acDiakParalhpthsDiethAr'] = isset($params['acDiakParalhpthsDiethAr']) ? $params['acDiakParalhpthsDiethAr'] : '3';
		$this->ws_params['acDiakParalhpthsDiethPer'] = isset($params['acDiakParalhpthsDiethPer']) ? $params['acDiakParalhpthsDiethPer'] : 'demo';
		$this->ws_params['diakParalhpthsThlef'] = isset($params['diakParalhpthsThlef']) ? $params['diakParalhpthsThlef'] : '2109999999';
		$this->ws_params['diakParalhpthsTk'] = isset($params['diakParalhpthsTk']) ? $params['diakParalhpthsTk'] : '15344';
		$this->ws_params['stationIdDest'] = isset($params['stationIdDest']) ? $params['stationIdDest'] : '';
		$this->ws_params['branchIdDest'] = isset($params['branchIdDest']) ? $params['branchIdDest'] : 0;
		$this->ws_params['diakTemaxia'] = isset($params['diakTemaxia']) ? $params['diakTemaxia'] : 1;
		$this->ws_params['diakVaros'] = isset($params['diakVaros']) ? $params['diakVaros'] : 1.00;
		$this->ws_params['diakXrewsh'] = isset($params['diakXrewsh']) ? $params['diakXrewsh'] : 2;
		$this->ws_params['diakWraMexri'] = isset($params['diakWraMexri']) ? $params['diakWraMexri'] : '';
		$this->ws_params['diakAntikatPoso'] = isset($params['diakAntikatPoso']) ? $params['diakAntikatPoso'] : 0;
		$this->ws_params['diakTroposPlAntikat'] = isset($params['diakTroposPlAntikat']) ? $params['diakTroposPlAntikat'] : '';
		$this->ws_params['hostName'] = isset($params['hostName']) ? $params['hostName'] : '';
		$this->ws_params['diakNotes'] = isset($params['diakNotes']) ? $params['diakNotes'] : 'demo';
		$this->ws_params['diakCountry'] = isset($params['diakCountry']) ? $params['diakCountry'] : 'GR';
		$this->ws_params['diakcFiller'] = isset($params['diakcFiller']) ? $params['diakcFiller'] : '1';
		$this->ws_params['acDiakStoixs'] = isset($params['acDiakStoixs']) ? $params['acDiakStoixs'] : '';
		$this->ws_params['customerId'] = isset($params['customerId']) ? $params['customerId'] : '2ΑΘ999999'; 
		$this->ws_params['diakParalhpthsCell'] = isset($params['diakParalhpthsCell']) ? $params['diakParalhpthsCell'] : '';
		$this->ws_params['diakParalhpthsOrofos'] = isset($params['diakParalhpthsOrofos']) ? $params['diakParalhpthsOrofos'] : '';
		$this->ws_params['diakParalhpthsCompany'] = isset($params['diakParalhpthsCompany']) ? $params['diakParalhpthsCompany'] : '';
		$this->ws_params['withReturn'] = isset($params['withReturn']) ? $params['withReturn'] : 0;
		$this->ws_params['diakcCompCus'] = isset($params['diakcCompCus']) ? $params['diakcCompCus'] : '1';
		$this->ws_params['specialDir'] = isset($params['specialDir']) ? $params['specialDir'] : '';
		$res = parent::ws_call();
		if ($res['status'] == '0')
			{
			foreach($res['response'] as $k => $v)
				{
				$data[$k] = $v;
				}

			return $data;
			}

		return $res['response'];
		}

	/*
	**
	* Delete Voucher
	*
	* Methods: deleteACSDeleteVoucher
	*/
	public

	function deleteVoucher($params)
		{
		$this->ws_service = '/ACSDeleteVoucher-portlet/api/axis/Plugin_DeleteVoucher_ACSDeleteVoucherService?wsdl';
		$this->ws_method = 'deleteACSDeleteVoucher';
		$this->ws_params['noPod'] = isset($params['noPod']) ? $params['noPod'] : exit;
		$res = parent::ws_call();
		if ($res['status'] == '0')
			{
			foreach($res['response'] as $k => $v)
				{
				$data[$k] = $v;
				}

			return $data;
			}

		return $res['response'];
		}

	public

	function receiptsList($params)
		{
		$this->ws_service = '/ACSReceiptsList-portlet/api/axis/Plugin_ACSReceiptsList_ACSReceiptsListService?wsdl';
		$this->ws_method = 'createACSReceiptsList';
		$this->ws_params['dateParal'] = isset($params['dateParal']) ? $params['dateParal'] : '2016-11-30';
		$this->ws_params['myData'] = isset($params['myData']) ? $params['myData'] : '1';
		$res = parent::ws_call();
		if ($res['status'] == '0')
			{
			foreach($res['response'] as $k => $v)
				{
				$data[$k] = $v;
				}

			return $data;
			}

		return $res['response'];
		}

	public

	function getUnprintedPods($params)
		{
		$this->ws_service = '/ACSReceiptsList-portlet/api/axis/Plugin_ACSReceiptsList_ACSUnprintedPodsService?wsdl';
		$this->ws_method = 'getUnprintedPods';
		$this->ws_params['dateParal'] = isset($params['dateParal']) ? $params['dateParal'] : '2016-11-30';
		$this->ws_params['myData'] = isset($params['myData']) ? $params['myData'] : '1';
		$res = parent::ws_call();
		if ($res['status'] == '0')
			{
			foreach($res['response'] as $k => $v)
				{
				$data[$k] = $v;
				}

			return $data;
			}

		return $res['response'];
		}

	public

	function getMassNumbers($params)
		{
		$this->ws_service = '/ACSReceiptsList-portlet/api/axis/Plugin_ACSReceiptsList_MassNumberEntryService?wsdl';
		$this->ws_method = 'getMassNumbers';
		$this->ws_params['dateParal'] = isset($params['dateParal']) ? $params['dateParal'] : '2016-11-30';
		$this->ws_params['lang'] = isset($params['lang']) ? $params['lang'] : 'GR';
		$res = parent::ws_call();
		if ($res['status'] == '0')
			{
			foreach($res['response'] as $k => $v)
				{
				$data[$k] = $v;
				}

			return $data;
			}

		return $res['response'];
		}

	public

	function tracking_info($params)
		{
		$this->ws_service = '/ACSTracking-portlet/api/axis/Plugin_acsTracking_TrackingSummaryService?wsdl';
		$this->ws_method = 'findByPod_no';
		$this->ws_params['pod_no'] = isset($params['pod_no']) ? $params['pod_no'] : exit;
		$this->ws_username = 'demo';
		$this->ws_password = 'demo';
		$this->ws_params['companyId'] = 'demo';
		$this->ws_params['companyPass'] = 'demo';
		$this->ws_params['username'] = 'demo';
		$this->ws_params['password'] = 'demo';
		$res = parent::ws_call();
		if ($res['status'] == '0')
			{
			foreach($res['response'][0] as $k => $v)
				{
				$data[$k] = $v;
				}

			return $data;
			}

		return $res['response'];
		}

	public

	function tracking($params)
		{
		$this->ws_service = '/ACSTracking-portlet/api/axis/Plugin_acsTracking_TrackingDetailsService?wsdl';
		$this->ws_method = 'findByPod_no';
		$this->ws_params['pod_no'] = isset($params['pod_no']) ? $params['pod_no'] : exit;
		$this->ws_username = 'demo';
		$this->ws_password = 'demo';
		$this->ws_params['companyId'] = 'demo';
		$this->ws_params['companyPass'] = 'demo';
		$this->ws_params['username'] = 'demo';
		$this->ws_params['password'] = 'demo';
		$res = parent::ws_call();
		return $res;
		}
	}
