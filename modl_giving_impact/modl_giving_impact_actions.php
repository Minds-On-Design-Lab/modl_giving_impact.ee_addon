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

class Modl_giving_impact_actions {

	private $my_path = false;
	private $loaded = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->EE =& get_instance();

		$this->lib_path = rtrim(dirname(__FILE__), '/')
			.'/libraries/api';

		$this->EE->load->library('giving_impact_api');
		$this->EE->load->helper('inflector');

	}

	public function post_opportunity() {
		$token = $this->EE->input->post('t');
		$title = $this->EE->input->post('title');
		$description = $this->EE->input->post('description');
		$status = $this->EE->input->post('status') ? 1 : 0;
		$youtube = $this->EE->input->post('youtube');
		$target = $this->EE->input->post('target') * 100;

		$return_url = $this->EE->input->post('r');

		// pack it
		$json = array(
			'campaign_token' => $token,
			'title' => $title,
			'description' => $description,
			'status' => $status
		);

		if( $youtube ) {
			$json['youtube_url'] = $youtube;
		}

		if( $target ) {
			$json['target'] = $target;
		}

		if( $_FILES && array_key_exists('image', $_FILES) ) {
			$image = $_FILES['image'];

			if( !$image['error'] ) {
				$raw = base64_encode(file_get_contents($image['tmp_name']));
				$type = $image['type'];

				$json['image_file'] = $raw;
				$json['image_type'] = $type;
			}
		}

		require_once $this->lib_path.'/opportunity.php';
		$api = new Modl_API_Opportunity;

		$result = $api->post_single($json);

		$new_token = $result['opportunity']['id_token'];

		if( $return_url ) {
			$return_url = base64_decode($return_url);
			if( strpos($return_url, 'http://') === false ) {
				$return_url = $this->EE->functions->create_url($return_url).$new_token;
			}
		}

		$this->EE->functions->redirect($return_url, 'location');
		return;
	}

}
/* End of file mod.modl_giving_impact.php */
/* Location: /system/expressionengine/third_party/giving_impact/mod.modl_giving_impact.php */