{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav' serviceHash=$gContent->mInfo}
<div class="display suggestion">
	<div class="floaticon">
		{if $print_page ne 'y'}
			{if $gContent->hasUpdatePermission()}
				<a title="{tr}Edit this suggestion{/tr}" href="{$smarty.const.SUGGESTION_PKG_URL}edit.php?suggestion_id={$gContent->mInfo.suggestion_id}">{booticon iname="icon-edit" ipackage="icons" iexplain="Edit Suggestion"}</a>
			{/if}
			{if $gContent->hasExpungePermission()}
				<a title="{tr}Remove this suggestion{/tr}" href="{$smarty.const.SUGGESTION_PKG_URL}remove_suggestion.php?suggestion_id={$gContent->mInfo.suggestion_id}">{booticon iname="icon-trash" ipackage="icons" iexplain="Remove Suggestion"}</a>
			{/if}
		{/if}<!-- end print_page -->
	</div><!-- end .floaticon -->

	<div class="header">
		<h2>Suggestion</h2>
		<h1>{$gContent->mInfo.title|escape|default:"Suggestion"}</h1>
		<div class="date">
			{tr}Created by{/tr}: {displayname user=$gContent->mInfo.creator_user user_id=$gContent->mInfo.creator_user_id real_name=$gContent->mInfo.creator_real_name}<br />
			{tr}Last modification by{/tr}: {displayname user=$gContent->mInfo.modifier_user user_id=$gContent->mInfo.modifier_user_id real_name=$gContent->mInfo.modifier_real_name}, {$gContent->mInfo.last_modified|bit_long_datetime}
		</div>
	</div><!-- end .header -->

	<div class="body">
		<div class="content">
			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='body' serviceHash=$gContent->mInfo}
			{$gContent->mInfo.data}
		</div><!-- end .content -->
		<div class="sources">
			<h3>Sources</h3>
			{$gContent->mInfo.sources}
		</div><!-- end .content -->
	</div><!-- end .body -->
</div><!-- end .suggestion -->
{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='view' serviceHash=$gContent->mInfo}
