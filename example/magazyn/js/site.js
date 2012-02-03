$(document).ready(function() {
    jbUser( function() {
        jbManageBar( jbLang( "Add" ), function() {
            var url = window.prompt( jbLang( "Type new page url" ) );
            if ( !url ) {
                return;
            }
            if ( url.indexOf(".html") == - 1 && url.indexOf(".dhtml") == -1 ) {
                url += '.dhtml?edit=1';
            } else if ( url.indexOf(".dhtml") == - 1 ) {
                url = url.replace(/\.html($|\?)/, ".dhtml$1?edit=1");
            } else {
                url += '?edit=1';
            }
            window.location = url;
        } );
        var url = window.location.protocol + '//' + window.location.host;
        if ( window.location.pathname == '/' ) {
            url += '/index.html';
        } else {
            url += window.location.pathname;
        }
        if ( url.indexOf(".dhtml") == - 1 ) {
            jbManageBar( jbLang( "Edit" ), url.replace(/\.html($|\?)/, ".dhtml$1?edit=1") );
        }
    } );
});
