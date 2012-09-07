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

	public function __call($f, $args = array()) {
		if( !array_key_exists(singular($f), $this->loaded) ) {
			$file = singular($f).'.php';
			$klass = 'Modl_API_'.ucfirst(singular($f));

			$full_path = $this->lib_path.'/'.$file;

			require_once $full_path;

			if( !file_exists($full_path) ) {
				$this->EE->output->fatal_error('Invalid API type '.$f);
				return;
			}

			$obj = new $klass;

			$this->loaded[singular($f)] = $obj;
		}

		/*
		if( count($this->EE->TMPL->tagparts) >= 3 ) {
			// possible future support for method calls within an API lib
			$m = $this->EE->TMPL->tagparts[2];
		}
		*/

		$obj = $this->loaded[singular($f)];

		$vars = $obj->process($this->EE->TMPL);

		return $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $vars);
	}

	public function post_opportunity() {
		$token = $this->EE->input->post('t');
		$title = $this->EE->input->post('title');
		$description = $this->EE->input->post('description');
		$status = $this->EE->input->post('status') ? 1 : 0;
		$youtube = $this->EE->input->post('youtube');
		$target = $this->EE->input->post('target');

		if( !$token || !$title || !$description ) {

			$data = array(
				'title'   => 'Missing required information',
				'heading' => 'Missing required information',
				'content' => 'You must provide token, title, status and description',
				'link'    => array($this->EE->functions->form_backtrack('-1'), 'Return to form')
			);

			$this->EE->output->show_message($data);
			return;
		}

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

		$this->EE->session->set_flashdata('opportunity_token', $new_token);

		if( $return_url ) {
			$return_url = base64_decode($return_url);
			if( strpos($return_url, 'http://') === false ) {
				$return_url = $this->EE->functions->create_url($return_url);
			}
		} else {
			$return_url = $this->EE->functions->form_backtrack('-1');
		}

		$this->EE->functions->redirect($return_url, 'location');
		return;
	}

	public function create_opportunity() {
		$action_url = $this->EE->functions->fetch_site_index(0, 0)
			.QUERY_MARKER.'ACT='
			.$this->EE->functions->fetch_action_id(
				'Modl_giving_impact', 'post_opportunity'
			);
		$token = $this->EE->TMPL->fetch_param('campaign', false);
		$return = $this->EE->TMPL->fetch_param('return', false);
		$label = $this->EE->TMPL->fetch_param('label', 'Submit');

		$open = '<form method="POST" action="'.$action_url
			.'" enctype="multipart/form-data">'
			."\n\n"
			.'<input type="hidden" value="'.$token.'" name="t" />';

		if( $return ) {
			$open .= "\n\n"
				.'<input type="hidden" name="r" value="'
				.base64_encode($return).'" />';
		}

		$inner = $this->EE->TMPL->tagdata;

		$close = '<input type="submit" value="'.$label.'" /></form>';

		$out = $open.$inner.$close;

		$vars = array();
		$created_opp = $this->EE->session->flashdata('opportunity_token');;
		if( $created_opp ) {
			$vars[] = array(
				'opportunity_token' => $created_opp
			);
		}

		return $this->EE->TMPL->parse_variables($out, $vars);
	}

}
/* End of file mod.modl_giving_impact.php */
/* Location: /system/expressionengine/third_party/giving_impact/mod.modl_giving_impact.php */