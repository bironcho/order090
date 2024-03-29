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
 * @version 	$Id: Helper.php 1270 2010-02-23 04:45:45Z huuphuoc $
 */

class Utility_Widgets_Twitter_Helper 
{
	public function helper() 
	{
		return $this;
	}
	
	public function format($content) 
	{
		// Replace a string beginning with http:// by a html link
		// http://php.net/manual/en/function.preg-replace.php#85722
		$in = array(
        	'`((?:https?|ftp)://\S+[[:alnum:]]/?)`si',
        	'`((?<!//)(www\.\S+[[:alnum:]]/?))`si'
        );
        $out = array(
        	'<a href="$1">$1</a> ',
        	'<a href="http://$1">$1</a>'
        );
		
		return preg_replace($in, $out, $content);
	}
}
