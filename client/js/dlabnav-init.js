
"use strict"

var dlabThemeSet1 = {};

function getUrlParams(dParam) 
	{
		var dPageURL = window.location.search.substring(1),
			dURLVariables = dPageURL.split('&'),
			dParameterName,
			i;

		for (i = 0; i < dURLVariables.length; i++) {
			dParameterName = dURLVariables[i].split('=');

			if (dParameterName[0] === dParam) {
				return dParameterName[1] === undefined ? true : decodeURIComponent(dParameterName[1]);
			}
		}
	}

(function($) {
	
	"use strict"
	
	/* var direction =  getUrlParams('dir');
	
	if(direction == 'rtl')
	{
        direction = 'rtl'; 
    }else{
        direction = 'ltr'; 
    } */
	
	
 dlabThemeSet1 = {
			typography: "Nunito",
			version: "light",
			layout: "vertical",
			primary: "color_3",
			headerBg: "color_1",
			navheaderBg: "color_12",
			sidebarBg: "color_12",
			sidebarStyle: "full",
			sidebarPosition: "fixed",
			headerPosition: "fixed",
			containerLayout: "full",
		};

	
	
	
	new dlabSettings(dlabThemeSet1); 

	jQuery(window).on('resize',function(){
        /*Check container layout on resize */
		///alert(dlabSettingsOptions.primary);
        dlabThemeSet1.containerLayout = $('#container_layout').val();
        /*Check container layout on resize END */
        
		new dlabSettings(dlabThemeSet1); 
	});
	
})(jQuery);