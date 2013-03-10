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
 * OuiSearch package tests
 *
 * @group Package
 * @group OuiSearchPackage
 */
class Tests_OuiSearch extends \TestCase
{
//	protected $fixture;

	public function setup()
	{
		// load the migrations config
		\Config::load('ouisearch', true);

		$table_name = \Config::get('ouisearch.table_name');

		// load fixture
		$path = dirname(__FILE__) . '/fixture.yml';
		if (!file_exists($path))
		{
			exit('No such file: ' . $path . PHP_EOL);
		}
		$data = file_get_contents($path);
		$fixture = \Format::forge($data, 'yaml')->to_array();

		// truncate table
		if (\DBUtil::table_exists($table_name))
		{
			\DBUtil::truncate_table($table_name);
		}
		else
		{
			\Migrate::latest('ouisearch', 'package');
		}

		// insert data
		foreach ($fixture as $row)
		{
			list($insert_id, $rows_affected)
				= \DB::insert($table_name)
					-> set($row)
					-> execute();
		}
	}

	/**
	 * Tests OuiSearch::lookup()
	 *
	 * @test
	 */
	public function test_lookup()
	{
		$test = OuiSearch::lookup('00-00-00');
		$this->assertEquals('HOGE CORPORATION', $test);

		$test = OuiSearch::lookup('00:00:00');
		$this->assertEquals('HOGE CORPORATION', $test);

		$test = OuiSearch::lookup('00-00-04-FF-AA-BB');
		$this->assertEquals('FUGA CORPORATION', $test);

		$test = OuiSearch::lookup('00:00:04:FF:FF:FF');
		$this->assertEquals('FUGA CORPORATION', $test);

		$test = OuiSearch::lookup('ff-ff-ff');
		$this->assertEquals('FOO CORPORATION OF JAPAN', $test);

		$test = OuiSearch::lookup('ffffff');
		$this->assertEquals('FOO CORPORATION OF JAPAN', $test);

		$test = OuiSearch::lookup('FF-FF-FF');
		$this->assertEquals('FOO CORPORATION OF JAPAN', $test);

		$test = OuiSearch::lookup('FFFFFF');
		$this->assertEquals('FOO CORPORATION OF JAPAN', $test);

		$test = OuiSearch::lookup('00-00-10');
		$this->assertFalse($test);

		$test = OuiSearch::lookup('00-00-00-AA');
		$this->assertFalse($test);

		$test = OuiSearch::lookup('00-00-00-AA-CC');
		$this->assertFalse($test);

		$test = OuiSearch::lookup('00:00-04');
		$this->assertFalse($test);

		$test = OuiSearch::lookup('00-00:04');
		$this->assertFalse($test);

		$test = OuiSearch::lookup('00:00-01-FF-AA-BB');
		$this->assertFalse($test);

		$test = OuiSearch::lookup('00-00:01:FF:AA:BB');
		$this->assertFalse($test);

		$test = OuiSearch::lookup('HOGE CORPORATION');
		$this->assertFalse($test);
	}

	/**
	 * Tests OuiSearch::search_organization()
	 *
	 * @test
	 */
	public function test_search_organization()
	{
		$test     = OuiSearch::search_organization('');
		$expected = array('00-00-00' => 'HOGE CORPORATION',
		                  '00-00-01' => 'HOGE CORPORATION',
		                  '00-00-02' => 'HOGE CORPORATION',
		                  '00-00-03' => 'HOGE CORPORATION',
		                  '00-00-04' => 'FUGA CORPORATION',
		                  '00-01-04' => 'FUGA CORPORATION',
		                  'FF-FF-FF' => 'FOO CORPORATION OF JAPAN',
		                  'FF-01-FF' => 'XXX');
		ksort($test);
		ksort($expected);
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_organization('00-00-00');
		$expected = array('00-00-00' => 'HOGE CORPORATION');
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_organization('00:00:00');
		$expected = array('00-00-00' => 'HOGE CORPORATION');
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_organization('0');
		$expected = array('00-00-00' => 'HOGE CORPORATION',
		                  '00-00-01' => 'HOGE CORPORATION',
		                  '00-00-02' => 'HOGE CORPORATION',
		                  '00-00-03' => 'HOGE CORPORATION',
		                  '00-00-04' => 'FUGA CORPORATION',
		                  '00-01-04' => 'FUGA CORPORATION');
		ksort($test);
		ksort($expected);
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_organization('00');
		$expected = array('00-00-00' => 'HOGE CORPORATION',
		                  '00-00-01' => 'HOGE CORPORATION',
		                  '00-00-02' => 'HOGE CORPORATION',
		                  '00-00-03' => 'HOGE CORPORATION',
		                  '00-00-04' => 'FUGA CORPORATION',
		                  '00-01-04' => 'FUGA CORPORATION');
		ksort($test);
		ksort($expected);
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_organization('00', 3);
		$expected = array('00-00-00' => 'HOGE CORPORATION',
		                  '00-00-01' => 'HOGE CORPORATION',
		                  '00-00-02' => 'HOGE CORPORATION');
		ksort($test);
		ksort($expected);
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_organization('00-00');
		$expected = array('00-00-00' => 'HOGE CORPORATION',
		                  '00-00-01' => 'HOGE CORPORATION',
		                  '00-00-02' => 'HOGE CORPORATION',
		                  '00-00-03' => 'HOGE CORPORATION',
		                  '00-00-04' => 'FUGA CORPORATION');
		ksort($test);
		ksort($expected);
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_organization('00:00');
		$expected = array('00-00-00' => 'HOGE CORPORATION',
		                  '00-00-01' => 'HOGE CORPORATION',
		                  '00-00-02' => 'HOGE CORPORATION',
		                  '00-00-03' => 'HOGE CORPORATION',
		                  '00-00-04' => 'FUGA CORPORATION');
		ksort($test);
		ksort($expected);
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_organization('00-00-04-FF-AA-BB');
		$expected = array('00-00-04' => 'FUGA CORPORATION');
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_organization('00-00-04-FF-AA');
		$expected = array('00-00-04' => 'FUGA CORPORATION');
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_organization('00:00:04:FF:FF:FF');
		$expected = array('00-00-04' => 'FUGA CORPORATION');
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_organization('00:00:04:FF:FF');
		$expected = array('00-00-04' => 'FUGA CORPORATION');
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_organization('ff-ff-ff');
		$expected = array('FF-FF-FF' => 'FOO CORPORATION OF JAPAN');
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_organization('ffffff');
		$expected = array('FF-FF-FF' => 'FOO CORPORATION OF JAPAN');
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_organization('FF-FF-FF');
		$expected = array('FF-FF-FF' => 'FOO CORPORATION OF JAPAN');
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_organization('FFFFFF');
		$expected = array('FF-FF-FF' => 'FOO CORPORATION OF JAPAN');
		$this->assertEquals($expected, $test);

		$test = OuiSearch::search_organization('00-00-10');
		$this->assertEquals(array(), $test);

		$test = OuiSearch::search_organization('00-00-00-AA');
		$expected = array('00-00-00' => 'HOGE CORPORATION');
		$this->assertEquals($expected, $test);

		$test = OuiSearch::search_organization('00-00-00-AA-CC');
		$expected = array('00-00-00' => 'HOGE CORPORATION');
		$this->assertEquals($expected, $test);

		$test = OuiSearch::search_organization('00:00-04');
		$this->assertEquals(array(), $test);

		$test = OuiSearch::search_organization('00-00:04');
		$this->assertEquals(array(), $test);

		$test = OuiSearch::search_organization('00:00-01-FF-AA-BB');
		$this->assertEquals(array(), $test);

		$test = OuiSearch::search_organization('00-00:01:FF:AA:BB');
		$this->assertEquals(array(), $test);

		$test = OuiSearch::search_organization('HOGE CORPORATION');
		$this->assertEquals(array(), $test);
	}

	/**
	 * Tests OuiSearch::search_oui()
	 *
	 * @test
	 */
	public function test_search_oui()
	{
		$test     = OuiSearch::search_oui('');
		$expected = array('00-00-00' => 'HOGE CORPORATION',
		                  '00-00-01' => 'HOGE CORPORATION',
		                  '00-00-02' => 'HOGE CORPORATION',
		                  '00-00-03' => 'HOGE CORPORATION',
		                  '00-00-04' => 'FUGA CORPORATION',
		                  '00-01-04' => 'FUGA CORPORATION',
		                  'FF-FF-FF' => 'FOO CORPORATION OF JAPAN',
		                  'FF-01-FF' => 'XXX');
		ksort($test);
		ksort($expected);
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_oui('CORPORATION');
		$expected = array('00-00-00' => 'HOGE CORPORATION',
		                  '00-00-01' => 'HOGE CORPORATION',
		                  '00-00-02' => 'HOGE CORPORATION',
		                  '00-00-03' => 'HOGE CORPORATION',
		                  '00-00-04' => 'FUGA CORPORATION',
		                  '00-01-04' => 'FUGA CORPORATION',
		                  'FF-FF-FF' => 'FOO CORPORATION OF JAPAN');
		ksort($test);
		ksort($expected);
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_oui('HOGE');
		$expected = array('00-00-00' => 'HOGE CORPORATION',
		                  '00-00-01' => 'HOGE CORPORATION',
		                  '00-00-02' => 'HOGE CORPORATION',
		                  '00-00-03' => 'HOGE CORPORATION');
		ksort($test);
		ksort($expected);
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_oui('HOGE', 2);
		$expected = array('00-00-00' => 'HOGE CORPORATION',
		                  '00-00-01' => 'HOGE CORPORATION');
		ksort($test);
		ksort($expected);
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_oui('XXX');
		$expected = array('FF-01-FF' => 'XXX');
		ksort($test);
		ksort($expected);
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_oui('FUGA');
		$expected = array('00-00-04' => 'FUGA CORPORATION',
		                  '00-01-04' => 'FUGA CORPORATION');
		ksort($test);
		ksort($expected);
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_oui('JAPAN');
		$expected = array('FF-FF-FF' => 'FOO CORPORATION OF JAPAN');
		ksort($test);
		ksort($expected);
		$this->assertEquals($expected, $test);

		$test     = OuiSearch::search_oui('BAR');
		$expected = array();
		$this->assertEquals($expected, $test);
	}
}
