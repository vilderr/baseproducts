/**
 * Created by VILDERR on 09.03.17.
 */

$(document).ready(function () {
    $("li.drop-down i").click(function () {

        var parent = $(this).parent();

        parent.toggleClass('active');

        if(parent.hasClass('active'))
        {
            $(this).removeClass('fa-angle-down');
            $(this).addClass('fa-angle-up');
        }
        else
        {
            $(this).removeClass('fa-angle-up');
            $(this).addClass('fa-angle-down');
        }
    })
});
