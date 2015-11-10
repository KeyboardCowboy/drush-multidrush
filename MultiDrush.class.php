<?php
/**
 * @file
 * Class to manage the multidrush commands.
 */

class MultiDrush {
  // The directory to store drush libraries.
  private $dir;

  // The path to composer if it can be found.
  private $composer = NULL;

  // Set meta information about each drush version.
  private $drush = array(
    6 => array(
      'subdir' => 'drush6',
      'composer_version' => '~6.0',
      'cmd_path' => 'vendor/drush/drush/drush',
    ),
    7 => array(
      'subdir' => 'drush7',
      'composer_version' => '~7.0',
      'cmd_path' => 'vendor/drush/drush/drush',
    ),
    8 => array(
      'subdir' => 'drush8',
      'composer_version' => 'dev-master',
      'cmd_path' => 'vendor/drush/drush/drush',
    ),
  );

  /**
   * Constructor.
   */
  public function __construct() {}

  /**
   * Validate and set the destination directory.
   *
   * @param string $dir
   *   The directory path.
   *
   * @return string|bool
   *   The validated directory or FALSE.
   */
  public function setDir($dir) {
    if (drush_mkdir($dir, TRUE)) {
      $this->dir = $dir;
      return $this->dir;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Check for any contents of the destination folder.
   *
   * @return bool
   *   Whether the dir is empty.
   */
  public function dirIsEmpty($subdir = NULL) {
    $path = $subdir ? "{$this->dir}/{$subdir}" : $this->dir;

    return (count(drush_scan_directory($path, '/.*/')) == 0);
  }

  /**
   * Download and prepare a version of drush.
   *
   * @param int $version
   *   The major drush version to install.
   *
   * @return bool
   */
  public function install($version) {
    // Check for valid versions.
    if (!isset($this->drush[$version])) {
      return drush_set_error(dt("Invalid version '{$version}' requested."));
    }

    // Get the definition for the desired version.
    $drush = $this->drush[$version];
    $path = "{$this->dir}/{$drush['subdir']}";

    // Make sure the directory exists.
    if (!drush_mkdir($path, TRUE)) {
      return FALSE;
    }

    // If it's not empty, skip this installation.
    if ($this->dirIsEmpty($drush['subdir'])) {
      if ($composer = $this->getComposer()) {
        if (drush_shell_exec("{$composer} -d={$path} require drush/drush {$drush['composer_version']}")) {
          $source = "{$this->dir}/{$drush['subdir']}/{$drush['cmd_path']}";
          $destination = "{$this->dir}/{$drush['subdir']}/drush";
          drush_shell_exec("ln -sf {$source} {$destination}");
          return TRUE;
        }
        return FALSE;
      }
      return FALSE;
    }
    else {
      return drush_log(dt("Drush {$version} is already installed.  Skipping."), 'status');
    }
  }

  /**
   * Get the path to a composer executable if one exists.
   *
   * @return null|string
   *   The system path to composer or an empty string.
   */
  private function getComposer() {
    // If we've already looked it up, return the stored path to composer.
    if (is_null($this->composer)) {
      if ($path = self::getCommand('composer.phar')) {
        $this->composer = $path;
      }
      elseif ($path = self::getCommand('composer')) {
        $this->composer = $path;
      }
      else {
        $this->composer = '';
      }
    }

    return $this->composer;
  }

  /**
   * Find the path to command.
   *
   * @param string $command
   *   The command to find.
   *
   * @return string
   *   The system path to run the command or empty if not found.
   */
  private static function getCommand($command) {
    drush_shell_exec("which {$command}");
    $stdout = (array) drush_shell_exec_output();

    return reset($stdout);
  }

  public static function createDirectory() {

  }
}
