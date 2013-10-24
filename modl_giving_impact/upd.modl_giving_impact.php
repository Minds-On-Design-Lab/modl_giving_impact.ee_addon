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
 * Giving Impact Module Install/Update File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Minds On Design Lab
 * @link		http://mod-lab.com
 */

class Modl_giving_impact_upd {


	public $version = '2.3.1';

	private $EE;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
	}

	// ----------------------------------------------------------------

	/**
	 * Installation Method
	 *
	 * @return 	boolean 	TRUE
	 */
	public function install()
	{
		$mod_data = array(
			'module_name'			=> 'Modl_giving_impact',
			'module_version'		=> $this->version,
			'has_cp_backend'		=> "y",
			'has_publish_fields'	=> 'n'
		);

		$this->EE->db->insert('modules', $mod_data);

		$this->EE->load->dbforge();

		// API Access Info
		$api_access_table_fields = array(
			'api_instance_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'site_id'		=> array(
            	'type'			=> 'INT',
            	'constraint' 	=> 11,
            	'null'			=> FALSE
            ),
            'api_key'          => array(
                'type'          => 'VARCHAR',
                'constraint'        => 255,
                'null'          => FALSE
            ),
            'pub_key'          => array(
                'type'          => 'VARCHAR',
                'constraint'        => 255,
                'null'          => FALSE
            )
        );

		$this->EE->dbforge->add_field($api_access_table_fields);
        $this->EE->dbforge->add_key('api_instance_id', TRUE);
        $this->EE->dbforge->create_table('modl_giving_impact_api_instance');

    // Opportunity Form Post Action
		$this->EE->db->insert('actions', array(
			'class'		=> 'Modl_giving_impact' ,
			'method'	=> 'post_opportunity'
		));

		// Donation Form Post Action
		$this->EE->db->insert('actions', array(
			'class'		=> 'Modl_giving_impact',
			'method'	=> 'post_donation'
		));


		
		return TRUE;
	}

	// ----------------------------------------------------------------

	/**
	 * Uninstall
	 *
	 * @return 	boolean 	TRUE
	 */
	public function uninstall()
	{
		$mod_id = $this->EE->db->select('module_id')
								->get_where('modules', array(
									'module_name'	=> 'Modl_giving_impact'
								))->row('module_id');

		$this->EE->db->where('module_id', $mod_id)
					 ->delete('module_member_groups');

		$this->EE->db->where('module_name', 'Modl_giving_impact')
					 ->delete('modules');


		//Drop Custom Tables
		$this->EE->load->dbforge();

		$this->EE->dbforge->drop_table('modl_giving_impact_api_instance');

		$this->EE->db
			->where('class', 'Modl_giving_impact')
			->delete('actions');

		return TRUE;
	}

	// ----------------------------------------------------------------

	/**
	 * Module Updater
	 *
	 * @return 	boolean 	TRUE
	 */
	public function update($current = '')
	{
		$this->EE->load->dbforge();
		// If you have updates, drop 'em in here.
		if( version_compare($current, '1.1', '<') ) {
			// add the site id
			$this->EE->dbforge->add_column(
				'modl_giving_impact_api_instance',
				array(
					'site_id' => array(
						'type'			=> 'INT',
						'constraint'	=> 11,
						'null'			=> FALSE
					)
				)
			);

			$this->EE->db->update(
				'modl_giving_impact_api_instance',
				array(
					'site_id' => 1
				)
			);
		} elseif( version_compare($current, '2', '<') ) {
			// remove account
			$this->EE->dbforge->drop_column(
				'modl_giving_impact_api_instance',
				'api_account'
			);

		} elseif( version_compare($current, '2.1', '<') ) {
			// Add action
			$this->EE->db->insert('actions', array(
				'class'		=> 'Modl_giving_impact' ,
				'method'	=> 'post_opportunity'
			));
		} elseif( version_compare($current, '2.2.2', '<') ) {
			// remove api_path
			$this->EE->dbforge->drop_column(
				'modl_giving_impact_api_instance',
				'api_path'
			);
		} elseif( version_compare($current, '2.3', '<') ) {
			// Add action
			$this->EE->db->insert('actions', array(
				'class'		=> 'Modl_giving_impact',
				'method'	=> 'post_donation'
			));
		} elseif( version_compare($current, '2.3.1', '<') ) {
			$this->EE->dbforge->add_column(
				'modl_giving_impact_api_instance',
				array(
		            'pub_key'          => array(
		                'type'          => 'VARCHAR',
		                'constraint'        => 255,
		                'null'          => FALSE
		            )
				)
			);
		}

		return TRUE;
	}

}
/* End of file upd.modl_giving_impact.php */
/* Location: /system/expressionengine/third_party/giving_impact/upd.modl_giving_impact.php */