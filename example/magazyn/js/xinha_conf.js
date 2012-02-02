var _editor_url     = "/mib/blx/example/xinha/";
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
            //"Linker",
            "TableOperations",
            "CSSPicker"
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
		xinha_config.toolbar =
        [
            ["popupeditor"],
            ["separator","formatblock",/*"fontname",*/"fontsize","bold","italic","underline","strikethrough"],
            //["separator","forecolor","hilitecolor","textindicator"],
            ["separator","subscript","superscript"],
            
            ["linebreak","separator","justifyleft","justifycenter","justifyright","justifyfull"],
            ["separator","insertorderedlist","insertunorderedlist","outdent","indent"],
            ["separator","inserthorizontalrule","createlink","insertimage","inserttable"],
            
            ["linebreak","separator","undo","redo","selectall","print"],
            (Xinha.is_gecko?[]:["cut","copy","paste","overwrite","saveas"]),
            ["separator","killword","clearfonts","removeformat","toggleborders","splitblock","lefttoright","righttoleft"],
            ["separator","htmlmode","showhelp","about"]
        ];

        CSSPicker.cssList = {
            "xinha-dark-box": { 'wrapper': 'div', 'name': 'Dark background' }
        };

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

		xinha_config.showLoading = false;
		xinha_config.statusBar = false;
        xinha_config.stripBaseHref = true;
        xinha_config.pageStyleSheets = [
            "http://magazyn.jaskiniabehemota.net/common/css/reset.css",
            "http://magazyn.jaskiniabehemota.net/blx/css/editor.css",
            "http://magazyn.jaskiniabehemota.net/blx/css/content.css"
        ];

		xinha_editors   = Xinha.makeEditors(xinha_editors, xinha_config, xinha_plugins);
		$("textarea").each(function() {
			if(this.className!='no-xinha') {
//                xinha_editors[this.id].config.height = '800px';
//                xinha_editors[this.id].config.width = '100%';
			}
		});
		Xinha.startEditors(xinha_editors);
    };
	Xinha._addEvent(window,'load', xinha_init); // this executes the xinha_init function on page load
                                                // and does not interfere with window.onload properties set by other scripts
});
