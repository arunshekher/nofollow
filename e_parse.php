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


	/* constructor */
	function __construct()
	{


	}


	/**
	 * @param string $text html/text to be processed.
	 * @param string $context Current context ie.  OLDDEFAULT | BODY | TITLE | SUMMARY | DESCRIPTION | WYSIWYG etc.
	 * @return string
	 */
	function toHtml($text, $context='')
	{
            //$text = str_replace('****', '<hr />', $text);
            
            
            //$text = $this->makeNoFollow($text);
            
            $text = $this->nf_searchReplace($text);
            return $text;
	}
        
        
        
        /**
	 * Adds rel="nofollow" attribute to html anchor tags if not present.
	 * If already have an rel attr. but no nofollow, appends nofollow attribute. 
	 * inserts rel="nofollow" for everything else.
	 * 
	 * @param str $anchor - string with opening anchor tag that is passed in
	 * @return str - The modified opening anchor tag string
	 * @access private
	 */
	protected function makeNoFollow($anchor)
	{
		if( strpos( $anchor, 'nofollow' ) )
		{ 
			return $anchor; 
		}
		
		if( strpos( $anchor, 'rel' ) )
		{
			$pattern = "/rel=([\"'])([^\\1]+?)\\1/";
			$replace = "rel=\\1\\2 nofollow\\1";			
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
	 * Scans through $text for anchor tags after spliting up by html tags
	 * (adopted from linkwords plugin.)
	 * 
	 * @param str $text - The anchor string that will be altered
	 * @param str $opts['context'] - default context
	 * @param bool $logflag - switch to log the makenofollow on post
         * @return string Modified text
	 * @access public
	 * @todo fix omit based on contexts
	 */
       
          
          
          public function nf_searchReplace(&$text) 
          {
              
              $nf_text = '';
              
              $pattern = '#(<.*?>)#mis';
              $fragments = preg_split($pattern, $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
              
              foreach ($fragments as $fragment ) 
              {
                  if ( strpos( $fragment, '<a' ) !== false && !strpos( $fragment, '<a' ) )
                  {
                      $nf_text .= $this->makeNoFollow($fragment);
                  }
                  else
                  {
                      $nf_text .= $fragment;
                  }
              }
              return $nf_text;
          }



}




?>