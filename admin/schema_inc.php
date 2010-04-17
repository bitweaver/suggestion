<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_suggestion/admin/schema_inc.php,v 1.2 2010/04/17 22:46:10 wjames5 Exp $
 * @package suggestion
 */
$tables = array(
	'suggestion_data' => "
		suggestion_id I4 PRIMARY,
		content_id I4 NOTNULL,
		mwh I4, 
		sources XL
		CONSTRAINT '
				, CONSTRAINT `suggestion_content_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content` (`content_id`)'
	",
	/*
	'suggestion_ref' => "
		suggestion_id I4 NOTNULL,
		url C(160) NOTNULL
	"
	*/
);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( SUGGESTION_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( SUGGESTION_PKG_NAME, array(
	'description' => "Suggestion package to demonstrate how to build a bitweaver package.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
));

// $indices = array();
// $gBitInstaller->registerSchemaIndexes( ARTICLES_PKG_NAME, $indices );

// Sequences
$gBitInstaller->registerSchemaSequences( SUGGESTION_PKG_NAME, array (
	'suggestion_data_id_seq' => array( 'start' => 1 )
));

/* // Schema defaults
$gBitInstaller->registerSchemaDefault( SUGGESTION_PKG_NAME, array(
	"INSERT INTO `".BIT_DB_PREFIX."bit_suggestion_types` (`type`) VALUES ('Suggestion')",
)); */

// User Permissions
$gBitInstaller->registerUserPermissions( SUGGESTION_PKG_NAME, array(
	array ( 'p_suggestion_admin'  , 'Can admin suggestion'           , 'admin'      , SUGGESTION_PKG_NAME ),
	array ( 'p_suggestion_update' , 'Can update any suggestion entry', 'editors'    , SUGGESTION_PKG_NAME ),
	array ( 'p_suggestion_create' , 'Can create a suggestion entry'  , 'registered' , SUGGESTION_PKG_NAME ),
	array ( 'p_suggestion_view'   , 'Can view suggestion data'       , 'basic'      , SUGGESTION_PKG_NAME ),
	array ( 'p_suggestion_expunge', 'Can delete any suggestion entry', 'admin'      , SUGGESTION_PKG_NAME ),
	array ( 'p_suggestion_edit_status', 'Can set the publish status of a suggestion', 'admin', SUGGESTION_PKG_NAME ),
));

// Default Preferences
$gBitInstaller->registerPreferences( SUGGESTION_PKG_NAME, array(
	array ( SUGGESTION_PKG_NAME , 'suggestion_default_ordering' , 'suggestion_id_desc' ),
	array ( SUGGESTION_PKG_NAME , 'suggestion_list_suggestion_id'   , 'y'              ),
	array ( SUGGESTION_PKG_NAME , 'suggestion_list_title'       , 'y'              ),
	array ( SUGGESTION_PKG_NAME , 'suggestion_list_description' , 'y'              ),
	array ( SUGGESTION_PKG_NAME , 'suggestion_home_id'          , 0                ),
));

// Version - now use upgrades dir to set package version number.
// $gBitInstaller->registerPackageVersion( SUGGESTION_PKG_NAME, '0.5.1' );

// Requirements
$gBitInstaller->registerRequirements( SUGGESTION_PKG_NAME, array(
	'liberty' => array( 'min' => '2.1.4' ),
));
?>
