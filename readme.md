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

	{exp:modl_giving_impact:campaigns campaign="[unique-campaign-token]"}
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

* campaign - STRING - Unique campaign token. **REQUIRED**
* status - STRING - Campaign status, "active", "inactive" or "both". **default = active**

### Campaign Giving Opportunities

Returns all giving opportunities within campaign with provided token

	{exp:modl_giving_impact:opportunities campaign="[unique-campaign-token]"}
		{gi_title}
		{gi_description}
		{gi_token}
		{gi_donation_url}
		{gi_share_url}
		{gi_donation_target}
		{gi_donation_total}
		{gi_image_url}
	{/exp:modl_giving_impact:opportunities}

#### Options

* campaign - STRING - Parent campaign. **REQUIRED**
* limit - INT - Limits results returned. **default = 10**
* offset - INT - Number of results to skip, useful for pagination. **default = 0**
* sort - STRING - Property to sort results by. Also accepts a direction preceded by a pipe. **default = created_at**
* status - STRING - Campaign status, "active", "inactive" or "both". **default = active**


### Single Giving Opportunity

Returns data for a Giving Opportunity with provided token

	{exp:modl_giving_impact:opportunities opportunity="[unique-giving-opp-token]"}
		{gi_title}
		{gi_description}
		{gi_token}
		{gi_donation_url}
		{gi_share_url}
		{gi_donation_target}
		{gi_donation_total}
		{gi_image_url}
	{/exp:modl_giving_impact:opportunities}

#### Options

* opportunity - STRING - Unique giving opportunity token. **REQUIRED**
* status - STRING - Campaign status, "active", "inactive" or "both". **default = active**

### Donation Log

Donations require either a campaign or opportunity token

	{exp:modl_giving_impact:donations opportunity="[unique-token]"}
		{gi_first_name}
		{gi_last_name}
		{gi_billing_address1}
		{gi_billing_city}
		{gi_billing_state}
		{gi_billing_postal_code}
		{gi_billing_country}
		{gi_total}
		{gi_level}
		{gi_email_address}
		{gi_referrer}
		{gi_offline}
		{gi_created_at}
		{gi_twitter_share}
		{gi_fb_share}
	{/exp:modl_giving_impact:donations}

or

	{exp:modl_giving_impact:donations campaign="[unique-token]"}
		{gi_first_name}
		{gi_last_name}
		{gi_billing_address1}
		{gi_billing_city}
		{gi_billing_state}
		{gi_billing_postal_code}
		{gi_billing_country}
		{gi_donation_total}
		{gi_donation_level}
		{gi_email_address}
		{gi_referrer}
		{gi_offline}
		{gi_created_at}
		{gi_twitter_share}
		{gi_fb_share}
	{/exp:modl_giving_impact:donations}

#### Options

* campaign **OR** opportunity - STRING - Parent campaign. **REQUIRED**
* limit - INT - Limits results returned. **default = 10**
* offset - INT - Number of results to skip, useful for pagination. **default = 0**
* sort - STRING - Property to sort results by. Also accepts a direction preceded by a pipe. **default = created_at**

### Create Giving Opportunity

Using the `{exp:modl_giving_impact:create_opportunity}` tag pair you can easily create a form to generate new opportunities.

	{exp:modl_giving_impact:create_opportunity campaign="[unique-tone]" return="[string]" label="Save Opportunity"}

		{if opportunity_token}
			Sweet! Your opportunity was created with token {opportunity_token}<br />
		{/if}

		<input type="text" name="title" /> - REQUIRED campaign title
		<textarea name="description"></textarea> - REQUIRED description
		<input type="select" value="1" name="status" /> - REQUIRED status

		<input type="file" name="image" /> - OPTIONAL image file
		<input type="text" name="target" /> - OPTIONAL target
		<input type="text" name="youtube" /> - OPTIONAL YouTube URL

	{/exp:modl_giving_impact:create_opportunity}

You may use the `{opportunity_token}` variable within the tag pair to check for returned token for the newly created opportunity.

Note that you **MUST** provide inputs for "title", "description" and "status" or your request will display an error.


#### Options

* campaign - STRING - parent campaign token **REQUIRED**
* return - STRING - a return URL **default - returns to form**
* label - STRING - label for submit button **default - Submit**

## Changelog

* 08222012 - Version 2.0
* 02172012 - Added MSM support