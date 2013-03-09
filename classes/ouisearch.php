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

namespace OuiSearch;

/**
 * LdapAuth basic login driver
 *
 * @package     Fuel
 * @subpackage  Auth
 */
class OuiSearch
{
	protected static $table_name;

	/**
	 * Initialization when loading class
	 */
	public static function _init()
	{
		// load the migrations config
		\Config::load('ouisearch', true);

		static::$table_name = \Config::get('ouisearch.table_name');
	}

	/**
	 * Get organization name by OUI or MAC address
	 *
	 * @param	string			$oui_or_mac		OUI or MAC address for search organization name
	 * @return  bool|string		organization name or false
	 */
	public static function lookup($oui_or_mac)
	{
		// normarize to OUI
		if (preg_match('/^[0-9A-Fa-f]{6}$/',  $oui_or_mac) || // XXXXXX
		    preg_match('/^[0-9A-Fa-f]{12}$/', $oui_or_mac))   // XXXXXXXXXXXX
		{
			$oui = strtoupper(implode('-', str_split(substr($oui_or_mac, 0, 2*3), 2)));
		}
		else if (preg_match('/^([0-9A-Fa-f]{2}-){2}[0-9A-Fa-f]{2}$/', $oui_or_mac) || // XX-XX-XX
		         preg_match('/^([0-9A-Fa-f]{2}:){2}[0-9A-Fa-f]{2}$/', $oui_or_mac) || // XX:XX:XX
		         preg_match('/^([0-9A-Fa-f]{2}-){5}[0-9A-Fa-f]{2}$/', $oui_or_mac) || // XX-XX-XX-XX-XX-XX
		         preg_match('/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/', $oui_or_mac))   // XX:XX:XX:XX:XX:XX
		{
			$oui = strtoupper(substr(str_replace(':', '-', $oui_or_mac), 0, 2*3+2));
		}
		else
		{
			return false;
		}

		// search DB
		$result = \DB::select()
					-> from(static::$table_name)
					-> where('oui', '=', $oui)
					-> execute();
		return
			!$result->count()
				? false
				: $result->get('organization');
	}

	/**
	 * Search organization name by OUI or MAC address
	 *
	 * @param	string			$oui_or_mac		OUI or MAC address part for search organization name
	 * @param	int				$limit			result limit
	 * @return  bool|array		OUI and organization name
	 */
	public static function search_organization($oui_or_mac, $limit = -1)
	{
		// normarize to OUI
		if (preg_match('/^([0-9A-Fa-f]{2}-){0,5}[0-9A-Fa-f]{2}$/', $oui_or_mac) || // - XX-XX-XX-XX-XX-XX
		    preg_match('/^([0-9A-Fa-f]{2}:){0,5}[0-9A-Fa-f]{2}$/', $oui_or_mac))   // - XX:XX:XX:XX:XX:XX
		{
			$oui = strtoupper(substr(str_replace(':', '-', $oui_or_mac), 0, 2*3+2));
		}
		else if (preg_match('/^[0-9A-Fa-f]{0,12}$/', $oui_or_mac))   // X - XXXXXXXXXXXX
		{
			$oui = strtoupper(implode('-', str_split(substr($oui_or_mac, 0, 2*3), 2)));
		}
		else
		{
			return array();
		}

		// search DB
		$query = \DB::select('oui', 'organization')
					-> from(static::$table_name)
					-> where('oui', 'like', $oui.'%')
					;
		if (0 < $limit) {
			$query = $query->limit($limit);
		}
		$result = array();
		foreach ($query->execute() as $row)
		{
			$result[$row['oui']] = $row['organization'];
		}
		return $result;
	}

	/**
	 * Search OUI by organization
	 *
	 * @param	string			$organization	organization for OUI search
	 * @param	int				$limit			result limit
	 * @return  bool|array		OUI and organization name
	 */
	public static function search_oui($organization, $limit = -1)
	{
		// search DB
		$query = \DB::select('oui', 'organization')
					-> from(static::$table_name)
					-> where('organization', 'like', '%'.$organization.'%')
					;
		if (0 < $limit) {
			$query = $query->limit($limit);
		}
		$result = array();
		foreach ($query->execute() as $row)
		{
			$result[$row['oui']] = $row['organization'];
		}
		return $result;
	}

}

// end of file ldapauth.php
