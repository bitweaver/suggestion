{* $Header$ *}
{strip}
<div class="floaticon">{bithelp}</div>

<div class="listing suggestion">
	<div class="header">
		<h1>{tr}Suggestion Records{/tr}</h1>
	</div>

	<div class="body">
		{minifind sort_mode=$sort_mode}

		{form id="checkform"}
			<input type="hidden" name="offset" value="{$control.offset|escape}" />
			<input type="hidden" name="sort_mode" value="{$control.sort_mode|escape}" />

			<table class="data">
				<tr>
					{if $gBitSystem->isFeatureActive( 'suggestion_list_suggestion_id' ) eq 'y'}
						<th>{smartlink ititle="Suggestion Id" isort=suggestion_id offset=$control.offset iorder=desc idefault=1}</th>
					{/if}

					{if $gBitSystem->isFeatureActive( 'suggestion_list_publish_status' ) eq 'y' &&
						$gBitSystem->isFeatureActive( 'liberty_display_status' ) && 
						$gBitSystem->isFeatureActive( 'liberty_display_status_menu' ) && 
						($gBitUser->hasPermission('p_liberty_edit_content_status') || $gBitUser->hasPermission('p_liberty_edit_all_status'))
						}
						<th width="20%">{smartlink ititle="Status" isort=title offset=$control.offset}</th>
					{/if}

					{if $gBitSystem->isFeatureActive( 'suggestion_list_title' ) eq 'y'}
						<th>{smartlink ititle="Title" isort=title offset=$control.offset}</th>
					{/if}

					{if $gBitSystem->isFeatureActive( 'suggestion_list_data' ) eq 'y'}
						<th>{smartlink ititle="Text" isort=data offset=$control.offset}</th>
					{/if}

					<th>{tr}Actions{/tr}</th>
				</tr>

				{foreach item=suggestion from=$suggestionList}
					<tr class="{cycle values="even,odd"}">
						{if $gBitSystem->isFeatureActive( 'suggestion_list_suggestion_id' )}
							<td><a href="{$smarty.const.SUGGESTION_PKG_URL}index.php?suggestion_id={$suggestion.suggestion_id|escape:"url"}" title="{$suggestion.suggestion_id}">{$suggestion.suggestion_id}</a></td>
						{/if}

						{if $gBitSystem->isFeatureActive( 'suggestion_list_publish_status' ) &&
							$gBitSystem->isFeatureActive( 'liberty_display_status' ) && 
							$gBitSystem->isFeatureActive( 'liberty_display_status_menu' ) && 
							($gBitUser->hasPermission('p_liberty_edit_content_status') || $gBitUser->hasPermission('p_liberty_edit_all_status'))
							}
							<td>{$gContent->getContentStatusName($suggestion.content_status_id)}</td>
						{/if}

						{if $gBitSystem->isFeatureActive( 'suggestion_list_title' )}
							<td>
								<a href="{$suggestion.display_url}">{$suggestion.title|escape}</a>
							</td>
						{/if}

						{if $gBitSystem->isFeatureActive( 'suggestion_list_data' )}
							<td>{$suggestion.data|escape}</td>
						{/if}

						<td class="actionicon">
						{if $gBitUser->hasPermission( 'p_suggestion_update' )}
							{smartlink ititle="Edit" ifile="edit.php" booticon="icon-edit" suggestion_id=$suggestion.suggestion_id}
						{/if}
						{if $gBitUser->hasPermission( 'p_suggestion_expunge' )}
							<input type="checkbox" name="checked[]" title="{$suggestion.title|escape}" value="{$suggestion.suggestion_id}" />
						{/if}
						</td>
					</tr>
				{foreachelse}
					<tr class="norecords"><td colspan="16">
						{tr}No records found{/tr}
					</td></tr>
				{/foreach}
			</table>

			{if $gBitUser->hasPermission( 'p_suggestion_expunge' )}
				<div style="text-align:right;">
					<script type="text/javascript">/* <![CDATA[ check / uncheck all */
						document.write("<label for=\"switcher\">{tr}Select All{/tr}</label> ");
						document.write("<input name=\"switcher\" id=\"switcher\" type=\"checkbox\" onclick=\"switchCheckboxes(this.form.id,'checked[]','switcher')\" /><br />");
					/* ]]> */</script>

					<select name="submit_mult" onchange="this.form.submit();">
						<option value="" selected="selected">{tr}with checked{/tr}:</option>
						<option value="remove_suggestion_data">{tr}remove{/tr}</option>
					</select>

					<noscript><div><input type="submit" class="btn" value="{tr}Submit{/tr}" /></div></noscript>
				</div>
			{/if}
		{/form}

		{pagination}
	</div><!-- end .body -->
</div><!-- end .admin -->
{/strip}
