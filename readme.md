# MODL Giving Impact

## Overview

An ExpressionEngine 2.x module to interact with Giving Impact(TM).

**Developed By:** Minds On Design Lab - http://mod-lab.com<br />
**Version:** 2.0<br />
**Copyright:** Copyright &copy; 2011 Minds On Design Lab<br />
**License:** Licensed under the MIT license - Please refer to LICENSE

## Installation

Install in system/expressionengine/third_party/modl_giving_impact

## Usage

## Campaigns

Returns all active campaigns

	{exp:modl_giving_impact:campaigns}
		{gi_title}
		{gi_description}
		{gi_token}
		{gi_donation_url}
		{gi_share_url}
		{gi_donation_target}
		{gi_donation_total}
		{gi_image_url}
	{/exp:modl_giving_impact:campaigns}

## Single Campaign

Returns data for campaign with provided token

	{exp:giving_impact:campaigns token="[unique-campaign-token]"}
		{gi_title}
		{gi_description}
		{gi_token}
		{gi_donation_url}
		{gi_share_url}
		{gi_donation_target}
		{gi_donation_total}
		{gi_image_url}
	{/exp:giving_impact:campaigns}

## Campaign Giving Opportunities

Returns all giving opportunities within campaign with provided token

	{exp:giving_impact:opportunities token="[unique-campaign-token]"}
		{gi_title}
		{gi_description}
		{gi_token}
		{gi_donation_url}
		{gi_share_url}
		{gi_donation_target}
		{gi_donation_total}
		{gi_image_url}
	{/exp:giving_impact:opportunities}


## Single Giving Opportunity

Returns data for a Giving Opportunity with provided token

	{exp:giving_impact:opportunities token="[unique-giving-opp-token]"}
		{gi_title}
		{gi_description}
		{gi_token}
		{gi_donation_url}
		{gi_share_url}
		{gi_donation_target}
		{gi_donation_total}
		{gi_image_url}
	{/exp:giving_impact:opportunities}


## Single Giving Opportunity Donation Log

Returns donation log data for a specific Giving Opportunity with provided token

	{exp:giving_impact:donations token="[unique-giving-opp-token]"}
		{gi_log_date}
		{gi_log_amount}
		{gi_log_name}
	{/exp:giving_impact:donations}


## Changelog

* 08222012 - Version 2.0
* 02172012 - Added MSM support