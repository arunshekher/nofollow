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
    
    
        private static $_nofollow_Prefs = array();
        private static $_nofollow_Active = false;
        private $_nofollow_ignoreDomains = array();
        private $_nofollow_ignorePages = array();


        /* constructor */
	function __construct()
	{
            // admin area doesnt require nofollow service
            if(e_ADMIN_AREA === true) 
            { 
                return; 
            }
            // set prefs
            self::Prefs();
            // set status
            self::Status();
        }
        
        
        /**
         * Pref Setter - Retrieve and set plugin preferences
         * 
         */
        protected static function Prefs()
        {
            self::$_nofollow_Prefs = e107::getPlugPref('nofollow');
        }
        
        
        /**
         * Status Setter - set plugin status
         * 
         */
        protected static function Status()
        {
            if ( count(self::$_nofollow_Prefs ) )
            {
                self::$_nofollow_Active = self::$_nofollow_Prefs['globally_on'];
            }
            else
            {
                self::$_nofollow_Active = false;
            }
        }

        /**
	 * @param string $text html/text to be processed.
	 * @param string $context Current context ie.  OLDDEFAULT | BODY | TITLE | SUMMARY | DESCRIPTION | WYSIWYG etc.
	 * @return string
	 */
	function toHtml($text, $context='')
	{
            //require_once e_HANDLER.'benchmark.php';
            //$bench = new e_benchmark();
            //$bench->start();
            
            $text = $this->nofollow_toHtml($text);
            
            //$text = $this->nofollow_toHtml_DOM( $text );
            
            //$bench->end()->logResult('Nofollow_DOM_Method-1');
            //$bench->end()->logResult('Nofollow_REGEX_Method-1');
            //$bench->end();
            //$bench->printResult();
            // Debug
            //print_a(self::$_nofollow_Active);
            return $text;
	}
        
        
        
        /**
	 * Adds rel="nofollow" attribute to html anchor tags if not present.
	 * If already have an rel attr. but no nofollow value, appends nofollow. 
	 * Inserts rel="nofollow" for everything else passed to it.
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
	protected function stamp_NoFollow($anchor)
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
	 * Split up $text by html tags scans for anchor tags and apply 
         * nofollow to suitable anchor tag candidates
	 * (adopted from linkwords plugin.)
	 * 
	 * @param str $text - text string that will be altered
	 * @param str $opts['context'] - default context
	 * @param bool $logflag - switch to log the makenofollow on post
	 * @return string Modified text
	 * @access public
	 * @todo fix omit based on contexts
	 */
       
          
          
          public function nofollow_toHtml($text) 
          {
              
              $nf_text = '';
              
              $pattern = '#(<.*?>)#mis';
              $fragments = preg_split($pattern, $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
              
              foreach ($fragments as $fragment ) 
              {
                  if ( strpos( $fragment, '<a' ) !== false && !strpos( $fragment, '<a' ) )
                  {
                      $nf_text .= $this->stamp_NoFollow($fragment);
                  }
                  else
                  {
                      $nf_text .= $fragment;
                  }
              }
              return $nf_text;
          }
          
          
          
          /**
           * Alternative method to add nofollow using PHP HTML DOM Parser
           * Has slight temporal edge over the REGEX method when tested with e107 benchmark class
           * 
           * @todo Have a known consequence of adding some unprintable space 
           * character or something that mess up bootstrap styling little bit, 
           * to be precise causing the caret sign to drop to a new line in some anchor tags.
           * Update: Found out that its(probably the foreach loop which iterates anchor tags) 
           * stripping 'style' attribute values from anchor tags.
           * 
           * @param type $text
           * @return type
           */
          protected function nofollow_toHtml_DOM( $text )
          {
                $dom = new DOMDocument;
 
                $dom->loadHTML( $text );

                $anchors = $dom->getElementsByTagName( 'a' );

                foreach( $anchors as $anchor )
                { 
                    $rel = array(); 

                    if ( $anchor->hasAttribute( 'rel' ) AND ( $relAtt = $anchor->getAttribute( 'rel' ) ) !== '' )
                    {
                       $rel = preg_split( '/\s+/', trim($relAtt) );
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
                }

                return $html;      
          }
          
          
          protected static function goodDomain( $domain )
          {
              
          }



}




?>