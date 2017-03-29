/**
 * Created by VILDERR on 18.03.17.
 */

var running = false;

function DoNext(NS) {
    var interval = parseInt(document.getElementById('interval').value);
    var query = '?IMPORT=Y'
        + '&INTERVAL=' + interval;

    if (!NS) {
        query += '&URL_DATA_FILE=' + document.getElementById('url_data_file').value;
        query += '&REFERENCE_ID=' + document.getElementById('reference_id_select_id').value;
        query += '&ACTION=' + document.getElementById('elementimport-action').value;
    }

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