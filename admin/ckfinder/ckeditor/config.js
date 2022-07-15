/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	//config.language = 'ru';
	config.uiColor = '#8eaebe';
	config.extraPlugins = 'font,spoiler,wordcount,notification,textselection'; 
	
	//config.scayt_autoStartup = false;
	//config.disableNativeSpellChecker = false;
	//config.removePlugins = 'scayt,contextmenu';

	config.toolbarGroups = [
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard', groups: ['clipboard', 'undo' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		{ name: 'paragraph', groups: [ 'indent', 'align', 'list', 'blocks', 'bidi', 'paragraph' ] },
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'about', groups: [ 'about' ] }
	];

	config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,Replace,Find,SelectAll,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CopyFormatting,Outdent,Indent,CreateDiv,BidiLtr,BidiRtl,Language,Unlink,Flash,Smiley,PageBreak,Iframe,ShowBlocks,About,Scayt';

	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	config.protectedSource.push(/<\?[\s\S]*?\?>/g);// разрешить php-код
	
	//config.protectedSource.push(/<!--dev-->[\s\S]*<!--\/dev-->/g);
	config.allowedContent = true; /* all tags */
	config.protectedSource.push( /<i class[\s\S]*?\>/g );
	config.protectedSource.push( /<\/i>/g );
	
	config.toolbar_Basic2 = [
		[ 'Maximize', 'Source', '-', 'Styles', 'Bold', 'Italic', 'Underline', 
		'Strike', 'RemoveFormat', 'NumberedList', 
		'BulletedList', 'Blockquote', 'Link', 'JustifyLeft', 
		'JustifyCenter','JustifyRight',{ name: 'oembed' }]
	];
	
	config.toolbar_Title = [
		[ 'Maximize', 'Source', 'Format', 'RemoveFormat', 'Link', 'JustifyLeft', 
		'JustifyCenter','JustifyRight']
	];
	
	config.wordcount = {
		// Whether or not you want to show the Paragraphs Count
		showParagraphs: true,
		// Whether or not you want to show the Word Count
		showWordCount: true,
		// Whether or not you want to show the Char Count
		showCharCount: true,
		// Whether or not you want to count Spaces as Chars
		countSpacesAsChars: false,
		// Whether or not to include Html chars in the Char Count
		countHTML: false,
		// Maximum allowed Word Count, -1 is default for unlimited
		maxWordCount: -1,
		// Maximum allowed Char Count, -1 is default for unlimited
		maxCharCount: -1,
		// Add filter to add or remove element before counting (see CKEDITOR.htmlParser.filter), Default value : null (no filter)
		filter: new CKEDITOR.htmlParser.filter({
			elements: {
				div: function( element ) {
					if(element.attributes.class == 'mediaembed') {
						return false;
					}
				}
			}
		})
	};
	

};