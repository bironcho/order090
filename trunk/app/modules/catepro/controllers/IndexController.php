<?php
class Catepro_IndexController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */

	public function listAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$cateproGateway = new Tomato_Modules_Catepro_Model_CateproGateway();
		$cateproGateway->setDbConnection($conn);
		$categories = $cateproGateway->getCateproTree();
		$this->view->assign('categories', $categories);
	}
	
	public function addAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$cateproGateway = new Tomato_Modules_Catepro_Model_CateproGateway();
		$cateproGateway->setDbConnection($conn);
		$categories = $cateproGateway->getCateproTree();
		$this->view->assign('categories', $categories);
		
		if ($this->_request->isPost()) {
			$user = Zend_Auth::getInstance()->getIdentity();
			$name = $this->_request->getPost('name');
			$slug = $this->_request->getPost('slug');
			$parentId = $this->_request->getPost('parentId');
			$meta = $this->_request->getPost('meta');
			$catepro = new Tomato_Modules_Catepro_Model_Catepro(array(
				'name'			=> $name,
				'slug'			=> $slug,
				'meta'			=> $meta,
				'created_date'	=> date('Y-m-d H:i:s'),
				'user_id'		=> $user->user_id,
			));
			$id = $cateproGateway->add($catepro, $parentId);
			if ($id > 0) {
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('category_add_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'catepro_add'));
			}
		}
	}
	
	public function editAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$cateproGateway = new Tomato_Modules_Catepro_Model_CateproGateway();
		$cateproGateway->setDbConnection($conn);
		$categories = $cateproGateway->getCateproTree();
		$this->view->assign('categories', $categories);
		
		$id = $this->_request->getParam('category_id');
		$catepro = $cateproGateway->getCateproById($id);
		$this->view->assign('category', $catepro);
		
		if ($this->_request->isPost()) {
			$deleteCatepro = true;
			$name = $this->_request->getPost('name');
			$slug = $this->_request->getPost('slug');
			$parentId = $this->_request->getPost('parentId');
			$meta = $this->_request->getPost('meta');
			$includeChildCatepro = $this->_request->getPost('include_child_catepro');
			$includeChildCatepro = ($includeChildCatepro == 1) ? true : false;
			
			$catepro->name = $name;
			$catepro->slug = $slug;
			$catepro->meta = $meta;
			$catepro->modified_date = date('Y-m-d H:i:s');

			/**
			 * Check change parent catepro
			 */
			$curParent = $cateproGateway->getCateproParent($catepro);
			if ((null == $curParent && $parentId == 0) 
					|| ($curParent != null && $curParent->catepro_id == $parentId)) {
				$deleteCatepro = false;
			}
			$cateproGateway->update($catepro, $parentId, $deleteCatepro, $includeChildCatepro);
			$this->_helper->getHelper('FlashMessenger')->addMessage(
				$this->view->translator('category_edit_success')
			);
			$this->_redirect($this->view->serverUrl().$this->view->url(array('category_id' => $id), 'catepro_edit'));
		}
	}
	
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$response = 'RESULT_ERROR';
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$cateproGateway = new Tomato_Modules_Catepro_Model_CateproGateway();
			$cateproGateway->setDbConnection($conn);
			$catepro = $cateproGateway->getCateproById($id);
			
			if ($catepro != null) {
				$cateproGateway->delete($catepro);
				$response = 'RESULT_OK';
			}
		}
		$this->getResponse()->setBody($response);
	}
}
	