var $ = jQuery;

function LoadPropriedades( $pid ) {
    if ( $('.propriedade-'+$pid).length > 0 ) {
        $('.propriedades-city-wrapper').addClass('active').children('.propriedade-'+$pid).show();
        
        return;    
    }
    
    $.ajax({
        dataType: 'json',
        method: 'POST',
        url: Grupoi9Ajax.ajaxurl,
        data: {
            action: 'load_propriedades',
            pid: $pid
        },
        beforeSend: function() {
            
        },
        success: function( data ) {
            if ( data.type != 'empty' ) {
                $('.propriedades-city-wrapper').append(data.content).addClass('active');
            
                $('.close-content').click(function() {
                    $('.propriedades-city-wrapper').removeClass('active').children('.propriedade-wrapper').hide();
                });
            }
        }
    });
}