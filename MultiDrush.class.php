<?php
/**
 * @file
 * Class to manage the multidrush commands.
 */

class MultiDrush {
  // The directory to store drush libraries.
  public $dir;

  // The bin directory where the drush command will be located.
  public $binDir;

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
  public function __construct($dir) {
    // Set the destination directory.
    if (drush_mkdir($dir, TRUE)) {
      $this->dir = $dir;
    }
    else {
      return drush_set_error('INVALID_DIR', dt("Unable to create directory {$dir}."));
    }

    // Set the bin directory.
    $bin_dir = getenv('HOME') . '/.drush/bin';
    if (drush_mkdir($dir, TRUE)) {
      $this->binDir = $bin_dir;
    }
    else {
      return drush_set_error('INVALID_DIR', dt("Unable to create bin directory {$dir}."));
    }

    return $this;
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
    if (!($drush = $this->validateVersion($version))) {
      return FALSE;
    }

    // Make sure the directory exists.
    $path = "{$this->dir}/{$drush['subdir']}";
    if (!drush_mkdir($path, TRUE)) {
      return FALSE;
    }

    // If it's not empty, skip this installation.
    if ($this->dirIsEmpty($drush['subdir'])) {
      if ($composer = $this->getComposer()) {
        if (drush_shell_exec("{$composer} -d={$path} require drush/drush {$drush['composer_version']}")) {
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
   * Make sure the requested version is valid.
   *
   * @param int $version
   *   The requested drush major version.
   *
   * @return bool|array
   *   The drush metadata for the requested version or FALSE if not valid.
   */
  private function validateVersion($version) {
    // Check for valid versions.
    if (!isset($this->drush[$version])) {
      return drush_set_error(dt("Invalid version 'drush {$version}' requested."));
    }
    else {
      return $this->drush[$version];
    }
  }

  /**
   * Switch to a different major drush version.
   *
   * @param int $version
   *   The version to switch to.
   *
   * @return bool
   *   Return FALSE if the requested version is invalid.
   */
  public function switchVersion($version) {
    // Check for valid versions.
    if (!($drush = $this->validateVersion($version))) {
      return FALSE;
    }

    $source = "{$this->dir}/{$drush['subdir']}/{$drush['cmd_path']}";
    $destination = "{$this->binDir}/drush";

    return drush_shell_exec("ln -sf {$source} {$destination}");
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

  /**
   * Detect/create the source directory for the drush libraries.
   *
   * @return string
   *   The drush library directory.
   */
  public static function getDir() {
    $dir = drush_get_option('multidrush-dir', getenv('HOME') . "/.drushlib");
    if ($override_dir = drush_get_option('dir', FALSE, 'cli')) {
      $dir = $override_dir;
    }

    return $dir;
  }
}
