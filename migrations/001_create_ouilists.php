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

namespace Fuel\Migrations;

class Create_ouilists
{
	/**
	 * Class initialization
	 */
	public function __construct()
	{
		// load the migrations config
		\Config::load('ouisearch', true);
	}

	public function up()
	{
		\DBUtil::create_table(
			\Config::get('ouisearch.table_name'),
			array(
				'id'           => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
				'oui'          => array('constraint' => 8, 'type' => 'varchar'),
				'organization' => array('type' => 'text'),
			//	'created_at'   => array('constraint' => 11, 'type' => 'int', 'null' => true),
			//	'updated_at'   => array('constraint' => 11, 'type' => 'int', 'null' => true),
			), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table(\Config::get('ouisearch.table_name'));
	}
}
