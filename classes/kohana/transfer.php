<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Module core
 * 
 * @package    Transfer
 * @category   Classes
 * @author     Javier Aranda <internet@javierav.com>
 * @copyright  (c) 2010 Javier Aranda
 * @license    http://javierav.com/licence
 */

abstract class Kohana_Transfer
{
  /**
   * @var array driver config
   */
  protected $_config;

  /**
   * @var array instances
   */
  protected static $_instances = array();

  /**
   * Create a new instance or return if exist
   * 
   * @param string $group name of config group
   * @return class instance of used driver
   */
  public static function instance($group = 'default')
  { 
    if(isset(Transfer::$instances[$group]))
    {
      return Transfer::$instances[$group];
    }

    $config = Kohana::config('transfer');

    if ( ! $config->offsetExists($group))
    {
      throw new Kohana_Transfer_Exception(Kohana::message('transfer', 'fail_load_config'), array(':group' => $group));
    }

    $config = $config->get($group);

    // Set the class name
    $class = 'Transfer_'.$config['driver'];

    return Transfer::$instances[$group] = new $class($config);
  }

  /**
   * Save the config and checks functions availability
   *
   * @param array $config driver config
   */
  public function __construct(array $config)
  {
    $this->_config = $config;

    // Checks if driver can be used
    if( ! $this->check() )
    {
      throw new Kohana_Transfer_Exception(Kohana::message('transfer', 'not_related_functions'), array(':url' => $this->info()));
    }
  }
}