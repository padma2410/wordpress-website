( function( api, $ ) {
    api.bind( 'ready', function() {
        //api.section.each( function ( section ) { console.log(section.contentContainer); } );

        $( ".aux-customizer-section-preview-link" ).on( "click", function( event ){
            event.preventDefault();
            api.previewer.previewUrl.set( $( event.currentTarget ).prop("href") );
        });

        // @if DEV
        /*
        api.control.each( function( control ) {
            if ( 'dropdown' === control.params.type ) {
            }
        } );
        */
        // @endif

    });
} )( wp.customize, jQuery );

