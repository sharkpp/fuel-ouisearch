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

namespace Fuel\Tasks;

/**
 * Run scaffolding or model generation based an an existing database tables.
 *
 * Based on https://github.com/mp-php/fuel-myapp/blob/master/tasks/scafdb.php
 *
 * @author     sharkpp
 * @copyright  2013+ sharkpp
 * @license    MIT License
 * @link       https://www.sharkpp.net/
 */
class ImportOui
{
	private static $table_name;

	/**
	 * Class initialization
	 */
	public function __construct()
	{
		// load the migrations config
		\Config::load('ouisearch', true);
	
		static::$table_name = \Config::get('ouisearch.table_name');
	}

	/**
	 * Show help.
	 *
	 * Usage (from command line):
	 *
	 * php oil refine importoui
	 */
	public static function run()
	{
		// read oui.txt and insert to DB
		$path = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'oui.txt';
		if (!file_exists($path))
		{
			$path = dirname($path).DIRECTORY_SEPARATOR;
			$output =<<<EOD
oui.txt not found.
Please download oui.txt from http://standards.ieee.org/develop/regauth/oui/oui.txt
And save to "$path"
EOD;
			\Cli::write($output);
			exit();
		}
		$oui_lists = file_get_contents($path);
		$oui_lists = str_replace("\r", "\n", str_replace("\r\n", "\n", $oui_lists));
		$oui_lists = explode("\n", $oui_lists);

		// insert to DB
		$count_of_insert = 0;
		\DB::start_transaction();
		foreach ($oui_lists as $line)
		{
			if (preg_match('/\s*(([0-9A-Fa-f]{2}-){2}[0-9A-Fa-f]{2})\s+[^\s]+\s+(.+)/', $line, $m))
			{
				$oui = strtoupper($m[1]);
				$organization = $m[3];
				$result = \DB::select()
							-> from(static::$table_name)
							-> where('oui', '=', $oui)
							-> execute();
				if (!$result->count())
				{
					$query = \DB::insert(static::$table_name);
					$count_of_insert++;
				}
				else if ($result->get('organization') != $organization)
				{
					$query = \DB::update(static::$table_name)
								-> where('id', '=', $result->get('id'));
				}
				else
				{ // skip, if organization equal
					continue;
				}
				$query
					-> set(array(
							'oui' => $oui,
							'organization' => $organization,
						))
					-> execute();
			}
		}
		\DB::commit_transaction();

		$output =<<<EOD
oui.txt import completed!
Insert $count_of_insert records.
EOD;
		\Cli::write($output);
	}
}
