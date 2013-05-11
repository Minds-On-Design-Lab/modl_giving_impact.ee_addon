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

/*
	No results hackery inspired by:
	http://experienceinternet.co.uk/blog/ee-gotchas-nested-no-results-tags/
*/

// ------------------------------------------------------------------------

class Modl_API_Donation extends Giving_impact_api {

	private $api_path = '';

	private $limit = 10;
	private $offset = 0;
	private $sort = 'created_at';
	private $dir = 'asc';
	private $status = 'active';
	private $related = false;

	private $max_limit = 100;

	public function process() {

		if( $this->EE->TMPL->fetch_param('campaign', false) ) {
			return $this->fetch_donations('campaign');
		}

		if( $this->EE->TMPL->fetch_param('opportunity', false) ) {
			return $this->fetch_donations('opportunity');
		}
	}

	public function fetch_donations($type) {
		$token = $this->EE->TMPL->fetch_param($type, false);

		$limit = $this->EE->TMPL->fetch_param('limit', $this->limit);
		$offset = $this->EE->TMPL->fetch_param('offset', $this->offset);
		$sort = $this->EE->TMPL->fetch_param('sort', $this->sort);
		$related = $this->EE->TMPL->fetch_param('related', $this->related);

		$dir = $this->dir;
		if( strpos($sort, '|') !== false ) {
			$temp = explode('|', $sort);
			$sort = $sort[0];
			if( $sort[1] == 'desc' || $sort[1] == 'asc' ) {
				$dir = $sort[1];
			}
		}

		switch( $this->EE->TMPL->fetch_param('status', false) ) {
			case 'active':
			case 'inactive':
			case 'both':
				$status = $this->EE->TMPL->fetch_param('status', false);
				break;
			default:
				$status = $this->status;
		}

		$url = $this->build_url(plural($type).'/'.$token.'/donations', array(
			'limit' => $limit,
			'offset' => $offset,
			'sort' => sprintf('%s|%s', $sort, $dir),
			'status' => $status,
			'related' => $related
		));
		$data = $this->get($url);

		if( !$data || !count($data['donations']) ) {
			$pattern = '#{if no_donations}(.*?){/if}#s';
			if( is_string($this->EE->TMPL->tagdata)
				&& preg_match($pattern, $this->EE->TMPL->tagdata, $matches) ) {
				return $matches[1];
			}
		}

		if( $data['error'] ) {
			$this->EE->output->fatal_error('Error: '.$data['message']);
		}

		// if( $related ) {
		// 	foreach( $data['donations'] as $k => $v ) {
		// 		if( array_key_exists('opportunity', $v) ) {
		// 			$ret = $this->prefix_tags('gi', array($v['opportunity']));
		// 			$data['donations'][$k]['opportunity'] = $ret;
		// 			$data['donations'][$k]['has_opportunity'] = true;
		// 		}
		// 		if( array_key_exists('campaign', $v) ) {
		// 			$ret = $this->prefix_tags('gi', array($v['campaign']));
		// 			$data['donations'][$k]['campaign'] = $ret;
		// 			$data['donations'][$k]['has_campaign'] = true;
		// 		}
		// 	}
		// }
		//

		$donations = array();
		foreach( $data['donations'] as $donation ) {
			if( array_key_exists('opportunity', $donation) ) {
				$donation['has_opportunity'] = true;
			}
			$donations[] = $donation;
		}
		return $this->prefix_tags('gi', $donations, true);
	}
}