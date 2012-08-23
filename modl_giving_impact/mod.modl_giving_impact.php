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

}
/* End of file mod.modl_giving_impact.php */
/* Location: /system/expressionengine/third_party/giving_impact/mod.modl_giving_impact.php */