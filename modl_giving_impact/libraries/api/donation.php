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

	private $api_path = 'donations';

	private $limit = 10;
	private $offset = 0;
	private $sort = 'created_at';
	private $dir = 'asc';
	private $status = 'active';
	private $related = false;

	private $max_limit = 100;

	public function fetch_single($rel = false) {
		$token = $this->EE->TMPL->fetch_param('donation', false);
		$supporter = $this->EE->TMPL->fetch_param('supporter', false);
		$related = $this->EE->TMPL->fetch_param('related', $rel);

		if( $token ) {
			$url = $this->build_url($this->api_path.'/'.$token, array(
				'related' => $related
			));
		} else {
			$url = $this->build_url($this->api_path, array(
				'related' => $related,
				'supporter' => $supporter
			));
		}

		$data = $this->get($url);

		if( !$data || !count($data['donation']) ) {
			return;
		}

		if( $data['error'] ) {
			$this->EE->output->show_user_error('general', 'Error: '.$data['message']);
		}

		return $this->prefix_tags('gi', array($data['donation']));
	}

	public function process() {

		if( $this->EE->TMPL->fetch_param('campaign', false) ) {
			return $this->fetch_donations('campaign');
		}

		if( $this->EE->TMPL->fetch_param('opportunity', false) ) {
			return $this->fetch_donations('opportunity');
		}

		if( $this->EE->TMPL->fetch_param('donation', false ) ) {
			return $this->fetch_single();
		}

		if( $this->EE->TMPL->fetch_param('supporter', false) ) {
			return $this->fetch_donations('supporter');
		}
	}

	public function fetch_donations($type) {
		$token = $this->EE->TMPL->fetch_param($type, false);
		$supporter = false;

		if( $type == 'supporter' ) {
			$supporter = $token;
			$token = false;
		}

		$limit = $this->EE->TMPL->fetch_param('limit', $this->limit);
		$offset = $this->EE->TMPL->fetch_param('offset', $this->offset);
		$sort = str_replace(
			'gi_', '', $this->EE->TMPL->fetch_param('sort', $this->sort)
		);
		$related = $this->EE->TMPL->fetch_param('related', $this->related);

		$dir = $this->dir;
		if( strpos($sort, '|') !== false ) {
			$temp = explode('|', $sort);
			$sort = $temp[0];
			if( $temp[1] == 'desc' || $temp[1] == 'asc' ) {
				$dir = $temp[1];
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

		if( $token ) {
			$url = $this->build_url(plural($type).'/'.$token.'/donations', array(
				'limit' => $limit,
				'offset' => $offset,
				'sort' => sprintf('%s|%s', $sort, $dir),
				'status' => $status,
				'related' => $related
			));
		} else {
			$url = $this->build_url('donations', array(
				'limit' => $limit,
				'offset' => $offset,
				'sort' => sprintf('%s|%s', $sort, $dir),
				'status' => $status,
				'related' => $related,
				'supporter' => $supporter
			));
		}
		$data = $this->get($url);

		if( !$data || !count($data['donations']) ) {
			$pattern = '#{if no_donations}(.*?){/if}#s';
			if( is_string($this->EE->TMPL->tagdata)
				&& preg_match($pattern, $this->EE->TMPL->tagdata, $matches) ) {
				return $matches[1];
			}
		}

		if( $data['error'] ) {
			$this->EE->output->show_user_error('general', 'Error: '.$data['message']);
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

	public function post_single($data, $related = false) {

		if( !$data ) {
			$this->EE->output->show_user_error('general', 'Could not encode JSON data');
		}

		$url = $this->build_url('donations', array(
				'related' => $related
			));

		return $this->post($url, $data);
	}

}