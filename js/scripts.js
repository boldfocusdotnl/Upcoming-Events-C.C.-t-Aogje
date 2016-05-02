(function( $ ) {
    
    //Initializing jQuery UI Datepicker
    $( '#uep-event-start-date' ).datepicker({
        dateFormat: 'MM dd, yy',
        onClose: function( selectedDate ){
            $( '#uep-event-end-date' ).datepicker( 'option', 'minDate', selectedDate );
        }
    });
    $( '#uep-event-end-date' ).datepicker({
        dateFormat: 'MM dd, yy',
        onClose: function( selectedDate ){
            $( '#uep-event-start-date' ).datepicker( 'option', 'maxDate', selectedDate );
        }
    });
    // time picker setup
     $('#uep-event-start-time').timepicker({ 
                      'timeFormat': 'H:i',
                      'scrollDefault': 'now',
                      'step': 15
                    });
    $('#uep-event-end-time').timepicker({ 
                      'timeFormat': 'H:i',
                      'scrollDefault': 'now',
                      'step': 15
                    });


})( jQuery );
