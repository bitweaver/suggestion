<?php
global $gContent;
require_once( SUGGESTION_PKG_PATH.'BitSuggestion.php');
require_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );

// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
if( empty( $gContent ) || !is_object( $gContent ) || !$gContent->isValid() ) {
	// if suggestion_id supplied, use that
	if( @BitBase::verifyId( $_REQUEST['suggestion_id'] ) ) {
		$gContent = new BitSuggestion( $_REQUEST['suggestion_id'] );

	// if content_id supplied, use that
	} elseif( @BitBase::verifyId( $_REQUEST['content_id'] ) ) {
		$gContent = new BitSuggestion( NULL, $_REQUEST['content_id'] );

	} elseif (@BitBase::verifyId( $_REQUEST['suggestion']['suggestion_id'] ) ) {
		$gContent = new BitSuggestion( $_REQUEST['suggestion']['suggestion_id'] );

	// otherwise create new object
	} else {
		$gContent = new BitSuggestion();
	}

	$gContent->load();
	$gBitSmarty->assign_by_ref( "gContent", $gContent );
}
?>
