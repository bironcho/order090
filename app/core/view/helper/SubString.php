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
 * @version 	$Id: SubString.php 958 2010-01-23 03:17:30Z huuphuoc $
 */

class Tomato_Core_View_Helper_SubString 
{
	public function subString($string, $limit = 100, $after = '...') 
	{
		if (null == $string) {
			return '';
		}
		if (strlen($string) <= $limit) {
			return $string;
		}
		
		// Check if module mbstring is already installed or not
		if (function_exists('mb_substr')) {
			return mb_substr($string, 0, $limit, 'UTF-8').$after;			
		} else {
			return substr($string, 0, $limit).$after;
		}
	}
}