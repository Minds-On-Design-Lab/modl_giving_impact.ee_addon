<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Giving Impact Module Front End File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Minds On Design Lab
 * @link		http://mod-lab.com
 */

class Modl_giving_impact {
	
	public $return_data;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		
		$this->EE->load->library('giving_impact_api');
		
	}
	
	// ----------------------------------------------------------------
	
	public function campaigns() {
		
		/** ---------------------------------------
		*	Get data from API and decode resulting JSON
		/** ---------------------------------------*/
		
		$data = $this->EE->giving_impact_api->get_campaigns_active();
		$vars = $this->_prep_multiple($data);
		
		$this->return_data = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $vars);
        return $this->return_data;
	}
	
	
	public function single_campaign() {
		
		/** ---------------------------------------
		*	Fetch parameter
		/** ---------------------------------------*/
				
		$this->token = (($token = $this->EE->TMPL->fetch_param('token')) === FALSE) ? $this->token : $token;
		
		/** ---------------------------------------
		*	Get data from API and decode resulting JSON
		/** ---------------------------------------*/
		
		$data = $this->EE->giving_impact_api->get_individual_campaign($this->token);
		
		$vars = $this->_prep_single($data);
		
		$this->return_data = $this->EE->TMPL->parse_variables_row($this->EE->TMPL->tagdata, $vars);
        return $this->return_data;
	}
	
	public function campaign_giving_opportunities() {
		
		/** ---------------------------------------
		*	Fetch parameter
		/** ---------------------------------------*/
				
		$this->token = (($token = $this->EE->TMPL->fetch_param('token')) === FALSE) ? $this->token : $token;
		
		/* Sorting params */
		$this->sort_by = $this->EE->TMPL->fetch_param('sort_by');
		/* Numeric = yes otherwise string */
		$this->sort_numeric = $this->EE->TMPL->fetch_param('sort_numeric');
		/* Backwards = yes otherwise forwards */
		$this->sort_backwards = $this->EE->TMPL->fetch_param('sort_backwards');
		
		/* Result Limit ordered */
		$this->limit = $this->EE->TMPL->fetch_param('limit');
		
		/* Result Limit random */
		$this->random = $this->EE->TMPL->fetch_param('random');
		
		/** ---------------------------------------
		*	Get data from API and decode resulting JSON
		/** ---------------------------------------*/
		
		$data = $this->EE->giving_impact_api->get_campaign_giving_opportunities($this->token);
		$vars = $this->_prep_multiple($data);
		
		if ($this->sort_by) 
		{
			$this->EE->load->library('MY_sort_associative_array');
	    	$sorter = new MY_sort_associative_array;
	    	
	    	if ($this->sort_numeric == "yes") 
	    	{
		    	$sorter->numeric = true;
		    	} else {
		    	$sorter->numeric = false;
		    }
	    	
	    	
	    	if ($this->sort_backwards == "yes") {
	    		$sorter->backwards = true;
		    	} else {
	    		$sorter->backwards = false;
	    	}
	    	
	    	$vars = $sorter->sort_associative_array($vars, $this->sort_by);
    	}
    	
    	if ($this->random) {
    		$vars = $this->_random_campaign_results($vars, $this->random);
    	}
    	
    	if ($this->limit) {
    		$vars = $this->_limit_campaign_results($vars, $this->limit);
    	}
		
		$this->return_data = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $vars);
        return $this->return_data;
	}
		
	public function single_giving_opportunity() {
		
		/** ---------------------------------------
		*	Fetch parameter
		/** ---------------------------------------*/
				
		$this->token = (($token = $this->EE->TMPL->fetch_param('token')) === FALSE) ? $this->token : $token;
		
		/** ---------------------------------------
		*	Get data from API and decode resulting JSON
		/** ---------------------------------------*/
		
		$data = $this->EE->giving_impact_api->get_individual_giving_opportunity($this->token);
		
		$vars = $this->_prep_single($data);
		
		$this->return_data = $this->EE->TMPL->parse_variables_row($this->EE->TMPL->tagdata, $vars);
        return $this->return_data;
	}
	
	
	public function single_giving_opportunity_donation_log() {
		
		/** ---------------------------------------
		*	Fetch parameter
		/** ---------------------------------------*/
				
		$this->token = (($token = $this->EE->TMPL->fetch_param('token')) === FALSE) ? $this->token : $token;
		
		/** ---------------------------------------
		*	Get data from API and decode resulting JSON
		/** ---------------------------------------*/
		
		$data = $this->EE->giving_impact_api->get_giving_opportunity_donation_log($this->token);
		
		$vars = $this->_prep_donation_log($data);

		$this->return_data = $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $vars);
        return $this->return_data;
	}
	
	
	private function _prep_single($data) {
		foreach ($data as $d) {
			$vars = array(
				'gi_title' => $d['title'],
				'gi_description' => $d['description'],
				'gi_token' => $d['campaign_token'],
				'gi_donation_url' => $d['donation_link'],
				'gi_givlnk' => $d['givlnk'],
				'gi_share_url' => $d['share'],
				'gi_donation_target' => $d['target'],
				'gi_donation_total' => $d['current'],
				'gi_image_url' => $d['campaign_image_url'],
				'gi_youtube_url'=> $d['youtube_url'],
				'gi_hash_tag' => $d['hash_tag'],
				'gi_display_target' => $d['display_target'],
				'gi_display_current' => $d['display_current'],
				'gi_has_giving_opportunities' => $d['has_giving_opportunities'],
				'gi_status' => $d['status'],
			);
		}
		return $vars;
	}
	
	private function _prep_multiple($data) {
		$vars = array();
		foreach ($data as $dl1) {
			foreach ($dl1 as $d) {
				/* Only return if status is active - would like to handle in API eventually */
				if ($d['status'] == '1') {
					$variable_row = array(
						'gi_title' => $d['title'],
						'gi_description' => $d['description'],
						'gi_token' => $d['campaign_token'],
						'gi_donation_url' => $d['donation_link'],
						'gi_givlnk' => $d['givlnk'],
						'gi_share_url' => $d['share'],
						'gi_donation_target' => $d['target'],
						'gi_donation_total' => $d['current'],
						'gi_image_url' => $d['campaign_image_url'],
						'gi_youtube_url'=> $d['youtube_url'],
						'gi_hash_tag' => $d['hash_tag'],
						'gi_display_target' => $d['display_target'],
						'gi_display_current' => $d['display_current'],
						'gi_has_giving_opportunities' => $d['has_giving_opportunities'],
						'gi_status' => $d['status'],
					);
				}
				$vars[] = $variable_row;
			}
		}		
		return $vars;
		
	}
	
	private function _limit_campaign_results($data, $limit) {
		$i = 0;
		$vars = array();
		foreach ($data as $d) {
			
			$variable_row = array(
				'gi_title' => $d['gi_title'],
				'gi_description' => $d['gi_description'],
				'gi_token' => $d['gi_token'],
				'gi_donation_url' => $d['gi_donation_url'],
				'gi_givlnk' => $d['gi_givlnk'],
				'gi_share_url' => $d['gi_share_url'],
				'gi_donation_target' => $d['gi_donation_target'],
				'gi_donation_total' => $d['gi_donation_total'],
				'gi_image_url' => $d['gi_image_url'],
				'gi_youtube_url'=> $d['gi_youtube_url'],
				'gi_hash_tag' => $d['gi_hash_tag'],
				'gi_display_target' => $d['gi_display_target'],
				'gi_display_current' => $d['gi_display_current'],
				'gi_has_giving_opportunities' => $d['gi_has_giving_opportunities'],
				'gi_status' => $d['gi_status'],
			);
			$vars[] = $variable_row;
			$i++;
						
			if ($limit) {
				if ($i == $limit) {
				break;
				}
			}	
		}
		return $vars;
	}
	
	private function _random_campaign_results($data, $limit) {
		
		/******
		* Check to make sure that the limit provided is not greater
		* than number of available in result array.  If limit is greater
		* auto set the limit to the count of array
		******/
		
		$total_in_array = count($data);
		if ($limit > $total_in_array) 
		{
			$limit = $total_in_array;
		}
		
		$i = 0;
		$keyarray = array_rand($data, $limit);		
		foreach ($keyarray as $d) {
			
			$vars[] = $data[$d];

			$i++;
						
			if ($limit) {
				if ($i == $limit) {
				break;
				}
			}	
		}
		return $vars;
	}
	
	private function _prep_donation_log($data) {
		$vars = array();

		foreach ($data as $d) {
			$variable_row = array(
				'gi_log_date' => $d['date'],
				'gi_log_name' => $d['name'],
				'gi_log_amount' => $d['amount'],
			);
			$vars[] = $variable_row;
		}

		return $vars;
	}
	
}
/* End of file mod.modl_giving_impact.php */
/* Location: /system/expressionengine/third_party/giving_impact/mod.modl_giving_impact.php */