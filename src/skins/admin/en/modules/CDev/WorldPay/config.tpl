{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * ____file_title____
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}
<p>
<span class="SuccessMessage" IF="updated">RBS WorldPay.com parameters were successfully changed. Please make sure that the RBS WorldPay payment method is enabled on the <a href="admin.php?target=payment_methods">Payment methods</a> page before you can start using it.</span>
<form action="admin.php" method="POST">
<input type="hidden" name="target" value="payment_method">
<input type="hidden" name="action" value="update">
<input type="hidden" name="payment_method" value="{pm.get(#payment_method#)}">
Use this page to configure your store to communicate with your RBS WorldPay.com payment processing gateway. Complete the fields below and press the "Update" button. 
<p><b>Note:</b> In order to track your RBS WorldPay orders by the shopping cart software you have to proceed the following steps.
<p>
As a default the payment response feature is set to OFF by default, to enable this feature:<br />
1. Log in to the Merchant Interface.<br />
2. Select <b>Installations</b> from the left hand navigation.<br />
3. Choose an installation and select the <b>Integration Setup</b> button for either the TEST or PRODUCTION environment.<br />
4. Check the <b>Payment Response enabled?</b> and <b>Enable the Shopper Response</b> checkboxes.<br />
5. Enter the <b>Payment Response URL</b> of this value: <b>{getShopUrl(#cart.php?target=callback&action=callback&order_id_name=cartId#):h}</b><br />
6. Select the <b>Save Changes</b> button <br />

<p>
<table border=0 cellspacing=10>

<tr>
	<td>Your RBS WorldPay Installation ID:</td>
	<td><input type=text name="params[inst_id]" size=24 value="{pm.params.inst_id:r}"></td>
</tr>

<tr>
	<td>Order prefix:</td>
	<td><input type=text name="params[prefix]" size=24 value="{pm.params.prefix:r}"></td>
</tr>

<tr>
<td>Processing mode:</td>
<td>
<select name="params[test]">
<option value="A" selected="{isSelected(pm.params.test,#A#)}">Test mode: AUTHORISED</option>
<option value="R" selected="{isSelected(pm.params.test,#R#)}">Test mode: REFUSED</option>
<option value="E" selected="{isSelected(pm.params.test,#E#)}">Test mode: ERROR</option>
<option value="C" selected="{isSelected(pm.params.test,#C#)}">Test mode: CAPTURED</option>
<option value="N" selected="{isSelected(pm.params.test,#N#)}">Live mode</option>
</select>
</td>
</tr>

<tr>
<td>Authentication mode:</td>
<td>
<select name="params[preauth]">
<option value="0" selected="{isSelected(pm.params.preauth,#0#)}">Full Auth (default mode)</option>
<option value="1" selected="{isSelected(pm.params.preauth,#1#)}">Pre-Auth</option>
</select>
</td>
</tr>

<tr>
<td align="left" width="200">
<p align="justify">
MD5 secret word:<br>
This secret must be specified in the <b>MD5 secret for transactions</b> box in the configuration settings for your installation.<br>
<b>Note</b>:If you wish to disable the MD5 functionality at any point, you should reset the value of the <b>MD5 secret for transactions box</b> as blank.
</p>
</td>
<td valign="top">
<input type="text" value="{pm.params.md5HashValue:r}" name="params[md5HashValue]" size="24">
</td>
</tr>

<tr>
<td>Currency:</td>
<td>
<select name="params[currency]">
<option value="AFA" selected="pm.params.currency=#AFA#">Afghani (Afghanistan)
<option value="DZD" selected="pm.params.currency=#DZD#">Algerian Dinar (Algeria)
<option value="ADP" selected="pm.params.currency=#ADP#">Andorran Peseta (Andorra)
<option value="ARS" selected="pm.params.currency=#ARS#">Argentine Peso (Argentina)
<option value="AMD" selected="pm.params.currency=#AMD#">Armenian Dram (Armenia)
<option value="AWG" selected="pm.params.currency=#AWG#">Aruban Guilder (Aruba)
<option value="AUD" selected="pm.params.currency=#AUD#">Australian Dollar (Australia)
<option value="AZM" selected="pm.params.currency=#AZM#">Azerbaijanian Manat (Azerbaijan)
<option value="BSD" selected="pm.params.currency=#BSD#">Bahamian Dollar (Bahamas)
<option value="BHD" selected="pm.params.currency=#BHD#">Bahraini Dinar (Bahrain)
<option value="THB" selected="pm.params.currency=#THB#">Baht (Thailand)
<option value="PAB" selected="pm.params.currency=#PAB#">Balboa (Panama)
<option value="BBD" selected="pm.params.currency=#BBD#">Barbados Dollar (Barbados)
<option value="BYB" selected="pm.params.currency=#BYB#">Belarussian Ruble (Belarus)
<option value="BEF" selected="pm.params.currency=#BEF#">Belgian Franc (Belgium)
<option value="BZD" selected="pm.params.currency=#BZD#">Belize Dollar (Belize)
<option value="BMD" selected="pm.params.currency=#BMD#">Bermudian Dollar (Bermuda)
<option value="VEB" selected="pm.params.currency=#VEB#">Bolivar (Venezuela)
<option value="BOB" selected="pm.params.currency=#BOB#">Boliviano (Bolivia)
<option value="BRL" selected="pm.params.currency=#BRL#">Brazilian Real (Brazil)
<option value="BND" selected="pm.params.currency=#BND#">Brunei Dollar (Brunei Darussalam)
<option value="BGN" selected="pm.params.currency=#BGN#">Bulgarian Lev (Bulgaria)
<option value="BIF" selected="pm.params.currency=#BIF#">Burundi Franc (Burundi)
<option value="CAD" selected="pm.params.currency=#CAD#">Canadian Dollar (Canada)
<option value="CVE" selected="pm.params.currency=#CVE#">Cape Verde Escudo (Cape Verde)
<option value="KYD" selected="pm.params.currency=#KYD#">Cayman Islands Dollar (Cayman Islands)
<option value="GHC" selected="pm.params.currency=#GHC#">Cedi (Ghana)
<option value="XOF" selected="pm.params.currency=#XOF#">CFA Franc BCEAO (Guinea-Bissau)
<option value="XAF" selected="pm.params.currency=#XAF#">CFA Franc BEAC (Central African Republic)
<option value="XPF" selected="pm.params.currency=#XPF#">CFP Franc (New Caledonia)
<option value="CLP" selected="pm.params.currency=#CLP#">Chilean Peso (Chile)
<option value="COP" selected="pm.params.currency=#COP#">Colombian Peso (Colombia)
<option value="KMF" selected="pm.params.currency=#KMF#">Comoro Franc (Comoros)
<option value="BAM" selected="pm.params.currency=#BAM#">Convertible Marks (Bosnia And Herzegovina)
<option value="NIO" selected="pm.params.currency=#NIO#">Cordoba Oro (Nicaragua)
<option value="CRC" selected="pm.params.currency=#CRC#">Costa Rican Colon (Costa Rica)
<option value="CUP" selected="pm.params.currency=#CUP#">Cuban Peso (Cuba)
<option value="CYP" selected="pm.params.currency=#CYP#">Cyprus Pound (Cyprus)
<option value="CZK" selected="pm.params.currency=#CZK#">Czech Koruna (Czech Republic)
<option value="GMD" selected="pm.params.currency=#GMD#">Dalasi (Gambia)
<option value="DKK" selected="pm.params.currency=#DKK#">Danish Krone (Denmark)
<option value="MKD" selected="pm.params.currency=#MKD#">Denar (The Former Yugoslav Republic Of Macedonia)
<option value="DEM" selected="pm.params.currency=#DEM#">Deutsche Mark (Germany)
<option value="AED" selected="pm.params.currency=#AED#">Dirham (United Arab Emirates)
<option value="DJF" selected="pm.params.currency=#DJF#">Djibouti Franc (Djibouti)
<option value="STD" selected="pm.params.currency=#STD#">Dobra (Sao Tome And Principe)
<option value="DOP" selected="pm.params.currency=#DOP#">Dominican Peso (Dominican Republic)
<option value="VND" selected="pm.params.currency=#VND#">Dong (Vietnam)
<option value="GRD" selected="pm.params.currency=#GRD#">Drachma (Greece)
<option value="XCD" selected="pm.params.currency=#XCD#">East Caribbean Dollar (Grenada)
<option value="EGP" selected="pm.params.currency=#EGP#">Egyptian Pound (Egypt)
<option value="SVC" selected="pm.params.currency=#SVC#">El Salvador Colon (El Salvador)
<option value="ETB" selected="pm.params.currency=#ETB#">Ethiopian Birr (Ethiopia)
<option value="EUR" selected="pm.params.currency=#EUR#">Euro (Europe)
<option value="FKP" selected="pm.params.currency=#FKP#">Falkland Islands Pound (Falkland Islands)
<option value="FJD" selected="pm.params.currency=#FJD#">Fiji Dollar (Fiji)
<option value="HUF" selected="pm.params.currency=#HUF#">Forint (Hungary)
<option value="CDF" selected="pm.params.currency=#CDF#">Franc Congolais (The Democratic Republic Of Congo)
<option value="FRF" selected="pm.params.currency=#FRF#">French Franc (France)
<option value="GIP" selected="pm.params.currency=#GIP#">Gibraltar Pound (Gibraltar)
<option value="XAU" selected="pm.params.currency=#XAU#">Gold
<option value="HTG" selected="pm.params.currency=#HTG#">Gourde (Haiti)
<option value="PYG" selected="pm.params.currency=#PYG#">Guarani (Paraguay)
<option value="GNF" selected="pm.params.currency=#GNF#">Guinea Franc (Guinea)
<option value="GWP" selected="pm.params.currency=#GWP#">Guinea-Bissau Peso (Guinea-Bissau)
<option value="GYD" selected="pm.params.currency=#GYD#">Guyana Dollar (Guyana)
<option value="HKD" selected="pm.params.currency=#HKD#">Hong Kong Dollar (Hong Kong)
<option value="UAH" selected="pm.params.currency=#UAH#">Hryvnia (Ukraine)
<option value="ISK" selected="pm.params.currency=#ISK#">Iceland Krona (Iceland)
<option value="INR" selected="pm.params.currency=#INR#">Indian Rupee (India)
<option value="IRR" selected="pm.params.currency=#IRR#">Iranian Rial (Islamic Republic Of Iran)
<option value="IQD" selected="pm.params.currency=#IQD#">Iraqi Dinar (Iraq)
<option value="IEP" selected="pm.params.currency=#IEP#">Irish Pound (Ireland)
<option value="ITL" selected="pm.params.currency=#ITL#">Italian Lira (Italy)
<option value="JMD" selected="pm.params.currency=#JMD#">Jamaican Dollar (Jamaica)
<option value="JOD" selected="pm.params.currency=#JOD#">Jordanian Dinar (Jordan)
<option value="KES" selected="pm.params.currency=#KES#">Kenyan Shilling (Kenya)
<option value="PGK" selected="pm.params.currency=#PGK#">Kina (Papua New Guinea)
<option value="LAK" selected="pm.params.currency=#LAK#">Kip (Lao People's Democratic Republic)
<option value="EEK" selected="pm.params.currency=#EEK#">Kroon (Estonia)
<option value="HRK" selected="pm.params.currency=#HRK#">Kuna (Croatia)
<option value="KWD" selected="pm.params.currency=#KWD#">Kuwaiti Dinar (Kuwait)
<option value="MWK" selected="pm.params.currency=#MWK#">Kwacha (Malawi)
<option value="ZMK" selected="pm.params.currency=#ZMK#">Kwacha (Zambia)
<option value="AOR" selected="pm.params.currency=#AOR#">Kwanza Reajustado (Angola)
<option value="MMK" selected="pm.params.currency=#MMK#">Kyat (Myanmar)
<option value="GEL" selected="pm.params.currency=#GEL#">Lari (Georgia)
<option value="LVL" selected="pm.params.currency=#LVL#">Latvian Lats (Latvia)
<option value="LBP" selected="pm.params.currency=#LBP#">Lebanese Pound (Lebanon)
<option value="ALL" selected="pm.params.currency=#ALL#">Lek (Albania)
<option value="HNL" selected="pm.params.currency=#HNL#">Lempira (Honduras)
<option value="SLL" selected="pm.params.currency=#SLL#">Leone (Sierra Leone)
<option value="ROL" selected="pm.params.currency=#ROL#">Leu (Romania)
<option value="BGL" selected="pm.params.currency=#BGL#">Lev (Bulgaria)
<option value="LRD" selected="pm.params.currency=#LRD#">Liberian Dollar (Liberia)
<option value="LYD" selected="pm.params.currency=#LYD#">Libyan Dinar (Libyan Arab Jamahiriya)
<option value="SZL" selected="pm.params.currency=#SZL#">Lilangeni (Swaziland)
<option value="LTL" selected="pm.params.currency=#LTL#">Lithuanian Litas (Lithuania)
<option value="LSL" selected="pm.params.currency=#LSL#">Loti (Lesotho)
<option value="LUF" selected="pm.params.currency=#LUF#">Luxembourg Franc (Luxembourg)
<option value="MGF" selected="pm.params.currency=#MGF#">Malagasy Franc (Madagascar)
<option value="MYR" selected="pm.params.currency=#MYR#">Malaysian Ringgit (Malaysia)
<option value="MTL" selected="pm.params.currency=#MTL#">Maltese Lira (Malta)
<option value="TMM" selected="pm.params.currency=#TMM#">Manat (Turkmenistan)
<option value="FIM" selected="pm.params.currency=#FIM#">Markka (Finland)
<option value="MUR" selected="pm.params.currency=#MUR#">Mauritius Rupee (Mauritius)
<option value="MZM" selected="pm.params.currency=#MZM#">Metical (Mozambique)
<option value="MXN" selected="pm.params.currency=#MXN#">Mexican Peso (Mexico)
<option value="MXV" selected="pm.params.currency=#MXV#">Mexican Unidad de Inversion (Mexico)
<option value="MDL" selected="pm.params.currency=#MDL#">Moldovan Leu (Republic Of Moldova)
<option value="MAD" selected="pm.params.currency=#MAD#">Moroccan Dirham (Morocco)
<option value="BOV" selected="pm.params.currency=#BOV#">Mvdol (Bolivia)
<option value="NGN" selected="pm.params.currency=#NGN#">Naira (Nigeria)
<option value="ERN" selected="pm.params.currency=#ERN#">Nakfa (Eritrea)
<option value="NAD" selected="pm.params.currency=#NAD#">Namibia Dollar (Namibia)
<option value="NPR" selected="pm.params.currency=#NPR#">Nepalese Rupee (Nepal)
<option value="ANG" selected="pm.params.currency=#ANG#">Netherlands (Netherlands)
<option value="NLG" selected="pm.params.currency=#NLG#">Netherlands Guilder (Netherlands)
<option value="YUM" selected="pm.params.currency=#YUM#">New Dinar (Yugoslavia)
<option value="ILS" selected="pm.params.currency=#ILS#">New Israeli Sheqel (Israel)
<option value="AON" selected="pm.params.currency=#AON#">New Kwanza (Angola)
<option value="TWD" selected="pm.params.currency=#TWD#">New Taiwan Dollar (Province Of China Taiwan)
<option value="ZRN" selected="pm.params.currency=#ZRN#">New Zaire (Zaire)
<option value="NZD" selected="pm.params.currency=#NZD#">New Zealand Dollar (New Zealand)
<option value="BTN" selected="pm.params.currency=#BTN#">Ngultrum (Bhutan)
<option value="KPW" selected="pm.params.currency=#KPW#">North Korean Won (Democratic People's Republic Of Korea)
<option value="NOK" selected="pm.params.currency=#NOK#">Norwegian Krone (Norway)
<option value="PEN" selected="pm.params.currency=#PEN#">Nuevo Sol (Peru)
<option value="MRO" selected="pm.params.currency=#MRO#">Ouguiya (Mauritania)
<option value="TOP" selected="pm.params.currency=#TOP#">Pa'anga (Tonga)
<option value="PKR" selected="pm.params.currency=#PKR#">Pakistan Rupee (Pakistan)
<option value="XPD" selected="pm.params.currency=#XPD#">Palladium
<option value="MOP" selected="pm.params.currency=#MOP#">Pataca (Macau)
<option value="UYU" selected="pm.params.currency=#UYU#">Peso Uruguayo (Uruguay)
<option value="PHP" selected="pm.params.currency=#PHP#">Philippine Peso (Philippines)
<option value="XPT" selected="pm.params.currency=#XPT#">Platinum
<option value="PTE" selected="pm.params.currency=#PTE#">Portuguese Escudo (Portugal)
<option value="GBP" selected="pm.params.currency=#GBP#">Pound Sterling (United Kingdom)
<option value="BWP" selected="pm.params.currency=#BWP#">Pula (Botswana)
<option value="QAR" selected="pm.params.currency=#QAR#">Qatari Rial (Qatar)
<option value="GTQ" selected="pm.params.currency=#GTQ#">Quetzal (Guatemala)
<option value="ZAL" selected="pm.params.currency=#ZAL#">Rand (Financial) (Lesotho)
<option value="ZAR" selected="pm.params.currency=#ZAR#">Rand (South Africa)
<option value="OMR" selected="pm.params.currency=#OMR#">Rial Omani (Oman)
<option value="KHR" selected="pm.params.currency=#KHR#">Riel (Cambodia)
<option value="MVR" selected="pm.params.currency=#MVR#">Rufiyaa (Maldives)
<option value="IDR" selected="pm.params.currency=#IDR#">Rupiah (Indonesia)
<option value="RUB" selected="pm.params.currency=#RUB#">Russian Ruble (Russian Federation)
<option value="RUR" selected="pm.params.currency=#RUR#">Russian Ruble (Russian Federation)
<option value="RWF" selected="pm.params.currency=#RWF#">Rwanda Franc (Rwanda)
<option value="SAR" selected="pm.params.currency=#SAR#">Saudi Riyal (Saudi Arabia)
<option value="ATS" selected="pm.params.currency=#ATS#">Schilling (Austria)
<option value="SCR" selected="pm.params.currency=#SCR#">Seychelles Rupee (Seychelles)
<option value="XAG" selected="pm.params.currency=#XAG#">Silver
<option value="SGD" selected="pm.params.currency=#SGD#">Singapore Dollar (Singapore)
<option value="SKK" selected="pm.params.currency=#SKK#">Slovak Koruna (Slovakia)
<option value="SBD" selected="pm.params.currency=#SBD#">Solomon Islands Dollar (Solomon Islands)
<option value="KGS" selected="pm.params.currency=#KGS#">Som (Kyrgyzstan)
<option value="SOS" selected="pm.params.currency=#SOS#">Somali Shilling (Somalia)
<option value="ESP" selected="pm.params.currency=#ESP#">Spanish Peseta (Spain)
<option value="LKR" selected="pm.params.currency=#LKR#">Sri Lanka Rupee (Sri Lanka)
<option value="SHP" selected="pm.params.currency=#SHP#">St Helena Pound (St Helena)
<option value="ECS" selected="pm.params.currency=#ECS#">Sucre (Ecuador)
<option value="SDD" selected="pm.params.currency=#SDD#">Sudanese Dinar (Sudan)
<option value="SRG" selected="pm.params.currency=#SRG#">Surinam Guilder (Suriname)
<option value="SEK" selected="pm.params.currency=#SEK#">Swedish Krona (Sweden)
<option value="CHF" selected="pm.params.currency=#CHF#">Swiss Franc (Switzerland)
<option value="SYP" selected="pm.params.currency=#SYP#">Syrian Pound (Syrian Arab Republic)
<option value="TJR" selected="pm.params.currency=#TJR#">Tajik Ruble (Tajikistan)
<option value="BDT" selected="pm.params.currency=#BDT#">Taka (Bangladesh)
<option value="WST" selected="pm.params.currency=#WST#">Tala (Samoa)
<option value="TZS" selected="pm.params.currency=#TZS#">Tanzanian Shilling (United Republic Of Tanzania)
<option value="KZT" selected="pm.params.currency=#KZT#">Tenge (Kazakhstan)
<option value="TPE" selected="pm.params.currency=#TPE#">Timor Escudo (East Timor)
<option value="SIT" selected="pm.params.currency=#SIT#">Tolar (Slovenia)
<option value="TTD" selected="pm.params.currency=#TTD#">Trinidad and Tobago Dollar (Trinidad And Tobago)
<option value="MNT" selected="pm.params.currency=#MNT#">Tugrik (Mongolia)
<option value="TND" selected="pm.params.currency=#TND#">Tunisian Dinar (Tunisia)
<option value="TRL" selected="pm.params.currency=#TRL#">Turkish Lira (Turkey)
<option value="UGX" selected="pm.params.currency=#UGX#">Uganda Shilling (Uganda)
<option value="ECV" selected="pm.params.currency=#ECV#">Unidad de Valor Constante (Ecuador)
<option value="CLF" selected="pm.params.currency=#CLF#">Unidades de fomento (Chile)
<option value="USN" selected="pm.params.currency=#USN#">US Dollar (Next day) (United States)
<option value="USS" selected="pm.params.currency=#USS#">US Dollar (Same day) (United States)
<option value="USD" selected="pm.params.currency=#USD#">US Dollar (United States)
<option value="UZS" selected="pm.params.currency=#UZS#">Uzbekistan Sum (Uzbekistan)
<option value="VUV" selected="pm.params.currency=#VUV#">Vatu (Vanuatu)
<option value="KRW" selected="pm.params.currency=#KRW#">Won (Republic Of Korea)
<option value="YER" selected="pm.params.currency=#YER#">Yemeni Rial (Yemen)
<option value="JPY" selected="pm.params.currency=#JPY#">Yen (Japan)
<option value="CNY" selected="pm.params.currency=#CNY#">Yuan Renminbi (China)
<option value="ZWD" selected="pm.params.currency=#ZWD#">Zimbabwe Dollar (Zimbabwe)
<option value="PLN" selected="pm.params.currency=#PLN#">Zloty (Poland)
</select>
</td>
</tr>

<tr>
	<td valign="top">
		<b>Security options:</b><br>
		These options protect your orders: they prohibit changing order total during payment process and paying in another currency.<br>
		<b>Note:</b> It is strongly recommended not to disable these options.
	</td>
	<td valign="top">
		<input type="checkbox" id="check_total_id" name="params[check_total]" value="1" checked="{isSelected(pm.params.check_total,#1#)}"><label for="check_total_id">Perform order total check after transaction</label><br>
		<input type="checkbox" id="check_currency_id" name="params[check_currency]" value="1" checked="{isSelected(pm.params.check_currency,#1#)}"><label for="check_currency_id">Perform payment currency check after transaction</label><br>
	</td>
</tr>

<tr>
<td colspan=2>
<input type=submit value=" Update ">
</td>
</tr>
</table>
</form>
