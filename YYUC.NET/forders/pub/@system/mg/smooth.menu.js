/* 
Kriesi (http://themeforest.net/user/Kriesi)
http://www.kriesi.at/archives/create-a-multilevel-dropdown-menu-with-css-and-improve-it-via-jquery 
*/

function quick() {
    $("#quick ul ").css({ display: "none" });
    $("#quick li").hover(function () {
	$(this).find('ul:first').css({visibility: "visible",display: "none"}).show(400);
    }, function () {
        $(this).find('ul:first').css({ visibility: "hidden" });
    });
}

$(document).ready(function () {
 $("#menu h6 a").click(function () {
        var link = $(this);
        var id = link.attr("id");
        var heading = $("#h-menu-" + id);
        var list = $("#menu-" + id);

        if (list.attr("class") == "closed") {
            heading.attr("class", "selected");
            list.attr("class", "opened");
        } else {
            heading.attr("class", "");
            list.attr("class", "closed");
        }
    });
    $("#menu li[class~=collapsible] a.lv2").click(function () {
        var child = $(this);
        if (child.is(".plus")) {
            child.removeClass('plus');
            child.addClass('minus');
        } else {
            child.removeClass('minus');
            child.addClass('plus');
        }

        child.parent().children("ul").each(function () {
            var child = $(this);
            if (child.attr("class") == "collapsed") {
                child.attr("class", "expanded");
            } else {
                child.attr("class", "collapsed");
            }
        });
    });
    quick();
});