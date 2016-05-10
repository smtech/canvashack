{extends file="subpage.tpl"}

{block name="subcontent"}

	<div class="container">
		<p>To enable CanvasHack in the new UI, click the button below to download the <code>canvash-loader.js</code> file which you will upload in the Theme Editor.</p>
		<p class="text-center"><a class="btn btn-primary" href="canvashack-loader.js.php?download=true">Download canvashack-loader.js</a></p>
		<p>To enable CanvasHack in the old UI (available until summer 2016), you can paste the URL below into your Global Javascript URL:</p>
		<p class="text-center"><code><a href="{$appURL}/canvashack-loader.js.php">{$appURL}/canvashack-loader.js.php</a></code></p>
	</div>

	{include file="control-panel-form.tpl"}

{/block}
