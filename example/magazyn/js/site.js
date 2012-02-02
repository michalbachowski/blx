$(document).ready(function() {
    jbUser( function() {
        jbManageBar( jbLang( "Add" ), function() {
            var url = window.prompt( jbLang( "Type new page url" ) );
            if ( url ) {
                window.location = url;
            }
        } );
        if ( window.location.href.indexOf(".dhtml") == - 1 ) {
            jbManageBar( jbLang( "Edit" ), window.location.href.replace(/\.html($|\?)/, ".dhtml$1") );
        }
    } );
});
