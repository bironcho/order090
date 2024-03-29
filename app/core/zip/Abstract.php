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
 * @version 	$Id: Abstract.php 1664 2010-03-22 06:15:38Z huuphuoc $
 * @since		2.0.4
 */

abstract class Tomato_Core_Zip_Abstract
{
	/**
	 * @var string
	 */
	protected $_file;
	
	public function __construct($file)
	{
		$this->_file = $file;
	}
	
	abstract public function open();
	
	abstract public function close();
	
	/**
	 * Extract to destination directory
	 * @param string $destination
	 */
	abstract public function extract($destination);
}
