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
 * @version 	$Id: PaginatorAdapter.php 1208 2010-02-21 13:54:37Z huuphuoc $
 * @since		2.0.3
 */

class Tomato_Core_Utility_PaginatorAdapter extends Zend_Paginator_Adapter_Iterator
{
	public function __construct(Iterator $iterator, $count)
	{
		parent::__construct($iterator);
		$this->_count = $count;
	}
}