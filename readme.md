# MODL Giving Impact

## Overview

An ExpressionEngine 2.x module to interact with Giving Impact &trade;. Giving Impact is an online fundraising platform driven by a thoughtful API to allow designers and developers to deliver customized online donation experiences for Non-profits easily, affordable, and flexibly.

For more about Giving Impact and to view our full documentation and learning reasources please visit [givingimpact.com](http://givingimpact.com)

### Module Credits

**Developed By:** Minds On Design Lab - http://mod-lab.com<br />
**Version:** 2.3.1<br />
**Copyright:** Copyright &copy; 2010-2013 Minds On Design Lab<br />
**License:** Licensed under the MIT license - Please refer to LICENSE

## Requirements

* PHP 5
* cURL
* Expression Engine 2.x
* A [Giving Impact](http://givingimpact.com) account.
	* Supports v2 of API
* Tested in EECMS 2.5 -> 2.6.1

## Installation

Install in system/expressionengine/third_party/modl_giving_impact

## Configuration

A Giving Impact Private API Key is required to connect the module to Giving Impact & can be entered on the modules settings screen. In addition a Public API Key is required if you are using the Custom Checkout feature.

## EECMS Usage

The following ExpressionEngine tag pairs reflect key methods available in the Giving Impact API.  Each pair returns all of it's related method's data as an EECMS tag with a "gi_" prefix added on. So for example in the GI API /campaigns method, there is a data element returned with a campaign's unique token labeled `id_token` which in the related EECMS tag pair would be returned as `{gi_id_token}`.

The following details the ExpressionEngine tags available, variables returned, parameters, and any unique variables available for use. It also details options for create forms to post data to Giving Impact.

To learn more about these methods, the date returned and using this module to bring a customized donation experience to your nonprofit's ExpressionEngine site, please visit [givingimpact.com](http://givingimpact.com)

### Menu

* [Root Tags](#roottags)
* [Campaigns](#campaigns)
* [Opportunities](#opportunities)
* [Donations](#donations)
* [Donation Checkout](#donation-checkoupt)
* [Opportunity Form](#opportunity-form)
* [Hooks](#hooks)

### Root Tags

	{exp:modl_giving_impact:public_key}

Returns public key string

	{exp:modl_giving_impact:private_key}

Returns private key string

### Campaigns

	{exp:modl_giving_impact:campaigns} Content {/exp:modl_giving_impact:campaigns}

#### Optional Parameters

| Parameter | Data Type | Description | Default |
| ------------ |:-------------|:-------------|:-------------|
| campaign | STRING | Unique campaign id_token. If provided will only return that campaign's data.  If not used, then will return multiple campaigns. | |
| limit | INT | Limits the number of results returned. | 10 |
| offset | INT | Number of results to skip, useful for pagination. | 0 |
| sort | STRING | Property to sort results by. Also accepts a direction preceded by a pipe, e.g. sort="gi_created_at&#124;desc"| gi_created_at |
| status | STRING | Campaign status, "active", "inactive" or "both". | active |

#### Single Variables

| Variable        | Description|
| ------------- |:-------------|
| {gi_id_token} | Unique API token and id for the campaign |
| {gi_status} | Returns `true` or `false` depending on whether the campaign is active or not. |
| {gi_title} | Title of the campaign |
| {gi_description} | Brief campaign description |
| {gi_donation_url} | URL to the hosted donation landing and processing pages. |
| {gi_donation_target} | Target donation amount (integer). |
| {gi_donation_minimum} | Minimum donation value accepted. |
| {gi_donation_total} | Current donation total (integer). |
| {gi_total_donations} | Current total number of donations. |
| {gi_has_giving_opportunities} | Returns `true` or `false` depending on whether the Campaign has Giving Opportunities or not. |
| {gi_total_opportunities} | Current total number of Giving Opportunities. |
| {gi_share_url} | URL to the hosted share page. Useful to offer social network sharing of the campaign using campaign data. Offers basic tracking of shares reported as part of campaign analytics within the Giving Impact dashboard as well as can be tracked in Google Analytics if a profile ID has been added to campaign. |
| {gi_shares_fb} | Total number of Facebook likes for this campaign made through the Giving Impact share feature. |
| {gi_shares_twitter} | Total number of Tweets made for this campaign made through the Giving Impact share feature. |
| {gi_image_url} | URL to campaign image. Image is hosted with Giving Impact |
| {gi_thumb_url} | URL to campaign thumbnail image. Image is hosted with Giving Impact |
| {gi_youtube_id} | YouTube ID for campaign video. |
| {gi_hash_tag} | Twitter hashtag for the campaign. |
| {gi_analytics_id} | Google Analytics Profile ID for the Campaign. |
| {gi_campaign_color} | Campaign accent color.  |
| {gi_header_font_color} | Campaign accent color.  |
| {gi_display_donation_target} | Returns `true` or `false` for the campaign preference to show or hide the target donation amount. Useful to use as a conditional around the `{gi_donation_target}` variable to respect this preference. |
| {gi_display_donation_total} | Returns `true` or `false` for the campaign preference to show or hide the current donation total. Useful to use as a conditional around the `{gi_donation_total}` variable to respect this preference. |

#### Variable Pairs

##### Campaign Fields

This is a collection of custom fields that are entered when creating and updating a child opportunity

	{gi_campaign_fields}
		...
	{/gi_campaign_fields}

The following is available in this tag pair:

| Variable        | Description|
| ------------- |:-------------|
| {campaign_fields_field_id} | Returns a unique identifier for the custom field |
| {campaign_fields_field_type} | Returns the type of field (dropdown, text, ...) |
| {campaign_fields_field_label} | Returns the label of the field |
| {campaign_fields_response} | Returns the donor's response if entered |
| {campaign_fields_status} | Returns `true` or `false` depending on whether the field is currently set to active or not |
| {campaign_fields_required} | Returns `true` or `false` depending on whether the field is currently required |


### Opportunities

	{exp:modl_giving_impact:opportunities campaign="{id_token}"} Content {/exp:modl_giving_impact:opportunities}

#### Required Parameters

You need to provide a campaign id_token **or** opportunity id_token.

* A campaign token will generate a list of children opportunities.
* An opportunity token will return the single opportunity.

| Parameter | Data Type | Description |
| ------------ |:-------------|:-------------|
| campaign | STRING | Parent campaign token. This is used to display the list of Giving Opportunities associated with a specific campaign. |
| opportunity | STRING | Unique giving opportunity token. This is used to display a single specific Giving Opportunity. |

##### Optional Parameters

The following are used to modify the returned list of giving opportunities when a parent campaign is specified.

| Parameter | Data Type | Description | Default |
| ------------ |:-------------|:-------------|:-------------|
| limit | INT | Limits the number of results returned. | 10 |
| offset | INT | Number of results to skip, useful for pagination. | 0 |
| sort | STRING | Property to sort results by. Also accepts a direction preceded by a pipe, e.g. sort="gi_created_at&#124;desc"| gi_created_at |
| status | STRING | Campaign status, "active", "inactive" or "both". | active |
| related | BOOLEAN | Entering true`will make available the `{gi_campaign}{/gi_campaign}` tag pair with a full set of variables related to the opportunity's parent campaign.  | false |

#### Single Variables

| Variable        | Description|
| ------------- |:-------------|
| {gi_id_token} | Unique API token and id for the Giving Opportunity. |
| {gi_status} | Returns `true` or `false` depending on whether the Giving Opportunity is active or not. |
| {gi_title} | Title of the Giving Opportunity |
| {gi_description} | Brief Giving Opportunity description |
| {gi_donation_url} | URL to the hosted donation landing and processing pages. |
| {gi_donation_target} | Target donation amount (integer). |
| {gi_donation_total} | Current donation total (integer). |
| {gi_total_donations} | Current total number of donations. |
| {gi_share_url} | URL to the hosted share page. Useful to offer social network sharing of the Giving Opportunity using Giving Opportunity data. Offers basic tracking of shares reported as part of campaign analytics within the Giving Impact dashboard. |
| {gi_shares_fb} | Total number of Facebook likes for this Giving Opportunity made through the Giving Impact share feature. |
| {gi_shares_twitter} | Total number of Tweets made for this Giving Opportunity made through the Giving Impact share feature. |
| {gi_image_url} |  URL to Giving Opportunity image. Image is hosted with Giving Impact. |
| {gi_thumb_url} |  URL to Giving Opportunity thumbnail image. Image is hosted with Giving Impact. |
| {gi_youtube_id} | YouTube ID for Giving Opportunity video. |

#### Variable Pairs

##### Campaign

If the parameter `related=true` is added to the tag the following tag pair becomes available:

	{gi_campaign}
		[All variables returned by the campaign tag above will be available here.]
	{/gi_campaign}

##### Campaign Responses

This is a collection of responses to the custom campaign fields defined by the parent campaign:

	{gi_campaign_responses}
		...
	{/gi_campaign_responses}

The following is available in this tag pair:

| Variable        | Description|
| ------------- |:-------------|
| {campaign_responses_field_id} | Returns a unique identifier for the custom field |
| {campaign_responses_field_type} | Returns the type of field (dropdown, text, ...) |
| {campaign_responses_field_label} | Returns the label of the field |
| {campaign_responses_response} | Returns the donor's response if entered |
| {campaign_responses_status} | Returns `true` or `false` depending on whether the field is currently set to active or not |
| {campaign_responses_required} | Returns `true` or `false` depending on whether the field is currently required |

### Donations

	{exp:modl_giving_impact:donations campaign="{id_token}} Content {/exp:modl_giving_impact:donations}

#### Parameters

##### Required Parameters

You need to provide a campaign token, opportunity token **or** dondation token. 

- A campaign token will generate a list of donations within the campaign, including those made through any children opportunities.
- An opportunity token will return a list of donations for the specified opportunity only.
- A donation token will return only the associated donation record data.


| Parameter | Data Type | Description |
| ------------ |:-------------|:-------------|
| campaign  | STRING | Parent campaign id_token |
| opportunity | STRING | Specfic opportunity id_token |
| donation | STRING | Specfic donation id_token |

##### Optional Parameters

| Parameter | Data Type | Description | Default |
| ------------ |:-------------|:-------------|:-------------|
| limit | INT | Limits the number of results returned. | 10 |
| offset | INT | Number of results to skip, useful for pagination. | 0 |
| sort | STRING | Property to sort results by. Also accepts a direction preceded by a pipe, e.g.    sort="gi_created_at&#124;desc"| gi_created_at |

#### Single Variables

| Variable        | Description|
| ------------- |:-------------|
| {gi_id_token} | Unique API token and id for the donation. |
| {gi_created_at} | Timestamp of donation date and time. |
| {gi_campaign} OR {gi_opportunity} | Unique API token for campaign OR opportunity that the donation is most directly associated with.|
| {gi_first_name} | Donor first name |
| {gi_last_name} | Donor last name |
| {gi_billing_address1} | Donor address |
| {gi_billing_city} | Donor city |
| {gi_billing_state} | Donor State |
| {gi_billing_postal_code} | Donor zip code |
| {gi_billing_country} | Donor country |
| {gi_donation_total} | Amount donated (integer) |
| {gi_donation_level} | The donation level selected if campaign is configured with donation levels. |
| {gi_contactl} | Returns `true` or `false` depending on whether the donor requested to be opted out of follow/up email communications.|
| {gi_email_address} | Donor email address unless donor has 'opted out' of receiving follow-up communications. |
| {gi_offline} |  Returns `true` or `false` depending on whether the donation was recorded offline (manually) or not. |
| {gi_twitter_share} | Returns `true` or `false` depending if the user shared the Campaign or Giving Opportunity with a tweet following their donation using the Giving Impact share available on donation confirmation page. |
| {gi_fb_share} | Returns `true` or `false` depending if the user shared the Campaign or Giving Opportunity with a Facebook Like following their donation using the Giving Impact share available on donation confirmation page. |

#### Variable Pairs

##### Custom Responses

	{gi_custom_responses}{/gi_custom_responses}

The following variables are available within this tag pair.

| Variable        | Description|
| ------------- |:-------------|
| {gi_field_id} | Returns a unique identifier for the custom field |
| {gi_field_type} | Returns the type of field (dropdown, text, ...) |
| {gi_field_label} | Returns the label of the field |
| {gi_response} | Returns the donor's response if entered |
| {gi_status} | Returns `true` or `false` depending on whether the field is currently set to active or not |

#### Conditional Variables

##### if no_donations

	{if no_donations}{/if}

This conditional will show its contents if there are no results returned for the donations tag.

### Donation Checkout

There are two options available for setting up checkout experiences for donations. You can use our Hosted Checkout page or you can setup your own custom checkout page. For more details about these two different options and how they work, please review our [Integration Docs (API)](http://givingimpact.com/docs/api/donation-checkout).

#### Hosted Checkout

The Hosted Checkout is a secure, super quick solution that utilizes all of the Giving Impact&trade; campaign customization goodness. The Hosted Checkout supports donation levels, donation fields to gather data from donors, messaging content, and whatever future enhancements that may come.

Implementing a solution with Hosted Checkout is as simple as pointing donors to the gi_donation_url provided in the Campaign and Giving Opportunity tag data.

#### Custom Checkout

The following details the tags and provides examples for generating custom checkout forms.

##### General Requirements

The following are general requirements for the Custom Checkout feature.

- Checkout MUST be hosted under SSL
- Must enter your Public API key to module settings for Custom Checkout
- Must include our Javascript Tag
- `id` paramter added to Javascript Tag must match that of the checkout form tag
- Must use the following names for credit card input fields: `cc_number`, `cc_cvc`, and `cc_exp`
- API requirements - Please review our [Integration Docs (API)](http://givingimpact.com/docs/api/donation-checkout) for more details about required fields and data formating.

##### Javascript Tag

The following tag is required and can be added below form and above `</body>` tag.

     {exp:modl_giving_impact:donate_js id="donate-form"}

###### Required Parameters

| Parameter | Data Type | Description |
| ------------ |:-------------|:-------------|
| id  | STRING | The id used by Javscript to target form. PLEASE NOTE that it is critical that the id in the Javascript tag matches that in the form tag |

#### Donation Form

    {exp:modl_giving_impact:donate_form 
      opportunity="######" 
      id="donate-form"
    }

    ... form content 

    {/exp:modl_giving_impact:donate_form}

###### Required Parameters

| Parameter | Data Type | Description |
| ------------ |:-------------|:-------------|
| id  | STRING | The id added to form tag. PLEASE NOTE that it is critical that the id in the Javascript tag matches that in the form tag |
| campaign **or** opportunity | STRING | id_token for either the Campaign **or** Giving Opportunity donation is towards. |

###### Optional Parameters

| Parameter | Data Type | Description | Default |
| ------------ |:-------------|:-------------|:-------------|
| return | STRING | a return URL that supports `{path=template_group/template}` | returns to template of form |
| class | STRING | CSS class applied to `<form>` ||
| notify | STRING | A valid email address to notify upon successful opportunity creation. Will send a simple notifcation email that included the name and total of the donation. ||

#### Campaign Example Donation Checkout Form

The following is an example of a **Campaign** checkout Form. Please note that all Campaign data as detailed above is available within the form opening and closing tags. You can see examples of this in both the donation levels and custom donation fields areas.

    {exp:modl_giving_impact:donate_form campaign="{id_token}" return="{path=thanks" class="gi-form" id="donate-form"}
    <fieldset>
      <legend>Donation</legend>
        <label class="required">Donation Amount:</label>
              
        <!-- Donation Level or Open Input -->
        
        {if gi_enable_donation_levels}
          {gi_donation_levels}
           <label for="radio1"><input type="radio" name="donation_amount" value="{donation_levels_amount}"> {exp:gi_helper:money}{donation_levels_amount}{/exp:gi_helper:money} - {donation_levels_label}</label>
          {/gi_donation_levels}
        {if:else}
          <input type="text" name="donation_amount" value="{value_donation_amount}" />
        {/if}

    </fieldset>
    <fieldset>
      <legend>Donor Information</legend>
        <label class="required">First Name:</label>
        <input type="text" name="first_name" value="{value_first_name}" />
   
        <label class="required">Last Name:</label>
        <input type="text" name="last_name" value="{value_last_name}" />
   
        <label class="required">Email:</label>
        <input type="text" name="email" value="{value_email}" />
        <label id="may_contact"><input type="checkbox" value="1" name="contact" id="may_contact" checked /> You may contact me with future updates</label>

        <!-- Custom Donation Fields -->
        {gi_custom_fields}
           {if custom_fields_status}
              <label{if custom_fields_required} class="required"{/if}>{custom_fields_field_label}</label>
           
               {if custom_fields_field_type == 'text'}
                   <input type="text" name="fields[{custom_fields_field_id}]" />
               {if:else}
                   <select name="fields[{custom_fields_field_id}]">
                       {custom_fields_options}
                           <option value="{value}">{value}</option>
                       {/custom_fields_options}
                   </select>
               {/if}
           {/if}
        {/gi_custom_fields}
    </fieldset>
    <fieldset>
      <legend>Payment Information</legend>
        <label class="required">Address:</label>
        <input type="text" name="street" value="{value_street}" placeholder="Street Address" />
        <input type="text" name="city" value="{value_city}" placeholder="City" />
        <input type="text" name="state" value="{value_state}" placeholder="State" />
        <input type="text" name="zip" value="{value_zip}" placeholder="Zip" />
                   
        <label class="required">CC Number:</label>
        <input type="text" name="cc_number" placeholder="1234 5679 9012 3456" />
   
        <label class="required">CVC:</label>
        <input type="text" name="cc_cvc" placeholder="Security code" />
   
        <label class="required">CC EXP:</label>
        <input type="text" name="cc_exp" placeholder="MM / YYYY" />
   
    </fieldset>

    <input type="submit" value="Donate" id="process-donation" class="button radius" />
    {/exp:modl_giving_impact:donate_form}

#### Giving Opportunity Example Donation Checkout Form


The following is an example of a **Giving Opportunity** checkout Form. Please note that all Giving Opportunity data as detailed above is available within the form opening and closing tags. You can see examples of this in both the donation levels and custom donation fields areas.

    {exp:modl_giving_impact:donate_form opportunity="{id_token}" return="{path=thanks" class="gi-form" id="donate-form"}
    <fieldset>
  	  <legend>Donation</legend>
  			<label class="required">Donation Amount:</label>
          {gi_campaign}
              
              <!-- Donation Level or Open Input -->
              
              {if campaign_enable_donation_levels}
                {campaign_donation_levels}
                   <label for="radio1"><input type="radio" name="donation_amount" value="{donation_levels_amount}"> {donation_levels_amount} - {donation_levels_label}</label>
                {/campaign_donation_levels}
              {if:else}
           		<input type="text" name="donation_amount" value="{value_donation_amount}" />
              {/if}

          {/gi_campaign}
		</fieldset>
    <fieldset>
  		<legend>Donor Information</legend>
        <label class="required">First Name:</label>
        <input type="text" name="first_name" value="{value_first_name}" />
   
        <label class="required">Last Name:</label>
        <input type="text" name="last_name" value="{value_last_name}" />
   
        <label class="required">Email:</label>
        <input type="text" name="email" value="{value_email}" />
   			<label id="may_contact"><input type="checkbox" value="1" name="contact" id="may_contact" checked /> You may contact me with future updates</label>
        
        {gi_campaign}

          <!-- Custom Donation Fields -->

            {campaign_custom_fields}
                {if custom_fields_status}
                   <label{if custom_fields_required} class="required"{/if}>{custom_fields_field_label}</label>
                  
                      {if custom_fields_field_type == 'text'}
                          <input type="text" name="fields[{custom_fields_field_id}]" />
                      {if:else}
                          <select name="fields[{custom_fields_field_id}]">
                              {custom_fields_options}
                                  <option value="{value}">{value}</option>
                              {/custom_fields_options}
                          </select>
                      {/if}
                {/if}
            {/campaign_custom_fields}

        {/gi_campaign} 
   	</fieldset>
    <fieldset>
			<legend>Payment Information</legend>
				<label class="required">Address:</label>
        <input type="text" name="street" value="{value_street}" placeholder="Street Address" />
        <input type="text" name="city" value="{value_city}" placeholder="City" />
        <input type="text" name="state" value="{value_state}" placeholder="State" />
        <input type="text" name="zip" value="{value_zip}" placeholder="Zip" />
                   
        <label class="required">CC Number:</label>
        <input type="text" name="cc_number" placeholder="1234 5679 9012 3456" />
   
        <label class="required">CVC:</label>
        <input type="text" name="cc_cvc" placeholder="Security code" />
   
        <label class="required">CC EXP:</label>
        <input type="text" name="cc_exp" placeholder="MM / YYYY" />
   
   	</fieldset>

    <input type="submit" value="Donate" id="process-donation" class="button radius" />
    {/exp:modl_giving_impact:donate_form}


### Opportunity Form

Using the Opportunity Form tag pair you can easily create a form to create new Giving Opportunities or update existing ones.

	{exp:modl_giving_impact:opportunity_form} Form Content {/exp:modl_giving_impact:opportunity_form}

#### Parameters

| Parameter | Data Type | Description | Default |
| ------------ |:-------------|:-------------|:-------------|
| campaign | STRING | parent campaign token **REQUIRED** | |
| opportunity | STRING | the opportunity token if updating an existing opportunity | |
| return | STRING | a return URL that supports `{path=template_group/template}` | returns to template of form |
| class | STRING | CSS class applied to `<form>` | |
| id | STRING | CSS ID applied to `<form>` | |
| notify | STRING | A valid email address to notify upon successful opportunity creation.  Will send a simple notifcation email that included the title and description of the opportunity. | |

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

#### Conditional Variables

##### if opportunity_token

	{if opportunity_token}{/if}

If the user submits the form successfully and is immediately returned to the template of the form, this conditional will show it contents. Can be used to show a success message or thank you.

#### Example Form

	{exp:modl_giving_impact:opportunity_form campaign="[unique-token]" return="{path=team/detail}" class="gi-form" notify="someone@somewhere.org"}

		{if opportunity_token}
			<p>Sweet! Your opportunity was created with token {opportunity_token}</p>
		{/if}
		
		<label class="required">Title:</label>
		<input type="text" name="title" value="{value_title}" />
		
		<label class="required">Description:</label>
		<textarea name="description">{value_description}</textarea>
	
		<label class="required">Status:</label>
		<input type="radio" name="status" value="true"{if value_status} checked{/if}>Active and accepting donations<br>
		<input type="radio" name="status" value="false"{if value_status} checked{/if}>Inactive
	
		<label>Team Photo:</label>
		<p class="directions">Add an image to display on your Page. Image dimensions should be 300 pixels x 200 pixels. File needs to be less than 100K and a web optimized jpeg, gif or png.</p>
		<input type="file" name="image" />
		
		<label>Donation Target:</label>
		<input type="text" name="target" value="{value_target}" />
		
		<label>YouTube Video ID:</label>
		<p class="directions">Enter the YouTube URL or Video Id to add a video to your Page.<br/>
		Ex: http://www.youtube.com/watch?v=**dtdo_pOwuHI**</p>
		<input type="text" name="youtube" value="{value_youtube}" />
		
    {gi_campaign_fields}
        {if campaign_fields_status}
              <label>{campaign_fields_field_label}{if campaign_fields_required}*{/if}:</label>
              {if campaign_fields_field_type == 'text'}
                  <input type="text" name="fields[{campaign_fields_field_id}]" />
              {if:else}
                  <select name="fields[{campaign_fields_field_id}]">
                      {campaign_fields_options}
                          <option value="{value}">{value}</option>
                      {/campaign_fields_options}
                  </select>
              {/if}
        {/if}
    {/gi_campaign_fields}

		{captcha}<br />
		<p class="directions">Please enter the letters/numbers you see above.</p>
		<input type="text" name="captcha" />
		
		<input type="submit" value="Save Opportunity" />
		
	{/exp:modl_giving_impact:opportunity_form}

## Hooks

The following developer hooks are avaialble to allow you tap into key actions with your own custom add-ons.

### gi_opportunity_return_data()

Once the Giving Opportunity form noted above is successfully processed you can access an array that includes the full API result for that specific opportunity created.

### gi_donation_return_data()

When using custom checkout form and when successfully processed you can access an array that includes the full API result for that specific donation created.


## Changelog
- 09182013 - Version 2.3.1
	- Public key is now a module option
	- Added public_key and private_key tags
- 09102013 - Version 2.3
	- Added Donation form processing, see docs for more info
	- Added 'gi_donation_return_data' hook.
- 08282013 - Version 2.2.6
	- Added support for updating existing opportunities with the "opportunity" parameter
	- Added support for custom campaign fields and custom campaign responses
	- Related campaign data will always be returned when updating or create an opportunity
- 08202013 - Version 2.2.5 - Update to add better support for single value indexed arrays
- 08202013 - Version 2.2.4 - update to include `gi_opportunity_return_data()` hook.
- 08152013 - Version 2.2.3
	- Update to opportunity form generation.
	- __IMPORTANT - form method has changed to `opportunity_form` from `create_opportunity` please update your templates!__
- 08062013 - Version 2.2.2 - update to configuration so the API Endpoint is set within the module and does not require manual entry.
- 07032013 - Version 2.2.1 - fix issue when using direction on donation method sorts.
- 06062013 - Version 2.2 - update to work with v2.0 API enhancements
	* Related Parameter - Get related Campaign or Giving Opportunity data with an opportunity or donation data set.
- 08222012 - Version 2.0
	- Full revision of Module designed to work with V2.0 of Giving Impact's API
- 02172012 - Added MSM support
