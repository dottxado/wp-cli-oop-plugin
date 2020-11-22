dottxado/wp-cli-oop-plugin
======================

Command to scaffold an OOP oriented WordPress plugin.
The scaffolded code is based on WPPB.io, with the following differences:
* there is Composer to manage PSR-4 namespaces and file includes;
* the "public" folder has been renamed to "front";
* the styles and scripts are enqueued with a fingerprint;
* the plugin name and version are plugin constants;
* the plugin classes are singletons.

Quick links: [Installing](#installing) | [Using](#using) | [Updating](#updating) | [Credits](#credits)

## Installing

Installing this package requires WP-CLI 2.0.0 or greater. Update to the latest stable release with `wp cli update` or `brew update` (depending on how you installed it).

Once you've done, you can install this package with:

    wp package install git@github.com:dottxado/wp-cli-oop-plugin.git

If your PHP runs out of memory, try changing the memory_limit inside your php.ini.


## Using

All the parameters are required:

    wp scaffold oop-plugin --name=<plugin_name> --slug=<slug> --description=<description>  --namespace=<unique-namespace> --dev-name=<developer-name> --dev-email=<email> --plugin-url=<url>

To ask for help:

    wp help scaffold oop-plugin

The autoload needs to be executed after the scaffold:

    composer dump-autoload

## Updating
This command will update ALL your installed packages:

    wp package update

## Credits
I'm following the path of the codebase used as a template to scaffold the plugin since it was first developed by [Tom McFarlin](http://twitter.com/tommcfarlin/), then handed over to [Devin Vinson](https://twitter.com/DevinVinson) and actually can be found [here](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate).
