<?php

/**
 * @file plugins/generic/langSciPages/LangSciPagesPlugin.inc.php
 *
 * Copyright (c) 2014 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class LangSciPagesPlugin
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class LangSciPagesPlugin extends GenericPlugin {


	function register($category, $path) {
			
		
		if (parent::register($category, $path)) {
			$this->addLocaleData();
			
			if ($this->getEnabled()) {
				HookRegistry::register ('LoadHandler', array(&$this, 'handleLangSciPagesRequest'));
			}
			return true;
		}
		return false;
	}

	function handleLangSciPagesRequest($hookName, $args) {

		$request = $this->getRequest();
		$templateMgr = TemplateManager::getManager($request);

		$page = $args[0];
		$op = $args[1];

		if ($page=='blog') {$args[1]='blog';}

		if ($page == 'information' && in_array($op, array('forAuthors','forEditors','forReaders','templates'))||
			 $page == 'about' && in_array($op, array('advisoryBoard','imprint'))||
			 $page == 'management' && in_array($op, array('groupMail'))||
			 $page == 'blog') {

			define('LANGSCIPAGES_PLUGIN_NAME', $this->getName());
			define('HANDLER_CLASS', 'LangSciPagesHandler');

			$this->import('LangSciPagesHandler');
			return true;
		} 
		return false;
	}

	function getDisplayName() {
		return __('plugins.generic.langSciPages.displayName');
	}

	function getDescription() {
		return __('plugins.generic.langSciPages.description');
	}

}

?>
