<!DOCTYPE html>
<html>

<head>
	<title>{$metadata['APP_NAME']}</title>
	<link rel="stylesheet" href="{$metadata['APP_URL']}/stylesheet.css" />
</head>

<body>

<header id="header">
	<div id="header-logo"></div>
	<ul id="navigation-menu">
		<li><a href="{$metadata['APP_URL']}/app.php">Home</a></li>
		<li><a href="{$metadata['APP_URL']}/admin">Admin</a></li>
	</ul>
</header>

{if count($messages) > 0}
<div id="messages">
	<ul>
		{foreach $messages as $message}
			<li>
				<div class="message {$message->class|default:"message"}">
					<span class="title">{$message->title}</span><br />
					<span class="content">{$message->content}</span>
				</div>
			</li>
		{/foreach}
	</ul>
</div>
{/if}