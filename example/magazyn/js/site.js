$(document).ready(function() {
    jbUser( function( user ) {
        if ( user.id == -1 ) {
            return;
        }
        // Add
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
            if ( url.indexOf("/") !== 0 ) {
                url = '/' + url;
            }
            window.location = url;
        } );
        // Edit
        var url = window.location.protocol + '//' + window.location.host;
        if ( window.location.pathname == '/' ) {
            url += '/index.html';
        } else {
            url += window.location.pathname;
        }
        if ( url.indexOf(".dhtml") == - 1 ) {
            jbManageBar( jbLang( "Edit" ), url.replace(/\.html($|\?)/, ".dhtml$1?edit=1") );
        }
        // Special pages
        if ( url.indexOf("_special.html") == -1 ) {
            jbManageBar( jbLang( "Special pages" ), "/_special.html" );
        }
        // Upload
        jbManageBar( jbLang( "Upload" ), (function () {
            var iframe;
            var dialog;
            return function() {
                if ( typeof( iframe ) == "undefined" ) {
                    iframe = $( "<iframe />" ).attr( "src", "http://kopalnia.heroes.net.pl/upload?format=light" );
                    dialog = $( "<div />" )
                        .attr( "id", "upload-form" )
                        .append( iframe )
                        .appendTo( "body" )
                        .dialog({
                            autoOpen: false,
                            modal: true,
                            title: jbLang( "Upload" ),
                            height: 530,
                            width: 500,
                            beforeClose: function( event, ui ) {
                                window.location.reload();
                            }
                        });
                }
                dialog.dialog( "open" );
                return false;
            };
        })() );
    } );
});
