<?php
// $Id$
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}
/**
 * Class for policies
 * @author Simon Roberts <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * @package kernel
 */
class RedirectorRedirection extends XoopsObject
{

    function RedirectorRedirection($id = null)
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 128);
        $this->initVar('type', XOBJ_DTYPE_OTHER, null, false, 128);		
        $this->initVar('groups', XOBJ_DTYPE_ARRAY, null, false, 255);
        $this->initVar('redirect_url', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('redirect_message', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('redirect_time', XOBJ_DTYPE_INT, null, false);
        $this->initVar('agents', XOBJ_DTYPE_OTHER, null, false);		
        $this->initVar('domains', XOBJ_DTYPE_ARRAY, null, false);
        $this->initVar('xml_conf', XOBJ_DTYPE_OTHER, null, false);

    }


    /**
     * get the policies name
	 * @param string $format format for the output, see {@link XoopsObject::getVar()}
     * @return string
     */
    function id($format="S")
    {
        return $this->getVar("id", $format);
    }
	
    function name($format="S")
    {
        return $this->getVar("name", $format);
    }

    function type($format="S")
    {
        return $this->getVar("type", $format);
    }

    function agents()
    {
        return $this->getVar("agents");
    }

    function groups($format="S")
    {
        return $this->getVar("groups", $format);
    }

    function redirect_url($format="S")
    {
        return $this->getVar("redirect_url", $format);
    }

    function redirect_message($format="S")
    {
        return $this->getVar("redirect_message", $format);
    }
	
    function redirect_time()
    {
        return $this->getVar("redirect_time");
    }
	
    function domains()
    {
        return $this->getVar("domains");
    }

    function xml_conf()
    {
        return $this->getVar("xml_conf");
    }

}


/**
* XOOPS policies handler class.
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS user class objects.
*
* @author  Simon Roberts <simon@chronolabs.org.au>
* @package kernel
*/
class RedirectorRedirectionHandler extends XoopsPersistableObjectHandler
{
    function __construct(&$db) 
    {
        parent::__construct($db, "redirections", 'RedirectorRedirection', "id", "name");
    }
	
	function kill($id)
	{
		global $xoopsDB;
		$sql = "DELETE FROM ".$xoopsDB->prefix('redirections').' WHERE id = '.$id;
		return $xoopsDB->queryF($sql);
	}
	
	function checkRedirection($Redirection)
	{
		if (!is_a($Redirection, 'RedirectorRedirection'))
			return false;
			
		// Checks Agents
		if (strlen($Redirection->agents())>0) {
			foreach(explode('|',$Redirection->agents()) as $agent)
				if (eregi ($agent, $HTTP_SERVER_VARS['HTTP_USER_AGENT']))
				{
					$agents_match=true;
				}
			} else {
					$agents_match=true;
			}
				
	
		// Checks User Groups
		if (count($Redirection->getVar('groups'))>0)
		{
			global $xoopsUser;
			$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : array(3,4);
			foreach($groups as $group)
				if (in_array ($group, $Redirection->getVar('groups')) )
				{
					$groups_match=true;
				}
		} else {
			$groups_match=true;
		}			
	
		// Checks Domains
		if (count($Redirection->getVar('domains'))>0) {
			$xstr = str_replace($_SERVER['HTTP_HOST'], '%s', XOOPS_URL);

			if (in_array (urlencode(XOOPS_URL), $Redirection->getVar('domains')) )
				{
					$domain_match=true;
				}
			} else {
					$domain_match=true;
			}
			
		if (in_array ('all', $Redirection->getVar('domains')) )
			{
				$domain_match=true;
			}
			

		if ($agents_match == true && $groups_match == true && $domain_match == true)
		{
			switch($Redirection->type()){
			case "301":
				if (strpos(XOOPS_URL.$_SERVER['REQUEST_URI'], 'modules/redirector/?id')==0)
				{
					header( "HTTP/1.1 301 Moved Permanently" ); 
					header( "Location: ".$Redirection->redirect_url());
					exit;				
				}
				break;
			case "302":
				if (strpos(XOOPS_URL.$_SERVER['REQUEST_URI'], 'modules/redirector/?id')==0)
				{
					header( "HTTP/1.1 302 Moved Temporarily" ); 
					header( "Location: ".$Redirection->redirect_url());
					exit;				
				}
				break;
			case "header":
				if (strpos(XOOPS_URL.$_SERVER['REQUEST_URI'], 'modules/redirector/?id')==0)
				{
					redirect_header($Redirection->redirect_url(), $Redirection->redirect_time(), $Redirection->redirect_message());
				}
				return true;
				exit;
			case "iframe":
				if (strpos(XOOPS_URL.$_SERVER['REQUEST_URI'], 'modules/redirector/?id')==0)
				{
					header( "HTTP/1.1 301 Moved Permanently" ); 
					header( "Location: ".XOOPS_URL.'/modules/redirector/?id='.$Redirection->getVar('id'));
					exit;				
				}
				break;
			default:
				return false;
			}
			return false;
		} else {
			return false;
		}
	}
}
?>