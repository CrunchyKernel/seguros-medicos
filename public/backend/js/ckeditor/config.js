/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'SpellChecker', 'Scayt' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' },
		{ name: 'iframe' }
	];
	config.extraPlugins = 'fakeobjects, iframe, dialog, dialogui';
	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript';

	// Se the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Make dialogs simpler.
	config.removeDialogTabs = 'image:advanced;link:advanced';
	
	// Custom Skin
	config.skin='themepixels';
	
	config.dialog_backgroundCoverColor = '#000' ;
	config.dialog_backgroundCoverOpacity = 0.65;
	
	config.filebrowserBrowseUrl = '../../../backend/js/ckfinder/ckfinder.html';
	config.filebrowserImageBrowseUrl = '../../../backend/js/ckfinder/ckfinder.html?type=Images';
	config.filebrowserFlashBrowseUrl = '../../../backend/js/ckfinder/ckfinder.html?type=Flash';
	config.filebrowserUploadUrl = '../../../backend/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
	config.filebrowserImageUploadUrl = '../../../backend/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';
	config.filebrowserFlashUploadUrl = '../../../backend/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';
	
	config.disableNativeSpellChecker = false;
	//config.removePlugins = 'scayt';
	//config.scayt_autoStartup = false;
	//config.scayt_disableOptionsStorage = 'all';
	config.scayt_sLang = 'es_ES';
	config.allowedContent = true;
	config.forcePasteAsPlainText = false;
	config.removeFormatAttributes = false;
	config.pasteFromWordRemoveFontStyles = false;
	config.pasteFromWordRemoveStyles = false;
	config.pasteFilter = null;
	
};
