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
 * @version 	$Id: Hook.php 1299 2010-02-24 08:15:04Z huuphuoc $
 * @since		2.0.3
 */

class Tomato_Modules_Seo_Hooks_GaTracker_Hook extends Tomato_Core_Hook
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * @return void
	 */
	public function action()
	{
		$code = $this->getParam('code');
		if (null == $code) {
			return;
		}
		$code = ltrim($code, ' ');
		$code = rtrim($code, ' ');
		if ($code != '') {
			echo <<<END
			<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("$code");
pageTracker._trackPageview();
} catch(err) {}
</script>
END;
		}
	}
}
