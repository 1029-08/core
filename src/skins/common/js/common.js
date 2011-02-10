/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Common functions
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 */
var URLHandler = {

  mainParams: {target: true, action: true},

  baseURLPart: ('undefined' != typeof(window.xliteConfig) ? xliteConfig.script : 'admin.php') + '?',
  argSeparator: '&',
  nameValueSeparator: '=',

  // Return query param
  getParamValue: function(name, params)
  {
    return name + this.nameValueSeparator + params[name];
  },

  // Get param value for the target and action params
  getMainParamValue: function(name, params)
  {
    return URLHandler.getParamValue(name, params);
  },

  // Get param value for the remained params
  getQueryParamValue: function(name, params)
  {
    return URLHandler.getParamValue(name, params);
  },

  // Build HTTP query
  implodeParams: function(params, method)
  {
    result = '';
    isStarted = false;

    for (x in params) {

      if (isStarted) {
        result += this.argSeparator;
      } else {
        isStarted = true;
      }

      result += method(x, params);
    }

    return result;
  },

  // Implode target and action params
  implodeMainParams: function(params)
  {
    return this.implodeParams(params, this.getMainParamValue);
  },

  // Implode remained params
  implodeQueryParams: function(params)
  {
    return this.implodeParams(params, this.getQueryParamValue);
  },
  
  // Return some params
  getParams: function(params, toReturn)
  {
    result = [];

    for (x in toReturn) {
      result[x] = params[x];
    }

    return result;
  },

  // Unset some params
  clearParams: function(params, toClear)
  {
    result = [];

    for (x in params) {
      if (!(x in toClear)) {
        result[x] = params[x];
      }
    }
    
    return result;
  },

  // Compose target and action
  buildMainPart: function(params)
  {
    return this.implodeMainParams(this.getParams(params, this.mainParams));
  },

  // Compose remained params
  buildQueryPart: function(params)
  {
    return this.argSeparator + this.implodeQueryParams(this.clearParams(params, this.mainParams));
  },

  // Compose URL
  buildURL: function(params)
  {
    return this.baseURLPart + this.buildMainPart(params) + this.buildQueryPart(params);
  }
}

/**
 * Columns selector
 */
jQuery(document).ready(
  function() {
    jQuery('input.column-selector').click(
      function(event) {
        if (!this.columnSelectors) {
          var idx = jQuery(this).parents('th').get(0).cellIndex;
          var table = jQuery(this).parents('table').get(0);
          this.columnSelectors = [];
          for (var r = 0; r < table.rows.length; r++) {
            this.columnSelectors.push(jQuery(':checkbox', table.rows[r].cells[idx]).get(0));
          }
          this.columnSelectors = jQuery(this.columnSelectors);
        }

        this.columnSelectors.attr('checked', this.checked ? 'checked' : '');
      }
    );
  }
);

// Dialog

// Abstract open dialog
function openDialog(selector, additionalOptions)
{
  if (!jQuery('.ui-dialog ' + selector).length) {
    var options =  {
      dialogClass: 'popup',
      draggable: false,
      modal: true,
      resizable: false,
      minWidth: 350,
      minHeight: 150,
      open: function(event) {
        jQuery('.ui-dialog').css(
          {
            overflow: 'visible'
          }
        );
      }
    }

    if (additionalOptions) {
      for (var k in additionalOptions) {
        options[k] = additionalOptions[k];
      }
    }

    // Grab title from h2/h1 tag
    var hTags = ['h2','h1'];
    var tagSelector;
    for (var i in hTags) {
      tagSelector = hTags[i] + ':first-child';
      if (!options.title && jQuery(selector + ' ' + tagSelector).length) {
        options.title = jQuery(selector + ' ' + tagSelector).html();
        jQuery(selector + ' ' + tagSelector).remove();
        break;
      }
    }

    jQuery(selector).dialog(options);

  } else {
    jQuery(selector).dialog('open');
  }
}

// Loadable dialog
function loadDialog(url, dialogOptions, callback)
{
  openWaitBar();

  var selector = 'tmp-dialog-' + (new Date()).getTime();

  jQuery.get(
    url,
    {},
    function(data, status, ajax) {
      if (data) {
        var div = jQuery(document.body.appendChild(document.createElement('div')))
          .hide()
          .html(jQuery.trim(data));
        if (1 == div.get(0).childNodes.length) {
          div = jQuery(div.get(0).childNodes[0]);
        }

        div.addClass(selector);

        openDialog('.' + selector, dialogOptions);
        closeWaitBar();

        if (callback) {
          callback();
        }
      }
    }
  );

  return '.' + selector;
}

// Load dialog by link
function loadDialogByLink(link, url, options)
{
  if (!link.linkedDialog) {
    link.linkedDialog = loadDialog(url, options);

  } else {
    openDialog(link.linkedDialog, options);
  }
}

function openWaitBar()
{
  if (typeof(window._waitBar) == 'undefined') {
    window._waitBar = document.body.appendChild(document.createElement('div'));
    var selector = 'wait-bar-' + (new Date()).getTime();
    window._waitBar.style.display = 'none';
    window._waitBar.className = 'wait-box ' + selector;

    var box = window._waitBar.appendChild(document.createElement('div'));
    box.className = 'box';

    window._waitBar = '.' + selector;
  }

  if (jQuery('.ui-dialog ' + window._waitBar).length) {
    jQuery(window._waitBar).dialog('open');

  } else {

    var options =  {
      dialogClass:   'popup',
      draggable:     false,
      modal:         true,
      resizable:     false,
      closeOnEscape: false,
      minHeight:     11,
      width:         200,
      open:          function() {
        jQuery(window._waitBar).css('min-height', 'auto');
        jQuery('.ui-dialog-titlebar', jQuery(window._waitBar).parents('.ui-dialog').eq(0)).remove();
      }
    };

    jQuery(window._waitBar).dialog(options);
  }
}

function closeWaitBar()
{
  if (typeof(window._waitBar) != 'undefined') {
    jQuery(window._waitBar).dialog('close');
  }
}

// Check for the AJAX support
function hasAJAXSupport()
{
  if (typeof(window.ajaxSupport) == 'undefined') {
    window.ajaxSupport = false;
    try {

      var xhr = window.ActiveXObject ? new ActiveXObject('Microsoft.XMLHTTP') : new XMLHttpRequest();
      window.ajaxSupport = xhr ? true : false;

    } catch(e) { }
  }

  return window.ajaxSupport;
}

function normalizeSelect(name) {
  var tmp = document.getElementById(name);
  if (tmp)
    tmp.options[tmp.options.length-1] = null;
}
        

function moveSelect(left, right, type) {
  if (type != 'R') {
    var tmp = left;
    left = right;
    right = tmp;
  }
  if (!left || !right)
    return false;

  while (right.selectedIndex != -1) {
    left.options[left.options.length] = new Option(right.options[right.selectedIndex].text, right.options[right.selectedIndex].value);
    right.options[right.selectedIndex] = null;
  }

  return true;
}

function saveSelects(objects) {
  if (!objects)
    return false;

  for (var sel = 0; sel < objects.length; sel++) {
    if (document.getElementById(objects[sel]))
      if (document.getElementById(objects[sel]+"_store").value == '')
        for (var x = 0; x < document.getElementById(objects[sel]).options.length; x++)
          document.getElementById(objects[sel]+"_store").value += document.getElementById(objects[sel]).options[x].value+";";
  }
  return true;
}

// Check list of checkboxes
function checkMarks(form, reg, lbl) {
  var is_exist = false;

  if (!form || form.elements.length == 0)
    return true;

  for (var x = 0; x < form.elements.length; x++) {
    if (form.elements[x].name.search(reg) == 0 && form.elements[x].type == 'checkbox' && !form.elements[x].disabled) {
      is_exist = true;

      if (form.elements[x].checked)
        return true;
    }
  }

  if (!is_exist)
    return true;

  if (lbl) {
    alert(lbl);

  } else if (lbl_no_items_have_been_selected) {
    alert(lbl_no_items_have_been_selected);

  }

  return false;
}

/*
  Parameters: 
  checkboxes       - array of tag names
  checkboxes_form    - form name with these checkboxes
*/
function change_all(flag, formname, arr) {
  if (!formname)
    formname = checkboxes_form;
  if (!arr)
    arr = checkboxes;
  if (!document.forms[formname] || arr.length == 0)
    return false;
  for (var x = 0; x < arr.length; x++) {
    if (arr[x] != '' && document.forms[formname].elements[arr[x]] && !document.forms[formname].elements[arr[x]].disabled) {
         document.forms[formname].elements[arr[x]].checked = flag;
      if (document.forms[formname].elements[arr[x]].onclick)
        document.forms[formname].elements[arr[x]].onclick();
    }
  }
}

function checkAll(flag, form, prefix) {
  if (!form) {
    return;
  }

  if (prefix) {
    var reg = new RegExp('^' + prefix, '');
  }
  for (var i = 0; i < form.elements.length; i++) {
    if (
      form.elements[i].type == "checkbox"
      && (!prefix || form.elements[i].name.search(reg) == 0)
      && !form.elements[i].disabled
    ) {
      form.elements[i].checked = flag;
    }
  }
}

/*
  Opener/Closer HTML block
*/
function visibleBox(id, skipOpenClose)
{
  var elm1 = document.getElementById('open' + id);
  var elm2 = document.getElementById('close' + id);
  var elm3 = document.getElementById('box' + id);

  if(!elm3) {
    return false;
  }

  if (skipOpenClose) {
    elm3.style.display = (elm3.style.display == '') ? 'none' : '';

  } else if (elm1) {
    if (elm1.style.display == '') {
      elm1.style.display = 'none';

      if (elm2) {
        elm2.style.display = '';
      }

      elm3.style.display = 'none';
      jQuery('.DialogBox').css('height', '1%');

    } else {
      elm1.style.display = '';
      if (elm2) {
        elm2.style.display = 'none';
      }

      elm3.style.display = '';
    }
  }

  return true;
}
