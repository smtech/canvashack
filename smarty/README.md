# smarty/

Files supporting the [Smarty](http://www.smarty.net/) templating system.

- smarty/templates -- the actual template files (where all the action is)
- smarty/configs -- store config files here to be loaded by Smarty
- smarty/cache -- Smarty's cache directory, must be writeable by web server user (usually `www-data` or `apache`)
- smarty/templates_c -- compiled templates, must be writeable by web server user