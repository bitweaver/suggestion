{* $Header: /cvsroot/bitweaver/_bit_suggestion/templates/edit_suggestion.tpl,v 1.1 2009/09/15 15:10:49 wjames5 Exp $ *}
{strip}
<div class="edit suggestion">
	{if $smarty.request.preview}
		<h2>Preview {$gContent->mInfo.title|escape}</h2>
		<div class="preview">
			{include file="bitpackage:suggestion/display_suggestion.tpl" page=`$gContent->mInfo.suggestion_id`}
		</div>
	{/if}

	<div class="header">
		{if $gContent->mInfo.suggestion_id}
			<h1>{tr}Edit Suggestion: {$gContent->mInfo.title|escape}{/tr}</h1>
		{else}
			<h1>{tr}Suggest Your Own Energy Measure{/tr}</h1>
			{if $gBitUser->isRegistered()}
				<h3>{tr}Welcome Back{/tr}, {$gBitUser->getDisplayName()}</h3>
			{/if}
		{/if}
	</div>

	<div class="body">
		{if $gBitThemes->isAjaxRequest()}
			{assign var=action value='javascript;'}
		{/if}
		{form action=$action enctype="multipart/form-data" id="edit_suggestion"}
			{jstabs}
				{jstab title="Edit"}
					{legend legend="Suggestion"}
						<input type="hidden" name="suggestion_id" value="{$gContent->mInfo.suggestion_id}" />
						{formfeedback warning=$errors.store}

						{if !$gBitUser->isRegistered()}
							<div class="row">
								{formfeedback error=$errors.validate}
								{formlabel label="Email Address" for="email"}
								{forminput}
									<input type="text" size="50" name="email" id="email" value="{$reg.email}"/>
								{/forminput}
							</div>

							<div class="row">
								{formlabel label="Real name" for="real_name"}
								{forminput}
									<input type="text" name="real_name" id="real_name" value="{$smarty.request.real_name}" />
								{/forminput}
							</div>
						{/if}

						<div class="row">
							{formfeedback warning=$errors.title}
							{formlabel label="Name Your Plan:" for="title"}
							{forminput}
								<input type="text" size="50" name="title" id="title" value="{$gContent->mInfo.title|escape}" />
							{/forminput}
						</div>

						<div class="row">
							{formfeedback warning=$errors.mwh}
							{formlabel label="Megawatt Hours / Year" for="mwh"}
							{forminput}
								<input type="text" size="25" name="mwh" id="mwh" value="{$gContent->mInfo.mwh}" />
							{/forminput}
						</div>

						<div class="row">
							{formfeedback warning=$errors.sources}
							{formlabel label="Share your research with us:" for="sources"}
							{formhelp note="(links, organizations, scientists etc.)"}
							{textarea id="souces" name="sources" rows=3 noformat="y"}{$gContent->mInfo.sources}{/textarea}
						</div>

						{textarea name="edit" label="Description:" noformat=false}{$gContent->mInfo.data}{/textarea}

						{* any simple service edit options *}
						{if !$gBitThemes->isAjaxRequest()}
							{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_mini_tpl"}
						{/if}

						<div class="row submit">
							<input type="{if $gBitThemes->isAjaxRequest()}button{else}submit{/if}" name="cancel_suggestion" value="{tr}Cancel{/tr}" />
							<input type="{if $gBitThemes->isAjaxRequest()}button{else}submit{/if}" name="save_suggestion" value="{tr}Save{/tr}" />
						</div>
					{/legend}
				{/jstab}

				{* any service edit template tabs *}
				{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_tab_tpl"}
			{/jstabs}
		{/form}
	</div><!-- end .body -->
</div><!-- end .suggestion -->

{/strip}
