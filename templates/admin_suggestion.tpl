{strip}
{form}
	{jstabs}
		{jstab title="List Settings"}
			{legend legend="List Settings"}
				<input type="hidden" name="page" value="{$page}" />
				{foreach from=$formSuggestionLists key=item item=output}
					<div class="form-group">
						{formlabel label=$output.label for=$item}
						{forminput}
							{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
							{formhelp note=$output.note page=$output.page}
						{/forminput}
					</div>
				{/foreach}
			{/legend}
		{/jstab}

		<div class="form-group submit">
			<input type="submit" class="btn btn-default" name="suggestion_settings" value="{tr}Change preferences{/tr}" />
		</div>
	{/jstabs}
{/form}
{/strip}
