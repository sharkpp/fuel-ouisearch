<?php
/**
 * OuiSearch is IEEE registered OUI search package for FuelPHP.
 *
 * @package    OuiSearch
 * @version    1.0
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2013+ sharkpp
 * @link       https://www.sharkpp.net/
 */

Autoloader::add_namespace('OuiSearch', __DIR__.'/classes/');

Autoloader::add_core_namespace('OuiSearch');

Autoloader::add_classes(array(
	'OuiSearch\\OuiSearch' => __DIR__.'/classes/ouisearch.php',
));


/* End of file bootstrap.php */