Yet another flat file CMS in PHP.

[Flattery Website](https://flattery.thowsenmedia.com)

Beware that Flattery is NOT ready for production. It is not feature-complete and may change drastically at any moment.

# At a glance

Flattery supports the following features:

- Themes
- Plugins
- Blocks (widgets)
- In-place editing of blocks via included liveeditor plugin
- Pages, as:
    - .txt files
    - .html files
    - or even .php files

Flattery stores all data in YAML.


# Architecture

Flattery creates a singleton of the "CMS" class. It acts as a container for the following subsystems/classes:

- Kernel
- Data
- Request
- Event
- Theme
- PluginManager
- PageManager

calling the flattery() function returns the CMS instance. You can get the data, or request object by doing flattery()->data or flattery('data'), similarly flattery()->request or flattery('request').

In addition to the flattery() function, there are helper functions for each of the major singleton classes, such as the data(), event() etc.

# File Structure

The file structure is fairly simple:

- /app/data contains the "database", each folder is a category, each file a "table" although these terms are loosely defined. Currently subfolders are not possible.
- /app/pages contains the pages
- /plugins contain plugins
- /themes contain themes
- /vendor is the composer dependencies (see getcomposer.org)
- /public is the default web root for your web server, for security reasons.

The folder structure can be changed to your liking, but must be updated in the bootstrap.php.

There's also a flattery.php file which is the console. To use it, run `php flattery.php`, there will be commands to manage plugins, themes etc.

# License
see LICENSE.md