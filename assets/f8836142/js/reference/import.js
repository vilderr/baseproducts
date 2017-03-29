/**
 * Created by VILDERR on 18.03.17.
 */

var running = false;

function DoNext(NS) {
    var query = '?type=' + document.getElementById('reference_type_id').value
        + '&reference_id=' + document.getElementById('reference_id').value;

    if(running)
    {
        var request = $.ajax({
            type: "POST",
            url: query,
            data: NS
        });

        request.done(function (result) {
            $('#import_result_div').html(result);
        });
    }

}

function StartImport() {
    running = document.getElementById('start_button').disabled = true;
    DoNext();
}

function EndImport()
{
    running = document.getElementById('start_button').disabled = false;
}