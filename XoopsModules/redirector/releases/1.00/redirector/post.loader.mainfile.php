<?php

	$redirect_handler = xoops_getmodulehandler('redirection', 'redirector');
	$redirects = $redirect_handler->getObjects(NULL);	
	
	foreach ($redirects as $redirect)
		$redirect_handler->checkRedirection($redirect);

?>
