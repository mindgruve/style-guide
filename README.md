# Mindgruve Style Guide

This style guide template is meant to provide a boilerplate for for your living style guide needs.

Using a configuration file, you can quickly set up a style guide page with all the necessary markup to review a complete set of
html elements. You can even use it for more complex markup like Bootstrap Components.

## Get Started

To get started:

1. Run `composer require mindgruve/style-guide` in the application root.
2. Run `vendor/bin/style-guide generate www/style-guide` to install the basic style guide mini-site to __www/style-guide__ and the config file to __config/mgStyleGuide.ini__.
3. Add all the styles and javascript you use in your site to the __mgStyleGuide.ini__ in the __config__ directory.

## Tips and Tricks

Now you can use the style guide for basic templating work. This is useful at the __component__ and the __page__ level.

Components can be added using the established patterns from the previous version of the style guide.

Page templates go in the __markup__ directory you set in your config. Any requests for __WEB_ROOT/home__ will load __base.html.twig__ and include __markup/home.html__ or __markup/home.html.twig__.
