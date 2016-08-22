{if $enableOAuth && $oauthVendors}
<div class="c_form">
	<table cellspacing="0" cellpadding="0" class="formtable">
		<caption>
			<h2>第三方账号登录</h2>		
		</caption>
		<tbody>
			<tr>
				{foreach $oauthVendors as $oauth}
				<td>
					<a style="display: block; margin: 0 110px 2em; width: 100px; border: 1px solid #486B26; background: #76A14F; line-height: 30px; font-size: 14px; text-align: center; text-decoration: none;" href="{$oauth.url}">
						<strong style="display: block; border-top: 1px solid #9EBC84; color: #FFF; padding: 0 0.5em;">{$oauth.name}</strong>
					</a>
				</td>
				{/foreach}
			</tr>
		</tbody>
	</table>
</div>
{/if}