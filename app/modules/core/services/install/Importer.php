<?php
/**
 * TomatoCMS
 *
 * LICENSE
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE Version 2
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-2.0.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@tomatocms.com so we can send you a copy immediately.
 *
 * @copyright	Copyright (c) 2009-2010 TIG Corporation (http://www.tig.vn)
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU GENERAL PUBLIC LICENSE Version 2
 * @version 	$Id: Importer.php 1965 2010-04-02 06:34:59Z huuphuoc $
 * @since		2.0.4
 */

/**
 * Import sample data from SQL file
 * Based on the algorithm provied by BigDump (http://www.ozerov.de/bigdump.php)
 */
class Tomato_Modules_Core_Services_Install_Importer
{
	/**
	 * Array of comment characters
	 */
	private static $_COMMENTS = array(
		'#',
		'--',
		'/*!'
	);
	
	/**
	 * @param string $file SQL file to import
	 */
	public static function import($file)
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$config = $conn->getConfig(); 
		mysql_connect($config['host'].':'.$config['port'], $config['username'], $config['password']);
		mysql_select_db($config['dbname']);
		
		$queries = self::_parse($file);
		foreach ($queries as $query) {
			mysql_query($query);
			/**
		 	 * FIXME: Use PDO instead of normal MySQL connection
		 	 * 	try {
		 	 * 		$conn->beginTransaction();
		 	 * 		$conn->query($query);
		 	 * 		$conn->commit();
		 	 * 	} cactch (Exception $ex) {
		 	 * 		$conn->rollBack()	
		 	 * 	}
		     */
		}
		mysql_close();
	}
	
	private static function _parse($file)
	{
		$queries = array();
		
		@ini_set('auto_detect_line_endings', true);
		@set_time_limit(0);

		$f = fopen($file, 'r');
		
		// Fize position
		$offset = 0;
		
		// Get the file size
		$filesize = filesize($file);
		
		// Query
		$query = '';
		
		while ($offset <= $filesize || $query != '') {
			$inParents = false;
			
			// Jump to position
			fseek($f, $offset);
			
			$dumpline = '';
			$temp = '';
			while (!feof($f) && substr($temp, -1) != "\n" && substr($temp, -1) != "\r") {
				$temp = fgets($f, 4096);
				$dumpline .= $temp;
			}
			if ($dumpline == '') {
				break;
			}
			$dumpline = str_replace("\r\n", "\n", $dumpline);
			$dumpline = str_replace("\r", "\n", $dumpline);
			
			if (!$inParents) {
				$skipLine = false;
				foreach (self::$_COMMENTS as $comment) {
					$pos = strpos($dumpline, $comment);
					if (!$inParents && (trim($dumpline) == '' || (is_int($pos) && $pos == 0))) {
						$skipLine = true;
						break;
					}
				}
				if ($skipLine) {
					$dumpline = '';
				}
			}

			$sqlDeslashed = str_replace("\\\\", "", $dumpline);
      		$parents = substr_count($sqlDeslashed, "'") - substr_count($sqlDeslashed, "\\'");
      		if ($parents % 2 != 0) {
        		$inParents = !$inParents;
      		}
      		$query .= $dumpline;
      		
			if (preg_match("/;$/", trim($query)) && !$inParents) {
				$query = self::_addPrefix($query);
				$queries[] = $query;
				
				// Reset query
				$query = '';
			}
			
      		$offset = ftell($f);
		}
		fclose($f);
		
		return $queries;
	}
	
	/**
	 * Try to find a table name in SQL and replace it with the one including prefix
	 * @param string $sql 
	 */
	private static function _addPrefix($sql)
	{
		$prefix = Tomato_Core_Db_Connection::getDbPrefix();
		$queries = array(
			'/DROP(\s+)TABLE(\s+)IF(\s+)EXISTS(\s+)`([\w_]+)`;/' 	=> 'DROP TABLE IF EXISTS `'.$prefix.'$5`;',
			'/CREATE(\s+)TABLE(\s+)`([\w_]+)`/' 					=> 'CREATE TABLE `'.$prefix.'$3`',
			'/LOCK(\s+)TABLES(\s+)`([\w_]+)`(\s+)WRITE;/' 			=> 'LOCK TABLES `'.$prefix.'$3` WRITE;',
			'/INSERT(\s+)INTO(\s+)`([\w_]+)`(\s+)VALUES/'			=> 'INSERT INTO `'.$prefix.'$3` VALUES',
		);
		foreach ($queries as $search => $replace) {
			$sql = preg_replace($search, $replace, $sql);
		}
		return $sql;
	}
}
