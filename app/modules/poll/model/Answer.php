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
 * @version 	$Id: Answer.php 967 2010-01-23 05:23:40Z huuphuoc $
 */

class Tomato_Modules_Poll_Model_Answer extends Tomato_Core_Model_Entity 
{
	protected $_properties = array(
		'answer_id' => null,
		'question_id' => null,
		'position' => null,
		'title' => null,
		'content' => null,
		'is_correct' => 0,
		'user_id' => null,
		'num_views' => 0,
	);
}