function toggleFGR(isOn, thumbs, toggle){				
	var speed = 800;
	if(isOn){		
		toggle.text('[Disable Flash Gallery]');	
		toggle.attr('title', 'Having problems with the Flash Gallery? Click here to reload and disable it.');
		thumbs.height('0');
		thumbs.css('visibility','hidden');
	}else{
		toggle.text('[Enable Flash Gallery]');
		toggle.attr('title', 'Want the cool Flash Gallery? Click here to turn it on!');
		thumbs.css('visibility','visible');			
	}				
};
jQuery(document).ready(function() {							
	jQuery(".fgr-toggle").click(function(event){
		var enabled = (document.cookie.indexOf("fgrhide=") === -1);
		var expiresdays = (enabled) ? 365 : -1;	
		var exdate=new Date(); exdate.setDate(exdate.getDate()+expiresdays);
		document.cookie="fgrhide=1; expires="+exdate.toGMTString()+"; path=/";
		toggleFGR(!enabled, jQuery(".gallery", jQuery("div .fgr_noflash")), jQuery(".fgr-toggle"));
		event.preventDefault();
		location.reload();		
	});
	toggleFGR((document.cookie.indexOf("fgrhide=") === -1), jQuery(".gallery", jQuery("div .fgr_noflash")), jQuery(".fgr-toggle"));
});					