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
 * @version 	$Id: Paginator.php 1535 2010-03-10 04:27:56Z huuphuoc $
 * @since		2.0.3
 */

class Core_View_Helper_Paginator extends Zend_View_Helper_Abstract 
{
	public function paginator()
	{
		return $this;
	}
	
	public function slide($paginator, $options = array())
	{
		$this->view->addScriptPath(TOMATO_APP_DIR . DS . 'modules' . DS . 'core' . DS . 'views' . DS . 'scripts');
		return $this->view->paginationControl($paginator, 'Sliding', '_partial' . DS . '_pagination.phtml', 
				array(
					'paginatorOptions' => $options
				));
	}
	
	/**
	 * Generate link to item
	 * 
	 * @param int $pageIndex Page index of item
	 * @param string $label Label of link
	 * @param array $options Array consist of two options:
	 * - path
	 * - itemLink 
	 * @return string
	 */
	public function buildLink($pageIndex, $label, $options = array())
	{
		$url = $options['path'];
		$str = str_replace('%d', $pageIndex, $options['itemLink']);
		if (0 == strncasecmp($options['itemLink'], 'javascript', 10)) {
			$url = $str;
		} else {
			$url = rtrim($url, '/') . '/' . $str;
		}
		return sprintf('<a href="%s">%s</a>', $url, $label);
	}
}
