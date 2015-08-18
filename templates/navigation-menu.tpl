{if isset($ltiUser)}
<ul id="navigation-menu">
	<li><a href="{$metadata['APP_URL']}/app.php">Home</a></li>
	<li><a href="{$metadata['APP_URL']}/admin">Admin</a></li>
	<li class="lti-user">{$ltiUser->fullname}</li>
</ul>
{/if}