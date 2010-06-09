<?php
/**
 * $Header$
 */

/**
 * required setup
 */
require_once( '../kernel/setup_inc.php' );
include_once( SUGGESTION_PKG_PATH.'BitSuggestion.php');
include_once( SUGGESTION_PKG_PATH.'lookup_suggestion_inc.php' );

$gBitSystem->verifyPackage( 'suggestion' );

if( !$gContent->isValid() ) {
	$gBitSystem->fatalError( "No suggestion indicated" );
}

$gContent->verifyExpungePermission();

if( isset( $_REQUEST["confirm"] ) ) {
	if( $gContent->expunge()  ) {
		header ("location: ".BIT_ROOT_URL );
		die;
	} else {
		vd( $gContent->mErrors );
	}
}

$gBitSystem->setBrowserTitle( tra( 'Confirm delete of: ' ).$gContent->getTitle() );
$formHash['remove'] = TRUE;
$formHash['suggestion_id'] = $_REQUEST['suggestion_id'];
$msgHash = array(
	'label' => tra( 'Delete Suggestion' ),
	'confirm_item' => $gContent->getTitle(),
	'warning' => tra( 'This suggestion will be completely deleted.<br />This cannot be undone!' ),
);
$gBitSystem->confirmDialog( $formHash,$msgHash );

?>
