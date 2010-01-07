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