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

class Modl_API_Campaign extends Giving_impact_api {

	private $api_path = 'campaigns';

	private $limit = 10;
	private $offset = 0;
	private $sort = 'created_at';
	private $dir = 'asc';
	private $status = 'active';

	private $max_limit = 100;

	public function process() {

		if( $this->EE->TMPL->fetch_param('campaign', false) ) {
			return $this->fetch_single();
		}

		return $this->fetch();
	}

	public function fetch_single() {
		$token = $this->EE->TMPL->fetch_param('campaign', false);

		switch( $this->EE->TMPL->fetch_param('status', false) ) {
			case 'active':
			case 'inactive':
			case 'both':
				$status = $this->EE->TMPL->fetch_param('status', false);
				break;
			default:
				$status = $this->status;
		}

		$url = $this->build_url($this->api_path.'/'.$token, array(
			'status' => $status
		));

		$data = $this->get($url);

		if( !$data || !count($data['campaign']) ) {
			return;
		}

		if( $data['error'] ) {
			$this->EE->output->fatal_error('Error: '.$data['message']);
		}

		return $this->prefix_tags('gi', array($data['campaign']));
	}

	public function fetch() {
		$limit = $this->EE->TMPL->fetch_param('limit', $this->limit);
		$offset = $this->EE->TMPL->fetch_param('offset', $this->offset);
		$sort = $this->EE->TMPL->fetch_param('sort', $this->sort);

		if( $sort && strpos($sort, 'gi_') === 0 ) {
			$sort = substr($sort, 3);
		}

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

		$url = $this->build_url(
			$this->api_path,
			array(
				'limit' => $limit,
				'offset' => $offset,
				'sort' => sprintf('%s|%s', $sort, $dir),
				'status' => $status
			)
		);

		$data = $this->get($url);

		if( !$data || !count($data['campaigns']) ) {
			return;
		}

		if( $data['error'] ) {
			$this->EE->output->fatal_error('Error: '.$data['message']);
		}

		return $this->prefix_tags('gi', $data['campaigns']);
	}

}