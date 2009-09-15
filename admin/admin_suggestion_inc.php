<?php
// $Header: /cvsroot/bitweaver/_bit_suggestion/admin/admin_suggestion_inc.php,v 1.1 2009/09/15 15:10:49 wjames5 Exp $

require_once( SUGGESTION_PKG_PATH.'BitSuggestion.php' );

$formSuggestionLists = array(
	"suggestion_list_suggestion_id" => array(
		'label' => 'Id',
		'note' => 'Display the suggestion id.',
	),
	"suggestion_list_publish_status" => array(
		'label' => 'Status',
		'note' => 'Display the published status of the content.',
	),
	"suggestion_list_title" => array(
		'label' => 'Title',
		'note' => 'Display the title.',
	),
	"suggestion_list_data" => array(
		'label' => 'Text',
		'note' => 'Display the text.',
	),
);
$gBitSmarty->assign( 'formSuggestionLists', $formSuggestionLists );

// Process the form if we've made some changes
if( !empty( $_REQUEST['suggestion_settings'] )) {
	$suggestionToggles = array_merge( $formSuggestionLists );
	foreach( $suggestionToggles as $item => $data ) {
		simple_set_toggle( $item, SUGGESTION_PKG_NAME );
	}
}

