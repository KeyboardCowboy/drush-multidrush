# MultiDrush
Manage multiple versions of Drush on your server.

## Requirements
Currently you must have composer installed and discoverable in your `$PATH` as
either `composer.phar` or `composer`.

You must also have `drush` already installed.  That was the easiest way to get
this off the ground.

There is [an issue](https://github.com/KeyboardCowboy/drush-multidrush/issues/1) 
to provide additional download options, but it adds significant complexity and 
time.  I promise I'll work on it, but for now it's probably easier to
simply [install composer](https://getcomposer.org/).

There is also [an issue](https://github.com/KeyboardCowboy/drush-multidrush/issues/5) 
to get this running without needing to first install drush.

## Why?
Each version of Drupal requires a different version of Drush.  If you are
running multiple versions of Drupal, you will need multiple versions of Drush.

## Who?
The main use case is developers who have already been using Drush to manage
D6 or D7 sites and are now getting involved in D8 and must have multiple
versions of Drush available depending on the site they are working on.

Or use Vagrant instead.  That works too.

## Installation
### With Composer
1. Run `composer require keyboardcowboy/drush-multidrush` inside any directory 
drush can scan.
1. Run `drush cc drush` to clear drush's cache.
1. Run `drush mdi` to download Drush 6, 7 and 8 and configure your `$PATH`

## Commands
* `drush multidrush-init (mdi)`  
Download Drush 6, 7 and 8 and prepare your `$PATH` to look for this version of
Drush.

* `drush multidrush-switch (mds, switch)`  
Switch to a different version of Drush.

## How it Works
Through the clever use of symlinking, we can switch which version of Drush is
found on the `$PATH`.  Currently we use composer to download three versions of
drush and then symlink to whichever you want to use.
## Dependencies
* [Composer](https://getcomposer.org/)

## Roadmap
The composer dependency is restrictive, I know.  I just need a little time ([or 
some help](https://github.com/KeyboardCowboy/drush-multidrush/issues/1)) to implement alternative download methods.

I could also use some help checking this cross-platform.  Currently it works
great on my Mac.  That's all I know.

## Troubleshooting
* Make sure you do not have a version of Drush downloaded into any of the places
 Drush looks for plugins.  If you do and switch to a version that is not the 
 same as the one in this directory, it's basically the same as crossing the 
 streams.
  
    * /etc/drush
    * ~/.drush
    * /sites/all/drush
