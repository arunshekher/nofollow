<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2010 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 *
 *
 * $Source: /cvs_backup/e107_0.8/e107_plugins/linkwords/e_tohtml.php,v $
 * $Revision$
 * $Date$
 * $Author$
 */

if ( ! defined('e107_INIT')) {
	exit;
}

require __DIR__ . '/NoFollow.php';

class nofollow_parse extends NoFollow
{


	/**
	 * e107 HTML parser routine callee
	 *
	 * @param string $text html/text to be processed.
	 * @param string $context current text parse context
	 *
	 * @return string
	 * @access public
	 */
	public function toHtml($text, $context = '')
	{
		if (
			self::$Active &&
			! self::isAdminArea() &&
			! self::isExcludePage() &&
			self::isInContext($context)
		) {

			$method = self::$parseMethod;

			if (method_exists($this, $method)) {

				$text = $this->$method($text);

				return $text;

			}
		}

		return $text;
	}



}

