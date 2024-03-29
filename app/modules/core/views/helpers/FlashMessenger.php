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
 * @version 	$Id: FlashMessenger.php 964 2010-01-23 04:05:48Z huuphuoc $
 */

class Core_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract 
{
	public function flashMessenger() 
	{
		$this->view->addScriptPath(Zend_Layout::getMvcInstance()->getLayoutPath());
		
		$flashMsgHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
		$this->view->assign('messages', $flashMsgHelper->getMessages()); 
		
		$this->view->addScriptPath(TOMATO_APP_DIR.DS.'modules'.DS.'core'.DS.'views'.DS.'scripts');
		return $this->view->render('_partial/_messages.phtml');
	}
}