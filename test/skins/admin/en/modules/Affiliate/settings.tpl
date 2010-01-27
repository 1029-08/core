
    {if:option.isName(#default_plan#)}
    <select name="{option.name}">
        <option value="" selected="{option.value=##}"> - select - </option>
        <option FOREACH="xlite.factory.XLite_Module_Affiliate_Model_AffiliatePlan.findAll(#enabled=1#),ap" value="{ap.plan_id}" selected="{option.value=ap.plan_id}">{ap.title:h}</option>
    </select>
    {end:}

    {if:option.isName(#tiers_number#)}
    <select name="{option.name}">
        <option value="1" selected="{option.value=#1#}">1 (default)</option>
        <option value="2" selected="{option.value=#2#}">2</option>
        <option value="3" selected="{option.value=#3#}">3</option>
        <option value="4" selected="{option.value=#4#}">4</option>
        <option value="5" selected="{option.value=#5#}">5</option>
    </select>
    {end:}

    {if:option.isName(#tier_commission_rates#)}
    <span IF="!config.Affiliate.tier_commission_rates">- no tiers -</span>
    <table border=0>
    <tr FOREACH="config.Affiliate.tier_commission_rates,tidx,tr"><td>Tier {tidx} Rate&nbsp;</td><td><input type=text name="tier_commission_rates[{tidx}]" value="{tr}" size=6></td><td>%</td></tr>
    </table>
    {end:}
