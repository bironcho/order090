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
 * @version 	$Id: Config.php 958 2010-01-23 03:17:30Z huuphuoc $
 */

class Tomato_Core_Plugin_Config 
{
	/**
	 * Get plugin information
	 * 
	 * @param string $plugin
	 * @return array
	 */
	public static function getPluginInfo($plugin) 
	{
		$plugin = strtolower($plugin);
		$file = TOMATO_APP_DIR.DS.'plugins'.DS.$plugin.DS.'about.xml';
		return self::getPluginInfoFromXml($file);
	}
	
	/**
	 * @param string $file
	 * @return array
	 */
	public static function getPluginInfoFromXml($file) 
	{
		if (!file_exists($file)) {
			return null;
		}
		$xml = simplexml_load_file($file);
		return array(
			'name' => strtolower($xml->name),
			'description' => $xml->description,
			'thumbnail' => $xml->thumbnail,
			'author' => $xml->author,
			'email' => $xml->email,
			'version' => $xml->version,
			'license' => $xml->license,
		);
	}
	
	/**
	 * Get plugin params
	 * 
	 * @param string $plugin
	 * @return array
	 */
	public static function getParams($plugin) 
	{
		$plugin = strtolower($plugin);
		$file = TOMATO_APP_DIR.DS.'plugins'.DS.$plugin.DS.'config.xml';
		if (!file_exists($file)) {
			return null;
		}
		$xml = simplexml_load_file($file);
		if ($xml->param == null || count($xml->param) == 0) {
			return null;
		}
		$params = array();
		foreach ($xml->param as $param) {
			$attr = $param->attributes();
			$params[] = array(
				'name' => $attr['name'],
				'description' => (string)$param->description,
				'value' => (string)$param->value,
			);
		}
		return $params;
	}
	
	/**
	 * Save configured params
	 * 
	 * @param string $plugin
	 * @param array $params
	 */
	public static function saveParams($plugin, $params) 
	{
		$plugin = strtolower($plugin);
		$file = TOMATO_APP_DIR.DS.'plugins'.DS.$plugin.DS.'config.xml';
		if (!file_exists($file)) {
			return null;
		}
		$xml = simplexml_load_file($file);
		foreach ($params as $key => $value) {
			$nodes = $xml->xpath('param[@name="'.addslashes($key).'"]');//->value = $value;
			if ($nodes && is_array($nodes)) {
				$nodes[0]->value = $value;
			}
		}
		$xml->asXML($file);
	}
}