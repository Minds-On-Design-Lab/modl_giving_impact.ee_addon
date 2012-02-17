<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package		Giving Impact
 * @author		Minds On Deisgn Lab
 * @copyright	Copyright (c) 2010, Minds On Design Lab Inc.
 * @license		
 * @link		http://givingimpact.com
 * @since		Version 1.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

class Giving_impact_api {

	var $api_base_url = '';
	var $campaign_token = '';
	var $api_account = '';
	var $api_key = '';
	var $credentials = '';
		
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		
		// Get existing credentials
    
	    $this->credentials = $this->EE->db
	    	->where('site_id', $this->EE->config->item('site_id'))
		    ->get('modl_giving_impact_api_instance');
		
		if ($this->credentials->num_rows() > 0) 
		{
			$this->api_account = $this->credentials->row('api_account');
			$this->api_key = $this->credentials->row('api_key');
			$this->api_base_url = $this->credentials->row('api_path');
		}

	}
	
	public function get_individual_campaign($token) {
		
		
		/** ---------------------------------------
		*	Construct API URL
		*
		*	Returns an individual giving campaign's data
		/** ---------------------------------------*/
		
		$url = $this->api_base_url.'accounts/'.$this->api_account.'/campaigns/'.$token.'.json';
		
		/** ---------------------------------------
		/**  Get and return json
		/** ---------------------------------------*/
		
		return $this->_json_get($url);
	}
	
	public function get_individual_giving_opportunity($token) {
		
		
		/** ---------------------------------------
		*   Construct API URL
		*
		*	Returns an individual giving opp's data
		/** ---------------------------------------*/
		
		$url = $this->api_base_url.'accounts/'.$this->api_account.'/giving_opportunity/'.$token.'.json';
		
		/** ---------------------------------------
		/**  Get and return json
		/** ---------------------------------------*/
		
		return $this->_json_get($url);
	}
	
	
	
	public function get_campaigns_active() {		
		
		/** ---------------------------------------
		/**  Construct API URL for all active campaigns
		/** ---------------------------------------*/
		
		$url = $this->api_base_url.'accounts/'.$this->api_account.'/campaigns.json';
		
		/** ---------------------------------------
		/**  Get and return json
		/** ---------------------------------------*/
		
		return $this->_json_get($url);

	}
	
	public function get_campaign_giving_opportunities($token) {		
		
		/** ---------------------------------------
		/**  Construct API URL for all active campaigns
		/** ---------------------------------------*/
		
		$url = $this->api_base_url.'accounts/'.$this->api_account.'/giving_opportunities/'.$token.'.json';
				
		/** ---------------------------------------
		/**  Get and return json
		/** ---------------------------------------*/

		return $this->_json_get($url);

	}
	
	public function get_giving_opportunity_donation_log($token) {
		
		/** ---------------------------------------
		*  Construct API URL
		*
		*  Returns donation log records for a
		*  specific giving opportunty
		/** ---------------------------------------*/
		
		$url = $this->api_base_url.'accounts/'.$this->api_account.'/giving_opportunity_donation_log/'.$token.'.json';

		/** ---------------------------------------
		/**  Get and return json
		/** ---------------------------------------*/
	
		return $this->_json_get($url);
		
	}
	
	
	private function _json_get($url)
	{
		/** ---------------------------------------
		/**  Engage CURL
		/** ---------------------------------------*/
		
		$raw_json = $this->_curl_fetch($url);
				
		/** ---------------------------------------
		/**  Decode resulting JSON
		/** ---------------------------------------*/
	
				
		$data = json_decode($raw_json, true);
		
		return $data;

	}
		
	private function _curl_fetch($url)
	{
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERPWD, $this->api_key);

		$data = curl_exec($ch);

		curl_close($ch);

		return $data;
	}

}
// END CLASS

/* End of file giving_impact_api.php */
/* Location: ./system/expressionengine/third_party/giving_impact/libraries/giving_impact_api.php */