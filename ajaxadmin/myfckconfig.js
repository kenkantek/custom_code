FCKConfig.AutoDetectLanguage = true ;
FCKConfig.ProcessHTMLEntities		= false ;
FCKConfig.IncludeLatinEntities		= false ;
FCKConfig.IncludeGreekEntities		= false ;

FCKConfig.EnterMode = 'br' ; // p | div | br
FCKConfig.ShiftEnterMode = 'p' ; // p | div | br

FCKConfig.LinkBrowser = true ;
FCKConfig.LinkBrowserURL = FCKConfig.BasePath + 'plugins/ajaxfilemanager_eu/ajaxfilemanager.php' ;
FCKConfig.LinkBrowserWindowWidth	= FCKConfig.ScreenWidth * 0.7 ;		// 70%
FCKConfig.LinkBrowserWindowHeight	= FCKConfig.ScreenHeight * 0.7 ;	// 70%

FCKConfig.ImageBrowser = true ;
FCKConfig.ImageBrowserURL = FCKConfig.BasePath + 'plugins/ajaxfilemanager_eu/ajaxfilemanager.php' ;
FCKConfig.ImageBrowserWindowWidth  = FCKConfig.ScreenWidth * 0.7 ;	// 70% ;
FCKConfig.ImageBrowserWindowHeight = FCKConfig.ScreenHeight * 0.7 ;	// 70% ;

FCKConfig.FlashBrowser = true ;
FCKConfig.FlashBrowserURL = FCKConfig.BasePath + 'plugins/ajaxfilemanager_eu/ajaxfilemanager.php' ;
FCKConfig.FlashBrowserWindowWidth  = FCKConfig.ScreenWidth * 0.7 ;	//70% ;
FCKConfig.FlashBrowserWindowHeight = FCKConfig.ScreenHeight * 0.7 ;	//70% ;

FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/SilverNarrow/' ;

FCKConfig.ToolbarSets["Default"] = [
	['Source','DocProps','-','Save','NewPage','Preview','-','Templates'],
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Form','Checkbox','Radio','TextField','Textarea','Select','Button','ImageButton','HiddenField'],
	'/',
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Outdent','Indent','Blockquote','CreateDiv'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Link','Unlink','Anchor'],
	['Image','Flash','Table','Rule','Smiley','SpecialChar','PageBreak'],
	'/',
	['FontSize','TextColor','BGColor'],
	['FitWindow','ShowBlocks','-','About']		// No comma for the last row.
] ;