<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_suggestion/remove.php,v 1.1 2009/09/15 15:10:49 wjames5 Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: remove.php,v 1.1 2009/09/15 15:10:49 wjames5 Exp $
 * @package suggestion
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );
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
