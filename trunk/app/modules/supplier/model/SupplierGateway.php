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
 * @version 	$Id: SupplierGateway.php 1669 2010-03-23 04:48:06Z huuphuoc $
 */

class Tomato_Modules_Supplier_Model_SupplierGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */
	public function convert($entity) 
	{
		return new Tomato_Modules_Supplier_Model_Supplier($entity); 
	}
	
	/**
	 * @param int $id
	 * @return Tomato_Modules_Supplier_Model_Supplier
	 */
	public function getSupplierById($id) 
	{
		$select = $this->_conn
					->select()
					->from(array('c' => $this->_prefix.'supplier'))
					->where('c.supplier_id = ?', $id)
					->limit(1);
		$row = $select->query()->fetchAll();
		$suppliers = new Tomato_Core_Model_RecordSet($row, $this);
		return (count($suppliers) == 0) ? null : $suppliers[0]; 
	}
	
	/**
	 * @param Tomato_Modules_Supplier_Model_Supplier $supplier
	 * @return int
	 */
	public function add($supplier) 
	{
		$data = array(
					'name'			=> $supplier->name,
					'slug'			=> $supplier->slug,
					'meta'			=> $supplier->meta,
					'address'		=> $supplier->address,
					'phone'			=> $supplier->phone,
					'email'			=> $supplier->email,
					'hour_open'		=> $supplier->hour_open,
					'hour_close'	=> $supplier->hour_close,
					'created_date'	=> $supplier->created_date,
					'user_id'		=> $supplier->user_id
		);
		$this->_conn->insert($this->_prefix.'supplier', $data);
		return $this->_conn->lastInsertId($this->_prefix.'supplier');
	}
	
	/**
	 * @param Tomato_Modules_Supplier_Model_Supplier $supplier
	 * @return void
	 */
	public function update($supplier) 
	{
			$where[] = 'supplier_id = '.$this->_conn->quote($supplier->supplier_id);
			$this->_conn->update($this->_prefix.'supplier', array(
					'name'		=> $supplier->name,
					'slug' 		=> $supplier->slug,
					'meta' 		=> $supplier->meta,
					'address'	=> $supplier->address,
					'phone' 	=> $supplier->phone,
					'email' 	=> $supplier->email,
					'hour_open' => $supplier->hour_open,
					'hour_close'=> $supplier->hour_close,
				), $where);
	}
	
	/**
	 * @param Tomato_Modules_Supplier_Model_Supplier $supplier
	 * @return void
	 */
	public function delete($supplier) 
	{
		if ($supplier != null) {
			$where[] = 'supplier_id = '.$this->_conn->quote($supplier->supplier_id);
			$this->_conn->delete($this->_prefix.'supplier', $where);
			
			$sql = 'UPDATE '.$this->_prefix.'supplier SET left_id = left_id - 1, right_id = right_id - 1'
					.' WHERE left_id BETWEEN '.$supplier->left_id.' AND '.$supplier->right_id;
			$this->_conn->query($sql);

			$sql = 'UPDATE '.$this->_prefix.'supplier SET right_id = right_id - 2'
					.' WHERE right_id > '.$this->_conn->quote($supplier->right_id);
			$this->_conn->query($sql);
			
			$sql = 'UPDATE '.$this->_prefix.'supplier SET left_id = left_id - 2'
					.' WHERE left_id > '.$this->_conn->quote($supplier->right_id);
			$this->_conn->query($sql);
		}
	}
	
	/**
	 * Get limit Suppliers
	 * 
	 * @return Tomato_Core_Model_RecordSet
	 */
	public function getSuppliers($start = null, $count = null) {
		$select = $this->_conn->select()->from($this->_prefix . 'supplier' )->order ( 'supplier_id DESC' );
		if (is_int ( $start ) && is_int ( $count )) {
			$select->limit ( $count, $start );
		}
		$rs = $select->query ()->fetchAll ();
		return new Tomato_Core_Model_RecordSet ( $rs, $this );
	}
	
	/**
	 * Count all Suppliers
	 * 
	 * @return int
	 */
	public function count() 
	{
		$select = $this->_conn
				->select()
				->from(array('a' => $this->_prefix.'supplier'), array('num_suppliers' => 'COUNT(*)'));
		$row = $select->query()->fetch();
		return $row->num_suppliers;
	}
	
	/**
	 * @param Tomato_Modules_Supplier_Model_Supplier $comment
	 * @return int
	 */
	public function toggleActive($supplier) 
	{
		$sql = 'UPDATE '.$this->_prefix.'supplier SET is_active = 1 - is_active, 
				activate_date = '.$this->_conn->quote($supplier->activate_date).'
				WHERE supplier_id = '.$this->_conn->quote($supplier->supplier_id);
		return $this->_conn->query($sql);
	}
}
