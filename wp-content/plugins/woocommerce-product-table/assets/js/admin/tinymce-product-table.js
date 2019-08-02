( function( window, document, undefined ) {
	"use strict";

	tinymce.PluginManager.add( 'producttable', function( editor, url ) {
		// Add product table button to visual editor toolbar
		editor.addButton( 'producttable', {
			title: 'Insert Product Table',
			cmd: 'insertProductTable',
			icon: 'dashicon dashicons-editor-table'
		} );

		editor.addCommand( 'insertProductTable', function() {
			editor.execCommand( 'mceInsertContent', false, '[product_table]' );
			return;
		} );

	} );

} )( window, document );