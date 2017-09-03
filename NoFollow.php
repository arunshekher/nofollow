<?php


abstract class NoFollow
{
	/**
	 * Operational status
	 *
	 * @var boolean
	 */
	protected $Active = false;
	/**
	 * Parsing method used
	 *
	 * @var string
	 */
	protected $parseMethod;
	/**
	 * Plugin Preferences
	 *
	 * @var array
	 */
	private $Prefs = [];
	/**
	 * Exclude/ignore domains
	 *
	 * @var array
	 */
	private $excludeDomains = [];
	/**
	 * Exclude/ignore pages
	 *
	 * @var array
	 */
	private $excludePages;
	/**
	 * Admin chosen contexts
	 *
	 * @var array
	 */
	private $filterContext;


	/**
	 * constructor
	 */
	public function __construct()
	{
		$this->init();

	}


	/**
	 * Initializes class
	 *
	 */
	protected function init()
	{
		$this->setPrefs()
			->setStatus()
			->setExcludePages()
			->setExcludeDomains()
			->setParseMethod();
	}


	/**
	 * Sets plugin preferences
	 *
	 */
	protected function setPrefs()
	{
		$this->Prefs = e107::getPlugPref('nofollow');
		return $this;
	}


	/**
	 * Sets plugin operation status preference
	 *
	 */
	protected function setStatus()
	{
		$this->Active = $this->Prefs['active'];
		return $this;
	}


	/**
	 * Sets exclude pages as a numeric array
	 *
	 */
	protected function setExcludePages()
	{
		$this->excludePages =
			$this->nlStringToArray($this->Prefs['ignore_pages']);
		return $this;
	}


	/**
	 * Converts newline delimited string to numeric array
	 *
	 * @param string $str_with_nl
	 *
	 * @return array
	 */
	protected function nlStringToArray($str_with_nl)
	{
		$str = str_replace(["\r\n", "\n\r"], "|", $str_with_nl);

		return explode("|", $str);
	}


	/**
	 * Sets NoFollow parse excluded domain names
	 *
	 */
	protected function setExcludeDomains()
	{
		$this->excludeDomains = $this->solveExcludeDomains();
		return $this;
	}


	/**
	 * Work out exclude domain names
	 *
	 * @return array | string
	 */
	protected function solveExcludeDomains()
	{
		if (isset($this->Prefs['ignore_domains'])) {
			$domains = $this->nlStringToArray($this->Prefs['ignore_domains']);
			$domains[] = e_DOMAIN;

			return array_unique($domains);
		}

		return [e_DOMAIN];
	}


	/**
	 * Sets parse method preference
	 *
	 */
	protected function setParseMethod()
	{
		$this->parseMethod = trim($this->Prefs['parse_method']);
		return $this;
	}


	/**
	 * Sets NoFollow parse filter application context
	 *
	 * @return array of admin chosen contexts
	 */
	protected function setFilterContexts()
	{
		$this->filterContext = $this->Prefs['filter_context'];
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
	protected function regexHtmlParse_Nofollow($text)
	{
		$nf_text = '';

		$pattern = '#(<.*?>)#mis';
		$fragments = preg_split($pattern, $text, -1,
			PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		foreach ($fragments as $fragment) {
			if (
				$this->isOpeningAnchor($fragment)
				&& $this->needNoFollow($fragment)
			) {
				$nf_text .= $this->stampNoFollow($fragment);
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
	protected function isOpeningAnchor($fragment)
	{
		if (
			stripos($fragment, '<a') !== false
			&& ! strpos($fragment, '<a')
		) {
			return true;
		}

		return false;
	}


	/**
	 * Checks if anchor need NoFollow
	 *
	 * @param $anchor
	 *
	 * @return bool
	 */
	protected function needNoFollow($anchor)
	{
		$hrefValue = $this->getHrefValue($anchor);
		if (null === $hrefValue || $this->isExcludeDomain($hrefValue)) {
			return false;
		}
		if ($this->isValidExternalUrl($hrefValue)) {
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
	protected function getHrefValue($anchor)
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
	protected function isExcludeDomain($input)
	{
		foreach ($this->excludeDomains as $exclude) {
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
	 * TODO revise pattern
	 */
	protected function isValidExternalUrl($input)
	{
		$pattern = '(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+'
		    . '[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.'
			. '[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]\.'
			. '[^\s]{2,}|www\.[a-zA-Z0-9]\.[^\s]{2,})';

		if (preg_match($pattern, trim($input))) {
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
	protected function stampNoFollow($anchor)
	{
		if (strpos($anchor, 'nofollow')) {
			return $anchor;
		}

		if (strpos($anchor, 'rel')) {
			$pattern = "/rel=([\"'])([^\\1]+?)\\1/";
			$replace = "rel=\\1nofollow \\2\\1 target=\"_blank\"";

			return preg_replace($pattern, $replace, $anchor);
		}
		$pattern = "/<a /";
		$replace = "<a rel=\"nofollow\" ";

		return preg_replace($pattern, $replace, $anchor);
	}


	/**
	 * Nofollow DOM parser method using simple_html_dom.php library
	 *
	 * @param string - $text
	 *
	 * @return string - Parsed $text
	 * @access protected
	 * TODO implement local and global loading.
	 */
	protected function simpleHtmlDomParse_Nofollow($text)
	{
		$this->loadLib();

		$dom = new simple_html_dom;
		$dom->load($text);
		$anchors = $dom->find('a');

		foreach ($anchors as $anchor) {
			if (
				(string)$anchor->rel === 'nofollow'
				|| $this->isAdminArea()
				|| $this->isExcludePage()
				|| ! $this->needNoFollow($anchor)
			) {
				continue;
			}
			if ((strpos((string)$anchor->rel,
						'nofollow') === false) && strpos((string)$anchor->rel,
					'external') !== false
			) {
				$anchor->rel = 'nofollow external';
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
	 * Resolve simple dom parser class path based on admin pref.
	 */
	protected function loadLib()
	{
		if ($this->Prefs['use_global_path']) {
			e107::library('load', 'simple_html_dom');
		} else {
			require_once __DIR__ . '/lib/simple_html_dom.php';
		}
	}


	/**
	 * Checks if admin area
	 *
	 * @return bool
	 */
	protected function isAdminArea()
	{
		return e_ADMIN_AREA;
	}


	/**
	 * Checks if current/present page matches an exclude page
	 *
	 * @todo do an if empty check for excludePages pref
	 *       exclude pages
	 * @return boolean
	 */
	protected function isExcludePage()
	{
		$current_page = e_REQUEST_URI; //$_SERVER['REQUEST_URI']
		if (count($this->excludePages) > 0) {
			foreach ($this->excludePages as $xpage) {
				if (strpos($current_page, $xpage) !== false) {
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * Checks if the currently parsing $text is in NoFollow parse filter context
	 *
	 * @param $context
	 *
	 * @return bool
	 */
	protected function isInContext($context)
	{
		$contextList = $this->getContextList();
		if (null === $contextList) {
			return true;
		}
		foreach ($contextList as $contextItem) {
			if ($contextItem === $context) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Gets context list according to preference
	 *
	 * @return array|null
	 */
	protected function getContextList()
	{
		$contextPref = $this->filterContext;

		switch ($contextPref) {
			case 1:
				return ['USER_TITLE', 'USER_BODY'];
				break;
			case 2:
				return ['TITLE', 'BODY', 'USER_TITLE', 'USER_BODY'];
				break;
			case 3:
			default:
				return null;
		}
	}


	/**
	 * Checks if the anchor tag URL is an excluded domain
	 *
	 * @param string $anchor
	 *
	 * @return boolean
	 */
	protected function hasExcludeDomain($anchor)
	{
		$excludes = $this->excludeDomains;

		$href = $this->getHrefValue($anchor);
		if (null !== $href && $this->isValidExternalUrl($href)) {
			foreach ($excludes as $exclude) {
				if (strpos($href, $exclude) !== false) {
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * Debug logger
	 *
	 * @param string $content - content to log
	 * @param string $logname - optional log name
	 */
	private static function _debugLog($content, $logname = 'Nofollow-Debug')
	{
		$path = e_PLUGIN . 'nofollow/' . $logname . '.log';
		file_put_contents($path, $content . "\n", FILE_APPEND);
		unset($path, $content);
	}

}

