function toggleFGR(isOn, thumbs, flash, toggle){				
	var speed = 800;
	if(isOn){		
		toggle.text("[Disable Flash Gallery]");	
		thumbs.slideUp(speed, function(){flash.slideDown(speed);});
	}else{
		toggle.text("[Enable Flash Gallery]");	
		flash.slideUp(speed, function(){thumbs.slideDown(speed);});				
	}				
};
jQuery(document).ready(function() {							
	jQuery(".fgr-toggle").click(function(event){
		var enabled = (document.cookie.indexOf("fgrhide=") === -1);
		var expiresdays = (enabled) ? 365 : -1;	
		var exdate=new Date(); exdate.setDate(exdate.getDate()+expiresdays);
		document.cookie="fgrhide=1; expires="+exdate.toGMTString()+"; path=/";
		toggleFGR(!enabled, jQuery(".gallery", jQuery("div .fgr_noflash")), jQuery(".fgr"), jQuery(".fgr-toggle"));
		event.preventDefault();	
	});
	toggleFGR((document.cookie.indexOf("fgrhide=") === -1), jQuery(".gallery", jQuery("div .fgr_noflash")), jQuery(".fgr"), jQuery(".fgr-toggle"));
	
});					