<?php

$plugin_info = array(
  'pi_name' => 'Giving Impact Formatter',
  'pi_version' => '1.0',
  'pi_author' => 'Minds On Design Lab',
  'pi_author_url' => 'http://mod-lab.com',
  'pi_description' => 'Number and money formatting for Giving Imact',
  'pi_usage' => gi_helper::usage()

  );

class MODL_GI_Formatter
{
	var $return_data = "";

	function __construct() {

	}

	public function money() {
		// $currency = ee()->TMPL->fetch_param('currency');

		$number = (int) ee()->TMPL->tagdata;
		if( !$number ) {
			return '0.00';
		}

   		return number_format($number/100, 2, '.', ',');
	}

	//  Plugin Usage
	// ----------------------------------------

	// This function describes how the plugin is used.
	//  Make sure and use output buffering

	function usage()
	{
		ob_start();
		?>Money: ${exp:gi_helper:money}150000{/exp:gi_helper:money} would return "1,500.00"
		<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
  	}
 	// END
}

?>