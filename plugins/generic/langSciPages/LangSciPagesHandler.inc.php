<?php

/**
 * @file plugins/generic/langSciPages/LangSciPagesHandler.inc.php
 *
 * Copyright (c) Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class LangSciPagesHandler
 *
 * Find the content and display the appropriate page
 *
 */

import('classes.handler.Handler');

class LangSciPagesHandler extends Handler {

	function groupMail($args, $request) {
		$templateMgr = $this->getTemplateManager($request)	;
		$templateMgr->assign('pageTitle', 'plugins.generic.langSciPages.title.groupMail');
		$templateMgr->assign('content', __('plugins.generic.langSciPages.content.groupMail'));
 		$this->assignPressUrl($templateMgr,'about');
		$this->displayTemplate($templateMgr,'page.tpl');
	}

	function imprint($args, $request) {
		$templateMgr = $this->getTemplateManager($request)	;
		$templateMgr->assign('pageTitle', 'plugins.generic.langSciPages.title.imprint');
		$templateMgr->assign('content', __('plugins.generic.langSciPages.content.imprint'));

 		$this->assignPressUrl($templateMgr,'about');
		$this->displayTemplate($templateMgr,'page.tpl');
	}


	function blog($args, $request) {
		header("Location:http://userblogs.fu-berlin.de/langsci-press/"); 
	}

	function newsletter($args, $request) {		
		$templateMgr = $this->getTemplateManager($request)	;
		$templateMgr->assign('pageTitle', 'plugins.generic.langSciPages.title.newsletter');
		$templateMgr->assign('content', __('plugins.generic.langSciPages.content.newsletter'));
		$this->displayTemplate($templateMgr,'page.tpl');
	}
	
	function advisoryBoard($args, $request) {				
		$templateMgr = $this->getTemplateManager($request)	;
		$templateMgr->assign('pageTitle', 'plugins.generic.langSciPages.title.advisoryBoard');
		$templateMgr->assign('content', __('plugins.generic.langSciPages.content.advisoryBoard'));
		$this->displayTemplate($templateMgr,'page.tpl');
	}

	function forAuthors($args, $request) {				
		$templateMgr = $this->getTemplateManager($request)	;
		$templateMgr->assign('pageTitle', 'plugins.generic.langSciPages.title.forAuthors');
		$templateMgr->assign('content', __('plugins.generic.langSciPages.content.forAuthors'));
		$this->assignOmpUrl($templateMgr,'information');
 		$this->assignPressUrl($templateMgr,'information');
		$this->displayTemplate($templateMgr,'page.tpl');
	}

	function forEditors($args, $request) {				
		$templateMgr = $this->getTemplateManager($request)	;
		$templateMgr->assign('pageTitle', 'plugins.generic.langSciPages.title.forEditors');
		$templateMgr->assign('content', __('plugins.generic.langSciPages.content.forEditors'));
 		$this->assignOmpUrl($templateMgr,'information');
 		$this->assignPressUrl($templateMgr,'information');
		$this->displayTemplate($templateMgr,'page.tpl');
	}

	function forReaders($args, $request) {				
		$templateMgr = $this->getTemplateManager($request)	;
		$templateMgr->assign('pageTitle', 'plugins.generic.langSciPages.title.forReaders');
		$templateMgr->assign('content', __('plugins.generic.langSciPages.content.forReaders'));
 		$this->assignPressUrl($templateMgr,'information');
		$this->displayTemplate($templateMgr,'page.tpl');
	}


	function templates($args, $request) {
		$templateMgr = $this->getTemplateManager($request)	;
		$templateMgr->assign('pageTitle', 'plugins.generic.langSciPages.title.templates');
		$templateMgr->assign('content', __('plugins.generic.langSciPages.content.templates'));
 		$this->assignOmpUrl($templateMgr,'information');
		$this->displayTemplate($templateMgr,'page.tpl');
	}

	function achievements($args, $request) {
		$templateMgr = $this->getTemplateManager($request)	;
		$templateMgr->assign('pageTitle', 'plugins.generic.langSciPages.title.achievements');
		$templateMgr->assign('content', __('plugins.generic.langSciPages.content.achievements'));
		$this->displayTemplate($templateMgr,'page.tpl');
	}

	function reviewConfigurations($args, $request) {
		$templateMgr = $this->getTemplateManager($request)	;
		$templateMgr->assign('pageTitle', 'plugins.generic.langSciPages.title.reviewConfigurations');
		$templateMgr->assign('content', __('plugins.generic.langSciPages.content.reviewConfigurations'));
		$this->displayTemplate($templateMgr,'page.tpl');
	}

	// must work with LangSci urls and local urls (index, pressname)
	function assignOmpUrl($templateMgr,$cut) {
 		$host = "http://$_SERVER[HTTP_HOST]";
		$shortUrl = substr($_SERVER[REQUEST_URI],1,strpos($_SERVER[REQUEST_URI], $cut)-1);
		$nativeUrl = substr($shortUrl ,0,strpos($shortUrl, '/'));
		if (!$nativeUrl=="") {
			$nativeUrl = $nativeUrl . "/";
		}
		$templateMgr->assign('ompUrl',$host ."/".$nativeUrl);	
	}

	function assignPressUrl($templateMgr,$cut) {
 		$host = "http://$_SERVER[HTTP_HOST]";
		$shortUrl = substr($_SERVER[REQUEST_URI],1,strpos($_SERVER[REQUEST_URI],$cut)-1);
		$templateMgr->assign('pressUrl',$host ."/".$shortUrl);	
	}

	function getTemplateManager($request)	{
		$this->validate();
		$press = $request->getPress();
		$this->setupTemplate($request, $press);
		$templateMgr = TemplateManager::getManager($request);	
		return $templateMgr;
	}

	function displayTemplate($templateMgr,$filename) {
		$langSciPagesPlugin = PluginRegistry::getPlugin('generic', LANGSCIPAGES_PLUGIN_NAME);
		$templateMgr->display($langSciPagesPlugin->getTemplatePath().$filename);
	}

}

?>
