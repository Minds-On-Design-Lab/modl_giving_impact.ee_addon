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

class Modl_API_Opportunity extends Giving_impact_api {

	private $api_path = 'opportunities';

	private $limit = 10;
	private $offset = 0;
	private $sort = 'created_at';
	private $dir = 'asc';

	private $max_limit = 100;

	public function process() {

		if( $this->EE->TMPL->fetch_param('token', false) ) {
			return $this->fetch_single();
		}

		return $this->fetch();
	}

	public function fetch_single() {
		$token = $this->EE->TMPL->fetch_param('token', false);

		$url = $this->build_url($this->api_path.'/'.$token);

		$data = $this->get($url);

		if( !$data || !count($data['opportunity']) ) {
			return;
		}

		if( $data['error'] ) {
			$this->EE->output->fatal_error('Error: '.$data['message']);
		}

		// if the tag content contains donation loop tag pair
		if( strpos($this->EE->TMPL->tagdata, '{gi_donations}') !== false ) {
			$url = $this->build_url($this->api_path.'/'.$token.'/donations');
			$donation_data = $this->get($url);

			$donations = array();

			if( count($donation_data['donations']) ) {
				// manually prefix and jam it on the end
				$donations = $this->prefix_tags(
					'gi_donation', $donation_data['donations']
				);
			}

			$data['opportunity']['donations'] = $donations;
		}

		return $this->prefix_tags('gi', array($data['opportunity']));
	}

	public function fetch() {
		$campaign = $this->EE->TMPL->fetch_param('campaign', false);
		$limit = $this->EE->TMPL->fetch_param('limit', $this->limit);
		$offset = $this->EE->TMPL->fetch_param('offset', $this->offset);
		$sort = $this->EE->TMPL->fetch_param('sort', $this->sort);

		$dir = $this->dir;
		if( strpos($sort, '|') !== false ) {
			$temp = explode('|', $sort);
			$sort = $sort[0];
			if( $sort[1] == 'desc' || $sort[1] == 'asc' ) {
				$dir = $sort[1];
			}
		}

		if( !$campaign ) {
			$this->EE->output->fatal_error('Campaign token is required');
			return;
		}

		$url = $this->build_url(
			'campaigns/'.$campaign.'/opportunities',
			array(
				'limit' => $limit,
				'offset' => $offset,
				'sort' => sprintf('%s|%s', $sort, $dir)
			)
		);

		$data = $this->get($url);

		if( !$data || !count($data['opportunities']) ) {
			return;
		}

		if( $data['error'] ) {
			$this->EE->output->fatal_error('Error: '.$data['message']);
		}

		return $this->prefix_tags('gi', $data['opportunities']);
	}

}