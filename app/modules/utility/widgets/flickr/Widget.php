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
 * @version 	$Id: Widget.php 1340 2010-02-28 16:17:29Z huuphuoc $
 * @since		2.0.3
 */

class Tomato_Modules_Utility_Widgets_Flickr_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$apiKey = $this->_request->getParam('apiKey');
		$username = $this->_request->getParam('username');
		$limit = $this->_request->getParam('limit', 6);
		$flickr = new Zend_Service_Flickr($apiKey);
		$resultSet = $flickr->userSearch($username, array('page' => 1, 'per_page' => $limit));
		$this->_view->assign('resultSet', $resultSet);
	}
}