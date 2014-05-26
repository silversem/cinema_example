$(function() {
    $('form#get_schedule_form').on("submit", function() {
        var url = '';
        var selectedCinemaId = $("#form_cinemaName option:selected").val();
        var selectedHallId = $("#form_hallName option:selected").val();
        url = '/api/cinema/' + selectedCinemaId + '/schedule/?hall=' + (selectedHallId == '' ? '' : selectedHallId);

        $.getJSON(url)
            .done(function(data){
                var items = [];
                $.each(data, function(key, val){
                    items.push(
                        "<li id='" + key + "'>" +
                            val.seance_time + ' | ' +
                            val.cinema_name + ' | ' +
                            val.hall_name + ' | ' +
                            val.film_name + ' | ' +
                            val.film_seance_id +
                        "</li>"
                    );
                });

                $('div#response').html($( "<ul/>", {
                    html: items.join( "" )
                }));
            })
            .fail(function( jqxhr, textStatus, error ) {
                var err = textStatus + ": " + jqxhr.responseJSON.error;
                $('div#response').html("<span style='color: red;'>Request Failed: " + err + "</span>");
            })
        ;

        return false;
    });


    $("#form_cinemaName").on("change", function() {
        var url = '';
        var selectedCinema = $("#form_cinemaName option:selected").val();
        url = '/api/cinema/' + selectedCinema + '/halls/';

        $.getJSON(url)
            .done(function(data){
                $('#form_hallName option').each(function() {
                    $(this).remove();
                });

                $('#form_hallName').append(
                    $('<option></option>').val('').html('Choose an option')
                );
                $.each(data, function(val, text) {
                    $('#form_hallName').append(
                        $('<option></option>').val(val).html(text)
                    );
                });
            })
            .fail(function( jqxhr, textStatus, error ) {
                var err = textStatus + ": " + jqxhr.responseJSON.error;
                $('div#response').html("<span style='color: red;'>Request Failed: " + err + "</span>");
            })
        ;
    });


    $('form#get_film_form').on("submit", function() {
        var url = '';
        var selectedFilmId = $("#form_filmName option:selected").val();
        url = '/api/film/' + selectedFilmId + '/schedule/';

        $.getJSON(url)
            .done(function(data){
                var items = [];
                $.each(data, function(key, val){
                    items.push(
                        "<li id='" + key + "'>" +
                            val.seance_time + ' | ' +
                            val.cinema_name + ' | ' +
                            val.hall_name + ' | ' +
                            val.film_name + ' | ' +
                            val.film_seance_id +
                            "</li>"
                    );
                });

                $('div#response').html($( "<ul/>", {
                    html: items.join( "" )
                }));
            })
            .fail(function( jqxhr, textStatus, error ) {
                var err = textStatus + ": " + jqxhr.responseJSON.error;
                $('div#response').html("<span style='color: red;'>Request Failed: " + err + "</span>");
            })
        ;

        return false;
    });


    $('form#get_film_seance_form').on("submit", function() {
        var url = '';
        var selectedFilmSeanceId = $("#form_filmSeanceName option:selected").val();
        url = '/api/session/' + selectedFilmSeanceId + '/places/';

        $.getJSON(url)
            .done(function(data){
                var items = [];
                var name = 'get_film_seance_places';
                $.each(data, function(key, val){
                    items.push(
                        '<input type="checkbox" name="' + name + '" id="' + name + '_' + key + '" value="' + val.place_id + '">' +
                        '<label for="' + name + '_' + key + '">' + val.place_number + '</label>'
                    );
                });

                var $form = $('<form id="' + name + '" action="#"><form/>');
                $form.html(items.join( "" ));
                $form.append('<br><input type="submit" name="submit" value="Buy ticket"/>');
                $('div#response').html($form);
            })
            .fail(function( jqxhr, textStatus, error ) {
                var err = textStatus + ": " + jqxhr.responseJSON.error;
                $('div#response').html("<span style='color: red;'>Request Failed: " + err + "</span>");
            })
        ;

        return false;
    });


    $('body').on("submit", 'form#get_film_seance_places', function() {
        var url = '/api/tickets/buy/';
        var selectedPlaceIds = new Array();
        selectedPlaceIds = $('input:checkbox:checked[name=get_film_seance_places]').map(function () {
            return this.value;
        }).get();
        var selectedFilmSeanceId = $("#form_filmSeanceName option:selected").val();
        var len = selectedPlaceIds.length;

        if (len == 0) {
            alert('Select places');
            return false;
        }

        $.post(url,  { session: selectedFilmSeanceId, places: selectedPlaceIds.join(',') })
            .done(function(data){
                refreshTicketsList();
                $('div#response').append('<span>' + data.msg + ' Key: ' + data.key + '</span><br />');
            }, "json")
            .fail(function( jqxhr, textStatus, error ) {
                var err = textStatus + ": " + jqxhr.responseJSON.error;
                $('div#response').html("<span style='color: red;'>Request Failed: " + err + "</span>");
            })
        ;

        $('form#get_film_seance_form > input[type=submit]').trigger('click');

        return false;
    });

    var refreshTicketsList = function(){
        var init_url = '/api/tickets/list/';
        $.getJSON(init_url)
            .done(function(data){
                $('div#orders').html('');
                $('div#orders').append('<h3>Orders:</h3><br>');
                $.each(data, function(key, val){
                    $('div#orders').append('<span>' + ' Key: ' + val + '</span><br />');
                });
            })
            .fail(function( jqxhr, textStatus, error ) {
                var err = textStatus + ": " + jqxhr.responseJSON.error;
                $('div#response').html("<span style='color: red;'>Request Failed: " + err + "</span>");
            })
        ;
    }

    refreshTicketsList();

    $('body').on("submit", 'form#reject_ticket_form', function() {
        var url = '/api/tickets/reject/';
        var key = $("#form_key").val();

        $.post(url,  { key: key })
            .done(function(data){
                refreshTicketsList();
                $('div#response').html('<span>' + data.msg + ' Key: ' + data.key + '</span><br />');
            }, "json")
            .fail(function( jqxhr, textStatus, error ) {
                var err = textStatus + ": " + jqxhr.responseJSON.error;
                $('div#response').html("<span style='color: red;'>Request Failed: " + err + "</span>");
            })
        ;

        return false;
    });

});
