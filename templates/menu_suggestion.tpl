{strip}
	<ul>
		{if $gBitUser->hasPermission( 'p_suggestion_view')}
			<li><a class="item" href="{$smarty.const.SUGGESTION_PKG_URL}list.php">{tr}List{/tr}</a></li>
		{/if}
		{if $gBitUser->hasPermission( 'p_suggestion_create' )}
			<li><a class="item" href="{$smarty.const.SUGGESTION_PKG_URL}edit.php">{tr}Create{/tr}</a></li>
		{/if}
		{if $gBitUser->hasPermission( 'p_suggestion_admin' )}
			<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=suggestion">{tr}Admin{/tr}</a></li>
		{/if}
	</ul>
{/strip}
