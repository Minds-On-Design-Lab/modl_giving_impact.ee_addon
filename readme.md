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

The following ExpressionEngine tag pairs reflect key methods available in the Giving Impact API.  Each pair returns all of it's related method's data as an ee tag with a "gi_" prefix added on. So for example in the GI API /campaigns method, there is data element returned with a campaign's unique token labeled `id_token` which in the related EE tag pair would be returned as `{gi_id_token}`.

The following details the ExpressionEngine tags available, data returned, parameters, and any unique variables available for use. It also details options for create forms to post data to Giving Impact.

To learn more about these methods, the date returned and using this module to bring a customized donation experience to your nonprofit's ExpressionEngine site, please visit [givingimpact.com](http://givingimpact.com)

### Template Tags

* [Campaigns](#campaigns)
* [Opportunities](#opportunities)
* [Donations](#donations)
* [Create Opportunity](#create-giving-opportunity)

### Campaigns

	{exp:modl_giving_impact:campaigns} Content {/exp:modl_giving_impact:campaigns}

#### Optional Parameters

* campaign - STRING - Unique campaign token. If provided will only return that campaign's data.  If not used, then will return multiple campaigns.
* limit - INT - Limits results returned. **default = 10**
* offset - INT - Number of results to skip, useful for pagination. **default = 0**
* sort - STRING - Property to sort results by. Also accepts a direction preceded by a pipe, e.g. `sort="created_at|desc"` **default = created_at**
* status - STRING - Campaign status, "active", "inactive" or "both". **default = active**

#### Variables

	{gi_id_token}

Unique API token and id for the campaign.
	
	{gi_title}

Title of the campaign
	
	{gi_description}

Brief campaign description

	{gi_image_url}

URL to campaign image. Image is hosted with Giving Impact.

	{gi_youtube_id}

YouTube ID for campaign video.

	{gi_donation_url}

URL to the hosted donation landing and processing pages.
 
	{gi_donation_target}

Target donation amount (integer).

	{gi_donation_total}

Current donation total (integer)/

	{gi_display_target}

Returns `true` or `false` for the campaign preference to show or hide the target donation amount. Useful to use as a conditional around the `{gi_donation_target}` variable to respect this preference.

	{gi_display_total}

Returns `true` or `false` for the campaign preference to show or hide the current donation total. Useful to use as a conditional around the `{gi_donation_total}` variable to respect this preference.

	{gi_givlnk}

A Giving Impact short URL that redirects to the hosted campaign donation landing and processing page.  Useful for sharing and multi-channel outreach.

	{gi_share_url}

URL to the hosted share page. Useful to offer social network sharing of the campaign using campaign data.	 Offers basic tracking of shares reported as part of campaign analytics within the Giving Impact dashboard.

	{gi_shares_fb}

Total number of Facebook likes for this campaign made through the Giving Impact share feature.

	{gi_shares_twitter}

Total number of Tweets made for this campaign made through the Giving Impact share feature.

	{gi_hash_tag}

Twitter hashtag for the campaign.

	{gi_has_giving_opportunities}

Returns `true` or `false` depending on whether the campaign is configured to support Giving Opportunities.

	{gi_status}

Returns `true` or `false` depending on whether the campaign is active or not.

### Opportunities

	{exp:modl_giving_impact:opportunities} Content {/exp:modl_giving_impact:opportunities}

#### Required Parameters

You need to provide a campaign token **or** opportunity token. A campaign token will generate a list of children opportunities. An opportunity token will return the single opportunity.

* campaign - STRING - Parent campaign token. This is used to display the list of Giving Opportunities associated with a specific campaign.
* opportunity - STRING - Unique giving opportunity token. This is used to display a single specific Giving Opportunity.

##### Optional Parameters
 
 The following are used to modify the returned list of giving opportunities when a parent campaign is specified.

* limit - INT - Limits results returned. **default = 10**
* offset - INT - Number of results to skip, useful for pagination. **default = 0**
* sort - STRING - Property to sort results by. Also accepts a direction preceded by a pipe. **default = created_at**
* status - STRING - Campaign status, "active", "inactive" or "both". **default = active**

#### Variables

	{gi_id_token}

Unique API token and id for the Giving Opportunity.
	
	{gi_title}

Title of the Giving Opportunity
	
	{gi_description}

Brief Giving Opportunity description

	{gi_image_url}

URL to Giving Opportunity image. Image is hosted with Giving Impact.

	{gi_youtube_id}

YouTube ID for Giving Opportunity video.

	{gi_donation_url}

URL to the hosted donation landing and processing pages.
 
	{gi_donation_target}

Target donation amount (integer).

	{gi_donation_total}

Current donation total (integer)/

	{gi_givlnk}

A Giving Impact short URL that redirects to the hosted Giving Opportunity donation landing and processing page. Useful for sharing and multi-channel outreach.

	{gi_share_url}

URL to the hosted share page. Useful to offer social network sharing of the Giving Opportunity using Giving Opportunity data. Offers basic tracking of shares reported as part of campaign analytics within the Giving Impact dashboard.

	{gi_shares_fb}

Total number of Facebook likes for this Giving Opportunity made through the Giving Impact share feature.

	{gi_shares_twitter}

Total number of Tweets made for this Giving Opportunity made through the Giving Impact share feature.

	{gi_hash_tag}

Twitter hashtag for the campaign.

	{gi_status}

Returns `true` or `false` depending on whether the Giving Opportunity is active or not.

#### Donations

	{exp:modl_giving_impact:donations} Content {/exp:modl_giving_impact:donations}
Donations require either a campaign or opportunity token

#### Parameters

##### Required Parameters

You need to provide a campaign token **or** opportunity token. A campaign token will generate a list of donations within the campaign, including those made through any children opportunities. An opportunity token will return a list of donations for the specified opportunity only.

* campaign  - STRING - Parent campaign.
* opportunity - STRING - Specfic opportunity

##### Optional Parameters
 
* limit - INT - Limits results returned. **default = 10**
* offset - INT - Number of results to skip, useful for pagination. **default = 0**
* sort - STRING - Property to sort results by. Also accepts a direction preceded by a pipe. **default = created_at**

#### Variables

	{gi_first_name}

Donor first name

	{gi_last_name}

Donor last name

	{gi_billing_address1}

Donor address

	{gi_billing_city}

Donor city

	{gi_billing_state}

Donor State

	{gi_billing_postal_code}

Donor zip code

	{gi_billing_country}

Donor country

	{gi_individual_total}

Amount donated (integer)

	{gi_donation_level}

The donation level selected if campaign is configured with donation levels. 

	{gi_email_address}

Donor email address unless donor has 'opted out' of receiving follow-up communications.

	{gi_referrer}

Referring URL visited prior to launching the hosted donation page. Captured when possible.
	
	{gi_offline}

Returns `true` or `false` depending on whether the donation was an 'offline' donation recorded manually through the Giving Impact dashboard or not.

	{gi_created_at}

Timestamp of donation date and time.

	{gi_twitter_share}

Returns `true` or `false` depending if the user shared the Campaign or Giving Opportunity with a tweet following their donation using the Giving Impact share available on donation confirmation page.

	{gi_fb_share}

Returns `true` or `false` depending if the user shared the Campaign or Giving Opportunity with a Facebook Like following their donation using the Giving Impact share available on donation confirmation page.

#### Conditionals

#### if no_donations

	{if no_donations}{/if}

This conditional will show its contents if there are no results returned for the donations tag.

### Create Giving Opportunity

Using the Create Opportunity tag pair you can easily create a form to create new opportunities.

	{exp:modl_giving_impact:create_opportunity} Form Content {/exp:modl_giving_impact:create_opportunity}

#### Parameters

* campaign - STRING - parent campaign token **REQUIRED**
* return - STRING - a return URL that supports `{path='template_group/template'}` **default - returns to template of form**
* class - STRING - CSS class applied to <form>
* id - STRING - CSS ID applied to <form>
* notify - STRING - A valid email address to notify upon successful opportunity creation.  Will send a simple notifcation email that included the title and description of the opportunity.

#### Validation and Required Fields

##### Required Form Fields

The following must be submitted otherwise your request will display an error.

* title
* description
* status - true = Active, false = Inactive
* captcha

##### Validation

Form uses ExpressionEngine's validation resulting in a system message being displayed when any required case fails.

You may use the following variables to repopulate the form upon return from validation error.

* `{value_title}`
* `{value_description}`
* `{value_status}`
* `{value_target}`
* `{value_youtube}`

#### Returned Data

On successful submission and processing of form data, the API and module will return the new Giving Opportunity's unique token. This value are returned in two ways.

1. The opportunity_token will be dynamically added as the last segment of the path specificed in the **return** parameter detailed above.
2. If you return to the same template that contains the form tag, you may use the `{opportunity_token}` variable within the form tag.

#### Conditionals

##### if opportunity_token

	{if opportunity_token}{/if}

If the user submits the form successfully and is immediately returned to the template of the form, this conditional will show it contents. Can be used to show a success message or thank you.

#### Example Form

	{exp:modl_giving_impact:create_opportunity campaign="[unique-token]" return="{path='team/detail'}" class="gi-form" notify="someone@somewhere.org"}

		{if opportunity_token}
			<p>Sweet! Your opportunity was created with token {opportunity_token}</p>
		{/if}
		<ul>
		<li>
			<label class="required">Title:</label> 
			<input type="text" name="title" value="{value_title}" />
		</li>
		<li>
			<label class="required">Description:</label>
			<textarea name="description">{value_description}</textarea>
		</li>
		<li>
			<label class="required">Status:</label>
			<input type="radio" name="status" value="true"{if value_status} checked{/if}>Active and accepting donations<br>
			<input type="radio" name="status" value="false"{if value_status} checked{/if}>Inactive
		</li>
		<li>
			<label>Team Photo:</label>	
			<p class="directions">Add an image to display on your Page. Image dimensions should be 300 pixels x 200 pixels. File needs to be less than 100K and a web optimized jpeg, gif or png.</p>
			<input type="file" name="image" />
		</li>
		<li>
			<label>Donation Target:</label>
			<input type="text" name="target" value="{value_target}" />
		</li>
		<li>
			<label>YouTube Video ID:</label>
			<p class="directions">Enter the YouTube URL or Video Id to add a video to your Page.<br/>
			Ex: http://www.youtube.com/watch?v=**dtdo_pOwuHI**</p>
			<input type="text" name="youtube" value="{value_youtube}" />
		</li>
		<li>
			{captcha}<br />
			<p class="directions">Please enter the letters/numbers you see above.</p>
			<input type="text" name="captcha" />
		</li>
			<input type="submit" value="Save Opportunity" />
		</ul>
	{/exp:modl_giving_impact:create_opportunity}


## Changelog

* 08222012 - Version 2.0
	* Full revision of Module designed to work with V2.0 of Giving Impact's API
* 02172012 - Added MSM support