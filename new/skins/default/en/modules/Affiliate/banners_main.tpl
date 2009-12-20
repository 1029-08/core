<span IF="!banners">There are no available banners</span>

<table IF="banners" border=0>
<tr><td>Placing a banner on your site is very easy. All you need to do is choose a banner, copy the piece of code next to it and insert it into your site page.</td></tr>
<tr><td>&nbsp;</td></tr>
<tbody FOREACH="banners,bidx,banner">
<tr><td>&nbsp;</td></tr>
<tr><td class=TextTitle>&quot;{banner.name:h}&quot; banner</td></tr>
<tr>
    <td><widget name="bannerWidget" class="CBanner" type="js" banner="{banner}"></td>
</tr>
<tr>
    <td>
    <textarea cols=80 rows=4><widget name="bannerWidget"></textarea>
    </td>
</tr>
<tr><td>&nbsp;</td></tr>
</tbody>
</table>
