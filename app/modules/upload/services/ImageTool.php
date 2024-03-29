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
 * @version 	$Id: ImageTool.php 1905 2010-04-01 02:34:44Z leha $
 */

abstract class Tomato_Modules_Upload_Services_ImageTool 
{
	/**
	 * Watermark positions
	 * @since 2.0.4
	 */
	const POS_TOP_LEFT 		= 'top_left';
	const POS_TOP_RIGHT 	= 'top_right';
	const POS_BOTTOM_LEFT 	= 'bottom_left';
	const POS_BOTTOM_RIGHT 	= 'bottom_right';
	const POS_MIDDLE_CENTER = 'middle_center';
	
	const FLIP_VERTICAL 	= 'vertical';
	const FLIP_HORIZONTAL 	= 'horizontal';
	
	const WATERMARK_FONT	= 'watermark.ttf';
	
	/**
	 * The file name
	 * 
	 * @var string
	 */
	protected $_file;

	/**
	 * File type: gif, jpg, jpeg, png
	 * 
	 * @var string
	 */
	protected $_fileType;

	/**
	 * Width of image
	 * 
	 * @var int
	 */
	protected $_width;

	/**
	 * Height of image
	 * 
	 * @var int
	 */
	protected $_height;
	
	/**
	 * @param string $file
	 */
	public function setFile($file) 
	{
		$this->_file = $file;

		// Get size of image
		$info = getimagesize($this->_file);

		$this->_width = $info[0];
		$this->_height = $info[1];

		$ext = explode('.', $file);
		$this->_fileType = strtolower($ext[count($ext) - 1]);
	}

	public function resizeLimit($newFile, $newWidth, $newHeight) 
	{
		$percent = ($this->_width > $newWidth)
					? (($newWidth * 100) / $this->_width) : 100;
		$newWidth = ($this->_width * $percent) / 100;
		$newHeight = ($this->_height * $percent) / 100;
		$this->_resize($newFile, $newWidth, $newHeight);
	}

	public function resize($newFile, $newWidth, $newHeight) 
	{
		$this->_resize($newFile, $newWidth, $newHeight);
	}
	
	public function crop($newFile, $newWidth, $newHeight, $resize = true, $cropX = null, $cropY = null) 
	{
		// Maintain ratio if image is smaller than resize
		$percent = ($this->_width > $newWidth) ? ($newWidth * 100) / ($this->_width) : 100;

		// Resize to one side to newWidth or newHeight
		if ($this->_width > $this->_height) {
			$percent = ($newHeight * 100) / $this->_height;
			$resizeHeight = $newHeight;
			$resizeWidth = ($this->_width * $percent) / 100;
		} else {
			$percent = ($newWidth * 100) / $this->_width;
			$resizeWidth = $newWidth;
			$resizeHeight = ($this->_height * $percent) / 100;
		}

		$cropX = (null == $cropX) ? ($resizeWidth - $newWidth) / 2 : $cropX;
		$cropY = (null == $cropY) ? ($resizeHeight - $newHeight) / 2 : $cropY;

		$this->_crop($newFile, $resizeWidth, $resizeHeight, $newWidth, $newHeight, $cropX, $cropY, $resize);
	}	

	/**
	 * @param string $newFile
	 * @param int $angle
	 * @return bool
	 */
	public abstract function rotate($newFile, $angle);
	
	/**
	 * @since 2.0.4
	 */
	public abstract function flip($newFile, $mode);

	/**
	 * @since 2.0.4
	 */
	public abstract function watermarkImage($overlayFile, $position);

	/**
	 * 
	 * @param string $overlayText
	 * @param string $position
	 * @param array $param
	 * @since 2.0.4
	 */
	public abstract function watermarkText($overlayText, $position, 
							$param = array('color' => 'FFF', 'rotation' => 0, 'opacity' => 50, 'size' => null));
	
	protected abstract function _resize($newFile, $newWidth, $newHeight);

	protected abstract function _crop($newFile, $resizeWidth, $resizeHeight, 
								$newWidth, $newHeight, $cropX, $cropY, $resize = true);	
}
