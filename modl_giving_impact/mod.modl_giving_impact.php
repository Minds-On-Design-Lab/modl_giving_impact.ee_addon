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
	private $creds = false;

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

		if( is_string($vars) ) {
			return $vars;
		}

		if( !$vars || !count($vars) ) {
			return $this->EE->TMPL->no_results();
		}
		return $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $vars);
	}

	public function public_key() {
		if( !$this->creds ) {
		    $this->creds = $this->EE->db
		    	->where('site_id', $this->EE->config->item('site_id'))
			    ->get('modl_giving_impact_api_instance');
		}

		return $this->creds->row('pub_key');
	}

	public function private_key() {
		if( !$this->creds ) {
		    $this->creds = $this->EE->db
		    	->where('site_id', $this->EE->config->item('site_id'))
			    ->get('modl_giving_impact_api_instance');
		}

		return $this->creds->row('api_key');
	}

	public function donate_js() {
		$full_path = $this->lib_path.'/campaign.php';
		require_once $full_path;

		$c = new Modl_API_Campaign;

		$apiUrl = $c->base_url();
		$publicKey = $this->public_key();

		$formId = $this->EE->TMPL->fetch_param('id') ? $this->EE->TMPL->fetch_param('id') : 'donate-form';

$out = <<<END
<script type="text/javascript" src="{$apiUrl}/checkout?key={$publicKey}"></script>
<script>
	(function(\$) {
	    \$(function() {

	    	// if jquery.payment is avaliable
	    	try {
		        \$('[name="cc_number"]').formatCardNumber();
		        \$('[name="cc_cvc"]').formatCardCVC();
		        \$('[name="cc_exp"]').formatCardExpiry();
	    	} catch(e) {}

	        $('#{$formId}').submit(function(e) {
	        	if( $(this).find('input[name="token"]').length >= 1 ) {
	        		return;
	        	}

	            e.preventDefault();
	            var \$this = \$(this).find('input[type="submit"]');

	            \$this.val('Processing...');
	            \$this.attr('disabled', true);

	            GIAPI.checkout({
	                'card':     \$('[name="cc_number"]').val(),
	                'cvc':      \$('[name="cc_cvc"]').val(),
	                'month':    \$('[name="cc_exp"]').val().substr(0,2),
	                'year':     \$('[name="cc_exp"]').val().substr(5,4),
	            }, function(token) {

	                if( !token ) {
	                    \$('[name="cc_number"]').addClass('error');
	                    \$('<small class="error">Your card was not accepted</small>').insertAfter(\$('[name="cc_number"]'));
	                    \$this.val('Donate');
	                    \$this.attr('disabled', false);
	                    return;
	                }
	                // the card token is returned, append to form and submit
	                \$('#donate-form').append($('<input type="hidden" value="'+token+'" name="token" />'));
	                \$('#donate-form').submit();
	            });
	        })
	    });
	})(jQuery);
</script>
END;

		return $out;

	}

	/**
	 * Process opportunity form data
	 */

	public function post_opportunity() {
		$token = $this->EE->input->post('t');
		$title = $this->EE->input->post('title');
		$description = $this->EE->input->post('description');
		$status = $this->EE->input->post('status') ? 1 : 0;
		$youtube = $this->EE->input->post('youtube');
		$target = $this->EE->input->post('target');
		$captcha = $this->EE->input->post('captcha');

		// $related = $this->EE->input->post('related', false);
		$related = true;
		$opportunity_token = $this->EE->input->post('ot', false);

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
				'link'    => array($this->EE->functions->form_backtrack('0'), 'Return to form')
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
				'link'    => array($this->EE->functions->form_backtrack('0'), 'Return to form')
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

		$full_path = $this->lib_path.'/campaign.php';
		require_once $full_path;

		$c = new Modl_API_Campaign;
		$obj = $c->fetch_campaign($token);

        $campaign_responses = array();
        if( array_key_exists('campaign_fields', $obj) ) {

            $responses = $this->EE->input->post('fields');

            $errors = array();

            foreach( $obj['campaign_fields'] as $f ) {
                if( $f['required'] && $f['status'] && !$responses[$f['field_id']] ) {
                    $errors['fields['.$f['field_id'].']'] = $f['field_label'].' is required';
                    break;
                }

                if( !array_key_exists($f['field_id'], $responses) ) {
                    continue;
                }
                $item = new stdClass;
                $item->response             = $responses[$f['field_id']];
                $item->campaign_field_id    = $f['field_id'];

                $campaign_responses[] = $item;
            }

            if( count($errors) ) {
				$this->EE->session->set_flashdata('formvals', serialize(array(
					'title' => $title,
					'description' => $description,
					'youtube' => $youtube,
					'target' => $target,
					'status' => $status,
					'fields', $responses
				)));

				$data = array(
					'title'   => 'Missing required information',
					'heading' => 'Missing required information',
					'content' => 'Missing required fields: '.implode(', ', $errors),
					'link'    => array($this->EE->functions->form_backtrack('0'), 'Return to form')
				);

				$this->EE->output->show_message($data);
				return;
            }
        }

		// pack it
		$json = array(
			'campaign_token' => $token,
			'title' => $title,
			'description' => $description,
			'status' => $status,
			'campaign_responses' => $campaign_responses
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

		$result = $api->post_single($json, $opportunity_token, $related);

		/**
		 * Hook To Access Return Data
		 */

		if ($this->EE->extensions->active_hook('gi_opportunity_return_data'))
		{
			$hook_result = $this->EE->extensions->call('gi_opportunity_return_data', $result);
			if ($this->EE->extensions->end_script === TRUE) return;
		}

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

	public function post_donation() {

		$token 				= $this->EE->input->post('t');
		$opportunity_token 	= $this->EE->input->post('ot', false);

		$first_name 		= $this->EE->input->post('first_name');
		$last_name 			= $this->EE->input->post('last_name');
		$email 				= $this->EE->input->post('email');
		$street 			= $this->EE->input->post('street');
		$city 				= $this->EE->input->post('city');
		$state 				= $this->EE->input->post('state');
		$zip 				= $this->EE->input->post('zip');
		$donation_level 	= $this->EE->input->post('donation_level');
		$donation_amount 	= $this->EE->input->post('donation_amount');

		$captcha 			= $this->EE->input->post('captcha');

		$card 				= $this->EE->input->post('token');

		$next = $this->EE->input->post('NXT');
		$notify = $this->EE->input->post('NTF');

		$toCheck = array(
			'first_name',
			'last_name',
			'email',
			'street',
			'city',
			'state',
			'zip'
		);

		if( $notify && !valid_email($notify) ) {
			$notify = false;
		}

		$errors = array();
		foreach( $toCheck as $v ) {
			if( !$this->EE->input->post($v) ) {
				$error[] = str_replace('_', ' ', $v).' is required';
			}
		}
		if( !$token && !$opportunity_token ) {
			$errors[] = 'Campaign or Opportunity token is required';
		}
		if( !$donation_level && !$donation_amount ) {
			$errors[] = 'You did not specify a donation amount';
		}
		if( !$card ) {
			$errors[] = 'Could not process credit card';
		}
		if( !valid_email($email) ) {
			$errors[] = 'Please enter a valid email address';
		}
        if( $donation_amount && $donation_amount != floor($donation_amount) ) {
        	$errors[] = 'Please enter a whole dollar amount';
        }

		if( count($errors) ) {

			$data = array(
				'title'   => 'Missing required information',
				'heading' => 'Missing required information',
				'content' => 'Missing required fields: '.implode(', ', $errors),
				'link'    => array($this->EE->functions->form_backtrack('0'), 'Return to form')
			);

			$this->EE->session->set_flashdata('formvals', serialize(array(
				'first_name'		=> $first_name,
				'last_name'			=> $last_name,
				'email'				=> $email,
				'street'			=> $street,
				'city'				=> $city,
				'state'				=> $state,
				'zip'				=> $zip,
				'donation_level'	=> $donation_level,
				'donation_amount'	=> $donation_amount
			)));

			$this->EE->output->show_message($data);
			return;
		}

		// $q = 'select captcha_id from exp_captcha where ip_address = "'.
		// 	$this->EE->db->escape_str($this->EE->input->ip_address()).'" and word = "'.
		// 	$this->EE->db->escape_str($captcha).'"';
		// $res = $this->EE->db->query($q);

		// if( !$res->num_rows() ) {
		// 	$data = array(
		// 		'title'   => 'Missing required information',
		// 		'heading' => 'Missing required information',
		// 		'content' => 'The text you entered didn\'t match the image.',
		// 		'link'    => array($this->EE->functions->form_backtrack('0'), 'Return to form')
		// 	);

		// 	$this->EE->session->set_flashdata('formvals', serialize(array(
		// 		'title' => $title,
		// 		'description' => $description,
		// 		'youtube' => $youtube,
		// 		'target' => $target,
		// 		'status' => $status
		// 	)));

		// 	$this->EE->output->show_message($data);
		// 	return;
		// }

		if( $token ) {
			$full_path = $this->lib_path.'/campaign.php';
			require_once $full_path;

			$c = new Modl_API_Campaign;
			$obj = $c->fetch_campaign($token);
		} else {
			$full_path = $this->lib_path.'/opportunity.php';
			require_once $full_path;

			$c = new Modl_API_Opportunity;
			$obj = $c->fetch_opportunity($opportunity_token, true);

			$obj = $obj['campaign'];
		}

        $custom_fields = array();
        if( array_key_exists('custom_fields', $obj) ) {

            $responses = $this->EE->input->post('fields');

            $errors = array();

            foreach( $obj['custom_fields'] as $f ) {
                if( $f['required'] && $f['status'] && !$responses[$f['field_id']] ) {
                    $errors['fields['.$f['field_id'].']'] = $f['field_label'].' is required';
                    break;
                }

                if( !array_key_exists($f['field_id'], $responses) ) {
                    continue;
                }

                $custom_responses[$f['field_id']] = $responses[$f['field_id']];
            }

            if( count($errors) ) {
				$this->EE->session->set_flashdata('formvals', serialize(array(
					'first_name'		=> $first_name,
					'last_name'			=> $last_name,
					'email'				=> $email,
					'street'			=> $street,
					'city'				=> $city,
					'state'				=> $state,
					'zip'				=> $zip,
					'donation_level'	=> $donation_level,
					'donation_amount'	=> $donation_amount
				)));

				$data = array(
					'title'   => 'Missing required information',
					'heading' => 'Missing required information',
					'content' => 'Missing required fields: '.implode(', ', $errors),
					'link'    => array($this->EE->functions->form_backtrack('0'), 'Return to form')
				);

				$this->EE->output->show_message($data);
				return;
            }
        }

		// pack it
		$json = array(
			'first_name'		=> $first_name,
			'last_name'			=> $last_name,
			'contact'			=> false,
			'email_address'		=> $email,
			'billing_address1'	=> $street,
			'billing_city'		=> $city,
			'billing_state'		=> $state,
			'billing_postal_code' => $zip,
			'billing_country'	=> 'US',
			'donation_total'	=> $donation_level ? $donation_level : $donation_amount,
			'custom_responses' 	=> $custom_responses,
			'card'				=> $card
		);

		if( $opportunity_token ) {
			$json['opportunity'] = $opportunity_token;
		} else {
			$json['campaign']	 = $token;
		}

		require_once $this->lib_path.'/donation.php';
		$api = new Modl_API_Donation;

		$result = $api->post_single($json);

		/**
		 * Hook To Access Return Data
		 */

		if ($this->EE->extensions->active_hook('gi_donation_return_data'))
		{
			$hook_result = $this->EE->extensions->call('gi_donation_return_data', $result);
			if ($this->EE->extensions->end_script === TRUE) return;
		}

		// ---------------------------------------------------------------------------------

		$new_token = $result['donation']['id_token'];

		$this->EE->session->set_flashdata('donation_token', $new_token);

		// send the email
		$webmaster = $this->EE->config->item('webmaster_email');
		if( $notify && $webmaster ) {

			$gi_name = $result['donation']['first_name'].' '.$result['donation']['last_name'];
			$gi_total = $result['donation']['donation_total'];

$message = <<<END
A new donation has been made!

Name: {$gi_name}

Total: {$gi_total}

----------
This is an automated email sent by the MODL Giving Impact ExpressionEngine Addon.
END;

			$this->EE->email->wordwrap = TRUE;
			$this->EE->email->mailtype = 'text';
//			$this->EE->email->debug = TRUE;
			$this->EE->email->from($webmaster);
			$this->EE->email->to($notify);
			$this->EE->email->subject('New Giving Impact Donation');
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

	public function donate_form() {
		$tagdata = $this->EE->TMPL->tagdata;

		if( $this->EE->TMPL->fetch_param('opportunity') ) {
			require_once $this->lib_path.'/opportunity.php';
			$m = new Modl_API_Opportunity;
			$obj = $m->fetch_single(true);
		} else {
			require_once $this->lib_path.'/campaign.php';
			$m = new Modl_API_Campaign;
			$obj = $m->fetch_single();
		}

		$vars = array(
			'value_first_name'	=> false,
			'value_last_name'	=> false,
			'value_email'		=> false,
			'value_street'		=> false,
			'value_city'		=> false,
			'value_state'		=> false,
			'value_zip'			=> false,
			'value_donation_amount'	=> false,
			'value_donation_level' => false
		);

		if( $this->EE->session->flashdata('formvals') ) {
			$vals = unserialize($this->EE->session->flashdata('formvals'));
			if( $vals && count($vals) ) {
				foreach( $vals as $k => $v ) {
					$vars['value_'.$k] = $v;
				}
			}
		}

		$vars = array_merge($vars, $obj[0]);

		$data = array(
			'action' => $this->EE->functions->fetch_current_uri(),
			'id' => $this->EE->TMPL->fetch_param('id', false),
			'class' => $this->EE->TMPL->fetch_param('class', false),
			'secure' => TRUE,
			'enctype' => 'multi'
		);

		$data['hidden_fields'] = array(
			'ACT' => $this->EE->functions->fetch_action_id('Modl_giving_impact', 'post_donation'),
			't' => $this->EE->TMPL->fetch_param('campaign', false),
			'ot' => $this->EE->TMPL->fetch_param('opportunity', false)
		);

		// If return parameter is used, add to hidden_fields

		if ($this->EE->TMPL->fetch_param('return', false)) {
			$data['hidden_fields']['NXT'] = $this->EE->TMPL->fetch_param('return', false);
		}

		// If notify parameter is user, add to hidden_fields

		if ($this->EE->TMPL->fetch_param('notify', false)) {
			$data['hidden_fields']['NTF'] = $this->EE->TMPL->fetch_param('notify', false);
		}

		// Create form wrapper

		$tagdata = $this->EE->functions->form_declaration($data) . $tagdata . '</form>';

		return $this->EE->TMPL->parse_variables($tagdata, array($vars));
	}

	/**
	 * Form for Giving Opportunity Creation
	 */

	public function opportunity_form() {

		$full_path = $this->lib_path.'/campaign.php';
		require_once $full_path;

		$tagdata = $this->EE->TMPL->tagdata;

		$c = new Modl_API_Campaign;
		$obj = $c->fetch_single();

		// Initiate the data array for form
		$data = array(
			'action' => $this->EE->functions->fetch_current_uri(),
			'id' => $this->EE->TMPL->fetch_param('id', false),
			'class' => $this->EE->TMPL->fetch_param('class', false),
			'secure' => TRUE,
			'enctype' => 'multi'
		);

		// Default hidden fields

		$data['hidden_fields'] = array(
			'ACT' => $this->EE->functions->fetch_action_id('Modl_giving_impact', 'post_opportunity'),
			't' => $this->EE->TMPL->fetch_param('campaign', false),
			'ot' => $this->EE->TMPL->fetch_param('opportunity', false),
			'r'	=> $this->EE->TMPL->fetch_param('related', false)
		);

		// If return parameter is used, add to hidden_fields

		if ($this->EE->TMPL->fetch_param('return', false)) {
			$data['hidden_fields']['NXT'] = $this->EE->TMPL->fetch_param('return', false);
		}

		// If notify parameter is user, add to hidden_fields

		if ($this->EE->TMPL->fetch_param('notify', false)) {
			$data['hidden_fields']['NTF'] = $this->EE->TMPL->fetch_param('notify', false);
		}

		// Create form wrapper

		$tagdata = $this->EE->functions->form_declaration($data) . $tagdata . '</form>';

		// Values for data, may be useful for edit in future and used in validation

		$vars = array(
			'opportunity_token' => false,
			'value_title' => false,
			'value_description' => false,
			'value_youtube' => false,
			'value_target' => false,
			'value_status' => false
		);

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

		// Return form and data
		//
		$vars = array_merge($vars, $obj[0]);

		return $this->EE->TMPL->parse_variables($tagdata, array($vars));

	}

}
/* End of file mod.modl_giving_impact.php */
/* Location: /system/expressionengine/third_party/giving_impact/mod.modl_giving_impact.php */