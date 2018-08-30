<?php
/**
 * 2Checkout Processor setup template.
 * Settings template.
 */

if( ! is_ssl() ){
?>
	<div class="error" style="border-left-color: #FF0;">
		<p>
			<?php esc_html_e( 'Your site is not using secure HTTPS. SSL/HTTPS is not required to use 2Checkout for Caldera Forms, but it is recommended.', 'cf-twocheckout' ); ?>
		</p>
	</div>
<?php } ?>

<div class="caldera-config-group">
	<label for="{{_id}}_sandbox"><?php _e('Sandbox Mode', 'cf-twocheckout'); ?></label>
	<div class="caldera-config-field">
		<input id="{{_id}}_sandbox" type="checkbox" class="field-config" name="{{_name}}[sandbox]" value="1" {{#if sandbox}}checked="checked"{{/if}}>
	</div>
</div>

<div class="caldera-config-group">
	<label><?php _e('API Username', 'cf-twocheckout'); ?></label>
	<div class="caldera-config-field">		
		<input type="text" id="{{_id}}_api_username" class="block-input required field-config" name="{{_name}}[api_username]" value="{{api_username}}" required><br>
		<small><?php _e( 'Go to Account » User Management in 2Checkout and create a user with API Access and API Updating.', 'cf-twocheckout' ); ?></small>
	</div>
</div>
<div class="caldera-config-group">
	<label><?php _e('API Password', 'cf-twocheckout'); ?></label>
	<div class="caldera-config-field">		
		<input type="text" id="{{_id}}_api_password" class="block-input required field-config" name="{{_name}}[api_password]" value="{{api_password}}" required><br>
		<small><?php _e( 'Password for the API user created.', 'cf-twocheckout' ); ?></small>
	</div>
</div>
<div class="caldera-config-group">
	<label><?php _e('Account Number', 'cf-twocheckout'); ?></label>
	<div class="caldera-config-field">		
		<input type="text" id="{{_id}}_account_number" class="block-input required field-config" name="{{_name}}[account_number]" value="{{account_number}}" required><br/>
		<small><?php _e( 'Click on the profile icon in 2Checkout to find your Account Number.', 'cf-twocheckout' ); ?></small>
	</div>
</div>
<div class="caldera-config-group">
	<label><?php _e('Secret Word', 'cf-twocheckout'); ?></label>
	<div class="caldera-config-field">		
		<input type="text" id="{{_id}}_secret_word" class="block-input required field-config" name="{{_name}}[secret_word]" value="{{secret_word}}" required><br/>
		<small><?php _e( 'Go to Account » Site Management. Look under Checkout Options to find the Secret Word.', 'cf-twocheckout' ); ?></small>
	</div>
</div>


<!-- <div class="caldera-config-group">
	<p class="description">
		<?php esc_html_e( 'This plugin uses PayPal Express, which is also known as the PayPal NVP/SOAP API.', 'cf-paypal' ); ?>
	</p>
	<p class="description">
		<a href="https://developer.paypal.com/docs/classic/api/apiCredentials/#creating-an-api-signature" target="_blank"><?php esc_html_e( 'Learn about getting API credentials here', 'cf-paypal' ); ?></a>
	</p>
	<p class="description">
		<?php esc_html_e( 'Your main API credentials can not be used in test mode.', 'cf-paypal' ); ?>
	</p>
	<p class="description">
		<a href="https://developer.paypal.com/docs/classic/lifecycle/sb_create-accounts/" target="_blank"><?php esc_html_e( 'Learn About Sandbox Credentials Here', 'cf-paypal' ); ?></a>
	</p>
</div> -->

<div class="caldera-config-group">
	<label><?php _e('Price', 'cf-twocheckout'); ?></label>
	<div class="caldera-config-field">
		{{{_field slug="price" type="calculation,text,hidden" exclude="system"}}}
	</div>
</div>
<div class="caldera-config-group">
	<label><?php _e('Currency', 'cf-twocheckout'); ?></label>
	<div class="caldera-config-field">
		<select id="{{_id}}_currency" class="required field-config" name="{{_name}}[currency]">
			<!-- popular currencies -->
			<option value="USD" {{#is currency value="USD"}}selected="selected"{{/is}}>US Dollars (USD)</option>
			<option value="AUD" {{#is currency value="AUD"}}selected="selected"{{/is}}>Australian Dollar (AUD)</option>
			<option value="GBP" {{#is currency value="GBP"}}selected="selected"{{/is}}>British Pound (GBP)</option>
			<option value="CAD" {{#is currency value="CAD"}}selected="selected"{{/is}}>Canadian Dollar (CAD)</option>
			<option value="EUR" {{#is currency value="EUR"}}selected="selected"{{/is}}>Euro (EUR)</option>
			<option value="JPY" {{#is currency value="JPY"}}selected="selected"{{/is}}>Japanese Yen (JPY)</option>

			<option>--</option>

			<option value="AFN" {{#is currency value="AFN"}}selected="selected"{{/is}}>Afghan Afghani (AFN)</option>
			<option value="ALL" {{#is currency value="ALL"}}selected="selected"{{/is}}>Albanian Lek (ALL)</option>
			<option value="DZD" {{#is currency value="DZD"}}selected="selected"{{/is}}>Algerian Dinar (DZD)</option>
			<option value="ARS" {{#is currency value="ARS"}}selected="selected"{{/is}}>Argentine Peso (ARS)</option>
			<option value="AZN" {{#is currency value="AZN"}}selected="selected"{{/is}}>Azerbaijani Manat (AZN)</option>
			<option value="BSD" {{#is currency value="BSD"}}selected="selected"{{/is}}>Bahamian Dollar (BSD)</option>
			<option value="BDT" {{#is currency value="BDT"}}selected="selected"{{/is}}>Bangladeshi Taka (BDT)</option>
			<option value="BBD" {{#is currency value="BBD"}}selected="selected"{{/is}}>Barbadian Dollar (BBD)</option>
			<option value="BZD" {{#is currency value="BZD"}}selected="selected"{{/is}}>Belize Dollar (BZD)</option>
			<option value="BMD" {{#is currency value="BMD"}}selected="selected"{{/is}}>Bermudan Dollar (BMD)</option>
			<option value="BOB" {{#is currency value="BOB"}}selected="selected"{{/is}}>Bolivian Boliviano (BOB)</option>
			<option value="BWP" {{#is currency value="BWP"}}selected="selected"{{/is}}>Botswana Pula (BWP)</option>
			<option value="BRL" {{#is currency value="BRL"}}selected="selected"{{/is}}>Brazilian Real (BRL)</option>
			<option value="BND" {{#is currency value="BND"}}selected="selected"{{/is}}>Brunei Dollar (BND)</option>
			<option value="BGN" {{#is currency value="BGN"}}selected="selected"{{/is}}>Bulgarian Lev (BGN)</option>
			<option value="CLP" {{#is currency value="CLP"}}selected="selected"{{/is}}>Chilean Peso (CLP)</option>
			<option value="CNY" {{#is currency value="CNY"}}selected="selected"{{/is}}>Chinese Yuan (CNY)</option>
			<option value="COP" {{#is currency value="COP"}}selected="selected"{{/is}}>Colombian Peso (COP)</option>
			<option value="CRC" {{#is currency value="CRC"}}selected="selected"{{/is}}>Costa Rican Colon (CRC)</option>
			<option value="HRK" {{#is currency value="HRK"}}selected="selected"{{/is}}>Croatian Kuna (HRK)</option>
			<option value="CZK" {{#is currency value="CZK"}}selected="selected"{{/is}}>Czech Koruna (CZK)</option>
			<option value="DKK" {{#is currency value="DKK"}}selected="selected"{{/is}}>Danish Krone (DKK)</option>
			<option value="DOP" {{#is currency value="DOP"}}selected="selected"{{/is}}>Dominican Peso (DOP)</option>
			<option value="XCD" {{#is currency value="XCD"}}selected="selected"{{/is}}>East Caribbean Dollar (XCD)</option>
			<option value="EGP" {{#is currency value="EGP"}}selected="selected"{{/is}}>Egyptian Pound (EGP)</option>
			<option value="FJD" {{#is currency value="FJD"}}selected="selected"{{/is}}>Fijian Dollar (FJD)</option>
			<option value="GTQ" {{#is currency value="GTQ"}}selected="selected"{{/is}}>Guatemalan Quetzal (GTQ)</option>
			<option value="HKD" {{#is currency value="HKD"}}selected="selected"{{/is}}>Hong Kong Dollar (HKD)</option>
			<option value="HNL" {{#is currency value="HNL"}}selected="selected"{{/is}}>Honduran Lempira (HNL)</option>
			<option value="HUF" {{#is currency value="HUF"}}selected="selected"{{/is}}>Hungarian Forint (HUF)</option>
			<option value="INR" {{#is currency value="INR"}}selected="selected"{{/is}}>Indian Rupee (INR)</option>
			<option value="IDR" {{#is currency value="IDR"}}selected="selected"{{/is}}>Indonesian Rupiah (IDR)</option>
			<option value="ILS" {{#is currency value="ILS"}}selected="selected"{{/is}}>Israeli New Shekel (ILS)</option>
			<option value="JMD" {{#is currency value="JMD"}}selected="selected"{{/is}}>Jamaican Dollar (JMD)</option>
			<option value="KZT" {{#is currency value="KZT"}}selected="selected"{{/is}}>Kazakhstani Tenge (KZT)</option>
			<option value="KES" {{#is currency value="KES"}}selected="selected"{{/is}}>Kenyan Shilling (KES)</option>
			<option value="LAK" {{#is currency value="LAK"}}selected="selected"{{/is}}>Lao Kip, Democratic Rep (LAK)</option>
			<option value="MMK" {{#is currency value="MMK"}}selected="selected"{{/is}}>Kyat, Myanmar (MMK)</option>
			<option value="LBP" {{#is currency value="LBP"}}selected="selected"{{/is}}>Lebanese Pound (LBP)</option>
		</select>
	</div>
</div>
<div class="caldera-config-group">
	<label><?php _e('Item Name', 'cf-twocheckout'); ?></label>
	<div class="caldera-config-field">		
		<input type="text" id="{{_id}}_item_name" class="block-input field-config" name="{{_name}}[item_name]" value="{{item_name}}">
	</div>
</div>
<div class="caldera-config-group">
	<label><?php _e('Item Description', 'cf-twocheckout'); ?></label>
	<div class="caldera-config-field">		
		<input type="text" id="{{_id}}_item_description" class="block-input field-config" name="{{_name}}[item_description]" value="{{item_description}}">
	</div>
</div>

<div class="caldera-config-group">
	<label><?php _e('Quantity Field', 'cf-twocheckout'); ?></label>
	<div class="caldera-config-field">
		{{{_field slug="item_quantity" exclude="system"}}}
	</div>
</div>


