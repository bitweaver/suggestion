<?php
// $Header: /cvsroot/bitweaver/_bit_suggestion/edit.php,v 1.3 2010/02/08 21:37:31 wjames5 Exp $

// Initialization
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'suggestion' );

require_once( SUGGESTION_PKG_PATH.'lookup_suggestion_inc.php' );

// Now check permissions to access this page
if( $gContent->isValid() ){
	$gContent->verifyUpdatePermission();
}else{
	$gContent->verifyCreatePermission();
}

// Display settings
$displayParams = array();

// Convenience for ajax request checks
if( $ajax = BitThemes::isAjaxRequest() ){
	if( !empty( $_REQUEST["preview"] ) || !empty( $_REQUEST["save_suggestion"] ) ){
		// Remove encoding
		$gContent->decodeAjaxRequest( $_REQUEST );
	}
	// Just return HTML
	$displayParams['format'] = 'center_only';
}

// Preview
if( !empty( $_REQUEST["preview"] ) ) {
	$gContent->preparePreview( $_REQUEST );
	$gBitSmarty->assign( 'preview', TRUE );
	$gContent->invokeServices( 'content_preview_function' );
// Store 
}elseif( !empty( $_REQUEST["save_suggestion"] ) ) {
	// force hide suggestions unless person has permission to auto publish
	if( !$gContent->hasUserPermission( 'p_suggestion_edit_status' ) || !$gBitUser->isAdmin() ) {
		$_REQUEST['content_status_id'] = -1;
	}
	// auto register user
	if( $gBitUser->isRegistered() ){ 
		$_REQUEST['user_id'] = $gBitUser->mUserId;
	}else{
		if( $gContent->registerUser( $_REQUEST ) ){
			$_REQUEST['modifier_user_id'] = $_REQUEST['user_id'];
		}
	}
	// store
	if( !empty( $_REQUEST['user_id'] ) && $gContent->store( $_REQUEST ) ) {
		$displayParams['display_mode'] = 'display';
		// if ajax request
		if( $ajax ){
			// display Thank You
			$gBitSystem->display( 'bitpackage:suggestion/display_thankyou.tpl', tra('Suggestion') , $displayParams );
		}else{
			// redirect to View
			header( "Location: ".$gContent->getDisplayUrl() );
		}
		die;
	}
// Edit
} else {
	$gContent->invokeServices( 'content_edit_function' );
}

// Make any errors available to the tpls
$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );

// Display the template
$displayParams['display_mode'] = 'edit';
$gBitSystem->display( 'bitpackage:suggestion/edit_suggestion.tpl', tra('Suggestion') , $displayParams );
