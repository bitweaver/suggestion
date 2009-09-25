<?php
/**
* $Header: /cvsroot/bitweaver/_bit_suggestion/BitSuggestion.php,v 1.2 2009/09/25 18:42:18 wjames5 Exp $
* $Id: BitSuggestion.php,v 1.2 2009/09/25 18:42:18 wjames5 Exp $
*/

/**
* loosely related to package _bit_energymeasures for taking suggestions about such datatypes
*
* date created 2009/9/1
* @author Will James <will@tekimaki.com>
* @version $Revision: 1.2 $ $Date: 2009/09/25 18:42:18 $ $Author: wjames5 $
* @class BitSuggestion
*/

require_once( LIBERTY_PKG_PATH.'LibertyMime.php' );

/**
* This is used to uniquely identify the object
*/
define( 'BITSUGGESTION_CONTENT_TYPE_GUID', 'bitsuggestion' );

class BitSuggestion extends LibertyMime {
	/**
	 * mSuggestionId Primary key for our mythical Suggestion class object & table
	 * 
	 * @var array
	 * @access public
	 */
	var $mSuggestionId;

	/**
	 * BitSuggestion During initialisation, be sure to call our base constructors
	 * 
	 * @param numeric $pSuggestionId 
	 * @param numeric $pContentId 
	 * @access public
	 * @return void
	 */
	function BitSuggestion( $pSuggestionId=NULL, $pContentId=NULL ) {
		LibertyMime::LibertyMime();
		$this->mSuggestionId = $pSuggestionId;
		$this->mContentId = $pContentId;
		$this->mContentTypeGuid = BITSUGGESTION_CONTENT_TYPE_GUID;
		$this->registerContentType( BITSUGGESTION_CONTENT_TYPE_GUID, array(
			'content_type_guid'   => BITSUGGESTION_CONTENT_TYPE_GUID,
			'content_description' => 'Suggestion',
			'handler_class'       => 'BitSuggestion',
			'handler_package'     => 'suggestion',
			'handler_file'        => 'BitSuggestion.php',
			'maintainer_url'      => 'http://www.bitweaver.org'
		));
		// Permission setup
		$this->mViewContentPerm    = 'p_suggestion_view';
		$this->mCreateContentPerm  = 'p_suggestion_create';
		$this->mUpdateContentPerm  = 'p_suggestion_update';
		$this->mAdminContentPerm   = 'p_suggestion_admin';
		$this->mExpungeContentPerm = 'p_suggestion_expunge';
	}

	/**
	 * load Load the data from the database
	 * 
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function load() {
		if( $this->verifyId( $this->mSuggestionId ) || $this->verifyId( $this->mContentId ) ) {
			// LibertyContent::load()assumes you have joined already, and will not execute any sql!
			// This is a significant performance optimization
			$lookupColumn = $this->verifyId( $this->mSuggestionId ) ? 'suggestion_id' : 'content_id';
			$bindVars = array();
			$selectSql = $joinSql = $whereSql = '';
			array_push( $bindVars, $lookupId = @BitBase::verifyId( $this->mSuggestionId ) ? $this->mSuggestionId : $this->mContentId );
			$this->getServicesSql( 'content_load_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$query = "
				SELECT suggestion.*, lc.*,
				uue.`login` AS modifier_user, uue.`real_name` AS modifier_real_name,
				uuc.`login` AS creator_user, uuc.`real_name` AS creator_real_name
				$selectSql
				FROM `".BIT_DB_PREFIX."suggestion_data` suggestion
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = suggestion.`content_id` ) $joinSql
					LEFT JOIN `".BIT_DB_PREFIX."users_users` uue ON( uue.`user_id` = lc.`modifier_user_id` )
					LEFT JOIN `".BIT_DB_PREFIX."users_users` uuc ON( uuc.`user_id` = lc.`user_id` )
				WHERE suggestion.`$lookupColumn`=? $whereSql";
			$result = $this->mDb->query( $query, $bindVars );

			if( $result && $result->numRows() ) {
				$this->mInfo = $result->fields;
				$this->mContentId = $result->fields['content_id'];
				$this->mSuggestionId = $result->fields['suggestion_id'];

				$this->mInfo['creator'] = ( !empty( $result->fields['creator_real_name'] ) ? $result->fields['creator_real_name'] : $result->fields['creator_user'] );
				$this->mInfo['editor'] = ( !empty( $result->fields['modifier_real_name'] ) ? $result->fields['modifier_real_name'] : $result->fields['modifier_user'] );
				$this->mInfo['display_name'] = BitUser::getTitle( $this->mInfo );
				$this->mInfo['display_url'] = $this->getDisplayUrl();
				$this->mInfo['parsed_data'] = $this->parseData();

				LibertyMime::load();
			}
		}
		return( count( $this->mInfo ) );
	}

	function preparePreview( &$pParamHash ){
		$this->verify( $pParamHash );

		if( !empty( $pParamHash['title'] ) ) {
			$this->mInfo['title'] = $pParamHash['title'];
		}
		if( isset( $pParamHash["edit"] ) ) {
			$this->mInfo["data"] = $pParamHash["edit"];
			$this->mInfo['no_cache']    = TRUE;
			$this->mInfo['parsed_data'] = $this->parseData( $this->mInfo['data'], (!empty($this->mInfo['format_guid']) ? $this->mInfo['format_guid'] : 'tikiwiki' ));
		}
	}

	/**
	 * store Any method named Store inherently implies data will be written to the database
	 * @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	 * This is the ONLY method that should be called in order to store( create or update )an suggestion!
	 * It is very smart and will figure out what to do for you. It should be considered a black box.
	 * 
	 * @param array $pParamHash hash of values that will be used to store the page
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function store( &$pParamHash ) {
		if( $this->verify( $pParamHash )&& LibertyMime::store( $pParamHash ) ) {
			$this->mDb->StartTrans();
			$table = BIT_DB_PREFIX."suggestion_data";
			if( $this->mSuggestionId ) {
				$locId = array( "suggestion_id" => $pParamHash['suggestion_id'] );
				$result = $this->mDb->associateUpdate( $table, $pParamHash['suggestion_store'], $locId );
			} else {
				$pParamHash['suggestion_store']['content_id'] = $pParamHash['content_id'];
				if( @$this->verifyId( $pParamHash['suggestion_id'] ) ) {
					// if pParamHash['suggestion_id'] is set, some is requesting a particular suggestion_id. Use with caution!
					$pParamHash['suggestion_store']['suggestion_id'] = $pParamHash['suggestion_id'];
				} else {
					$pParamHash['suggestion_store']['suggestion_id'] = $this->mDb->GenID( 'suggestion_data_id_seq' );
				}
				$this->mSuggestionId = $pParamHash['suggestion_id'] = $pParamHash['suggestion_store']['suggestion_id'];

				if(	$result = $this->mDb->associateInsert( $table, $pParamHash['suggestion_store'] ) ){
					// send an email notification to subscribers
					
					// load up creator user in case user_id was forced and is not the same as gBitUser
					$user = new BitUser( $pParamHash['user_id'] );
					$user->load();
					$userName = $user->getDisplayName();
					$userEmail = $user->getField( 'email' );
					
					// Draft the message body:
					$body = "/----- ".tra('A new suggestion was submitted.')." -----/\n\n"
							."Submitted by: \n"
							.$userName."\n"
							.$userEmail."\n\n"
							."Title: \n"
							.$pParamHash['title']."\n\n"
							."Megawatt Hours / Year: \n"
							.$pParamHash['suggestion_store']['mwh']."\n\n"
							."Description: \n"
							.$pParamHash['edit']."\n\n"
							."Sources: \n"
							.$pParamHash['suggestion_store']['sources'];

					$msgHash = array( 'subject' => tra('New Suggestion').': '.$pParamHash['title'], 'alt_message' => $body );

					global $gSwitchboardSystem;
					// register the sender to be able to access it
					$gSwitchboardSystem->registerSender( SUGGESTION_PKG_TITLE, 'new suggestion' );
					// fire a notification 
					$gSwitchboardSystem->sendEvent( SUGGESTION_PKG_TITLE, 
												   'new suggestion', 
												   $pParamHash['content_id'], 
												   $msgHash 
												);

					// cheating by accessing directly - remove the sender because access should be restricted
					unset( $gSwitchboardSystem->mSenders[SUGGESTION_PKG_TITLE] );
				}
			}

			// $this->storeRefs( $pParamHash );

			$this->mDb->CompleteTrans();
			$this->load();
		} else {
			$this->mErrors['store'] = 'Failed to save this suggestion.';
		}

		return( count( $this->mErrors )== 0 );
	}

	function storeRefs( &$pParamHash ){
		if( $this->verifyRefs( $pParamHash ) && !empty( $pParamHash['store_refs'] ) ){
			$this->mDb->StartTrans();
			$table = BIT_DB_PREFIX."suggestion_ref";
			foreach( $pParamHash['store_refs'] as $ref ){ 
				// if the reference doesnt already exist, store it
				$query = "SELECT ref.* FROM `".BIT_DB_PREFIX."suggestion_ref` ref WHERE ref.`suggestion_id` = ? and ref.`url` = ?";
				if( !$this->mDb->getOne( $query, $ref ) ){
					if( !$this->mDb->associateInsert( $table, $ref ) ){
						$this->mDb->RollbackTrans();
					}
				}
			}
			$this->mDb->CompleteTrans();
		}
	}

	/**
	 * verify Make sure the data is safe to store
	 * @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	 * This function is responsible for data integrity and validation before any operations are performed with the $pParamHash
	 * NOTE: This is a PRIVATE METHOD!!!! do not call outside this class, under penalty of death!
	 * 
	 * @param array $pParamHash reference to hash of values that will be used to store the page, they will be modified where necessary
	 * @access private
	 * @return boolean TRUE on success, FALSE on failure - $this->mErrors will contain reason for failure
	 */
	function verify( &$pParamHash ) {
		// make sure we're all loaded up of we have a mSuggestionId
		if( $this->verifyId( $this->mSuggestionId ) && empty( $this->mInfo ) ) {
			$this->load();
		}

		if( @$this->verifyId( $this->mInfo['content_id'] ) ) {
			$pParamHash['content_id'] = $this->mInfo['content_id'];
		}

		// It is possible a derived class set this to something different
		if( @$this->verifyId( $pParamHash['content_type_guid'] ) ) {
			$pParamHash['content_type_guid'] = $this->mContentTypeGuid;
		}

		if( @$this->verifyId( $pParamHash['content_id'] ) ) {
			$pParamHash['suggestion_store']['content_id'] = $pParamHash['content_id'];
		}

		if( !empty( $pParamHash['data'] ) ) {
			$pParamHash['edit'] = $pParamHash['data'];
		}

		if( !empty( $pParamHash['mwh'] ) ) {
			$pParamHash['suggestion_store']['mwh'] = $pParamHash['mwh'];
			// clean up anything ugly
			$pParamHash['suggestion_store']['mwh'] = intval( str_replace(",", "", $pParamHash['suggestion_store']['mwh'] ) );
			if( $pParamHash['suggestion_store']['mwh'] === 0 ){
				$this->mErrors['mwh'] = tra( 'You must provide a valid Megawatts/Hour value, a whole number.' );
			}
		}

		if( !empty( $pParamHash['sources'] ) ) {
			$pParamHash['suggestion_store']['sources'] = $pParamHash['sources'];
		}


		// If title specified truncate to make sure not too long
		if( !empty( $pParamHash['title'] ) ) {
			if( strlen( $pParamHash['title'] ) > 160 ){
				$this->mErrors['title'] = 'The title is too long. Maximum title length is 160 characters. Your current title is '.strlen( $pParamHash['title'] ).' characters.';
			}else{
				$pParamHash['content_store']['title'] = $pParamHash['title'];
			}
		} else if( empty( $pParamHash['title'] ) ) { // else is error as must have title
			$this->mErrors['title'] = 'You must enter a title.';
		}

		return( count( $this->mErrors )== 0 );
	}

	function verifyRefs( &$pParamHash ){
		if( !empty( $pParamHash['suggestion_id'] ) && !empty( $pParamHash['urls'] ) ) {
			$pParamHash['ref_store'] = array();
			foreach( $pParamHash['urls'] as $url ){
				array_push( $pParamHash['ref_store'], array( 'suggestion_id' => $pParamHash['suggestion_id'], 'url' => $url ) ); 
			}
		}

		return( count( $this->mErrors )== 0 );
	}

	/**
	 * expunge 
	 * 
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure
	 */
	function expunge() {
		global $gBitSystem;
		$ret = FALSE;
		if( $this->isValid() ) {
			$this->mDb->StartTrans();
			$query = "DELETE FROM `".BIT_DB_PREFIX."suggestion_data` WHERE `content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			if( LibertyMime::expunge() ) {
				$ret = TRUE;
			}
			$this->mDb->CompleteTrans();
			// If deleting the default/home suggestion record then unset this.
			if( $ret && $gBitSystem->getConfig( 'suggestion_home_id' ) == $this->mSuggestionId ) {
				$gBitSystem->storeConfig( 'suggestion_home_id', 0, SUGGESTION_PKG_NAME );
			}
		}
		return $ret;
	}

	/**
	 * isValid Make sure suggestion is loaded and valid
	 * 
	 * @access public
	 * @return boolean TRUE on success, FALSE on failure
	 */
	function isValid() {
		return( @BitBase::verifyId( $this->mSuggestionId ) && @BitBase::verifyId( $this->mContentId ));
	}

	/**
	 * getList This function generates a list of records from the liberty_content database for use in a list page
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return array List of suggestion data
	 */
	function getList( &$pParamHash ) {
		// this makes sure parameters used later on are set
		LibertyContent::prepGetList( $pParamHash );

		$selectSql = $joinSql = $whereSql = '';
		$bindVars = array();
		array_push( $bindVars, $this->mContentTypeGuid );
		$this->getServicesSql( 'content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

		// this will set $find, $sort_mode, $max_records and $offset
		extract( $pParamHash );

		if( is_array( $find ) ) {
			// you can use an array of pages
			$whereSql .= " AND lc.`title` IN( ".implode( ',',array_fill( 0,count( $find ),'?' ) )." )";
			$bindVars = array_merge ( $bindVars, $find );
		} elseif( is_string( $find ) ) {
			// or a string
			$whereSql .= " AND UPPER( lc.`title` )like ? ";
			$bindVars[] = '%' . strtoupper( $find ). '%';
		}

		$query = "
			SELECT suggestion.*, lc.* $selectSql
			FROM `".BIT_DB_PREFIX."suggestion_data` suggestion
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = suggestion.`content_id` ) $joinSql
			WHERE lc.`content_type_guid` = ? $whereSql
			ORDER BY ".$this->mDb->convertSortmode( $sort_mode );
		$query_cant = "
			SELECT COUNT(*)
			FROM `".BIT_DB_PREFIX."suggestion_data` suggestion
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = suggestion.`content_id` ) $joinSql
			WHERE lc.`content_type_guid` = ? $whereSql";
		$result = $this->mDb->query( $query, $bindVars, $max_records, $offset );
		$ret = array();
		while( $res = $result->fetchRow() ) {
			$res['display_url'] = $this->getDisplayUrl( null, $res );
			$ret[] = $res;
		}
		$pParamHash["cant"] = $this->mDb->getOne( $query_cant, $bindVars );

		// add all pagination info to pParamHash
		LibertyContent::postGetList( $pParamHash );
		return $ret;
	}

	/**
	 * getDisplayUrl Generates the URL to the suggestion page
	 * 
	 * @access public
	 * @return string URL to the suggestion page
	 */
	function getDisplayUrl( $pContentId = NULL, $pParamHash = NULL ) {
		global $gBitSystem;
		$ret = NULL;
		if( @$this->isValid() ) {
			$suggestionId = $this->mSuggestionId;
		}elseif( !empty($pParamHash['suggestion_id'] ) ) {
			$suggestionId = $pParamHash['suggestion_id'];
		}
		if( !empty( $suggestionId ) ){
			if( $gBitSystem->isFeatureActive( 'pretty_urls' ) || $gBitSystem->isFeatureActive( 'pretty_urls_extended' )) {
				$ret = SUGGESTION_PKG_URL.$suggestionId;
			} else {
				$ret = SUGGESTION_PKG_URL."index.php?suggestion_id=".$suggestionId;
			}
		}
		return $ret;
	}

	function registerUser( &$pParamHash ){
		global $gBitSystem, $gBitSmarty, $gBitUser;
		// check if user already exists
		if( $gBitUser->userExists( array( 'email' => $pParamHash['email'] ) ) ){
			$userData = $gBitUser->getUserInfo( array( 'email' => $pParamHash['email'] ) );	
			$pParamHash['user_id'] = $userData['user_id'];
			$gBitSmarty->assign_by_ref( 'newUser', $userData );
		}else{
			// force some settings
			$usersValidate = $gBitSystem->isFeatureActive( 'users_validate_user' );
			$gBitSystem->setConfig( 'users_validate_user', FALSE );
			// create new user
			$reg = array( 'real_name' => $pParamHash['real_name'], 'email'=>$pParamHash['email'] );
			if( empty( $reg['password'] ) ){
				$reg['password'] = $gBitUser->genPass( 9 );
			}
			$userClass = $gBitSystem->getConfig( 'user_class', 'BitPermUser' );
			$newUser = new $userClass();
			// register, notification is disabled
			if( $newUser->register( $reg, FALSE ) ) {
				$pParamHash['user_id'] = $newUser->mUserId;
				$gBitSmarty->assign_by_ref( 'newUser', $newUser->mInfo );
			}else{
				$this->mErrors = array_merge( $this->mErrors, $newUser->mErrors );
			}
			// restore settings
			$gBitSystem->setConfig( 'users_validate_user', $usersValidate );
		}
		return( count( $this->mErrors )== 0 );
	}

}
