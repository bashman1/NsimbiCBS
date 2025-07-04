"use strict";

var dlabThemeSet6 = {};

function getUrlParams(dParam) {
  var dPageURL = window.location.search.substring(1),
    dURLVariables = dPageURL.split("&"),
    dParameterName,
    i;

  for (i = 0; i < dURLVariables.length; i++) {
    dParameterName = dURLVariables[i].split("=");

    if (dParameterName[0] === dParam) {
      return dParameterName[1] === undefined
        ? true
        : decodeURIComponent(dParameterName[1]);
    }
  }
}

(function ($) {
  "use strict";

  /* var direction =  getUrlParams('dir');
	
	if(direction == 'rtl')
	{
        direction = 'rtl'; 
    }else{
        direction = 'ltr'; 
    } */

  dlabThemeSet6 = {
    typography: "Nunito",
    version: "light",
    layout: "horizontal",
    primary: "color_5",
    headerBg: "color_1",
    navheaderBg: "color_1",
    sidebarBg: "color_5",
    sidebarStyle: "icon-hover",
    sidebarPosition: "fixed",
    headerPosition: "static",
    containerLayout: "full",
  };

  new dlabSettings(dlabThemeSet6);

  jQuery(window).on("resize", function () {
    /*Check container layout on resize */
    ///alert(dlabSettingsOptions.primary);
    dlabThemeSet6.containerLayout = $("#container_layout").val();
    /*Check container layout on resize END */

    new dlabSettings(dlabThemeSet6);
  });
})(jQuery);
