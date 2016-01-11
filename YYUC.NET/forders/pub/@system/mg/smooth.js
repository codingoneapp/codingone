$(document).ready(function () {
    /* messages fade away when dismiss is clicked */
    $('body').delegate(".message > .dismiss > a","click", function (event) {
        var value = $(this).attr("href");
        var id = value.substring(value.indexOf('#') + 1);

        $("#" + id).fadeOut('slow', function () { });

        return false;
    });
    if(window.parent&&window.parent!=window){
    	parent.childrencolor = $("#color");
    	$("#color").attr("href",$("#color",parent.document).attr("href"));
    }else if($.trim( $.cookie('syscolor'))!=''){
    	$("#color").attr("href", "" + style_path + "/" + $.cookie('syscolor') + ".css");
    }
    /* color picker */
    $("#colors-switcher > a").click(function () {
        $.cookie('syscolor',$(this).attr("class"));
        if(window.childrencolor){
        	window.childrencolor.attr("href", "" + style_path + "/" + $(this).attr("class") + ".css");
        }
        $("#color").attr("href", "" + style_path + "/" + $(this).attr("class") + ".css");
        return false;
    });
    
});
function showWindow(title,url,w,h){
	if(!w)w=600;
	if(!h)h=400;
	$('#login').remove();
	$('body').append('<div id="login" style="width:'+(w)+'px;display:none;"><div class="title" style="width:'+(w)+'px;"><h5>'+title+'</h5><div class="corner tl"></div><div class="corner tr"></div></div><div class="inner" style="padding:0px;width:'+w+'px;height:'+h+'px;"><iframe src="'+url+'" frameborder="0" style="width:'+w+'px;height:'+h+'px;" ></iframe></div></div>');
	pop('login',w+40,h+80,false);
}
$(function(){
	$('img').each(function(){
		if($.trim($(this).attr('src'))==''){
			$(this).attr('src','/upload/provider/bai.gif');
		}		
	});
	
});
