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

	}

	public function form_start() {
		$action_url = $this->EE->functions->fetch_site_index(0, 0)
			.QUERY_MARKER.'ACT='
			.$this->EE->functions->fetch_action_id(
				'Modl_giving_impact', 'post_opportunity'
			);
		$token = $this->EE->TMPL->fetch_param('campaign', false);

		$out = '<form method="POST" action="'.$action_url
			.'" content-type="multipart/form-data">'
			."\n\n"
			.'<input type="hidden" value="'.$token.'" name="t" />';


		return $out;
	}

	public function form_end() {
		$val = $this->EE->TMPL->fetch_param('value', 'Submit');

		$out = '<input type="submit" value="'.$val.'" /></form>';
		return $out;
	}

	public function form_title() {
		$class = $this->EE->TMPL->fetch_param('class', false);
		$id = $this->EE->TMPL->fetch_param('id', false);
		$val = $this->EE->TMPL->fetch_param('value', false);

		$out = '<input type="text" name="title" class="'.$class
			.'" id="'.$id.'" value="'.$val.'" />';

		return $out;
	}

	public function form_description() {
		$class = $this->EE->TMPL->fetch_param('class', false);
		$id = $this->EE->TMPL->fetch_param('id', false);
		$val = $this->EE->TMPL->fetch_param('value', false);

		$out = '<textarea name="description" class="'.$class
			.'" id="'.$id.'">'.$val.'</textarea>';

		return $out;
	}

	public function form_status() {
		$class = $this->EE->TMPL->fetch_param('class', false);
		$id = $this->EE->TMPL->fetch_param('id', false);
		$checked = $this->EE->TMPL->fetch_param('checked', false);

		$out = '<input type="checkbox" name="status" value="1" class="'.$class
			.'" id="'.$id.'"';
		if( $checked ) {
			$out .= ' checked';
		}
		$out .= ' />';

		return $out;
	}

	public function form_image() {
		$class = $this->EE->TMPL->fetch_param('class', false);
		$id = $this->EE->TMPL->fetch_param('id', false);

		$out = '<input type="file" name="image" class="'.$class
			.'" id="'.$id.'" />';

		return $out;
	}

	public function form_target() {
		$class = $this->EE->TMPL->fetch_param('class', false);
		$id = $this->EE->TMPL->fetch_param('id', false);
		$val = $this->EE->TMPL->fetch_param('value', false);

		$out = '<input type="text" name="target" class="'.$class
			.'" id="'.$id.'" value="'.$val.'" />';

		return $out;
	}

	public function form_youtube() {
		$class = $this->EE->TMPL->fetch_param('class', false);
		$id = $this->EE->TMPL->fetch_param('id', false);
		$val = $this->EE->TMPL->fetch_param('value', false);

		$out = '<input type="text" name="youtube" class="'.$class
			.'" id="'.$id.'" value="'.$val.'" />';

		return $out;
	}


}
/* End of file mod.modl_giving_impact.php */
/* Location: /system/expressionengine/third_party/giving_impact/mod.modl_giving_impact.php */