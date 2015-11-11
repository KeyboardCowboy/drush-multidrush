# MultiDrush
Manage multiple versions of Drush on your server.

## Requirements
Currently you must have composer installed and discoverable in your `$PATH` as
either `composer.phar` or `composer`.

There is [an issue](https://github.com/KeyboardCowboy/drush-multidrush/issues/1) 
to provide additional download options, but it adds significant complexity and 
time.  It's probably easier to [install composer](https://getcomposer.org/).

## Why?
Each version of Drupal requires a different version of Drush.  If you are
running multiple versions of Drupal, you will need multiple versions of Drush.

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
