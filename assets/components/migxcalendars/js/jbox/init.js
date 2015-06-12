
$(document).ready(function() {
    
    $('.hasevents').each(function( index ) {
        var id = $( this).attr('id');
        $('#' + id).jBox('Tooltip', {
            getTitle: 'data-title',
            content: $('#' + id + '_smarthbox'),
            closeOnMouseleave: true
        });    
    });    
});