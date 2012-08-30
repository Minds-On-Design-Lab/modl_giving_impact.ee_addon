# MODL Giving Impact

## Overview

An ExpressionEngine 2.x module to interact with Giving Impact &trade;.

**Developed By:** Minds On Design Lab - http://mod-lab.com<br />
**Version:** 2.0<br />
**Copyright:** Copyright &copy; 2010-2012 Minds On Design Lab<br />
**License:** Licensed under the MIT license - Please refer to LICENSE

## Requirements

* PHP 5
* cURL
* Expression Engine 2.x
* A [Giving Impact](http://givingimpact.com) account.

## Installation

Install in system/expressionengine/third_party/modl_giving_impact

## Usage

### Campaigns

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

#### Options

* limit - INT - Limits results returned. **default = 10**
* offset - INT - Number of results to skip, useful for pagination. **default = 0**
* sort - STRING - Property to sort results by. Also accepts a direction preceded by a pipe. **default = created_at**
* status - STRING - Campaign status, "active", "inactive" or "both". **default = active**

#### Example

	{exp:modl_giving_impact:campaigns sort="title"}
		{gi_title}
		{gi_description}
		{gi_token}
		{gi_donation_url}
		{gi_share_url}
		{gi_donation_target}
		{gi_donation_total}
		{gi_image_url}
	{/exp:modl_giving_impact:campaigns}

	{exp:modl_giving_impact:campaigns limit="10" sort="created_at|desc"}
		{gi_title}
		{gi_description}
		{gi_token}
		{gi_donation_url}
		{gi_share_url}
		{gi_donation_target}
		{gi_donation_total}
		{gi_image_url}
	{/exp:modl_giving_impact:campaigns}


### Single Campaign

Returns data for campaign with provided token

	{exp:giving_impact:campaigns campaign="[unique-campaign-token]"}
		{gi_title}
		{gi_description}
		{gi_token}
		{gi_donation_url}
		{gi_share_url}
		{gi_donation_target}
		{gi_donation_total}
		{gi_image_url}
	{/exp:giving_impact:campaigns}

#### Options

* campaign - STRING - Unique campaign token. **REQUIRED**
* status - STRING - Campaign status, "active", "inactive" or "both". **default = active**

### Campaign Giving Opportunities

Returns all giving opportunities within campaign with provided token

	{exp:giving_impact:opportunities campaign="[unique-campaign-token]"}
		{gi_title}
		{gi_description}
		{gi_token}
		{gi_donation_url}
		{gi_share_url}
		{gi_donation_target}
		{gi_donation_total}
		{gi_image_url}
	{/exp:giving_impact:opportunities}

#### Options

* campaign - STRING - Parent campaign. **REQUIRED**
* limit - INT - Limits results returned. **default = 10**
* offset - INT - Number of results to skip, useful for pagination. **default = 0**
* sort - STRING - Property to sort results by. Also accepts a direction preceded by a pipe. **default = created_at**
* status - STRING - Campaign status, "active", "inactive" or "both". **default = active**


### Single Giving Opportunity

Returns data for a Giving Opportunity with provided token

	{exp:giving_impact:opportunities opportunity="[unique-giving-opp-token]"}
		{gi_title}
		{gi_description}
		{gi_token}
		{gi_donation_url}
		{gi_share_url}
		{gi_donation_target}
		{gi_donation_total}
		{gi_image_url}
	{/exp:giving_impact:opportunities}

#### Options

* opportunity - STRING - Unique giving opportunity token. **REQUIRED**
* status - STRING - Campaign status, "active", "inactive" or "both". **default = active**

### Donation Log

Donations are returned as part of a single campaign or opportunity

	{exp:giving_impact:opportunities opportunity="[unique-token]"}
		{gi_title}
		{gi_description}

		{gi_donations}
            {gi_donation_first_name}
            {gi_donation_last_name}
            {gi_donation_billing_address1}
            {gi_donation_billing_city}
            {gi_donation_billing_state}
            {gi_donation_billing_postal_code}
            {gi_donation_billing_country}
            {gi_donation_donation_total}
            {gi_donation_donation_level}
            {gi_donation_email_address}
            {gi_donation_referrer}
            {gi_donation_offline}
            {gi_donation_created_at}
            {gi_donation_twitter_share}
            {gi_donation_fb_share}
		{/gi_donations}
	{/exp:giving_impact:donations}

##### NOTE:

Donation logs will not be returned for multiple campaigns or opportunities. To retrieve a donation log, you must specify a campaign token.

## Changelog

* 08222012 - Version 2.0
* 02172012 - Added MSM support