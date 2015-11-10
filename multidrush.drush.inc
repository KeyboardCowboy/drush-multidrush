<?php
/**
 * @file
 * Manage multiple versions of drush on your server.
 */

require_once __DIR__ . '/MultiDrush.class.php';

/**
 * Implements hook_drush_command().
 */
function multidrush_drush_command() {
  $items['multidrush-init'] = array(
    'description' => 'Initialize multidrush by downloading the various versions of drush.',
    'bootstrap' => DRUSH_BOOTSTRAP_NONE,
    'aliases' => array('mdi'),
    'options' => array(
      'dir' => dt("The directory to store the drush libraries.  Defaults to '~/.drushlib'"),
    ),
    'examples' => array(),
  );

  return $items;
}

/**
 * Command callback.
 * drush multidrush-init
 */
function drush_multidrush_init() {
  $multidrush = new MultiDrush();

  // Get the directory to install to.
  $dir = drush_get_option('multidrush-dir', getenv('HOME') . "/.drushlib");
  if ($override_dir = drush_get_option('dir', FALSE, 'cli')) {
    $dir = $override_dir;
  }

  // Set the destination directory.
  if (!$multidrush->setDir($dir)) {
    return drush_set_error('INVALID_DIR', dt("Unable to create directory {$dir}."));
  }

  drush_log(dt('Installing Drush 6...'), 'status');
  $multidrush->install(6);

  drush_log(dt('Installing Drush 7...'), 'status');
  $multidrush->install(7);

  drush_log(dt('Installing Drush 8...'), 'status');
  $multidrush->install(8);
}
