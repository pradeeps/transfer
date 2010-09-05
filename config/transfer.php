<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Config file for Transfer module
 *
 * @package    Transfer
 * @category   Config
 * @author     Javier Aranda <internet@javierav.com>
 * @copyright  (c) 2010 Javier Aranda
 * @license    http://javierav.com/licence
 */

return array(
    'default' => array(
        'driver'      => 'FTP',
        'hostname'    => 'localhost',
        'port'        => 21,
        'username'    => '',
        'password'    => '',
    ),

    'sftp'    => array(
        'driver'      => 'SFTP',
        'hostname'    => 'localhost',
        'port'        => 22,
        'username'    => '',
        //'pubkeyfile'  => '/path/to/id_rsa.pub',
        //'privkeyfile' => '/path/to/id_rsa',
        'password'    => '',
        //'fingerprint' => '',
    ),
);