<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Interface for Transfer drivers
 *
 * @package    Transfer
 * @category   Interface
 * @author     Javier Aranda <internet@javierav.com>
 * @copyright  (c) 2010 Javier Aranda
 * @license    http://javierav.com/licence
 */

interface Kohana_Transfer_Interface
{
  /**
   * Checks if driver related functions are availables
   */
  public function check();

  /**
   * Connect operation
   */
  public function connect();

  /**
   * Downloads a remote file to a local file
   */
  public function download($remote_file, $local_file);

  /**
   * Uploads a local file to a remote server
   */
  public function upload($local_file, $remote_file, $create_mode = 0644);

  /**
   * Creates a remote directory
   */
  public function mkdir($dirname, $mode = 0777, $recursive = false);

  /**
   * Deletes a remote directory
   */
  public function rmdir($dirname);

  /**
   * Deletes a remote file
   */
  public function delete($remote_file);

  /**
   * Renames a remote file
   */
  public function rename($from, $to);

  /**
   * Disconnect from server
   */
  public function __destruct();

  /**
   * Executes a command in the server
   */
  public function exec($command);
}