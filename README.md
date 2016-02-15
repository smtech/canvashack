# CanvasHack

This is a "reflexive LTI add-on" for Canvas that allows for a minor ecosystem of smaller, modular CSS and JavaScript tweaks to the Canvas UI. The "reflexive" nature of this structure allows the plug-ins to access the Canvas API to make more complex modifications and additions to the UI (e.g. per-course assignment templates or additional global navigation menus).

### Pre-Release Warning

This is currently in a pre-release state: I'm using it on the St. Mark's live instance. But I haven't documented my code terribly well, and there are a number of tweaks that are likely to take place before the 1.0 release, most notably a change to the current manifest structure, which was dealt with expediently, rather than correctly.

### How _does_ it work?

This LTI is heavily dependent on the [Composer](http://getcomposer.org) PHP package-management system. Each of [the current plugins](https://packagist.org/search/?q=%2Bcanvashack%20-installer) is a separate package. A plugin package has the following core qualities:

  1. It has a manifest file at its root: `manifest.xml`
  2. The manifest file defines some basic metadata about the plugin, and then describes which pages and DOM elements in the Canvas  UI are modified by this plugin.
  3. The manifest references other CSS and Javascript files, which may themselves refer back to other scripts in the package. (For example: to make the [per-course templates](https://github.com/smtech/canvashack-plugin-templates) "go", the manifest defines a regex to match the front page of a course and provides a script file that embeds a template chooser form. When the template is selected by a user, that selection is processed by a script (`template-copy.php`) that accesses the API and then redirects the user back to the newly created, templated object.
