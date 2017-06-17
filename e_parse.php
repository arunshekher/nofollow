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



if (!defined('e107_INIT')) { exit; }

class nofollow_parse
{
    
        /**
         * Plugin Preferences
         * @var array 
         */
        private static $_Prefs = array();
        /**
         * Operational status
         * @var boolean 
         */
        private static $_Active = false;
        /**
         * Exclude/ignore domains
         * @var array 
         */
        private static $_excludeDomains = array();
        /**
         * Exclude/ignore pages
         * @var array 
         */
        private static $_excludePages = array();
        /**
         * Parsing method used
         * @var type 
         */
        private static $_parseMethod = null;

        const HOST_SITE = SITEURLBASE;


        /**
         * constructor
         * @return
         */
	function __construct()
	{
            // if plugin not installed or admin area - return
            if( /*!e107::isInstalled('nofollow') ||*/ e_ADMIN_AREA === true  ) //TODO: Include exclude page check too here incorporating e107 static getPlugPrefs() method
            { 
                return; 
            }
            
            // - set plugin prefs
            self::$_Prefs = self::_getPrefs();
            // Begin - set status 
            self::$_Active = self::_getStatus();
            
            // if plugin not active - return
            if ( !self::$_Active )
            {
                return;
            }
            
            // - set exclude pages
            self::$_excludePages = self::_getExcludePages();
            // - set exclude domains
            self::$_excludeDomains = self::_getExcludeDomains();
            // - set parse method
            self::$_parseMethod = self::_getParseMethod();
            
            // If an exclude page - return
            if ( self::_excludePage() )
            {
                return;
            }
        }
        
        /**
         * Get plugin operation status preference
         * @return integer|boolean
         */
        protected static function _getStatus()
        {
            //return e107::getPlugPref( 'nofollow', 'globally_on', 0 ) ;
            return self::$_Prefs['globally_on'];
            
        }
        
        /**
         * Retrieve and return plugin preferences
         * @access protected
         * @return array associative array of plugin preferences
         */
        protected static function _getPrefs()
        {
            return e107::getPlugPref('nofollow');
        }
        
        /**
         * Get exclude pages as a numeric array
         * @return array
         */
        protected static function _getExcludePages()
        {
            return explode( "\n", self::$_Prefs['ignore_pages'] );
        }
        
        /**
         * Get exclude pages as a numeric array
         * @return type
         */
        protected static function _getExcludeDomains()
        {
            if ( is_array( self::$_Prefs ) && isset( self::$_Prefs['ignore_domains'] ) )
            {
                return self::nl_string_toArray( self::$_Prefs['ignore_domains'] );
            }
            
            return e_DOMAIN;
        }
        
        /**
         * Get parse method preference
         * @return type
         */
        protected static function _getParseMethod()
        {
            //return trim( e107::getPlugPref( 'nofollow', 'parse_method', 'regexHtmlParse_Nofollow' ) );
            return trim( self::$_Prefs['parse_method'] );
        }
        
        
        /**
         * Helper method - Convert newline delimited string to numeric array
         * @param type $str_with_nl
         * @return array
         */
        protected static function nl_string_toArray( $str_with_nl )
        {
            $str = str_replace( ["\r\n", "\n\r"], "|", $str_with_nl );
            return array_unique( explode( "|", $str ) );
        }
        
        
        /**
         * Check if present page is a strpos of exclude page
         * @todo preferably need a foreach loop to loop through all the listed exclude pages
         * @return boolean
         */
        protected static function _excludePage()
        {
            $present_page = $_SERVER['REQUEST_URI']; //e_REQUEST_URI
            
            self::_debugLog($present_page, 'Current-Page');
            
            return in_array($present_page, self::$_excludePages);
            
        }
        
        /**
         * Check if the anchor tag URL is an excluded domain
         * @param string $anchor
         * @return boolean
         * @todo add foreach loop to iterate through all exclude domains when have multiple
         */
        protected static function _excludeDomain( $anchor )
        {
            $excludes = 'physioblasts.org';// <-- test implementation
            
            $href = self::_getHrefValue( $anchor );
            
            self::_debugLog($href);
            
            if ( strpos( $href, $excludes ) !== false )
            {
                return true;
            }
            return false;
        }
        
        /**
         * Get the href attribute value of anchor tag
         * @param string $anchor
         * @return string Href value | ***empty***
         */
        protected static function _getHrefValue( $anchor )
        {
            preg_match('~<a (?>[^>h]++|\Bh|h(?!ref\b))*href\s*=\s*["\']?\K[^"\'>\s]++~i', $anchor, $matches);
            
            if ( ! empty( $matches ) )
            {
                return $matches[0];
            }
            return "***empty***";
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
        
        protected function mandateNofollow( $input )
        {
            $href = self::_getHrefValue( $input );
            
            if ( ! $href && self::_excludeDomain( $href ) )
            {
                return false;
            }
            return true;
        }
        
        /**
         * e107 HTML parser hook
         * @access public
	 * @param string $text html/text to be processed.
	 * @param string $context Current context ie.  OLDDEFAULT | BODY | TITLE | SUMMARY | DESCRIPTION | WYSIWYG etc.
	 * @return string
	 */
	public function toHtml( $text, $context = '' )
	{
            
            if ( self::$_Active )
            {
                
                if ( method_exists( $this, self::$_parseMethod ) )
                {
                    
                    $text = call_user_func( array( self, self::$_parseMethod ), $text );
                    
                    return $text;
                    
                }
                else
                {
                    
                    return $text;
                    
                }
                
            }
            
            return $text;
	}
        
        
        
        /**
	 * Helper method for regexHtmlParse_Nofollow Add rel="nofollow" attribute to HTML anchor elements if not existing.
	 * If already have rel attr. but without 'nofollow' value, append 'nofollow' to its value. 
	 * Insert rel="nofollow" for every other anchor elements passed to it.
	 * 
	 * @param str $anchor - string with opening anchor tag that is passed in
	 * @return str - The modified opening anchor tag string
	 * @access protected
         * 
         * @todo Add another conditional which will look for rel="external" 
         * in anchor tag and replace with rel="external nofollow" target="_blank" 
         * because appending nofollow to an rel attribute with external vaue will 
         * break e107's JS way of making the link open in new window
         * @todo may be refactor the name to 'insert_Nofollow' or 'add_Nofollow'
	 */
	protected static function stamp_NoFollow( $anchor )
	{
            if( strpos( $anchor, 'nofollow' ) )
            { 
                    return $anchor; 
            }

            if( strpos( $anchor, 'rel' ) )
            {
                    $pattern = "/rel=([\"'])([^\\1]+?)\\1/";
                    //$replace = "rel=\\1\\2 nofollow\\1";
                    $replace = "rel=\\1\\2 nofollow\\1 target=\"_blank\"";// <-- this works but have to confirm how accurate it is.			
                    return preg_replace($pattern, $replace, $anchor);
            } 
            else 
            {
                    $pattern = "/<a /";
                    $replace = "<a rel=\"nofollow\" ";
                    return preg_replace($pattern, $replace, $anchor);
            }
	}	
	
        /**
         * Split up $text by HTML tags and inner text scan for anchor tags and 
         * apply nofollow to 'suitable' anchor tag candidates
         * (adopted from linkwords plugin.)
         * 
         * @param str $text - text string that will be altered
         * @param str $opts['context'] - default context
         * @param bool $logflag - switch to log the makenofollow on post
         * @return string Modified text
         * @access protected
         * @todo fix omit based on contexts
         */
        protected static function regexHtmlParse_Nofollow( $text ) 
        {

            $nf_text = '';

            $pattern = '#(<.*?>)#mis';
            $fragments = preg_split( $pattern, $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );

            foreach ( $fragments as $fragment ) 
            {
                if ( strpos( $fragment, '<a' ) !== false && ! strpos( $fragment, '<a' ) )
                { 
                    if ( ! self::_excludeDomain( $fragment ) ) //@TODO: simplify this double negative conditional and too many nesting
                    {
                        $nf_text .= self::stamp_NoFollow( $fragment );
                    }
                    else
                    {
                        $nf_text .= $fragment;
                    }
                    
                }
                else
                {
                    $nf_text .= $fragment;
                }
            }
            return $nf_text;
        }
          
          
        
        /**
         * Boilerplate Sub-method to break-apart the above method logic for simplicity 
         * and maintainalbility and add the operational conditional checks of plugin
         * 
         * @param string $anchor
         * @return string
         * @todo develop the method, do the _excludeDomain() and internal link checks here
         */
        protected function processAnchor( $anchor )
        {
            // IF _excludeDomain() OR internalLink()
            //      RETURN $anchor
            // ELSE
            //      RETURN stamp_Nofollow( $anchor );
            return $processed;
        }
        
        /**
         * Experimental alternative method to add nofollow using PHP DOM Parser
         * Has slight temporal edge over the REGEX method when benchmarked with e107 
         * benchmark class.
         * 
         * @todo Have a known consequence of adding some unprintable space 
         * character or something that mess up bootstrap styling little bit, 
         * to be precise causing the caret sign to drop to a new line in some 
         * anchor tags.
         * 
         * Update: Found out that its(probably the logic inside foreach loop which 
         * iterates anchor tags) stripping 'style' attribute values from anchor tags.
         * 
         * Update: Not stripping of style tag, it's a <p tag that it adds around 
         * anchor text which breaks botstrap layout 
         * 
         * @param string $text
         * @return string
         */
        protected static function htmlDomParse_Nofollow( $text )
        {
            $dom = new DOMDocument;

            $dom->loadHTML( $text );

            $anchors = $dom->getElementsByTagName( 'a' );

            foreach( $anchors as $anchor )
            { 
                $rel = array(); 
                
                if ( $anchor->hasAttribute( 'rel' ) AND ( $relAtt = $anchor->getAttribute( 'rel' ) ) !== '' )
                {
                    //$rel = preg_split( '/\s+/', trim($relAtt) );
                    //$rel = str_replace( " ", ".", trim( $relAtt ) );
                    //$rel = array_unique( explode( ".", $rel ) );
                    $rel = array_unique( explode( " ", trim( $relAtt ) ) );
                }

                if ( in_array( 'nofollow', $rel ) )
                {
                  continue;
                }

                if ( in_array( 'external', $rel ) )
                {
                    $anchor->setAttribute( 'target', '_blank');
                }

                $rel[] = 'nofollow';
                
                $anchor->setAttribute( 'rel', implode( ' ', $rel ) );
            }

            $dom->saveHTML();

            $html = '';

            foreach( $dom->getElementsByTagName( 'body' )->item( 0 )->childNodes as $element ) {
                $html .= $dom->saveXML( $element, LIBXML_NOEMPTYTAG );
                //$html .= $dom->saveXML( $element, LIBXML_HTML_NOIMPLIED );
                //$html .= $dom->saveHTML( $element );
            }

            return $html;      
        }
          
        
        
        /**
         * Nofollow DOM parser using simple_html_dom.php library
         * @todo cleanup unwanted code
         * 
         * @param string - $text
         * @return string - Parsed $text
         */
        protected static function simpleHtmlDomParse_Nofollow( $text )
        {
            //require_once( 'lib/simple_html_dom.php' );
            e107::library('load', 'simple_html_dom');
            
            $dom = new simple_html_dom;
            
            $dom->load($text);
            
            $anchors = $dom->find('a');
            
            foreach ( $anchors as $anchor )
            {
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
                
                if ( (string) $anchor->rel == 'nofollow' )
                {
                    continue;
                }
                
                if (  ( strpos(  (string) $anchor->rel, 'nofollow' ) === false )  
                        && strpos( (string) $anchor->rel, 'external' ) !== false )
                {
                    $anchor->rel = 'nofollow';
                    $anchor->target = '_blank';
                }
                else
                {
                    $anchor->rel = 'nofollow';
                } 
                
              
            }
            
            $text = $dom->save();
            
            $dom->clear();
            
            return $text;
        }
        
        
        
        /**
         * Debug logger
         * @param string $content String content that's being passed in as argument
         * @param string $logname Optional log file name
         */
        private static function _debugLog( $content, $logname = 'Nofollow-Debug' ) {
            $path = e_PLUGIN.'nofollow/'.$logname.'.log';
            file_put_contents($path, $content."\n", FILE_APPEND);
            unset($path, $content);
        }


}




?>