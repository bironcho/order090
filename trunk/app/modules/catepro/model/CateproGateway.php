<?php
class Tomato_Modules_Catepro_Model_CateproGateway extends Tomato_Core_Model_Gateway 
{
	/**
	 * (non-PHPdoc)
	 * @see app/core/model/Tomato_Core_Model_Gateway#convert($entity)
	 */
	public function convert($entity) 
	{
		return new Tomato_Modules_Catepro_Model_Catepro($entity); 
	}
	
	/**
	 * @param int $id
	 * @return Tomato_Modules_Catepro_Model_Catepro
	 */
	public function getCateproById($id) 
	{
		$select = $this->_conn
					->select()
					->from(array('c' => $this->_prefix.'catepro'))
					->where('c.category_id = ?', $id)
					->limit(1);
		$row = $select->query()->fetchAll();
		$categories = new Tomato_Core_Model_RecordSet($row, $this);
		return (count($categories) == 0) ? null : $categories[0]; 
	}
	
	/**
	 * Get sub-categories of given catepro
	 * 
	 * @param int $cateproId Catepro id
	 * @return Tomato_Core_Model_RecordSet
	 */
	public function getSubCategories($cateproId, $depth = 1) 
	{
		$sql = 'SELECT node.category_id, node.slug, node.name, (COUNT(parent.name) - (sub_tree.depth + 1)) AS depth,
						node.left_id, node.right_id
				FROM '.$this->_prefix.'catepro AS node,
					'.$this->_prefix.'catepro AS parent,
					'.$this->_prefix.'catepro AS sub_parent,
					(
						SELECT node.category_id, (COUNT(parent.name) - 1) AS depth
						FROM '.$this->_prefix.'catepro AS node,
						'.$this->_prefix.'catepro AS parent
						WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
						AND node.category_id = '.$this->_conn->quote($cateproId).'
						GROUP BY node.name
						ORDER BY node.left_id
					) AS sub_tree
				WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
				AND node.left_id BETWEEN sub_parent.left_id AND sub_parent.right_id
				
				AND sub_parent.category_id = sub_tree.category_id
				GROUP BY node.category_id';
		if ($depth) {
			$sql .= ' HAVING depth <= 1';
		}
		$sql .= ' ORDER BY node.left_id';
		
		$rs = $this->_conn->query($sql)->fetchAll();
		return new Tomato_Core_Model_RecordSet($rs, $this);
	}
	
	/**
	 * @param Tomato_Modules_Catepro_Model_Catepro $catepro
	 * @param int $parentId
	 * @return int
	 */
	public function add($catepro, $parentId = null) 
	{
		if ($parentId) {
			$sql = 'SELECT right_id FROM '.$this->_prefix.'catepro WHERE category_id = '.$this->_conn->quote($parentId);
		} else {
			$sql = 'SELECT MAX(right_id) as right_id FROM '.$this->_prefix.'catepro';
		}
		$right = $this->_conn->query($sql)->fetch();
		$rightId = ($parentId) ? $right->right_id : $right->right_id + 1;
		if ($rightId != null) {
			$sql = 'UPDATE '.$this->_prefix.'catepro SET left_id = IF(left_id > 
					'.$this->_conn->quote($rightId).', left_id + 2, left_id), 
					right_id = IF(right_id >= '.$this->_conn->quote($rightId).', 
					right_id + 2, right_id)';
			$this->_conn->query($sql);
			$data = array(
						'name'			=> $catepro->name,
						'slug'			=> $catepro->slug,
						'meta'			=> $catepro->meta,
						'created_date'	=> $catepro->created_date,
						'user_id'		=> $catepro->user_id,
						'left_id'		=> $rightId,
						'right_id'		=> $rightId + 1,
			);
			if (isset($catepro->category_id) && $catepro->category_id != null) {
				$data['category_id'] = $catepro->category_id;
			}
			$this->_conn->insert($this->_prefix.'catepro', $data);
			return $this->_conn->lastInsertId($this->_prefix.'catepro');
		}
	}
	
	/**
	 * @param Tomato_Modules_Catepro_Model_Catepro $catepro
	 * @param int $parentId
	 * @param bool $deleteCatepro
	 * @param bool $includeChild
	 * @return void
	 */
	public function update($catepro, $parentId = null, $deleteCatepro = false, $includeChild = true) 
	{
		if ($deleteCatepro) {
			if ($includeChild) {
				$oldCategories = $this->getSubCategories($catepro->category_id, null);
				if (count($oldCategories) > 0) {
					/**
					 * Delete catepro
					 */
					$width = $catepro->right_id - $catepro->left_id + 1;
					$sql = 'DELETE FROM '.$this->_prefix.'catepro 
								WHERE left_id BETWEEN '.$catepro->left_id.' 
													AND '.$catepro->right_id;
					$this->_conn->query($sql);
					$sql = 'UPDATE '.$this->_prefix.'catepro 
								SET right_id = right_id - '.$width.' 
								WHERE right_id > '.$catepro->right_id;
					$this->_conn->query($sql);
					$sql = 'UPDATE '.$this->_prefix.'catepro 
								SET left_id = left_id - '.$width.' 
								WHERE left_id > '.$catepro->right_id;
					$this->_conn->query($sql);
					
					/**
					 * Add catepro
					 */
					$preDepth = null;
					$preCateproId = null;
					foreach ($oldCategories as $oldCatepro) {
						$parentId = (null != $preDepth && $oldCatepro->depth > $preDepth) 
													? $preCateproId : $parentId;
						$this->add($oldCatepro, $parentId);
						$preDepth = $oldCatepro->depth;
						$preCateproId = $oldCatepro->category_id; 
					} 
				}
				
			} else {
				$this->delete($catepro);
				$this->add($catepro, $parentId);
			}
		} else {
			$where[] = 'category_id = '.$this->_conn->quote($catepro->category_id);
			$this->_conn->update($this->_prefix.'catepro', array(
					'name'		=> $catepro->name,
					'slug' 		=> $catepro->slug,
				), $where);
		}
	}
	
	/**
	 * @param Tomato_Modules_Catepro_Model_Catepro $catepro
	 * @return void
	 */
	public function delete($catepro) 
	{
		if ($catepro != null) {
			$where[] = 'category_id = '.$this->_conn->quote($catepro->category_id);
			$this->_conn->delete($this->_prefix.'catepro', $where);
			
			$sql = 'UPDATE '.$this->_prefix.'catepro SET left_id = left_id - 1, right_id = right_id - 1'
					.' WHERE left_id BETWEEN '.$catepro->left_id.' AND '.$catepro->right_id;
			$this->_conn->query($sql);

			$sql = 'UPDATE '.$this->_prefix.'catepro SET right_id = right_id - 2'
					.' WHERE right_id > '.$this->_conn->quote($catepro->right_id);
			$this->_conn->query($sql);
			
			$sql = 'UPDATE '.$this->_prefix.'catepro SET left_id = left_id - 2'
					.' WHERE left_id > '.$this->_conn->quote($catepro->right_id);
			$this->_conn->query($sql);
		}
	}
	
	/**
	 * Build catepro tree with depth for each item
	 * 
	 * @return Tomato_Core_Model_RecordSet
	 */
	public function getCateproTree() 
	{
		$sql = 'SELECT node.category_id, node.name, node.slug, (COUNT(parent.name) - 1) AS depth,
					node.left_id, node.right_id
				FROM '.$this->_prefix.'catepro AS node,
					'.$this->_prefix.'catepro AS parent
				WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
				GROUP BY node.category_id
				ORDER BY node.left_id';
		$rs = $this->_conn->query($sql)->fetchAll();
		return new Tomato_Core_Model_RecordSet($rs, $this);
	}
	
	/**
	 * @param Tomato_Modules_Catepro_Model_Catepro $catepro
	 * @return Tomato_Core_Model_RecordSet
	 * @since 2.0.3
	 */
	public function getCateproParent($catepro) {
		$select = $this->_conn->select()
						->from(array('c' => $this->_prefix.'catepro'))
						->where('c.left_id < '.$this->_conn->quote($catepro->left_id))
						->where('c.right_id > '.$this->_conn->quote($catepro->right_id))
						->order('c.left_id DESC')
						->limit(1);
		$row = $select->query()->fetchAll();
		$categories = new Tomato_Core_Model_RecordSet($row, $this);
		return (count($categories) == 0) ? null : $categories[0]; 
	}
}
