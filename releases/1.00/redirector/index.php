<?php

	include ('../../mainfile.php');

	if (!isset($_REQUEST['id']))
		redirect_header(XOOPS_URL, 3,_AM_REDIRECT_NOID);
	
	$id = intval($_REQUEST['id']);
	$redirect_handler = xoops_getmodulehandler('redirection', 'redirector');
	$redirect = $redirect_handler->get($id);	
	
	$xoopsOption['template_main'] = "redirector_frameredir.html";
	include XOOPS_ROOT_PATH . '/header.php';	
	
	global $xoopsTpl;
	
	$xoopsTpl->assign('xoops_redirecturl', $redirect->redirect_url());
	
	include XOOPS_ROOT_PATH . '/footer.php';	
?>
