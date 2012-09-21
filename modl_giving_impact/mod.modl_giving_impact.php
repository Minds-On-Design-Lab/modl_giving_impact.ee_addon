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
		$this->EE->load->library('email');

		$this->EE->load->helper('inflector');
		$this->EE->load->helper('text');
		$this->EE->load->helper('email');

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

		if( !$vars || !count($vars) ) {
			return $this->EE->TMPL->no_results();
		}
		return $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $vars);
	}

	public function post_opportunity() {
		$token = $this->EE->input->post('t');
		$title = $this->EE->input->post('title');
		$description = $this->EE->input->post('description');
		$status = $this->EE->input->post('status') ? 1 : 0;
		$youtube = $this->EE->input->post('youtube');
		$target = $this->EE->input->post('target');
		$captcha = $this->EE->input->post('captcha');

		$next = $this->EE->input->post('NXT');
		$notify = $this->EE->input->post('NTF');

		if( $notify && !valid_email($notify) ) {
			$notify = false;
		}

		if( !$token || !$title || !$description ) {
			$errors = array();
			if( !$token ) {
				$errors[] = 'Campaign Token';
			}
			if( !$title ) {
				$errors[] = 'Title';
			}
			if( !$description ) {
				$errors[] = 'Description';
			}

			$errors = implode(', ', $errors);

			$data = array(
				'title'   => 'Missing required information',
				'heading' => 'Missing required information',
				'content' => 'Missing required fields: '.$errors,
				'link'    => array($this->EE->functions->form_backtrack(), 'Return to form')
			);

			$this->EE->session->set_flashdata('formvals', serialize(array(
				'title' => $title,
				'description' => $description,
				'youtube' => $youtube,
				'target' => $target,
				'status' => $status
			)));

			$this->EE->output->show_message($data);
			return;
		}

		$q = 'select captcha_id from exp_captcha where ip_address = "'.
			$this->EE->db->escape_str($this->EE->input->ip_address()).'" and word = "'.
			$this->EE->db->escape_str($captcha).'"';
		$res = $this->EE->db->query($q);

		if( !$res->num_rows() ) {
			$data = array(
				'title'   => 'Missing required information',
				'heading' => 'Missing required information',
				'content' => 'The text you entered didn\'t match the image.',
				'link'    => array($this->EE->functions->form_backtrack(), 'Return to form')
			);

			$this->EE->session->set_flashdata('formvals', serialize(array(
				'title' => $title,
				'description' => $description,
				'youtube' => $youtube,
				'target' => $target,
				'status' => $status
			)));

			$this->EE->output->show_message($data);
			return;
		}

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

		// send the email
		$webmaster = $this->EE->config->item('webmaster_email');
		if( $notify && $webmaster ) {

			$gi_title = $result['opportunity']['title'];
			$gi_description = $result['opportunity']['description'];
			$gi_donation_url = $result['opportunity']['donation_url'];
			$gi_status = $result['opportunity']['status'] ? 'Active' : 'Inactive';

$message = <<<END
A new Giving Opportunity has been created!

Title: {$gi_title}

Description: {$gi_description}

Status: {$gi_status}

----------
This is an automated email sent by the MODL Giving Impact ExpressionEngine Addon.
END;

			$this->EE->email->wordwrap = TRUE;
			$this->EE->email->mailtype = 'text';
//			$this->EE->email->debug = TRUE;
			$this->EE->email->from($webmaster);
			$this->EE->email->to($notify);
			$this->EE->email->subject('New Giving Impact Opportunity');
			$this->EE->email->message(entities_to_ascii($message));
			$this->EE->email->Send();
		}

		if( $next ) {
			$return_url = $next;
		} else {
			$return_url = $this->EE->functions->form_backtrack();
		}

		$this->EE->functions->redirect($return_url.'/'.$new_token, 'location');
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
		$class = $this->EE->TMPL->fetch_param('class', false);
		$id = $this->EE->TMPL->fetch_param('id', false);
		$notify = $this->EE->TMPL->fetch_param('notify', false);

		if( $notify && !valid_email($notify) ) {
			$notify = false;
		}



		$open = '<form method="POST" action="'.$action_url
			.'" enctype="multipart/form-data"';

		if( $class ) {
			$open .= ' class="'.$class.'"';
		}
		if( $id ) {
			$open .= ' id="'.$id.'"';
		}

		$open .= '>'
			."\n\n"
			.'<input type="hidden" value="'.$token.'" name="t" />';

		$open .= "\n"
			.'<input type="hidden" name="RET" value="'.
			$this->EE->functions->fetch_current_uri()
			.'" />';

		if( $return ) {
			$open .= "\n"
				.'<input type="hidden" name="NXT" value="'.
				$return
				.'" />';
		}
		if( $notify ) {
			$open .= "\n"
				.'<input type="hidden" name="NTF" value="'.
				$notify
				.'" />';
		}

		$inner = $this->EE->TMPL->tagdata;

		$close = '</form>';

		$out = $open.$inner.$close;

		// we stored this earlier, makes it easy to check for created opp inside form
		$vars = array(
			'opportunity_token' => false,
			'value_title' => false,
			'value_description' => false,
			'value_youtube' => false,
			'value_target' => false,
			'value_status' => false
		);

		$created_opp = $this->EE->session->flashdata('opportunity_token');;
		if( $created_opp ) {
			$vars['opportunity_token'] = $created_opp;
		}
		if( $this->EE->session->flashdata('formvals') ) {
			$vals = unserialize($this->EE->session->flashdata('formvals'));
			if( $vals && count($vals) ) {
				$vars['value_title'] = $vals['title'];
				$vars['value_description'] = $vals['description'];
				$vars['value_youtube'] = $vals['youtube'];
				$vars['value_target'] = $vals['target'];
				$vars['value_status'] = $vals['status'];
			}
		}

		return $this->EE->TMPL->parse_variables($out, array($vars));
	}

}
/* End of file mod.modl_giving_impact.php */
/* Location: /system/expressionengine/third_party/giving_impact/mod.modl_giving_impact.php */