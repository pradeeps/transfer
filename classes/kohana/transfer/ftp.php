<?php defined('SYSPATH') or die('No direct script access.');

/**
 * FTP Driver for Transfer module
 *
 * @package    Transfer
 * @category   Classes
 * @author     Javier Aranda <internet@javierav.com>
 * @copyright  (c) 2010 Javier Aranda
 * @license    http://javierav.com/licence
 */

class Kohana_Transfer_FTP extends Transfer implements Kohana_Transfer_Interface
{
  /**
   * @var resource FTP connection
   */
  private $_ftp;


  /**
   * Checks if driver related functions are availables
   */
  public function check()
  {
    return function_exists('ftp_connect');
  }

  /**
   * Connect operation
   */
  public function connect()
  {
    if( $this->_ftp != null )
    {
      // Already connected
      return;
    }

    // Connect to server
    $host = ( isset($this->_config['hostname']) ) ? $this->_config['hostname'] : 'localhost';
    $port = ( isset($this->_config['port']) ) ? $this->_config['port'] : 21;
    $username = ( isset($this->_config['username']) ) ? $this->_config['username'] : 'anonymous';
    $password = ( isset($this->_config['password']) ) ? $this->_config['password'] : 'anonymous@example.com';

    $this->_ftp = ftp_connect($host, $port);

    if( $this->_ftp === FALSE )
    {
      throw new Kohana_Transfer_Exception(Kohana::message('transfer', 'fail_open_connection'), array(':host' => $host, 'port' => $port));
    }

    if( ! @ftp_login($this->_ftp, $username, $password) )
    {
      throw new Kohana_Transfer_Exception(Kohana::message('transfer', 'fail_authentication'));
    }

    // Pasive mode
    @ftp_pasv($this->_ftp, true);
  }

  /**
   * Downloads a remote file to a local file
   *
   * @param string $remote_file path to the remote file
   * @param string $local_file path to the local file
   * @return bool TRUE on success or FALSE on failure
   */
  public function download($remote_file, $local_file)
  {
    $this->connect();

    return @ftp_get($this->_ftp, $local_file, $remote_file, FTP_BINARY);
  }

  /**
   * Uploads a local file to a remote server
   *
   * @param string $local_file path to the local file
   * @param string $remote_file path to the remote file
   * @param int $create_mode the file will be created with the mode specified by this param
   * @return bool TRUE on success or FALSE on failure
   */
  public function upload($local_file, $remote_file, $create_mode = 0644)
  {
    $this->connect();

    // Upload
    $output = @ftp_put($this->_ftp, $remote_file, $local_file, FTP_BINARY);

    // Change permissions
    $output = $output && (@ftp_chmod($this->_ftp, $create_mode, $remote_file) !== FALSE);

    return $output;
  }

  /**
   * Creates a remote directory
   *
   * @param string $dirname path of the new directory
   * @param integer $mode permissions on the new directory
   * @param bool $recursive If TRUE any parent directories required for dirname will be automatically created as well
   * @return bool TRUE on success or FALSE on failure
   */
  public function mkdir($dirname, $mode = 0777, $recursive = false)
  {
    $this->connect();

    if( ! $recursive )
    {
      // Create
      $output = @ftp_mkdir($this->_ftp, $dirname);

      // Change permissions
      $output = $output && (@ftp_chmod($this->_ftp, $mode, $dirname) !== FALSE);

      return $output;
    }
    else
    {
      // Recursive mode by Ashus (http://php.net/manual/en/function.ftp-mkdir.php#86357)
      
      $dir = explode('/', $dirname);
      $path = '';
      $output = true;

      for( $i=0; $i < count($dir); $i++ )
      {
        $path .= '/' . $dir[$i];

        if( ! @ftp_chdir($this->_ftp, $path) )
        {
          @ftp_chdir($this->_ftp, '/');

          if( ! @ftp_mkdir($this->_ftp, $path) )
          {
            return FALSE;
          }
          else
          {
            $output = $output && (@ftp_chmod($this->_ftp, $mode, $path) !== FALSE);
          }
        }
      }

      return $output;
    }
  }

  /**
   * Deletes a remote directory
   *
   * @param string $dirname path of the directory
   * @return bool TRUE on success or FALSE on failure
   */
  public function rmdir($dirname)
  {
    $this->connect();

    return @ftp_rmdir($this->_ftp, $dirname);
  }

  /**
   * Deletes a remote file
   *
   * @param string $remote_file path to the remote file
   * @return bool TRUE on success or FALSE on failure
   */
  public function delete($remote_file)
  {
    $this->connect();

    return @ftp_delete($this->_ftp, $remote_file);
  }

  /**
   * Renames a remote file
   *
   * @param string $from the current file that is being renamed
   * @param string $to the new file name that replaces
   * @return bool TRUE on success or FALSE on failure
   */
  public function rename($from, $to)
  {
    $this->connect();

    return @ftp_rename($this->_ftp, $from, $to);
  }

  /**
   * Disconnect from server
   */
  public function __destruct()
  {
    if( $this->_ftp )
    {
      ftp_close($this->_ftp);
    }
  }

  /**
   * Executes a command in the server
   *
   * @param string $command command to execute
   * @return mixed string with response, false if nothing
   */
  public function exec($command)
  {
    $this->connect();

    return @ftp_raw($this->_ftp, $command);
  }
}