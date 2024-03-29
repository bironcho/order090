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
 * @version 	$Id: PhotoController.php 1934 2010-04-01 19:14:48Z huuphuoc $
 */

class Multimedia_PhotoController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
		
	public function uploadAction() 
	{
		$this->view->addHelperPath(TOMATO_APP_DIR.DS.'modules'.DS.'upload'.DS.'views'.DS.'helpers', 'Upload_View_Helper_');
		
		$conn = Tomato_Core_Db_Connection::getMasterConnection();		
		$setGateway = new Tomato_Modules_Multimedia_Model_SetGateway();
		$setGateway->setDbConnection($conn);
		$sets = $setGateway->getAllSets();
		$this->view->assign('sets', $sets);
	
		$config = Tomato_Core_Module_Config::getConfig('upload');		
		$sizes = array();
		foreach ($config->size->toArray() as $key => $value) {
			list($method, $width, $height) = explode('_', $value);
			$sizes[$key] = array('method' => $method, 'width' => $width, 'height' => $height);
		}
		
		$text = isset($config->watermark->text) ? $config->watermark->text : null;
		$color = isset($config->watermark->color) ? $config->watermark->color : null;
		$image = isset($config->watermark->image) ? $config->watermark->image : null;
		$position = isset($config->watermark->position) ? $config->watermark->position : null;
		$sizesApplied = isset($config->watermark->sizes) ? $config->watermark->sizes : null;
		$sizesApplied = explode(',', $sizesApplied);
		
		$this->view->assign('sizes', $sizes);
		$this->view->assign('text', $text);
		$this->view->assign('color', $color);
		$this->view->assign('image', $image);
		$this->view->assign('position', $position);
		$this->view->assign('sizesApplied', $sizesApplied);
		
		if ($this->_request->isPost()) {
			/**
			 * Save watermark option to module configuration file
			 * @since 2.0.4
			 */
			$watermark = $this->_request->getPost('watermark');
			if ($watermark) {
				$typeMark = $this->_request->getPost('watermarkType');
				$text = $this->_request->getPost('watermarkText');
				$color = $this->_request->getPost('watermarkColor');
				$image = $this->_request->getPost('watermarkImageUrl');
				$position = $this->_request->getPost('watermarkPosition');
				$sizes = $this->_request->getPost('sizes');
				$sizes = implode(',', $sizes);
				
				$file = TOMATO_APP_DIR.DS.'modules'.DS.'upload'.DS.'config'.DS.'config.ini';		
				$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
				$config = $config->toArray();
				
				unset($config['watermark']['text']);
				unset($config['watermark']['color']);
				unset($config['watermark']['image']);
				if ('text' == $typeMark && $text) {
					$config['watermark']['text'] = $text;
					$config['watermark']['color'] = $color;
				}
				if ('image' == $typeMark && $image) {
					$config['watermark']['image'] = $image;
				}
				$config['watermark']['position'] = $position;
				$config['watermark']['sizes'] = $sizes;
				$writer = new Zend_Config_Writer_Ini();
				$writer->write($file, new Zend_Config($config));
			}
			
			$user = Zend_Auth::getInstance()->getIdentity();
			$title = $this->_request->getPost('title');
			
			if ($title && $title != ',') {
				$crop = $this->_request->getPost('crop');
				$general = $this->_request->getPost('general');
				$large = $this->_request->getPost('large');
				$medium = $this->_request->getPost('medium');
				$original = $this->_request->getPost('original');
				$small = $this->_request->getPost('small');
				$square = $this->_request->getPost('square');
				$setId = $this->_request->getPost('set');
				$newSet = $this->_request->getPost('newSet');
				
				$arrTitle = explode(',', $title);
				$arrCrop = explode(',', $crop);
				$arrGeneral = explode(',', $general);
				$arrLarge = explode(',', $large);
				$arrMedium = explode(',', $medium);
				$arrOriginal = explode(',', $original);
				$arrSmall = explode(',', $small);
				$arrSquare = explode(',', $square);
				$isAdd = false;
				for ($i = 1; $i < count($arrTitle); $i++) {
					$arrImageTitle = explode(".", $arrTitle[$i]);
					$imageName = substr($arrTitle[$i], 0, strlen($arrTitle[$i]) - strlen($arrImageTitle[count($arrImageTitle)-1]) - 1);
					$file = new Tomato_Modules_Multimedia_Model_File(array(
						'title' => $imageName,
						'slug' => Tomato_Core_Utility_String::removeSign($imageName, '-', true),
						'image_crop' => $arrCrop[$i],
						'image_general' => $arrGeneral[$i],
						'image_medium' => $arrMedium[$i],
						'image_original' => $arrOriginal[$i],
						'image_small' => $arrSmall[$i],
						'image_square' => $arrSquare[$i],
						'image_large' => $arrLarge[$i],
						'created_date' => date('Y-m-d H:i:s'),
						'created_user' => $user->user_id,
						'created_user_name' => $user->user_name,
						'url' => $arrOriginal[$i],
						'file_type' => 'image',					
						'is_active' => true,
					));
					$fileGateway = new Tomato_Modules_Multimedia_Model_FileGateway();
					$fileGateway->setDbConnection($conn);
					$fileId = $fileGateway->add($file);
					
					if($newSet && !$isAdd) {
						$isAdd = true;
						$set = new Tomato_Modules_Multimedia_Model_Set(array(
							'title' => $newSet,
							'created_date' => date('Y-m-d H:i:s'),
							'created_user_id' => $user->user_id,
							'created_user_name' => $user->user_name,
							'is_active' => true,
						));
						$setGateway = new Tomato_Modules_Multimedia_Model_SetGateway();
						$setGateway->setDbConnection($conn);
						$setId = $setGateway->add($set);
					}
					if($setId) {	
						$conn->insert(Tomato_Core_Db_Connection::getDbPrefix().'multimedia_file_set_assoc', array(
							'file_id' => $fileId,
							'set_id' => $setId,
						));
					}
				}
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('photo_upload_success')
				);
				$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'multimedia_photo_upload'));
			}			
		}
	}
}
