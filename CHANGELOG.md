# Changelog

## 1.4.2
- Fix style guide index page markup path bug

## 1.4.1
- Fix generator bug with project root

## 1.4.0
- Add git ignore
- Add support for a style guide generator

## 1.3.4
- Add gitkeep for markup directory

## 1.3.3
- Fix invalid paths set up for twig bug

## 1.3.2
- Add git support

## 1.3.1
- Add missing versions of compiled css & js
- Remove repositories definition from composer file
- Add switch for minified vs non-minified versions of compiled css & js

## 1.3.0
- Remove dependency on Bootstrap. Style guide now uses a custom build of Bootstrap ~3.3.2
- Move javascript & less source code out of web root and add build step for style guide module development (does increase work for other developers)
- Add support for an alternate template directory by setting markupPath in config.ini

## 1.2.2
- Refactor Style Guide for PHP 5.3 compatibility

## 1.2.1
- Fix prescript outputting with html entities bug
- Improve instructions in README.md by adding composer install

## 1.2.0
- Switch to Twig based templates
- All sections are now generated from the config file
- Added a default config file

## 1.1.1
- Only sort files when pulling from a directory
- Update Colors section boilerplate & styles

## 1.1.0
- Remove json_decode from formatVariable
- Clean up index.php
- Remove unnecessary methods from StyleGuide
- Add support for loading markup from config.ini

## 1.0.0
- Moved to composer
- Added unit tests!

## 0.2.0
- Moved code to StyleGuide class

## 0.1.0
- Complete overhaul for Mindgruve
