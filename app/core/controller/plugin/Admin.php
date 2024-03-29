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
 * @version 	$Id: Admin.php 958 2010-01-23 03:17:30Z huuphuoc $
 */

class Tomato_Core_Controller_Plugin_Admin extends Zend_Controller_Plugin_Abstract 
{
	public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
		$uri = $request->getRequestUri();
		$uri = strtolower($uri);
		$uri = rtrim($uri, '/').'/';
		if (strpos($uri, '/admin/') === false) {
			return;
		}
		// Auto switch to admin layout
		if (Zend_Layout::getMvcInstance() != null) {
			Zend_Layout::getMvcInstance()->setLayout('admin');
		}
	}
}