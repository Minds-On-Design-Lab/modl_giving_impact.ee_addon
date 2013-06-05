<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package		Giving Impact
 * @author		Minds On Deisgn Lab
 * @copyright	Copyright (c) 2012, Minds On Design Lab Inc.
 * @license
 * @link		http://givingimpact.com
 * @since		Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

class Giving_impact_api {

	private $base_url	= false;
	private $api_key	= false;
	private $user_agent = 'Modl_Giving_Impact/EE_Addon';

	public function __construct() {
		$this->EE =& get_instance();

		$this->user_agent .= ' '.$this->EE->config->item('site_name')
			.'('.$this->EE->config->item('site_url').')';

		// Get existing credentials
	    $creds = $this->EE->db
	    	->where('site_id', $this->EE->config->item('site_id'))
		    ->get('modl_giving_impact_api_instance');

		if ($creds->num_rows() > 0)  {
			$this->api_key = $creds->row('api_key');
			$this->base_url = $creds->row('api_path').'/v2';
		}

		// Get sitename for user_agent
		$this->user_agent = 'EECMS/'.$this->EE->config->item('site_url');
	}

	/**
	 * Prefixes tags returned to template
	 *
	 * @param string $pfx
	 * @param array  $data data returned from API
	 *
	 * @return array
	 *
	 * @access protected
	 * @final
	 */
	protected function prefix_tags($pfx, $data, $recurse = false) {
		$out = array();

		foreach( $data as $item ) {
			$row = array();

			foreach( $item as $k => $v ) {

				if( is_array($v) ) {
					reset($v);
					if( is_int(key($v)) ) {
						$row[$pfx.'_'.$k] = $this->prefix_indexed($k, $v);
					} else {
						$row[$pfx.'_'.$k] = $this->prefix_assoc($k, $v);
					}
				} else {
					$row[$pfx.'_'.$k] = $v;
				}
			}

			$out[] = $row;
		}

		return $out;
	}

	/**
	 * prefixes indexed arrays
	 * @param  string $pfx
	 * @param  array $data
	 * @return array
	 */
	protected function prefix_indexed($pfx, $data) {
		$out = array();
		if( is_array(reset($data)) ) {
			foreach( $data as $i => $item ) {
				$row = array();
				foreach( $item as $k => $v ) {
					if( is_array($v) ) {
						reset($v);
						if( is_int(key($v)) ) {
							$row[$pfx.'_'.$k] = $this->prefix_indexed($k, $v);
						} else {
							$row[$pfx.'_'.$k] = $this->prefix_assoc($k, $v);
						}
					} else {
						$row[$pfx.'_'.$k] = $v;
					}
				}
				$out[] = $row;
			}

			return $out;
		} else {
			return $data;
		}
	}

	/**
	 * prefixes associative arrays
	 * @param  string $pfx
	 * @param  array $data
	 * @return array
	 */
	protected function prefix_assoc($pfx, $data) {
		$out = array();
		foreach( $data as $k => $v ) {
			if( is_array($v) ) {
				reset($v);
				if( is_int(key($v)) ) {
					$out[$pfx.'_'.$k] = $this->prefix_indexed($k, $v);
				} else {
					$out[$pfx.'_'.$k] = $this->prefix_assoc($k, $v);
				}
			} else {
				$out[$pfx.'_'.$k] = $v;
			}
		}

		return array($out);
	}

	/**
	 * Builds an API URL
	 *
	 * @param string $path the API action subpath,
	 *                     e.g. categories, opportunities
	 * @param array  $args key/value array of GET parameters
	 *
	 * @return string
	 *
	 * @access protected
	 * @final
	 */
	protected function build_url($path, $args = array()) {
		$query = '';
		if( count($args) ) {
			$query = '?'.http_build_query($args);
		}
		return sprintf(
			'%s/%s%s', rtrim($this->base_url, '/'), rtrim($path, '/'), $query
		);
	}

	/**
	 * Fetch and return data from API
	 *
	 * @param string $url
	 *
	 * @return array
	 *
	 * @access protected
	 * @final
	 */
	protected function get($url) {

		$raw_json = $this->_curl_fetch($url);
		$data = json_decode($raw_json, true);

		if( $data['error'] ) {
			$this->EE->output->fatal_error('Error: '.$data['message']);
		}

		return $data;

	}

	public function post($url, $data) {
		$raw_json = $this->_curl_fetch($url, json_encode($data));

		$return = json_decode($raw_json, true);

		if( $return['error'] ) {
			$this->EE->output->fatal_error('Error: '.$return['message']);
		}

		return $return;
	}

	protected function _curl_fetch($url, $data = false) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://". $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
		curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__).'/cacert.pem');
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			array('X-GI-Authorization: '.$this->api_key)
		);

		if( $data ) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt(
				$ch,
				CURLOPT_HTTPHEADER,
				array(
					'X-GI-Authorization: '.$this->api_key,
					'Content-Type: application/json'
				)
			);
		}

		$data = curl_exec($ch);

		curl_close($ch);

		return $data;
	}

}
// END CLASS

/* End of file giving_impact_api.php */
/* Location: ./system/expressionengine/third_party/giving_impact/libraries/giving_impact_api.php */