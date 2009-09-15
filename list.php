<?php
// $Header: /cvsroot/bitweaver/_bit_suggestion/list.php,v 1.1 2009/09/15 15:10:49 wjames5 Exp $
// Copyright (c) 2004 bitweaver Suggestion
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// Initialization
require_once( '../bit_setup_inc.php' );
require_once( SUGGESTION_PKG_PATH.'BitSuggestion.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'suggestion' );

// Look up the content
require_once( SUGGESTION_PKG_PATH.'lookup_suggestion_inc.php' );

// Now check permissions to access this page
$gContent->verifyViewPermission();

// Remove suggestion data if we don't want them anymore
if( isset( $_REQUEST["submit_mult"] ) && isset( $_REQUEST["checked"] ) && $_REQUEST["submit_mult"] == "remove_suggestion_data" ) {

	// Now check permissions to remove the selected suggestion data
	$gBitSystem->verifyPermission( 'p_suggestion_update' );

	if( !empty( $_REQUEST['cancel'] ) ) {
		// user cancelled - just continue on, doing nothing
	} elseif( empty( $_REQUEST['confirm'] ) ) {
		$formHash['delete'] = TRUE;
		$formHash['submit_mult'] = 'remove_suggestion_data';
		foreach( $_REQUEST["checked"] as $del ) {
			$tmpPage = new BitSuggestion( $del);
			if ( $tmpPage->load() && !empty( $tmpPage->mInfo['title'] )) {
				$info = $tmpPage->mInfo['title'];
			} else {
				$info = $del;
			}
			$formHash['input'][] = '<input type="hidden" name="checked[]" value="'.$del.'"/>'.$info;
		}
		$gBitSystem->confirmDialog( $formHash, 
			array(
				'warning' => tra('Are you sure you want to delete ').count( $_REQUEST["checked"] ).' suggestion records?',
				'error' => tra('This cannot be undone!')
			)
		);
	} else {
		foreach( $_REQUEST["checked"] as $deleteId ) {
			$tmpPage = new BitSuggestion( $deleteId );
			if( !$tmpPage->load() || !$tmpPage->expunge() ) {
				array_merge( $errors, array_values( $tmpPage->mErrors ) );
			}
		}
		if( !empty( $errors ) ) {
			$gBitSmarty->assign_by_ref( 'errors', $errors );
		}
	}
}

// Create new suggestion object
$suggestion = new BitSuggestion();
$suggestionList = $suggestion->getList( $_REQUEST );
$gBitSmarty->assign_by_ref( 'suggestionList', $suggestionList );

// getList() has now placed all the pagination information in $_REQUEST['listInfo']
$gBitSmarty->assign_by_ref( 'listInfo', $_REQUEST['listInfo'] );

// Display the template
$gBitSystem->display( 'bitpackage:suggestion/list_suggestion.tpl', tra( 'Suggestion' ) , array( 'display_mode' => 'list' ));

?>
