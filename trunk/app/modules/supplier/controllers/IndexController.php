<?php
class Supplier_IndexController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */

	public function listAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$supplierGateway = new Tomato_Modules_Supplier_Model_SupplierGateway();
		$supplierGateway->setDbConnection($conn);

		
		$perPage = 20;
		$pageIndex = $this->_request->getParam('pageIndex');
		if (null == $pageIndex || '' == $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		$this->view->assign('pageIndex', $pageIndex);
		
		$suppliers = $supplierGateway->getSuppliers($start, $perPage);
		$numSuppliers = $supplierGateway->count();

		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($suppliers, $numSuppliers));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
		    'path' => $this->view->url(array(), 'supplier_index_list'),
		    'itemLink' => 'page-%d',
		));
		$this->view->assign('numSuppliers', $numSuppliers);
		$this->view->assign ( 'suppliers', $suppliers );
	}
	
	public function addAction() 
	{
		$this->view->addHelperPath(TOMATO_APP_DIR.DS.'modules'.DS.'upload'.DS.'views'.DS.'helpers', 'Upload_View_Helper_');
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		
		//Cate Product
		$cateproGateway = new Tomato_Modules_Catepro_Model_CateproGateway();
		$cateproGateway->setDbConnection($conn);
		$categories = $cateproGateway->getCateproTree();
		$this->view->assign('categories', $categories);
		
		$supplierGateway = new Tomato_Modules_Supplier_Model_SupplierGateway();
		$supplierGateway->setDbConnection($conn);
		
		if ($this->_request->isPost()) {
			$user = Zend_Auth::getInstance()->getIdentity();
			$name = $this->_request->getPost('name');
			$slug = $this->_request->getPost('slug');
			$meta = $this->_request->getPost('meta');
			$address = $this->_request->getPost('address');
			$phone = $this->_request->getPost('phone');
			$email = $this->_request->getPost('email');
			$hour_open = $this->_request->getPost('hour_open');
			$hour_close = $this->_request->getPost('hour_close');
			$category_id = $this->_request->getPost('category_id');
			$image_general = $this->_request->getPost('fileImage');
			$supplier = new Tomato_Modules_Supplier_Model_Supplier(array(
				'name'			=> $name,
				'slug'			=> $slug,
				'meta'			=> $meta,
				'address'		=> $address,
				'phone'			=> $phone,
				'email'			=> $email,
				'hour_open'		=> $hour_open,
				'hour_close' 	=> $hour_close,
				'category_id'	=> $category_id,
				'image_general'	=> $image_general,
				'created_date'	=> date('Y-m-d H:i:s'),
				'user_id'		=> $user->user_id,
			));
			$id = $supplierGateway->add($supplier);
			if ($id > 0) {
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('supplier_add_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'supplier_index_add'));
			}
		}
	}
	
	public function editAction() 
	{
		$this->view->addHelperPath(TOMATO_APP_DIR.DS.'modules'.DS.'upload'.DS.'views'.DS.'helpers', 'Upload_View_Helper_');
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		
		//Cate Product
		$cateproGateway = new Tomato_Modules_Catepro_Model_CateproGateway();
		$cateproGateway->setDbConnection($conn);
		$categories = $cateproGateway->getCateproTree();
		$this->view->assign('categories', $categories);
		
		//Supplier
		$supplierGateway = new Tomato_Modules_Supplier_Model_SupplierGateway();
		$supplierGateway->setDbConnection($conn);
		$id = $this->_request->getParam('supplier_id');
		$supplier = $supplierGateway->getSupplierById($id);
		$this->view->assign('supplier', $supplier);
		
		if ($this->_request->isPost()) {
			$name = $this->_request->getPost('name');
			$slug = $this->_request->getPost('slug');
			$meta = $this->_request->getPost('meta');
			$address = $this->_request->getPost('address');
			$phone = $this->_request->getPost('phone');
			$email = $this->_request->getPost('email');
			$hour_open = $this->_request->getPost('hour_open');
			$hour_close = $this->_request->getPost('hour_close');
			$category_id = $this->_request->getPost('category_id');
			$delImage = $this->_request->getPost('delImage');
			$supplier->name = $name;
			$supplier->slug = $slug;
			$supplier->meta = $meta;
			$supplier->address = $address;
			$supplier->phone = $phone;
			$supplier->email = $email;
			$supplier->hour_open = $hour_open;
			$supplier->hour_close = $hour_close;
			$supplier->category_id = $category_id;
			$supplier->modified_date = date('Y-m-d H:i:s');
			$supplier->image_general = $this->_request->getPost('fileImage');
			if(!empty($delImage))
			{
				$supplier->image_general =  'delImage';
			}
			$supplierGateway->update($supplier);
			
			$this->_helper->getHelper('FlashMessenger')->addMessage(
				$this->view->translator('supplier_edit_success')
			);
			$this->_redirect($this->view->serverUrl().$this->view->url(array('supplier_id' => $id), 'supplier_index_edit'));
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
			$supplierGateway = new Tomato_Modules_Supplier_Model_SupplierGateway();
			$supplierGateway->setDbConnection($conn);
			$supplier = $supplierGateway->getSupplierById($id);
			
			if ($supplier != null) {
				$supplierGateway->delete($supplier);
				$response = 'RESULT_OK';
			}
		}
		$this->getResponse()->setBody($response);
	}
	
	public function activateAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($this->_request->isPost()) {
			$id = $this->_request->getPost('id');
						
			$conn = Tomato_Core_Db_Connection::getMasterConnection();
			$gateway = new Tomato_Modules_Supplier_Model_SupplierGateway();	
			$gateway->setDbConnection($conn);
			$supplier = $gateway->getSupplierById($id);
			if (null == $supplier) {
				$this->_response->setBody('RESULT_NOT_FOUND');
				return;
			}
			$supplier->activate_date = date('Y-m-d H:i:s');
			$gateway->toggleActive($supplier);
			$isActive = 1 - $supplier->is_active;
			$data = array(
				'name' 	=> $this->view->escape($supplier->name),
				'is_active'	=> $isActive,
			);
			$this->_response->setBody(Zend_Json::encode($data));			
		}
	}
	
	/* ========== Front actions =========================================== */
	public function viewAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		//Supplier
		$supplierGateway = new Tomato_Modules_Supplier_Model_SupplierGateway();
		$supplierGateway->setDbConnection($conn);
		$id = $this->_request->getParam('supplier_id');
		$supplier = $supplierGateway->getSupplierById($id);
		$this->view->assign('supplier', $supplier);
	}
	    
	public function cateAction() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$supplierGateway = new Tomato_Modules_Supplier_Model_SupplierGateway();
		$supplierGateway->setDbConnection($conn);
		
		//CatePro
		$categoryId = $this->_request->getParam('category_id');
		$cateproGateway = new Tomato_Modules_Catepro_Model_CateproGateway();
		$cateproGateway->setDbConnection($conn);
		$category = $cateproGateway->getCateproById($categoryId);	
		$this->view->assign('category', $category);
		
		$perPage = 20;
		$pageIndex = $this->_request->getParam('pageIndex');
		if (null == $pageIndex || '' == $pageIndex) {
			$pageIndex = 1;
		}
		$start = ($pageIndex - 1) * $perPage;
		$this->view->assign('pageIndex', $pageIndex);
		
		//Supplier
		$select = $conn->select()
						->from(array('s' => Tomato_Core_Db_Connection::getDbPrefix().'supplier'), array('supplier_id','category_id','name'))
						->where('s.category_id = ?', $categoryId)
						->where('s.is_active = ?', 1)
						->order('s.activate_date DESC')
						->limit($perPage, $start);
		$rs = $select->query()->fetchAll();
		$suppliers = new Tomato_Core_Model_RecordSet($rs, $supplierGateway);
		$this->view->assign('suppliers', $suppliers);
		
		//Pagging
		$select = $conn
				->select()
				->from(array('s' => Tomato_Core_Db_Connection::getDbPrefix().'supplier'), array('num_suppliers' => 'COUNT(*)'))
				->where('s.category_id = ?', $categoryId)
				->where('s.is_active = ?', 1);
		$row = $select->query()->fetch();
		$numSuppliers =  $row->num_suppliers;
		$paginator = new Zend_Paginator(new Tomato_Core_Utility_PaginatorAdapter($suppliers, $numSuppliers));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
		    'path' => $this->view->url(array(), 'supplier_index_list'),
		    'itemLink' => 'page-%d',
		));
		$this->view->assign('numSuppliers', $numSuppliers);
		
	}
}
	