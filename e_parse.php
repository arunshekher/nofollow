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

//if ( basename( $_SERVER['PHP_SELF'] ) == basename(__FILE__) ) { die('Access denied'); }

if (e_ADMIN_AREA === true || ! e107::isInstalled('nofollow')) {
	return;
}


class nofollow_parse
{

	/**
	 *
	 */
	const HOST_SITE = SITEURLBASE;
	/**
	 * Plugin Preferences
	 *
	 * @var array
	 */
	private static $_Prefs = [];
	/**
	 * Operational status
	 *
	 * @var boolean
	 */
	private static $_Active = false;
	/**
	 * Exclude/ignore domains
	 *
	 * @var array
	 */
	private static $_excludeDomains = [];
	/**
	 * Exclude/ignore pages
	 *
	 * @var array
	 */
	private static $_excludePages = [];
	/**
	 * Parsing method used
	 *
	 * @var type
	 */
	private static $_parseMethod = null;


	/**
	 * constructor
	 */
	public function __construct()
	{
		// if plugin not installed or admin area - return
		//@TODO: Add exclude page check here
		if ( ! e107::isInstalled('nofollow') || e_ADMIN_AREA === true) {
			return;
		}

		// - set plugin prefs
		self::$_Prefs = self::_getPrefs();
		// Begin - set status
		self::$_Active = self::_getStatus();

		// if plugin not active - return
		if ( ! self::$_Active) {
			return;
		}

		// - set exclude pages
		self::$_excludePages = self::_getExcludePages();
		// - set exclude domains
		self::$_excludeDomains = self::_getExcludeDomains();
		// - set parse method
		self::$_parseMethod = self::_getParseMethod();

		// If an exclude page - return
		if (self::_excludePage()) {
			return;
		}
	}


	/**
	 * Retrieve and return plugin preferences
	 *
	 * @access protected
	 * @return array associative array of plugin preferences
	 */
	protected static function _getPrefs()
	{
		return e107::getPlugPref('nofollow');
	}


	/**
	 * Get plugin operation status preference
	 *
	 * @return integer|boolean
	 */
	protected static function _getStatus()
	{
		return self::$_Prefs['active'];

	}


	/**
	 * Get exclude pages as a numeric array
	 *
	 * @return array
	 */
	protected static function _getExcludePages()
	{
		return explode("\n", self::$_Prefs['ignore_pages']);
	}


	/**
	 * Get exclude pages as a numeric array
	 *
	 * @return array|string
	 */
	protected static function _getExcludeDomains()
	{
		if (isset(self::$_Prefs['ignore_domains'])) {
			return self::nl_string_toArray(self::$_Prefs['ignore_domains']);
		}

		return e_DOMAIN;
	}


	/**
	 * Helper method - Convert newline delimited string to numeric array
	 *
	 * @param type $str_with_nl
	 *
	 * @return array
	 */
	protected static function nl_string_toArray($str_with_nl)
	{
		$str = str_replace(["\r\n", "\n\r"], "|", $str_with_nl);

		return array_unique(explode("|", $str));
	}


	/**
	 * Get parse method preference
	 *
	 * @return string
	 */
	protected static function _getParseMethod()
	{
		return trim(self::$_Prefs['parse_method']);
	}


	/**
	 * Check if present page is a strpos of exclude page
	 *
	 * @todo preferably need a foreach loop to loop through all the listed
	 *       exclude pages
	 * @return boolean
	 */
	protected static function _excludePage()
	{
		$present_page = $_SERVER['REQUEST_URI']; //e_REQUEST_URI

		self::_debugLog($present_page, 'Current-Page');

		return in_array($present_page, self::$_excludePages);

	}


	/**
	 * Debug logger
	 *
	 * @param string $content String content that's being passed in as argument
	 * @param string $logname Optional log file name
	 */
	private static function _debugLog($content, $logname = 'Nofollow-Debug')
	{
		$path = e_PLUGIN . 'nofollow/' . $logname . '.log';
		file_put_contents($path, $content . "\n", FILE_APPEND);
		unset($path, $content);
	}


	/**
	 * Split up $text by HTML tags and inner text scan for anchor tags and
	 * apply nofollow to 'suitable' anchor tag candidates
	 * (adopted from linkwords plugin.)
	 *
	 * @param str  $text - text string that will be altered
	 * @param str  $opts ['context'] - default context
	 * @param bool $logflag - switch to log the makenofollow on post
	 *
	 * @return string Modified text
	 * @access protected
	 * @todo   fix omit based on contexts
	 */
	protected static function regexHtmlParse_Nofollow($text)
	{

		$nf_text = '';

		$pattern = '#(<.*?>)#mis';
		$fragments = preg_split($pattern, $text, -1,
			PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		foreach ($fragments as $fragment) {
			if (self::isOpeningAnchor($fragment) && ! self::hasExcludeDomain($fragment)) {
				$nf_text .= self::stamp_NoFollow($fragment);
			} else {
				$nf_text .= $fragment;
			}
		}

		return $nf_text;
	}


	/**
	 * @psuedocode
	 * A combined method for checking excluded domains and internal links
	 * Method name: 'needNofollow' 'requireNofollow' or something similar
	 */
	// Get the anchor tag fragment
	//      IF has href value
	//              IF has a base domain in Href value
	//                  IF the domain is listed in exclude list
	//                      RETURN true
	//                  ELSE
	//                      RETURN false
	//              ELSE
	//                  RETURN true
	//     ELSE RETURN false

	/**
	 * Check if the anchor tag URL is an excluded domain
	 *
	 * @param string $anchor
	 *
	 * @return boolean
	 * @todo add foreach loop to iterate through all exclude domains when have
	 *       multiple
	 */
	protected static function hasExcludeDomain($anchor)
	{
		$excludes = self::$_excludeDomains;

		$href = self::_getHrefValue($anchor);

		// debug
		self::_debugLog($href);

		foreach ($excludes as $exclude) {
			if (strpos($href, $exclude) !== false) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Returns the href attribute value of an anchor tag
	 *
	 * @param string $anchor
	 *
	 * @return string href attribute value | null
	 */
	protected static function _getHrefValue($anchor)
	{
		preg_match('~<a (?>[^>h]++|\Bh|h(?!ref\b))*href\s*=\s*["\']?\K[^"\'>\s]++~i',
			$anchor, $matches);

		if ($matches) {
			return $matches[0];
		}

		return null;
	}


	/**
	 * Helper method for regexHtmlParse_Nofollow Add rel="nofollow" attribute
	 * to HTML anchor elements if not present. If already have rel attr. but
	 * no 'nofollow', append 'nofollow' to its value. Insert
	 * rel="nofollow" for every other anchor elements passed to it.
	 *
	 * @param $anchor - string with opening anchor tag that is passed in
	 *
	 * @return string - The modified opening anchor tag string
	 * @access protected
	 * @todo   may be refactor the name to 'insert_Nofollow' or 'add_Nofollow'
	 */
	protected static function stamp_NoFollow($anchor)
	{
		if (strpos($anchor, 'nofollow')) {
			return $anchor;
		}

		if (strpos($anchor, 'rel')) {
			$pattern = "/rel=([\"'])([^\\1]+?)\\1/";
			//$replace = "rel=\\1\\2 nofollow\\1";
			$replace =
				"rel=\\1\\2 nofollow\\1 target=\"_blank\"";// <-- this works but have to confirm how accurate it is.

			return preg_replace($pattern, $replace, $anchor);
		} else {
			$pattern = "/<a /";
			$replace = "<a rel=\"nofollow\" ";

			return preg_replace($pattern, $replace, $anchor);
		}
	}


	/**
	 * Nofollow DOM parser using simple_html_dom.php library
	 *
	 * @todo   cleanup unwanted code
	 *
	 * @param string - $text
	 *
	 * @return string - Parsed $text
	 * @access protected
	 */
	protected static function simpleHtmlDomParse_Nofollow($text)
	{
		//require_once( 'lib/simple_html_dom.php' );
		e107::library('load', 'simple_html_dom');

		$dom = new simple_html_dom;

		$dom->load($text);

		$anchors = $dom->find('a');

		foreach ($anchors as $anchor) {
			/*
            if ( (string) $anchor->rel == 'nofollow' )
            {
                continue;
            }
            else
            {
                $anchor->rel = 'nofollow';
            }
            */
			// if no 'nofollow' & yes 'external' then add nofollow and add target=_blank

			if ((string)$anchor->rel == 'nofollow') {
				continue;
			}

			if ((strpos((string)$anchor->rel,
						'nofollow') === false) && strpos((string)$anchor->rel,
					'external') !== false
			) {
				$anchor->rel = 'nofollow';
				$anchor->target = '_blank';
			} else {
				$anchor->rel = 'nofollow';
			}


		}

		$text = $dom->save();

		$dom->clear();

		return $text;
	}


	/**
	 * @param $fragment
	 *
	 * @return bool
	 */
	protected static function isOpeningAnchor($fragment)
	{
		if (strpos($fragment, '<a') !== false && ! strpos($fragment,'<a'))  {
			return true;
		}
		return false;
	}


	/**
	 * e107 HTML parser hook
	 *
	 * @param string $text html/text to be processed.
	 * @param string $context Current context ie.  OLDDEFAULT | BODY | TITLE |
	 *                        SUMMARY | DESCRIPTION | WYSIWYG etc.
	 *
	 * @return string
	 * @access public
	 */
	public function toHtml($text, $context = '')
	{
		// todo: use context - USER_TITLE, USER_BODY is all that's really needs
		// ..checking but can also use BODY and title which is used in news. Need to understand more
		if (self::$_Active) {

			$method = self::$_parseMethod;

			if (method_exists($this, $method)) {

				$text = $this->$method($text);

				return $text;

			} else {

				return $text;

			}

		}

		return $text;
	}


	/**
	 * @param $input
	 *
	 * @return bool
	 */
	protected function mandateNofollow($input)
	{
		$href = self::_getHrefValue($input);

		if (null === $href || self::hasExcludeDomain($href)) {
			return false;
		}

		return true;
	}


	/**
	 * Boilerplate Sub-method to break-apart the above method logic for
	 * simplicity and maintainalbility and add the operational conditional
	 * checks of plugin
	 *
	 * @param string $anchor
	 *
	 * @return string
	 * @todo develop the method, do the hasExcludeDomain() and internal link
	 *       checks here
	 */
	protected function processAnchor($anchor)
	{
		// IF hasExcludeDomain() OR internalLink()
		//      RETURN $anchor
		// ELSE
		//      RETURN stamp_Nofollow( $anchor );
		return $processed;
	}


}
