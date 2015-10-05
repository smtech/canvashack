{extends file="subpage.tpl"}

{block name="subcontent"}

	<div class="container">
		<dl>
			<dt>Global JavaScript URL</dt>
				<dd><code><a href="{$metadata['GLOBAL_JAVASCRIPT_URL']}">{$metadata['GLOBAL_JAVASCRIPT_URL']}</a></code></dd>
			<dt>Global CSS URL</dt>
				<dd><code><a href="{$metadata['GLOBAL_CSS_URL']}">{$metadata['GLOBAL_CSS_URL']}</a></code></dd>
		</dl>
	</div>

	{include file="control-panel-form.tpl"}

{/block}