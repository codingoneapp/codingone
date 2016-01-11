/* sets the class of the tr containing the checked checkbox to selected */
function set_tr_class(element, selected) {
    if (selected) {
    	element.addClass('selected');
    	element.find('.xlsinp').addClass('selected');
    } else {
    	element.removeClass('selected');
    	element.find('.xlsinp').removeClass('selected');
    }
}

$(document).ready(function () {
    /* checks all the checkboxes within a table */
    $('body').delegate("table input[class=checkall]","click", function (event) {
        var checked = $(this).attr("checked");

        $("table input[type=checkbox]").each(function () {
            this.checked = checked;

            if (checked) {
                set_tr_class($(this).parent().parent(), true);
            } else if($(this).parent().parent().find('input:checked').size()==0){
                set_tr_class($(this).parent().parent(), false);
            }
        });
    });

    /* sets the class of the table tr when a checkbox within the table is checked */
    $('body').delegate("table input[type=checkbox]","click", function (event) {
        if ($(this).attr("checked")) {
            set_tr_class($(this).parent().parent(), true);
        } else if($(this).parent().parent().find('input:checked').size()==0){
            set_tr_class($(this).parent().parent(), false);
        }
    });
});