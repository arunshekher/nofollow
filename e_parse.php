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

class nofollow_parse
{


	/**
	 * Plugin Preferences
	 * @var array
	 */
	private static $Prefs = [];
	/**
	 * Operational status
	 * @var boolean
	 */
	private static $Active = false;
	/**
	 * Exclude/ignore domains
	 * @var array
	 */
	private static $excludeDomains = [];
	/**
	 * Exclude/ignore pages
	 * @var array
	 */
	private static $excludePages = [];
	/**
	 * Parsing method used
	 * @var string
	 */
	private static $parseMethod;
	/**
	 * Admin chosen contexts
	 * @var array
	 */
	private static $filterContext;


	/**
	 * constructor
	 */
	public function __construct()
	{
		$this->init();

	}

	protected function init()
	{
		self::setPrefs();
		self::setStatus();
		self::setExcludePages();
		self::setExcludeDomains();
		self::setParseMethod();

	}


	/**
	 * Sets plugin preferences
	 *
	 * @access protected
	 * @return array associative array of plugin preferences
	 */
	protected static function setPrefs()
	{
		self::$Prefs = e107::getPlugPref('nofollow');
	}


	/**
	 * Sets plugin operation status preference
	 *
	 * @return integer|boolean
	 */
	protected static function setStatus()
	{
		self::$Active = self::$Prefs['active'];
	}


	/**
	 * Sets exclude pages as a numeric array
	 *
	 * @return array
	 */
	protected static function setExcludePages()
	{
		self::$excludePages = self::nlStringToArray(self::$Prefs['ignore_pages']);
	}


	/**
	 * Sets NoFollow parse excluded domain names
	 * @return array | string
	 */
	protected static function setExcludeDomains()
	{
		self::$excludeDomains = self::solveExcludeDomains();
	}


	/**
	 * Sets NoFollow parse filter application context
	 * @return indexed array of admin chosen contexts
	 */
	protected static function setFilterContexts()
	{
		self::$filterContext = self::$Prefs['filter_context'];
	}

	/**
	 * Gets parse method preference
	 *
	 * @return string
	 */
	protected static function setParseMethod()
	{
		self::$parseMethod = trim(self::$Prefs['parse_method']);
	}

	/**
	 * Get exclude pages as a numeric array
	 *
	 * @return array
	 */
	protected static function solveExcludeDomains()
	{
		if (isset(self::$Prefs['ignore_domains'])) {
			$domains = self::nlStringToArray(self::$Prefs['ignore_domains']);
			$domains[] = e_DOMAIN;
			return array_unique($domains);
		}

		return [e_DOMAIN];
	}


	/**
	 * Helper method - Converts newline delimited string to numeric array
	 *
	 * @param type $str_with_nl
	 *
	 * @return array
	 */
	protected static function nlStringToArray($str_with_nl)
	{
		$str = str_replace(["\r\n", "\n\r"], "|", $str_with_nl);

		return explode("|", $str);
	}


	/**
	 * Checks if current/present page matches an exclude page
	 *
	 * @todo do an if empty check for excludePages pref
	 *       exclude pages
	 * @return boolean
	 */
	protected static function isExcludePage()
	{
		$current_page = e_REQUEST_URI; //$_SERVER['REQUEST_URI']
		if (count(self::$excludePages)) {
			foreach (self::$excludePages as $xpage) {
				if (strpos($current_page, $xpage) !== false) {
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * Checks if admin area
	 * @return bool
	 */
	protected static function isAdminArea()
	{
		return e_ADMIN_AREA;
	}


	/**
	 * Splits up $text by HTML tags and inner text scan for anchor tags and
	 * apply 'nofollow' to 'suitable' anchor tag candidates
	 * (adopted from linkwords plugin.)
	 *
	 * @param string $text
	 *
	 * @return string Modified text
	 * @access protected
	 */
	protected static function regexHtmlParse_Nofollow($text)
	{
		$nf_text = '';

		$pattern = '#(<.*?>)#mis';
		$fragments = preg_split($pattern, $text, -1,
			PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		foreach ($fragments as $fragment) {
			if (self::isOpeningAnchor($fragment) && self::needNoFollow($fragment)) {
				$nf_text .= self::stampNoFollow($fragment);
			} else {
				$nf_text .= $fragment;
			}
		}

		return $nf_text;
	}


	/**
	 * Tells if the text fragment is an opening anchor tag
	 *
	 * @param $fragment
	 *
	 * @return bool
	 */
	protected static function isOpeningAnchor($fragment)
	{
		if (stripos($fragment, '<a') !== false && ! strpos($fragment, '<a')) {
			return true;
		}

		return false;
	}


	/**
	 * Checks if anchor need NoFollow
	 * @param $anchor
	 *
	 * @return bool
	 */
	protected static function needNoFollow($anchor)
	{
		$hrefValue = self::getHrefValue($anchor);
		if (null === $hrefValue || self::isExcludeDomain($hrefValue)) {
			return false;
		}
		if (self::isValidExternalUrl($hrefValue)) {
			return true;
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
	protected static function getHrefValue($anchor)
	{
		$pattern =
			'~<a (?>[^>h]++|\Bh|h(?!ref\b))*href\s*=\s*["\']?\K[^"\'>\s]++~i';

		if (preg_match($pattern, $anchor, $match)) {
			return $match[0];
		}

		return null;
	}


	/**
	 * Checks if given URL string is in the excluded domains list
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	protected static function isExcludeDomain($input)
	{
		foreach (self::$excludeDomains as $exclude) {
			if (stripos($input, $exclude) !== false) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Checks if given string is a valid URL
	 *
	 * @param $input
	 *
	 * @return bool
	 * TODO revise pattern - is flawed, gives false positive for internal urls
	 */
	protected static function isValidExternalUrl($input)
	{
		$url_pattern =
			'/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-_=#]+'
			. '\.([a-zA-Z0-9\&\.\/\?\:@\-_=#]){2,}/';
		if (preg_match($url_pattern, trim($input))) {
			return true;
		}

		return false;
	}


	/**
	 * Helper method - for regexHtmlParse_Nofollow Add rel="nofollow" attribute
	 * to HTML anchor elements if not present.
	 *
	 * @param $anchor - string opening anchor tag
	 *
	 * @return string
	 * @access protected
	 */
	protected static function stampNoFollow($anchor)
	{
		if (strpos($anchor, 'nofollow')) {
			return $anchor;
		}

		if (strpos($anchor, 'rel')) {
			$pattern = "/rel=([\"'])([^\\1]+?)\\1/";
			//$replace = "rel=\\1\\2 nofollow\\1";
			$replace = "rel=\\1\\2 nofollow\\1 target=\"_blank\"";

			return preg_replace($pattern, $replace, $anchor);
		} else {
			$pattern = "/<a /";
			$replace = "<a rel=\"nofollow\" ";

			return preg_replace($pattern, $replace, $anchor);
		}
	}


	/**
	 * Nofollow DOM parser method using simple_html_dom.php library
	 *
	 * @param string - $text
	 *
	 * @return string - Parsed $text
	 * @access protected
	 */
	protected static function simpleHtmlDomParse_Nofollow($text)
	{
		require_once __DIR__ . '/lib/simple_html_dom.php';
		//e107::library('load', 'simple_html_dom');

		$dom = new simple_html_dom;
		$dom->load($text);
		$anchors = $dom->find('a');

		foreach ($anchors as $anchor) {
			if (
				(string)$anchor->rel === 'nofollow' ||
				self::isAdminArea() ||
				self::isExcludePage() ||
				! self::needNoFollow($anchor)
			) {
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


	/**
	 * Checks if the currently parsing $text is in NoFollow parse filter context
	 * @param $context
	 *
	 * @return bool
	 */
	protected static function isInContext($context)
	{
		$contextList = self::getContextList();
		if (null === $contextList) {
			return true;
		}
		foreach ($contextList as $contextItem) {
			if ($contextItem === $context) return true;
		}
		return false;
	}


	/**
	 * Gets context list according to preference
	 * @return array|null
	 */
	protected static function getContextList()
	{
		//$pref = e107::getPlugPref('nofollow', 'filter_context');
		$contextPref = self::$filterContext;
		switch ($contextPref) {
			case 1:
				return ['USER_TITLE', 'USER_BODY'];
				break;
			case 2:
				return ['TITLE', 'BODY', 'USER_TITLE', 'USER_BODY'];
				break;
			case 3:
				return null;
				break;
		}
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
	 * Checks if the anchor tag URL is an excluded domain
	 *
	 * @param string $anchor
	 *
	 * @return boolean
	 */
	protected static function hasExcludeDomain($anchor)
	{
		$excludes = self::$excludeDomains;

		$href = self::getHrefValue($anchor);
		// todo: check here if href has an http:// https:// prefix  or a domain name if not return false.
		if (null !== $href && self::isValidExternalUrl($href)) { // todo: and notExclude domain need nofollow
			foreach ($excludes as $exclude) {
				if (strpos($href, $exclude) !== false) {
					return true;
				}
			}
		}

		return false;
	}


}

