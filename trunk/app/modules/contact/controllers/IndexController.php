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
 * @version 	$Id: CommentController.php 2040 2010-04-02 08:13:30Z hoangninh $
 */

class Contact_IndexController extends Zend_Controller_Action {
	/* ========== Backend actions =========================================== */
	
	public function listAction() {
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$contactGateway = new Tomato_Modules_Contact_Model_ContactGateway();
		$contactGateway->setDbConnection($conn);
		
		$perPage = 2;
		$pageIndex = $this->_request->getParam('pageIndex');
		if (null == $pageIndex || '' == $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		$this->view->assign('pageIndex', $pageIndex);
		
		$contacts = $contactGateway->getContacts($start, $perPage);
		$numContacts = $contactGateway->count();

		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($contacts, $numContacts));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
		    'path' => $this->view->url(array(), 'contact_index_list'),
		    'itemLink' => 'page-%d',
		));
		$this->view->assign ( 'contacts', $contacts );
	}
	public function addAction() {
	
	}

}
