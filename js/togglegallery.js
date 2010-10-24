function toggleFGR(isOn, thumbs, toggle){
	try{
	var speed = 800;
	if(isOn){		
		toggle.text('[Disable Flash Gallery]');	
		toggle.attr('title', 'Having problems with the Flash Gallery? Click here to disable it.');		
		thumbs.slideUp(speed, function(){
			jQuery(".fgr_container").each( 
			function(){	
				var n = jQuery('.fgr', this);
				var id = jQuery(this).attr("id").substr(10); //"container_".length
				if (n.attr('id') != id) {
					n = jQuery('<span class="fgr" id="'+id+'"></span>');
					jQuery(this).append(n);
				}				
				eval("load"+id)();
			}
		);			
		});	
	}else{
		toggle.text('[Enable Flash Gallery]');
		toggle.attr('title', 'Want the cool Flash Gallery? Click here to turn it on!');
		jQuery(".fgr").each( 
			function(){
				eval("unload"+jQuery(this).attr("id"))();
			}
		);	
		thumbs.slideDown(speed);			
	}
	}catch(e){};	
};
jQuery(document).ready(function() {
	try{
	jQuery(".fgr-toggle").click(function(event){
		var enabled = (document.cookie.indexOf("fgrhide=") === -1);
		var expiresdays = (enabled) ? 365 : -1;	
		var exdate=new Date(); exdate.setDate(exdate.getDate()+expiresdays);
		document.cookie="fgrhide=1; expires="+exdate.toGMTString()+"; path=/";
		toggleFGR(!enabled, jQuery(".fgr_noflash"), jQuery(".fgr-toggle"));
		event.preventDefault();		
	});
	toggleFGR((document.cookie.indexOf("fgrhide=") === -1), jQuery(".fgr_noflash"), jQuery(".fgr-toggle"));
	}catch(e){};
});					