<?php defined('SYSPATH') or die('No direct script access.');

/**
 * SFTP Driver for Transfer module
 *
 * @package    Transfer
 * @category   Classes
 * @author     Javier Aranda <internet@javierav.com>
 * @copyright  (c) 2010 Javier Aranda
 * @license    http://javierav.com/licence
 */

class Kohana_Transfer_SFTP extends Transfer implements Kohana_Transfer_Interface
{
  /**
   * @var resource SSH2 connection
   */
  private $_ssh2;

  /**
   * @var resource SSH2/SFTP connection
   */
  private $_sftp;

  
  /**
   * Checks if driver related functions are availables
   */
  public function check()
  {
    return function_exists('ssh2_connect');
  }

  /**
   * Connect operation
   */
  public function connect()
  {
    if( $this->_ssh2 != null )
    {
      // Already connected
      return;
    }
    
    // Realizar la conexión al servidor
    $host = ( isset($this->_config['hostname']) ) ? $this->_config['hostname'] : 'localhost';
    $port = ( isset($this->_config['port']) ) ? $this->_config['port'] : 22;
    $username = ( isset($this->_config['username']) ) ? $this->_config['username'] : '';
    $password = ( isset($this->_config['password']) ) ? $this->_config['password'] : null;

    $this->_ssh2 = ssh2_connect($host, $port);

    if( $this->_ssh2 === FALSE )
    {
      throw new Kohana_Transfer_Exception(Kohana::message('transfer', 'fail_open_connection'), array(':host' => $host, 'port' => $port));
    }

    // Si se especifica el campo fingerprint en la configuracion compararlo con el del servidor
    if( isset($this->_config['fingerprint']) )
    {
      if( strtolower(ssh2_fingerprint($this->_ssh2)) != strtolower($this->_config['fingerprint']) )
      {
        throw new Kohana_Transfer_Exception(Kohana::message('transfer', 'fail_fingerprint_validation'), array(':key' => ssh2_fingerprint($this->_ssh2)));
      }
    }

    // Si se especifica un certificado, realizar la conexión con él
    if( isset($this->_config['pubkeyfile']) AND isset($this->_config['privkeyfile']) )
    {
      if( ! @ssh2_auth_pubkey_file($this->_ssh2, $username, $this->_config['pubkeyfile'], $this->_config['privkeyfile'], $password) )
      {
        throw new Kohana_Transfer_Exception(Kohana::message('transfer', 'fail_authentication'));
      }
    }
    // Si no, autenticar de la forma tradicional
    else
    {
      if( ! @ssh2_auth_password($this->_ssh2, $username, $password) )
      {
        throw new Kohana_Transfer_Exception(Kohana::message('transfer', 'fail_authentication'));
      }
    }

    // Enable SFTP mode
    $this->_sftp = ssh2_sftp($this->_ssh2);
  }

  /**
   * Downloads a remote file to a local file
   */
  public function download($remote_file, $local_file)
  {
    $this->connect();
  }

  /**
   * Uploads a local file to a remote server
   */
  public function upload($local_file, $remote_file, $create_mode = 0644)
  {
    $this->connect();
  }

  /**
   * Creates a remote directory
   */
  public function mkdir($dirname, $mode = 0777, $recursive = false)
  {
    $this->connect();
  }

  /**
   * Deletes a remote directory
   */
  public function rmdir($dirname)
  {
    $this->connect();
  }

  /**
   * Deletes a remote file
   */
  public function delete($remote_file)
  {
    $this->connect();
  }

  /**
   * Renames a remote file
   */
  public function rename($from, $to)
  {
    $this->connect();
  }

  /**
   * Disconnect from server
   */
  public function __destruct()
  {
    // SSH2 wrapper do not have any function to disconnect from server
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

    $stream = ssh2_exec($this->_ssh2, $command);

    if( $stream === false )
    {
      return false;
    }

    stream_set_blocking($stream, true);
    $output = stream_get_contents($stream);
    
    fclose($stream);

    return $output;
  }
}