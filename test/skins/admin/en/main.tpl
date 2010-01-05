<!-- [begin] -->
<html>
<head>
    <title>LiteCommerce online store builder</title>
	<meta http-equiv="Content-Type" content="text/html; charset={charset}">
    <meta name="ROBOTS" content="NOINDEX">
    <meta name="ROBOTS" content="NOFOLLOW">
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
    <LINK href="skins/admin/en/style.css"  rel=stylesheet type=text/css>
</head>
<body LEFTMARGIN=0 TOPMARGIN=0 RIGHTMARGIN=0 BOTTOMMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0>
<!-- [/begin] -->
<table border=0 width="100%" height="100%">
<tr>
<td valign=top>

<!-- [top] -->
<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
<TR>
   <TD class="Head" background="images/head_demo_01.gif" WIDTH=494 HEIGHT=73 style="background-color: #FFFFFF; background-repeat: no-repeat;" valign=top><IMG SRC="images/logo_demo.gif" WIDTH=275 HEIGHT=60 BORDER="0"><br><IMG SRC="images/spacer.gif" WIDTH=494 HEIGHT=1 BORDER="0"></TD>
   <TD class="Head" background="images/head_demo_02.gif" WIDTH="100%">
   <IMG SRC="images/spacer.gif" WIDTH=1 HEIGHT=1 BORDER="0"></TD>
   <td align="right" valign="top" nowrap background="images/head_demo_02.gif">
   	  <br>
      Welcome <span class="FormButton"><span IF="!auth.profile.billing_firstname=##">{auth.profile.billing_title} {auth.profile.billing_firstname} {auth.profile.billing_lastname}</span><span IF="auth.profile.billing_firstname=##">{auth.profile.login}</span></span>!<br>
      (<span class="FormButton">{auth.profile.login}</span> logged in)</span>
      <span IF="recentAdmins">,&nbsp;<a href="admin.php?target=recent_login" onClick="this.blur()"><u>login history </u></a></span>
      </span>
   	  <br>
      <a href="admin.php?target=login&action=logoff" class="FormButton"><img src="images/go.gif" width="13" height="13" border="0" align="absmiddle"> Log Off</a>
   </td>
   <td>&nbsp;&nbsp;&nbsp;</td>
</TR>
</TABLE>
<table width="100%" border=0 cellpadding=0 cellspacing=0>
<tr>
   <td width="100%" background="skins/admin/en/images/head_line.gif" height=12 align=right>Version: {config.Version.version}&nbsp;&nbsp;&nbsp;</td>
</tr>
</table>
<BR>
<!-- [/top] -->

<!-- [main_view] -->
<table border="0" width="100%" align="center" cellpadding="0" cellspacing="0">
<tr>
    <td nobr width="150" valign="top">
<!-- [left] -->
<widget template="common/sidebar_box.tpl" dir="management">
<widget template="common/sidebar_box.tpl" dir="catalog">
<widget template="common/sidebar_box.tpl" dir="settings">
<widget template="common/sidebar_box.tpl" dir="maintenance">
<widget template="common/sidebar_box.tpl" dir="look_feel">
<widget template="common/sidebar_box.tpl" dir="help">
<widget module="Affiliate" template="common/sidebar_box.tpl" dir="modules/Affiliate/menu">
<!-- [/left] -->
    </td>
    <td width="10">&nbsp;</td>
    <td valign="top">
    <noscript>
            <table border=0 width=500 cellpadding=2 cellspacing=0 align=center>
            <tr>
                <td align=center class=ErrorMessage nowrap>This site requires JavaScript to function properly.<br>Please enable JavaScript in your web browser.</td>
            </tr>
            </table>
    </noscript>
<!-- [center] -->
<widget template="location.tpl" visible="{!target=#main#}">
<p>
<widget target="access_denied" template="access_denied.tpl">
<widget module="DemoMode" target="settings,payment_method,payment_methods,modules,memberships" template="common/dialog.tpl" body="modules/DemoMode/warning.tpl" head="Demo mode warning">
<widget template="common/dialog.tpl" head="Customer zone warning" body="customer_zone_warning.tpl" visible="{customerZoneWarning}">
<widget target="main" template="common/dialog.tpl" head="Welcome to the Administrator Zone" body="menu.tpl">
<widget target="modules" template="common/dialog.tpl" head="Modules" body="modules.tpl">
<widget target="module" template="common/dialog.tpl" head="Module {page} settings" body="general_settings.tpl">

<widget module="HTMLCatalog" target="catalog" template="common/dialog.tpl" head="HTML catalog" body="modules/HTMLCatalog/catalog.tpl">
<widget module="AutoUpdateCatalog" mode="confirm" target="autoupdate_catalog" template="common/dialog.tpl" head="Confirm HTML catalog update" body="modules/AutoUpdateCatalog/body.tpl">

<widget name="categoriesWidget" target="categories" template="common/dialog.tpl" head="Manage categories" body="categories/body.tpl" visible="{!mode=#delete_all#}">
<widget module="FeaturedProducts" template="common/dialog.tpl" head="Featured products" body="modules/FeaturedProducts/featuredProducts.tpl" visible="{categoriesWidget.visible}">
<span IF="!xlite.LayoutOrganizerEnabled">
<widget target="category" class="XLite_View_Tabber" body="{pageTemplate}" switch="page" visible="{!mode=#delete#}">
</span>
<span IF="xlite.LayoutOrganizerEnabled">
<widget module="LayoutOrganizer" target="category" class="XLite_View_Tabber" body="{pageTemplate}" switch="page" visible="{!mode=#delete#}">
</span>
<widget target="categories" template="common/dialog.tpl" body="categories/delete_all.tpl" head="Confirmation" mode="delete_all">
<widget target="category" template="common/dialog.tpl" body="categories/delete.tpl" head="Confirmation" mode="delete">

<widget target="card_types" template="common/dialog.tpl" body="card_types.tpl" head="Edit credit card types">

<widget target="settings" class="XLite_View_Tabber" body="general_settings.tpl" switch="page">

<widget template="users/search.tpl" target="users">
<widget target="recent_login" template="common/dialog.tpl" body="recent_login.tpl" head="Login history">

<widget target="payment_methods" template="common/dialog.tpl" body="payment_methods/body.tpl" head="Payment methods">

<widget target="product_list" template="product/product_list_form.tpl">
<widget target="product" class="XLite_View_Tabber" body="{pageTemplate}" switch="page">

<widget target="extra_fields" template="common/dialog.tpl" body="product/extra_fields_form.tpl" head="Global extra fields">

<widget target="add_product" mode="" template="common/dialog.tpl" body="product/add.tpl" head="Add New Product">
<widget target="add_product" mode="notification" template="common/dialog.tpl" body="product/add_notification.tpl" head="Notification">

<widget target="profile" mode="delete" template="common/dialog.tpl" head="Delete profile - Confirmation" body="profile/confirm_delete.tpl">
<widget target="wysiwyg" template="common/dialog.tpl" head="HTML design import/export" body="wysiwyg.tpl">
<widget target="profile" mode="modify" class="XLite_View_RegisterForm" template="common/dialog.tpl" head="Modify profile" body="profile/body.tpl" name="profileForm">
<widget target="profile" mode="register" class="XLite_View_RegisterForm" template="common/dialog.tpl" head="Add new user" body="profile/body.tpl" name="registerForm">

<widget target="order_list,order,advanced_security" module="AdvancedSecurity" template="modules/AdvancedSecurity/advanced_security.tpl">
<widget module="AntiFraud" target='order'  visible="{mode}" mode="{mode}" template="common/dialog.tpl" body="modules/AntiFraud/tracking/message.tpl" head="AntiFraud Service Notification">
<widget module="AntiFraud" target='order' template="common/dialog.tpl" body="modules/AntiFraud/order.tpl" head="AntiFraud Service" visible="{order.details.af_result}">
<widget target="currencies" module="MultiCurrency" template="common/dialog.tpl" body="modules/MultiCurrency/currencies.tpl">
<widget target="advanced_search" module="AdvancedSearch" template="common/dialog.tpl" body="modules/AdvancedSearch/config.tpl">
<widget target="order_list" template="order/search.tpl">

<span IF="!xlite.AOMEnabled">
{if:!xlite.GoogleCheckoutEnabled}
<widget target="order" template="common/dialog.tpl" body="order/order.tpl" head="Order # {order.order_id}">
{else:}
<widget module="AOM" template="modules/AOM/main.tpl">
{end:}
</span>
<span IF="xlite.AOMEnabled">
<widget module="AOM" template="modules/AOM/main.tpl">
</span>
<widget target="shipping_methods" class="XLite_View_Tabber" body="{pageTemplate}" switch="target">
<widget target="shipping_rates" class="XLite_View_Tabber" body="{pageTemplate}" switch="target">
<widget target="shipping_zones" class="XLite_View_Tabber" body="{pageTemplate}" switch="target">
<widget module="GoogleCheckout" template="modules/GoogleCheckout/main.tpl">

<widget template="stats.tpl">
<widget module="EcommerceReports" template="modules/EcommerceReports/reports.tpl">

<widget target="payment_method" body="{pm.configurationTemplate}" template="common/dialog.tpl" head="{pm.processorName} settings">

<widget target="global_product_options" module="ProductOptions" template="common/dialog.tpl" head="Global product options" body="modules/ProductOptions/global_product_options.tpl">

<widget target="css_edit" template="common/dialog.tpl" body="css_editor/css_edit.tpl" head="CSS Editor">
<widget target="image_edit" template="common/dialog.tpl" body="image_editor/edit.tpl" head="Image Editor">
<widget target="change_skin" template="common/dialog.tpl" body="change_skin.tpl" head="Change Current Skin">
<widget target="countries" template="common/dialog.tpl" body="countries.tpl" head="Countries">
<widget target="states" template="common/dialog.tpl" body="states.tpl" head="States">
<widget class="XLite_View_Tabber" target="taxes" body="{pageTemplate}" switch="page">
<widget class="XLite_View_Tabber" target="db" body="{pageTemplate}" switch="page">

<widget target="import_users" template="common/dialog.tpl" body="import_users.tpl" head="Import users">
<widget target="import_catalog" class="XLite_View_Tabber" body="{pageTemplate}" switch="page">
<widget target="export_catalog" class="XLite_View_Tabber" body="{pageTemplate}" switch="page">
<widget target="update_inventory" class="XLite_View_Tabber" body="{pageTemplate}" switch="page">

<widget target="memberships" template="common/dialog.tpl" body="memberships.tpl" head="Membership levels">
<widget target="template_editor" class="XLite_View_Tabber" body="{pageTemplate}" switch="editor">
<widget target="image_files" template="common/dialog.tpl" body="image_files.tpl" head="Image files">
<widget module="GiftCertificates" target="gift_certificates" template="common/dialog.tpl" body="modules/GiftCertificates/list.tpl" head="Gift certificates">
<widget module="GiftCertificates" target="add_gift_certificate" template="common/dialog.tpl" body="modules/GiftCertificates/add_gift_certificate.tpl" head="Add gift certificate">
<widget module="GiftCertificates" target="gift_certificate" template="common/dialog.tpl" body="modules/GiftCertificates/gift_certificate.tpl" head="Gift certificate">
<widget module="GiftCertificates" target="gift_certificate_ecards" template="common/dialog.tpl" body="modules/GiftCertificates/ecards.tpl" head="Gift certificate e-Cards">
<widget module="GiftCertificates" target="gift_certificate_ecard" template="common/dialog.tpl" body="modules/GiftCertificates/ecard.tpl" head="Gift certificate e-Card">
<widget module="GiftCertificates" target="gift_certificate_select_ecard" template="common/dialog.tpl" body="modules/GiftCertificates/select_ecard.tpl" head="Select e-Card">
<widget module="Promotion" target="SpecialOffers" template="common/dialog.tpl" body="modules/Promotion/special_offers.tpl" head="Special Offers">
<widget module="Promotion" target="SpecialOffer" mode="" template="common/dialog.tpl" body="modules/Promotion/special_offer1.tpl" head="Special Offer Type">
<widget module="Promotion" target="SpecialOffer" mode="details" template="common/dialog.tpl" body="modules/Promotion/special_offer2.tpl" head="Special Offer Details">
<widget module="Promotion" target="DiscountCoupons" template="common/dialog.tpl" body="modules/Promotion/coupons.tpl" head="Discount coupons">
<widget module="Promotion" template="modules/Promotion/main.tpl">

<widget module="UPS" target="ups" class="XLite_View_Tabber" body="{pageTemplate}" switch="target">
<widget module="UPS" target="ups"  template="common/dialog.tpl" body="modules/UPS/test.tpl" head="UPS Live Test">
<widget module="USPS" target="usps" class="XLite_View_Tabber" body="{pageTemplate}" switch="target">
<widget module="USPS" target="usps"  template="common/dialog.tpl" body="modules/USPS/test.tpl" head="USPS Live Test">
<widget module="Intershipper" target="intershipper" class="XLite_View_Tabber" body="{pageTemplate}" switch="target">
<widget module="Intershipper" target="intershipper"  template="common/dialog.tpl" body="modules/Intershipper/test.tpl" head="Intershipper Live Test">
<widget module="AustraliaPost" template="modules/AustraliaPost/main.tpl">
<widget module="CanadaPost" target="cps" class="XLite_View_Tabber" body="{pageTemplate}" switch="target">
<widget module="CanadaPost" target="cps" template="common/dialog.tpl" body="modules/CanadaPost/test.tpl" head="Canada Post Live Test">
<widget module="DemoMode" target="demo_mode"  template="common/dialog.tpl" body="modules/DemoMode/denied.tpl" head="Demo Mode Access Error">
<widget module="XCartImport" target="xcart_import" template="common/dialog.tpl" body="modules/XCartImport/dialog.tpl" head="Import X-Cart data">
<widget module="Affiliate" template="modules/Affiliate/main.tpl">
<widget module="Newsletters" template="modules/Newsletters/newsletters.tpl">
<widget module="UPSOnlineTools" template="modules/UPSOnlineTools/main.tpl">
<widget module="ProductAdviser" template="modules/ProductAdviser/main.tpl">
<widget module="WishList" target="wishlists" template="modules/WishList/wishlists.tpl" head="Wish Lists">
<widget module="WishList" target="wishlist" template="common/dialog.tpl" body="modules/WishList/wishlist.tpl" head="Wish List">
<widget module="LayoutOrganizer" template="modules/LayoutOrganizer/main.tpl">
<widget module="WholesaleTrading" template="modules/WholesaleTrading/main.tpl"> 
<widget module="FlyoutCategories" template="modules/FlyoutCategories/main.tpl">
<!-- [/center] -->
    </td>
    <td width="10">&nbsp;</td>
    <td width="0" valign="top">
<!-- [right] -->
<!-- [/right] -->
    </td>
</table>
<!-- [/main_view] -->

</td>
</tr>

<!-- align code -->
<script language="JavaScript">
if (navigator.appName.indexOf('Microsoft') >= 0) {
    document.write('<TR><TD height="100%"><img src="images/spacer.gif" width=1 height=1></TD></TR>');
} else {
    document.write('<TR><TD><img src="images/spacer.gif" width=1 height=1></TD></TR>');
}    
</script>


<tr>
<td align="center">
<!-- [bottom] -->
<table WIDTH="100%" BORDER=0 CELLPADDING=3 CELLSPACING=0>
<tr>
<td bgcolor=#E0E0E0 HEIGHT=15 align=left>
&nbsp;<font color="#8A8A8A">Powered by LiteCommerce:</font> <a href="http://www.litecommerce.com"><font color="#8A8A8A"><u>ecommerce software</u></font></a>
</td>
<td bgcolor=#E0E0E0 HEIGHT=15 align=right>
<font color="#8A8A8A">Copyright &copy; {config.Company.start_year} {config.Company.company_name}</font>
&nbsp;</td>
</tr>
</table>
<!-- [/bottom] -->

</td>
</tr>
</table>

<!-- [end] -->

</body>
</html>
<!-- [/end] -->
