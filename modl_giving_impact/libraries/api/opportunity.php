<?php

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

		if( strpos($this->EE->TMPL->tagdata, '{gi_donations}') !== false ) {
			$url = $this->build_url($this->api_path.'/'.$token.'/donations');
			$donation_data = $this->get($url);

			$donations = array();

			if( count($donation_data['donations']) ) {
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
		$sort = $this->EE->TMPL->fetch_param('sort_by', $this->sort);

		$dir = $this->dir;
		if( $this->EE->TMPL->fetch_param('sort_reverse', 'n') == 'y' ) {
			$dir = 'desc';
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