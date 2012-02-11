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
 * Giving Impact Module Control Panel File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Minds On Design Lab
 * @link		http://mod-lab.com
 */

class Modl_giving_impact_mcp {
	
	public $return_data;
	
	private $_base_url;
	private $_form_base;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		
		$this->_base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=giving_impact';
		
		$this->_form_base = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=giving_impact';
		
		$this->EE->cp->set_right_nav(array(
			'module_home' => $this->_base_url,
			'add_api' => $this->_base_url.AMP.'method=add_edit_api',
			'documentation' => 'https://github.com/Minds-On-Design-Lab/modl_giving_impact.ee_addon',
		));
	}
	
	// ----------------------------------------------------------------

	/**
	 * Index Function
	 *
	 * @return 	void
	 */
	public function index()
	{
		$vars = array();
		
		$this->EE->cp->set_variable('cp_page_title', lang('giving_impact_module_name'));
		
		// check for existing credentials
	    $credentials = $this->EE->db->get('giving_impact_api_instance');
		
		if ($credentials->num_rows() > 0) 
		{
			$data['api_account'] = $credentials->row('api_account');
			$data['api_key'] = $credentials->row('api_key');
			$data['api_path'] = $credentials->row('api_path');
		} 
		else 
		{
			$data['api_account'] = '';
			$data['api_key'] = '';
			$data['api_path'] = '';
		}
		
		return $this->EE->load->view('index', $data, TRUE);	
	}
	
	public function add_edit_api() { 
		// Set page title & breadcrumb
	    $this->EE->cp->set_variable('cp_page_title', lang('add_api'));
		$this->EE->cp->set_breadcrumb($this->_base_url, lang('giving_impact_module_name'));
		
	    // Load libs
	    $this->EE->load->library('form_validation');
	
	    // Helpers
	    $this->EE->load->helper('form');
		
		// Validation Rules
	    $this->EE->form_validation->set_rules('api_account','lang:account','required');
	    $this->EE->form_validation->set_rules('api_key','lang:key','required');
	    $this->EE->form_validation->set_rules('api_path','lang:path','required');
	    $this->EE->form_validation->set_error_delimiters('<div class="notice">', '</div>');
	
	    if ($this->EE->form_validation->run())
	    {
	        // We passed the test, onward!
	        $this->_do_add_credentials();
	    }
				    
	    // check for existing credentials
	    $credentials = $this->EE->db->get('giving_impact_api_instance');
				
		if ($credentials->num_rows() > 0) 
		{
			$api_instance_id = $credentials->row('api_instance_id');
			$data['api_account'] = $credentials->row('api_account');
			$data['api_key'] = $credentials->row('api_key');
			$data['api_path'] = $credentials->row('api_path');
		}
		else 
		{
			$api_instance_id = '';
			$data['api_account'] = '';
			$data['api_key'] = '';
			$data['api_path'] = '';
		}
		
	    $data['form_action'] = $this->_form_base.AMP.'method=add_edit_api';
	    $data['form_hidden'] = array(
	    	'api_instance_id' => $api_instance_id,
	    );
	    
	
	    return $this->EE->load->view('add_api_credentials', $data, TRUE);
	}
	
	public function documentation() { 		
		// Set page title & breadcrumb
	    $this->EE->cp->set_variable('cp_page_title', lang('documentation'));
		$this->EE->cp->set_breadcrumb($this->_base_url, lang('giving_impact_module_name'));
		
		return $this->EE->load->view('documentation', '', TRUE);
	}
	
	private function _do_add_credentials() {
	    $data = array(
	        'api_account' => $this->EE->input->post('api_account'),
	        'api_key' => $this->EE->input->post('api_key'),
	        'api_path' => $this->EE->input->post('api_path')
	    );
	    
	    // check for existing credentials and update if exist, insert if not
	    $instance = $this->EE->input->post('api_instance_id');
				
		if (empty($instance)) 
		{
			$this->EE->db->insert('giving_impact_api_instance', $data);		}
		else 
		{
			$this->EE->db->where('api_instance_id', $instance);
            $this->EE->db->update('giving_impact_api_instance', $data);
		}
	
	    $this->EE->session->set_flashdata('message_success', lang('credentials_added'));
	
	    $this->EE->functions->redirect($this->_base_url);
	}

	
}
/* End of file mcp.modl_giving_impact.php */
/* Location: /system/expressionengine/third_party/giving_impact/mcp.modl_giving_impact.php */