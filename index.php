<?php
// $Header: /cvsroot/bitweaver/_bit_suggestion/index.php,v 1.3 2010/02/08 21:37:31 wjames5 Exp $

// Initialization
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'suggestion' );

if( !empty( $_REQUEST['suggestion_id'] ) ){
	// Look up the content
	require_once( SUGGESTION_PKG_PATH.'lookup_suggestion_inc.php' );

	if( !$gContent->isValid() ) {
		$gBitSystem->setHttpStatus( 404 );

		$msg = tra( "The requested suggestion (id=".$_REQUEST['suggestion_id'].") could not be found." );

		$gBitSystem->fatalError( tra( $msg ) ); 
	}else{
		// Now check permissions to access this content 
		$gContent->verifyViewPermission();

		// Add a hit to the counter
		$gContent->addHit();

		// Display the template
		$gBitSystem->display( 'bitpackage:suggestion/display_suggestion.tpl', tra( 'Suggestion' ).' - '.$gContent->getTitle() , array( 'display_mode' => 'display' ));
	}
}else{
	include_once( SUGGESTION_PKG_PATH.'list.php' );
}

