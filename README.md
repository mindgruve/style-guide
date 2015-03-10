# Mindgruve Style Guide

This style guide template is meant to provide a baseline for future development projects.

Using a configuration file, you can quickly set up a style guide page with all the necessary markup to review a complete set of
html elements. You can even use it for more complex markup like Bootstrap Components.

To get started:

1. Add this `repositories` entry to your `composer.json`

        {
            "type": "git",
            "url": "https://github.com/mindgruve/style-guide.git"
        }
2. Run `composer require mindgruve/style-guide` in the application root.
3. Run `vendor/bin/style-guide generate www/style-guide` to install the basic style guide mini-site to __www/style-guide__ and the config file to __confgi/mgStyleGuide.ini__.
4. Add all the styles and javascript you use in your site to the __mgStyleGuide.ini__ in the __config__ directory.
