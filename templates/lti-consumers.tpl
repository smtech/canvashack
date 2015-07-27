{extends file="page.tpl"}
{block name="content"}

{if count($consumers) > 0}
	<table id="lti-consumers">
		{foreach $consumers as $consumer}
			{if empty($requestKey) || (!empty($requestKey) && $requestKey != $consumer['consumer_key'])}
				{assign var="closed" value=true}
			{else}
				{assign var="closed" value=false}
			{/if}
			<tr {if !$closed}class="open-record"{/if}>
				<td>
					<dl>
					{foreach $fields as $field}
						{if !empty($consumer[$field])}
							<dt>{$field}</dt>
								<dd>{$consumer[$field]}</dd>
						{/if}
					{/foreach}
					</dl>
				</td>
				{if $closed}
					<td>
						<form action="{$formAction}" method="post">
							<input type="hidden" name="consumer_key" value="{$consumer['consumer_key']}" />
							<input type="hidden" name="action" value="select" />
							<input type="submit" value="Edit" />
						</form>
						<form action="{$formAction}" method="post">
							<input type="hidden" name="consumer_key" value="{$consumer['consumer_key']}" />
							<input type="hidden" name="action" value="delete" />
							<input type="submit" value="Delete" />
						</form>
					</td>
				{/if}
			</tr>
		{/foreach}
	</table>
{else}
	<p>No consumers</p>
{/if}
	
	<form action="{$formAction}" method="post">
		<label for="name">Name <input type="text" name="name" id="name" value="{$name}"/></label>
		<label for="key">Key <input type="text" name="key" id="key" value="{$key}" /></label>
		<label for="secret">Secret <input type="text" name="secret" id="secret" value="{$secret}" /></label>
		<label for="enabled">Enabled <input type="checkbox" name="enabled" id="enabled" value="1" {if $enabled} checked{/if} /></label>
		<input type="hidden" name="action" value="{if !empty($name)}update{else}insert{/if}" />
		<input type="submit" value="{if !empty($name)}Update{else}Add{/if} Consumer" />
	</form>
	{if !empty($name)}
	<form action="{$formAction}" method="post">
		<input type="hidden" name="consumer_key" value="{$key}" />
		<input type="hidden" name="action" value="delete" />
		<input type="submit" value="Delete" />
	</form>
	{/if}
	<input type="button" value="Cancel" onclick="window.location.href='{$formAction}'" />
	
	<p>To install this LTI, users should choose configuration type <em>By URL</em> and provide their consumer key and secret above. They should point their installer at <code><a href="{$metadata['APP_CONFIG_URL']}">{$metadata['APP_CONFIG_URL']}</a></code>

{/block}