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
 * @version 	$Id: Widget.php 1267 2010-02-23 04:39:27Z huuphuoc $
 */

class Tomato_Modules_News_Widgets_MostViewed_Widget extends Tomato_Core_Widget 
{
	protected function _prepareShow() 
	{
		$categoryId = $this->_request->getParam('category_id');
		$limit = $this->_request->getParam('limit', 15);
		
		$conn = Tomato_Core_Db_Connection::getSlaveConnection();
		$articleGateway = new Tomato_Modules_News_Model_ArticleGateway();
		$articleGateway->setDbConnection($conn);
		$select = $conn->select()
						->from(array('a' => Tomato_Core_Db_Connection::getDbPrefix().'news_article'), array('article_id', 'category_id', 'title', 'slug', 'activate_date', 'num_views'))
						->joinLeft(array('c' => Tomato_Core_Db_Connection::getDbPrefix().'category'), 'a.category_id = c.category_id', array('category_name' => 'name'));
		if ($categoryId != null && $categoryId != '') {
			$select = $select->where('a.category_id = ?', $categoryId);
		}						
		$select = $select->where('a.status = ?', 'active')
						->order('a.num_views DESC')
						->limit($limit);
						
		$rs = $select->query()->fetchAll();
		$articles = new Tomato_Core_Model_RecordSet($rs, $articleGateway);
		$this->_view->assign('articles', $articles);
		$this->_view->assign('categoryId', $categoryId);
		
		$this->_view->assign('uuid', uniqid());
	}
		
	protected function _prepareConfig() 
	{
		$conn = Tomato_Core_Db_Connection::getMasterConnection();
		$categoryGateway = new Tomato_Modules_Category_Model_CategoryGateway();
		$categoryGateway->setDbConnection($conn);
		
		$categories = $categoryGateway->getCategoryTree();
		$this->_view->assign('categories', $categories); 
	}
}
