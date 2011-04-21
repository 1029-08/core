{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Language options dialog
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 *}
<div class="language-options-dialog">
  <h2>{t(#X Language (Code: Y)#,_ARRAY_(#language#^editLanguage.name,#code#^editLanguage.code))}</h2>
  <form action="admin.php" method="post" name="confirm_delete_form">
    <input type="hidden" name="target" value="languages" />
    <input type="hidden" name="action" value="update_language" />
    <input type="hidden" name="language" value="{language}" />
    <input type="hidden" name="page" value="{page}" />
    <input type="hidden" name="lng_id" value="{editLanguage.lng_id}" />

    <ul class="form">

      <li class="default">
        <label>{t(#Language name in X#,_ARRAY_(#language#^defaultLanguage.name))}:</label>
        <input type="text" name="name[{defaultLanguage.code}]" value="{getTranslation(defaultLanguage)}" style="background-image: url({defaultLanguage.flagURL});" lang="{defaultLanguage.code}" xml:lang="{defaultLanguage.code}" />
      </li>

      <li IF="!editLanguage.code=defaultLanguage.code" class="native">
        <label>{t(#Native language name#)}:</label>
        <input type="text" name="name[{editLanguage.code}]" value="{getTranslation(editLanguage)}" style="background-image: url({editLanguage.flagURL});" lang="{editLanguage.code}" xml:lang="{editLanguage.code}" />
      </li>

    </ul>

    <div class="{getSwictherClass()}">
      {if:!canSwitch()}
        <img src="images/spacer.gif" alt="" /> {t(#The language is enabled#):h}
      {else:}
        {if:editLanguage.enabled}
          <img src="images/spacer.gif" alt="" /> {t(#The language is enabled#):h}
        {else:}
          <img src="images/spacer.gif" alt="" /> {t(#The language is disabled#):h}
        {end:}
      {end:}

      {if:canSwitch()}
        [
          <a href="admin.php?target=languages&action=switch&lng_id={editLanguage.lng_id}&language={language}&page={page}">{if:editLanguage.enabled}{t(#Disable#)}{else:}{t(#Enable#)}{end:}</a>
        ]
      {end:}

    </div>

    {if:!canSwitch()}
      <div class="default-language">{t(#Default for customer zone and can not disabled or deleted#)}</div>
    {else:}
      <div class="default-switcher">
        {if:editLanguage.enabled}
          <input type="checkbox" name="default" id="default_switcher" value="Y" />
          <label for="default_switcher">{t(#Set this language as default for customer zone#)}</label>
        {else:}
          {t(#Disabled language can not be set as default for customer zone#)}
        {end:}
      </div>
    {end:}

    <div class="buttons">
      <a IF="canDelete()" href="admin.php?target=languages&action=delete&lng_id={editLanguage.lng_id}&page={page}" class="delete" onclick="javascript: return confirmLanguageDelete(this, {editLanguage.lng_id}, '', '{editLanguage.flagURL}', '{editLanguage.name}');"><span>{t(#Delete Language#)}</span></a>
      <widget class="\XLite\View\Button\Submit" label="Save" style="main invert" />
    </div>
  </form>
</div>
