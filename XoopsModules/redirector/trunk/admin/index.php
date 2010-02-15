<?php

include 'admin_header.php';
include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
include_once '../include/functions.php';

error_reporting(E_ALL);
global $xoopsDB;

  if (isset($_GET)) {
    foreach ($_GET as $k => $v) {
      ${$k} = $v;
    }
  }

  if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
      ${$k} = $v;
    }
  }
  
switch ($op){
case "delete":
	if (!$id)
		{
		redirect_header('index.php', 2, _AM_REDIRECT_NOID);
	} else {
		$redirect_handler = xoops_getmodulehandler('redirection', 'redirector');
		switch ($id){
		default:
			$redirect = $redirect_handler->get($id);	
		}
		if ($redirect_handler->kill($id))
			redirect_header('index.php?op=list', 2, _AM_REDIRECT_DELETEGOOD);
		else
			redirect_header('index.php?op=edit&id='.$redirect->getVar('id'), 2, _AM_REDIRECT_DELETEBAD);		
		exit;
		break;
	}
case "save":
	if (!$id)
		{
		redirect_header('index.php', 2, _AM_REDIRECT_NOID);
	} else {
		$redirect_handler = xoops_getmodulehandler('redirection', 'redirector');
		switch ($id){
		case "new":
			$redirect = $redirect_handler->create();	
			break;
		default:
			$redirect = $redirect_handler->get($id);	
		}

		$redirect->setVar('name', $name);
		$redirect->setVar('type', $type);
		$redirect->setVar('groups', $groups);
		$redirect->setVar('redirect_url', $redirect_url);
		$redirect->setVar('redirect_message', $redirect_message);
		$redirect->setVar('redirect_time', $redirect_time);
		$redirect->setVar('agents', $agents);
		$redirect->setVar('domains', $domains);
		$redirect->setVar('xml_conf', $xml_conf);		
		
		if ($redirect_handler->insert($redirect))
			redirect_header('index.php?op=edit&id='.$redirect->id(), 2, _AM_REDIRECT_SAVEGOOD);
		else
			redirect_header('index.php?op=list', 2, _AM_REDIRECT_SAVEBAD);		
	}
	
	exit;
	break;
	
case "new":
case "edit":
	
	if (!$id)
	{
		$id='new';
	} else {
		$redirect_handler = xoops_getmodulehandler('redirection', 'redirector');
		$redirect = $redirect_handler->get($id);
		$name = $redirect->name();
		$type = $redirect->type();
		$groups = $redirect->groups();
		$redirect_url = $redirect->redirect_url();
		$redirect_message = $redirect->redirect_message();
		$redirect_time = $redirect->redirect_time();
		$agents = $redirect->agents();
		$domains = $redirect->domains();
		$xml_conf = $redirect->xml_conf();
	}
		
	$form_new = new XoopsThemeForm(_XS_NEWREDIRECT, "newredirect", $_SERVER['PHP_SELF'] ."");
	$form_new->setExtra( "enctype='multipart/form-data'" ) ;
	
	$ele_tray[$id] = new XoopsFormElementTray($name.($id=='new')?_XS_NEWREDIRECT_NEW:'','&nbsp;',$name);
	
	$form_new->addElement(new XoopsFormText(_XS_NEWREDIRECT_NAME, "name", 45, 128, $name));	
	$form_sel = new XoopsFormSelect(_XS_NEWREDIRECT_TYPE, "type", $type);
	$form_sel->addOptionArray(array('302' => '302 Redirection',
									'301' => '301 Redirection',
									'iframe' => 'IFrame Redirection',
									'header' => 'Header Redirection'));
	$form_new->addElement($form_sel);	
	$form_new->addElement(new XoopsFormSelectGroup(_XS_NEWREDIRECT_GROUP, "groups", true, $groups, 4, true));		
	$form_new->addElement(new XoopsFormTextArea(_XS_NEWREDIRECT_URL, "redirect_url", $redirect_url, 6, 50));
	$form_new->addElement(new XoopsFormTextArea(_XS_NEWREDIRECT_MESSAGE, "redirect_message", $redirect_message, 6, 50));			
	
	
	$form_selb = new XoopsFormSelect(_XS_NEWREDIRECT_TIME, "redirect_time", $redirect_time);
	$form_selb->addOptionArray(array('1' => '1 Second',
									'3' => '3 Seconds',
									'5' => '5 Seconds',
									'10' => '10 Seconds'));
	$form_new->addElement($form_selb);	

	$form_new->addElement(new XoopsFormTextArea(_XS_NEWREDIRECT_AGENTS, "agents", $agents, 10, 50));				
	$form_new->addElement(new XoopsFormCheckBoxDomains(_XS_NEWREDIRECT_DOMAINS, "domains", $domains));	
 	$form_new->addElement(new XoopsFormHidden("op", "save"));
	$form_new->addElement(new XoopsFormHidden("id", $id));
	$form_new->addElement(new XoopsFormButton('', 'send', _SEND, 'submit'));
	xoops_cp_header();
	adminMenu(1);
	$form_new->display();
	footer_adminMenu();
	xoops_cp_footer();
	break;
case "list":
default:

	$redirect_handler = xoops_getmodulehandler('redirection', 'redirector');
	$redirects = $redirect_handler->getObjects(NULL);
	
	$form = new XoopsThemeForm(_XS_REDIRECTS, "redirects", $_SERVER['PHP_SELF'] ."");
	$form->setExtra( "enctype='multipart/form-data'" ) ;
	
	$ele_tray = array();
	
	foreach($redirects as $redirect) {
	
		$id = $redirect->id();
		$name = $redirect->name();
		$type = $redirect->type();
		$groups = $redirect->groups();
		$redirect_url = $redirect->redirect_url();
		$redirect_message = $redirect->redirect_message();
		$redirect_time = $redirect->redirect_time();
		$agents = $redirect->agents();
		$domains = $redirect->domains();
		$xml_conf = $redirect->xml_conf();
	
		$ele_tray[$id] = new XoopsFormElementTray($name,'&nbsp;',$name);
		$ele_tray[$id]->addElement(new XoopsFormHidden("id", $id));
		$ele_tray[$id]->addElement(new XoopsFormLabel("&nbsp;", '<a href="'.XOOPS_URL.'/modules/redirector/admin/index.php?op=edit&id='.$redirect->getVar('id').'">'._EDIT.'</a>'));		
		$ele_tray[$id]->addElement(new XoopsFormLabel("&nbsp;", '<a href="'.XOOPS_URL.'/modules/redirector/admin/index.php?op=delete&id='.$redirect->getVar('id').'">'._DELETE.'</a>'));		
		$ele_tray[$id]->addElement(new XoopsFormLabel("type:", $type));		
		$ele_tray[$id]->addElement(new XoopsFormLabel("name:", $name));							
		
		$form->addElement($ele_tray[$id]);
	}
	
	xoops_cp_header();
	adminMenu(2);
	$form->display();
	footer_adminMenu();
	xoops_cp_footer();
	break;
	
}	

?>