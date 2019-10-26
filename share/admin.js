/**
 * The current controller name
 *
 * @type String
 */
var sCurrentController = '';

/**
 * The ID of the current Model
 *
 * @type Number
 */
var iCurrentModelId = 0;

/**
 * Generate and return the controller name from the specified URL
 *
 * @param {String} sUrl - The URL to extract the controller name from, if there is one
 * @returns {String}
 */
function urlController(sUrl)
{
  var aMatches = sUrl.match(/^.*admin\/(.*?)(\/|$)/);
  return aMatches && aMatches.length > 0 ? aMatches[1] : '';
}

/**
 * Generate and return the model ID from the specified URL
 *
 * @param {String} sUrl - The URL to extract the model ID from, if there is one
 * @returns {String}
 */
function urlModelId(sUrl)
{
  var aMatches = sUrl.match(/^.*admin\/.*?\/(\d+?)/);
  return aMatches && aMatches.length > 0 ? parseInt(aMatches[1]) : 0;
}

/**
 * Generate the model section from the specified data, then insert it into the DOM
 *
 * @param {Object} oData
 */
function buildModel(oData)
{
  var sLowerController = oData.controllerType.toLowerCase();
  var sModelNav = '';
  var sPageTitle = oData.controllerType + ' #' + oData.id + ' > ' + oData.modelTitle + ' > ' + oData.action.charAt(0).toUpperCase() + oData.action.slice(1);
  $(document).prop('title', sPageTitle);

  for (var sAction in oData.subMenu)
  {
    var sCurrent = oData.action === sAction ? 'current ' : '';
    sModelNav += '  <a class="model ' + sCurrent + 'tab ' + sLowerController + ' ' + sAction + '" href="' + oData.modelUri + '/' + sAction + '">' + oData.subMenu[sAction] + '</a>\n';
  }

  if (iCurrentModelId !== oData.id)
  {
    iCurrentModelId = oData.id;
    $('#content > nav.tabSet').children().removeClass('current');
    $('#content > nav.tabSet > span').remove();
    $('#content > nav.tabSet').append('<span class="current tab ' + sLowerController + ' ' + oData.action + ' noLink">' + oData.controllerType + ' #' + oData.id + '</span>');
  }

  $('#controllerOutput').html('      <div id="model">\
      <h2 class="title">' + oData.modelTitle + '</h2>\
      <div class="tabSet">\
' + sModelNav + '\
      </div>\
      <div id="page">\
      </div>\
    </div>\n');
}

/**
 * Update the primary admin navigation based on the specified new controller name
 *
 * @param {String} sControllerName - The name of the controller to display
 */
function updateAdminNav(sControllerName)
{
  sCurrentController = sControllerName.toLowerCase();
  $('#admin > nav > a').show();
  $('#admin > nav > .controller').hide();
  $('#content > .tabSet > a').hide();

  if (sCurrentController)
  {
    $('#content > .tabSet > span').not('.' + sCurrentController).remove();
    $('#admin > nav > a.' + sCurrentController).hide();
    $('#admin > nav > .controller.' + sCurrentController).show();
    $('#content > .tabSet > .' + sCurrentController).show();
  }
}

/**
 * Generate and insert data from the specified URL
 *
 * @param {String} sUrl - The URL to generate and insert data from
 * @param {String} sType (optional) - The type of data being inserted (defaults to 'controller')
 * @param {String} sFormData (optional) - The form data to submit
 * @param {Boolean} bHasFiles (optional) - The form data contains files to upload...
 */
function updateNav(sUrl, sType, sFormData, bHasFiles)
{
  if (arguments.length < 2) { sType = 'controller'; }
  if (arguments.length < 3) { sFormData = false; }
  if (arguments.length < 4) { bHasFiles = false; }

  var sUrlController = urlController(sUrl);

  if (sCurrentController !== sUrlController)
  {
    updateAdminNav(sUrlController);
    sType = 'controller';
  }

  var iUrlModelId = urlModelId(sUrl);
  var sOverlayId = sUrlController + iUrlModelId + '-' + Math.floor(1000 * Math.random());

  //if the type is 'model' *and* '#model > #page' exists then use it... otherwise use controllerOutput
  var sOverlayTarget = sType === 'model' && $('#model > #page').length ? '#model > #page' : '#controllerOutput';

  history.pushState(null, '', sUrl);
  var sUrlJoinCharacter = sUrl.match(/\?/) ? '&' : '?';

  var hAjaxConfig =
  {
    beforeSend: function()
    {
      var oTarget = $(sOverlayTarget);
      var oTargetPosition = oTarget.position();
      oTarget.append('<div class="overlay" id="' + sOverlayId + '" style="height: ' + oTarget.height() + 'px; left: ' + oTargetPosition.left + 'px; top: ' + oTargetPosition.top + 'px; width: ' + oTarget.width() + 'px;' + '"></div>');
    },
    method: 'GET',
    dataType: 'json',
    url: sUrl.match(/#/) ? sUrl.replace(/#/, sUrlJoinCharacter + 'ajax=click#') : sUrl + sUrlJoinCharacter + 'ajax=click'
  };

  if (sFormData)
  {
    hAjaxConfig['method'] = 'POST';
    hAjaxConfig['data'] = sFormData;

    if (bHasFiles)
    {
      hAjaxConfig['cache'] = false;
      hAjaxConfig['contentType'] = false;
      hAjaxConfig['processData'] = false;
    }
  }

  $.ajax(hAjaxConfig)
  .done(function(oData, sStatus, oRequest)
  {
    if (oData.content.match(/<html/g))
    {
      document.open('text/html');
      document.write(oData.content);
      document.close();
    }
    else if (oData.error)
    {
      $('#controllerOutput').html(oData.error);
    }
    else if (oData.action)
    {
      var bQuick = sUrl.match(/search\/quick$/);

      if (bQuick || (oData.id > 0 && oData.subMenu[oData.action]))
      {
        sType = 'model';

        if (bQuick && oData.modelUri && oData.modelUri !== sUrl)
        {
          history.pushState(null, '', oData.modelUri);
        }
      }

      switch (sType)
      {
        case 'model':
          if (oData.id)
          {
            buildModel(oData);
          }

          $('#model > #page').empty().html(oData.content);
          $('#model > .tabSet > a.' + oData.controllerType.toLowerCase() + '.' + oData.action).addClass('current').siblings().removeClass('current');
          break;

        default:
          var sPageTitle = oData.controllerType + ' > ' + oData.action.charAt(0).toUpperCase() + oData.action.slice(1);
          $(document).prop('title', sPageTitle);

          $('#controllerOutput').empty().html(oData.content);
          $('#content > .tabSet > span').remove();
          $('#content > .tabSet > a.' + oData.controllerType.toLowerCase() + '.' + oData.action).addClass('current').siblings().removeClass('current');
      }
    }
  })
  .always(function()
  {
    $(sOverlayId).remove();
  });
}

$(function()
{
  /**
   * Handle clicks on the controller list with AJAX instead of the default URL
   */
  $('#admin').on('click', 'nav.controllerList > a', function(e)
  {
    updateAdminNav($(this).attr('class'));
    updateNav($(this).attr('href'));
    e.preventDefault();
  });

  /**
   * Handle clicks on the current controller's tabs with AJAX instead of the default URL
   */
  $('#content').on('click', 'nav.tabSet > a', function(e)
  {
    updateNav($(this).attr('href'));
    e.preventDefault();
  });

  /**
   * Handle clicks on the specified URLs in the controllerOutput with AJAX instead of the default URL
   */
  $('#controllerOutput').on('click', 'a.controller', function(e)
  {
    updateNav($(this).attr('href'), 'controller');
    e.preventDefault();
  });

  /**
   * Handle clicks on the specified URLs in the controllerOutput with AJAX instead of the default URL
   */
  $('#controllerOutput').on('click', 'a.model', function(e)
  {
    updateNav($(this).attr('href'), 'model');
    e.preventDefault();
  });

  /**
   * Make options that only work if boxes are checked only visible when they are checked
   */
  $('#controllerOutput').on('click', '.LimboniaSortGridCellCheckbox', function()
  {
    var bChecked = $('.LimboniaSortGridCellCheckbox:checked').length > 0;
    $('.LimboniaSortGridDelete').toggle(bChecked);
    $('.LimboniaSortGridEdit').toggle(bChecked);
  });

  /**
   * Handle clicks on the specified URLs in the top header with AJAX instead of the default URL
   */
  $('body > header').on('click', 'a.model', function(e)
  {
    updateNav($(this).attr('href'), 'model');
    e.preventDefault();
  });

  /**
   * Handle form submission with AJAX instead of the default URL
   */
  $('#admin').on('click', 'form button#No', function(e)
  {
    var oForm = $(this).parent();
    var sUri = $(oForm).prop('action');
    var sType = urlModelId(sUri) > 0 ? 'model' : 'controller';
    oFormData = oForm.serialize() + '&No=1';
    updateNav(sUri, sType, oFormData);
    e.preventDefault();
  });

  /**
   * Handle form submission with AJAX instead of the default URL
   */
  $('#admin').on('submit', 'form', function(e)
  {
    var sUri = $(this).prop('action');
    var sType = urlModelId(sUri) > 0 ? 'model' : 'controller';
    var oFormData = null;
    var bHasFiles = false;

    //Look for file input fields
    var aFileInput = $(this).children('input[type=file]');

    //if there any file input fields
    if (aFileInput.length > 0)
    {
      //if the FormData class doesn't exist
      if (!window.FormData)
      {
        //then do nothing else and process this post normally instead of using AJAX
        return;
      }

      //get the form object
      var oForm = $(this);

      //use the form object to generate the the FormData object
      oFormData = new FormData(oForm[0]);
      bHasFiles = true;
    }
    else
    {
      oFormData = $(this).serialize();
    }

    updateNav(sUri, sType, oFormData, bHasFiles);
    e.preventDefault();
  });
});