<?php
class Tomato_Modules_Supplier_Widgets_Newest_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$limit = $this->_request->getParam('limit', 15);
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$supplierGateway = new Tomato_Modules_Supplier_Model_SupplierGateway();
		$supplierGateway->setDbConnection($conn);
		$suppliers = $supplierGateway->getSuppliers(0, $limit);
		$this->_view->assign('suppliers', $suppliers);
	}
}