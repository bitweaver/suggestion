<?php
global $gBitSystem;

$registerHash = array(
	'package_name' => 'suggestion',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => TRUE,
);
$gBitSystem->registerPackage( $registerHash );

// If package is active and the user has view auth then register the package menu
if( $gBitSystem->isPackageActive( 'suggestion' ) && $gBitUser->hasPermission( 'p_suggestion_view' ) ) {
	$menuHash = array(
		'package_name'  => SUGGESTION_PKG_NAME,
		'index_url'     => SUGGESTION_PKG_URL.'index.php',
		'menu_template' => 'bitpackage:suggestion/menu_suggestion.tpl',
	);
	$gBitSystem->registerAppMenu( $menuHash );

	$gLibertySystem->registerService( LIBERTY_SERVICE_SUGGESTION, SUGGESTION_PKG_NAME, array(
		'content_store_function'		=> 'suggestion_content_store',
		// 'content_verify_function'		=> 'group_content_verify',
	) );

	/**
	 * load up switchboard.
	 * we need to include bit_setup_inc incase groups gets loaded first.
	 * this is a dirty hack since we don't have a way to set the load order of bit_setup_inc.php files yet.
	 * TODO: have a load order for bit_setup_inc.php files and remove this terrible hack
	 */
	if( is_file( BIT_ROOT_PATH.'switchboard/bit_setup_inc.php' )) {
		require_once( BIT_ROOT_PATH.'switchboard/bit_setup_inc.php' );
		if( $gBitSystem->isPackageActive( 'switchboard' ) && $gBitUser->hasPermission('p_suggest_new_notify') ) {
			global $gSwitchboardSystem;
			$gSwitchboardSystem->registerSender( SUGGESTION_PKG_TITLE, 'new suggestion' );
		}
	}
}
