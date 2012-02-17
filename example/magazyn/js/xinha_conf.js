var _editor_url     = "/xinha/";
var _editor_lang    = "pl";

$(document).ready(function() {
	var xinha_editors = null;
	var xinha_init    = null;
	var xinha_config  = null;
	var xinha_plugins = null;

	xinha_init = function()
	{
		xinha_plugins = xinha_plugins ? xinha_plugins :
		[
			'CharacterMap',
			'SuperClean',
			'ExtendedFileManager',
            "ContextMenu",
            "SmartReplace",
            "Stylist",
            "Linker",
            "TableOperations",
            "DefinitionList",
            "InsertSnippet2"
		];
        if(!Xinha.loadPlugins(xinha_plugins, xinha_init)) {
            return;
        }
		var tmpEditors	= [];
		$("textarea").each(function() {
			if(this.className!='no-xinha') {
				tmpEditors.push(this.id);
			}
		});
		xinha_editors =  tmpEditors;
        xinha_config = new Xinha.Config();
        xinha_config.InsertSnippet2.snippets = "/snippets.xml";
		xinha_config.toolbar =
        [
            ["popupeditor", "htmlmode"],
            ["separator","formatblock","bold","italic","underline","strikethrough"],
            ["separator","subscript","superscript"],
            ["separator","justifyleft","justifycenter","justifyright","justifyfull"],
            ["separator","insertorderedlist","insertunorderedlist"],
            ["separator","inserthorizontalrule","createlink","insertimage","inserttable"],
            ["separator","undo","redo","selectall"],
            (Xinha.is_gecko?[]:["cut","copy","paste","overwrite","saveas"]),
            ["separator","killword","clearfonts","removeformat","toggleborders","splitblock"]
        ];
        
        /*
		[
			["popupeditor","separator"],
			["formatblock","bold","italic","underline","strikethrough","forecolor"],
		//	["separator","forecolor","hilitecolor"],
			["separator","subscript","superscript","insertunorderedlist"],
			["linebreak","separator","justifyleft","justifycenter","justifyright","justifyfull"],
			["separator","insertorderedlist","insertunorderedlist","outdent","indent"],
			["separator","inserthorizontalrule","createlink","insertimage","inserttable"],
			["linebreak","separator","undo","redo","selectall","print"],
		//	(Xinha.is_gecko?[]:["cut","copy","paste","overwrite","saveas"]),
			["separator","killword","clearfonts","removeformat","htmlmode","separator","about"],
		//	["separator","htmlmode","showhelp","about"]
		];*/
		xinha_config.formatblock =
		{
			"&mdash; format &mdash;"  : "",
			"Heading 2": "h2",
			"Heading 3": "h3",
			"Heading 4": "h4",
            "Normal"   : "p",
            "Code"     : "pre",
            "Address"  : "address"
        };
        xinha_config.Linker.backend = '/scan.php';
		xinha_config.showLoading = false;
		xinha_config.statusBar = false;
        xinha_config.flowToolbars = false;
        xinha_config.mozParaHandler = 'best';
//        xinha_config.height = '800px';
//        xinha_config.width = '100%';
        xinha_config.pageStyleSheets = [
            "http://magazyn.jaskiniabehemota.net/common/css/reset.css",
            "http://magazyn.jaskiniabehemota.net/blx/css/editor.css",
            "http://magazyn.jaskiniabehemota.net/blx/css/content.css",
            "http://magazyn.jaskiniabehemota.net/" + siteRealm + "/css/layout.css"
        ];
        xinha_config.baseURL = document.location.protocol + "//" + document.location.host;
        xinha_config.getHtmlMethod = "TransformInnerHTML";
        xinha_config.htmlRemoveTags = /span|font/;
        xinha_config.autofocus = "content";

        // styilist
        xinha_config.stylistLoadStyles('ul.xinha-horizontal-menu { zoom: 1 }', {'ul.xinha-horizontal-menu' : 'Horizontal menu'});
        xinha_config.stylistLoadStyles('p.xinha-dark-box {}', {'p.xinha-dark-box' : 'Dark box'});
        xinha_config.stylistLoadStyles('a.with-image {}', {'a.with-image' : 'Anchor with image'});
        xinha_config.stylistLoadStyles('table.with-border {}', {'table.with-border' : 'Table with border'});
        xinha_config.stylistLoadStyles('table.with-full-border {}', {'table.with-full-border' : 'Table with full border'});
        xinha_config.stylistLoadStyles('ul.xinha-left-menu {}', {'ul.xinha-left-menu' : 'Menu on the left'});

		xinha_editors   = Xinha.makeEditors(xinha_editors, xinha_config, xinha_plugins);
		Xinha.startEditors(xinha_editors);
    };
	Xinha._addEvent(window,'load', xinha_init); // this executes the xinha_init function on page load
                                                // and does not interfere with window.onload properties set by other scripts
});
