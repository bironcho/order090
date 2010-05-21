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
 * @version 	$Id: CommentGateway.php 1669 2010-03-23 04:48:06Z huuphuoc $
 */

class Tomato_Modules_Contact_Model_ContactGateway extends Tomato_Core_Model_Gateway {
	public function convert($entity) {
		return new Tomato_Modules_Contact_Model_Contact ( $entity );
	}
	public function getContacts($start = null, $count = null) {
		$select = $this->_conn->select ()->from ( $this->_prefix . 'contact' )->order ( 'contact_id DESC' );
		if (is_int ( $start ) && is_int ( $count )) {
			$select->limit ( $count, $start );
		}
		$rs = $select->query ()->fetchAll ();
		return new Tomato_Core_Model_RecordSet ( $rs, $this );
	}
/**
	 * @param array $exp Search expression (@see find)
	 * @return int
	 */
	public function count() 
	{
		$select = $this->_conn
				->select()
				->from(array('a' => $this->_prefix.'contact'), array('num_contacts' => 'COUNT(*)'));
		$row = $select->query()->fetch();
		return $row->num_contacts;
	}
}
