{extends file="page.tpl"}
{block name="content"}

	<div class="container">
		{if count($consumers) > 0}
			<table id="lti-consumers" class="table table-striped table-hover">
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
								<form action="{$formAction}" method="post" class="form-horizontal">
									<div class="form-group">
										<input type="hidden" name="consumer_key" value="{$consumer['consumer_key']}" />
										<button type="submit" name="action" value="select" class="btn btn-default">Edit</button>
										<button type="submit" name="action" value="delete" class="btn btn-danger">Delete</button>
									</div>
								</form>
							</td>
						{/if}
					</tr>
				{/foreach}
			</table>
		{else}
			<p>No consumers</p>
		{/if}
	</div>
	<div class="container">
		<form action="{$formAction}" method="post" class="form-horizontal">
			<div class="form-group">
				<label for="name" class="control-label col-sm-2">Name</label>
				<div class="col-sm-10">
					<input type="text" name="name" id="name" value="{$name}" class="form-control" autofocus="autofocus" />
				</div>
			</div>
			
			<div class="form-group">
				<label for="key" class="control-label col-sm-2">Consumer Key</label>
				<div class="col-sm-10">
					<input type="text" name="key" id="key" value="{$key}" class="form-control" {if !empty($name)}disabled="disabled"{/if} />
					{if !empty($name)}<input type="hidden" name="consumer_key" value="{$key}" />{/if}
				</div>
			</div>
			
			<div class="form-group">
				<label for="secret" class="control-label col-sm-2">Shared Secret</label>
				<div class="col-sm-10">
					<input type="text" name="secret" id="secret" value="{$secret}" class="form-control" />
				</div>
			</div>
			
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<div class="checkbox">
						<label for="enabled" class="control-label">
							<input type="checkbox" name="enabled" id="enabled" value="1" {if $enabled} checked{/if} />
							Enabled
						</label>
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" name="action" value="{if !empty($name)}update{else}insert{/if}" class="btn btn-primary">{if !empty($name)}Update{else}Add{/if} Consumer</button>
					{if !empty($name)}
						<button type="submit" name="action" value="delete" class="btn btn-danger">Delete</button>
					{/if}
					<button type="button" onclick="window.location.href='{$formAction}'" class="btn btn-default">Cancel</button>
				</div>
			</div>
		</form>
	</div>

	<div class="container">
		<p>To install this LTI, users should choose configuration type <em>By URL</em> and provide their consumer key and secret above. They should point their installer at:</p>
		<p><code><a href="{$metadata['APP_CONFIG_URL']}">{$metadata['APP_CONFIG_URL']}</a></code></p>
	</div>

{/block}