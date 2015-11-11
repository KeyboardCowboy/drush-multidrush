<?php
/**
 * @file
 * Manage multiple versions of drush on your server.
 */

require_once __DIR__ . '/MultiDrush.class.php';

/**
 * Implements hook_drush_command().
 *
 * @todo: Create a command to update Drush verisons.
 *   https://github.com/KeyboardCowboy/drush-multidrush/issues/3
 */
function multidrush_drush_command() {
  $options = array(
    'dir' => "The directory to store the drush libraries.  Defaults to '~/.drushlib'",
  );

  $items['multidrush-init'] = array(
    'description' => 'Initialize multidrush by downloading the various versions of drush.',
    'bootstrap' => DRUSH_BOOTSTRAP_NONE,
    'aliases' => array('mdi'),
    'examples' => array(),
    'options' => $options,
  );

  $items['multidrush-switch'] = array(
    'description' => 'Switch to a different version of drush.',
    'bootstrap' => DRUSH_BOOTSTRAP_NONE,
    'aliases' => array('mds', 'switch'),
    'arguments' => array(
      'version' => "The drush major version to switch to. (6, 7 or 8)",
    ),
    'arguments required' => TRUE,
    'options' => $options,
  );

  return $items;
}

/**
 * Command callback.
 * drush multidrush-init
 *
 * @todo: Allow users to specify which versions to download.
 *   https://github.com/KeyboardCowboy/drush-multidrush/issues/2
 *
 * @todo: Allow users to choose the default version on init.
 *   https://github.com/KeyboardCowboy/drush-multidrush/issues/4
 */
function drush_multidrush_init() {
  // Get the directory to install to.
  $dir = MultiDrush::getDir();

  // Install drush versions.
  if ($multidrush = new MultiDrush($dir)) {
    drush_log(dt('Installing Drush 6...'), 'status');
    $multidrush->install(6);

    drush_log(dt('Installing Drush 7...'), 'status');
    $multidrush->install(7);

    drush_log(dt('Installing Drush 8...'), 'status');
    $multidrush->install(8);

    // Set Drush 8 as the default.
    $multidrush->switchVersion(8);

    // Inform the user to add this path to their $PATH.
    drush_print(dt("\nDrush 6, 7 and 8 have been installed!  There is ONE more step."));
    drush_print(dt("\n* Add the following to your \$PATH environment variable: {$multidrush->binDir}"));
    drush_print(dt("\nThis is usually set in your ~/.bashrc or ~/.bash_profile file and would look something like this: 'export PATH=\"\$PATH:{$multidrush->binDir}\"'"));

    drush_print(dt("These versions of drush will override any you have currently installed."));
  }
}

/**
 * Command callback.
 * drush multidrush-switch
 *
 * @param int $version
 *   The drush major version to switch to.
 */
function drush_multidrush_switch($version) {
  // Get the directory to install to.
  $dir = MultiDrush::getDir();

  // Install drush versions.
  if ($multidrush = new MultiDrush($dir)) {
    $multidrush->switchVersion($version);
  }
}
