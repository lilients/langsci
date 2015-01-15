<?php

/**
 * @file plugins/generic/hallOfFame/HallOfFamePlugin.inc.php
 *
 * Copyright (c) 2014 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class HallOfFamePlugin
 * 
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class HallOfFamePlugin extends GenericPlugin {


	function register($category, $path) {
			
		if (parent::register($category, $path)) {
			$this->addLocaleData();
			
			if ($this->getEnabled()) {
				HookRegistry::register ('LoadHandler', array(&$this, 'handleHallOfFameRequest'));
			}
			return true;
		}
		return false;

	}

	function handleHallOfFameRequest($hookName, $args) {

		$request = $this->getRequest();
		$templateMgr = TemplateManager::getManager($request);

		$page = $args[0];
		$op = $args[1];

		if ($page == 'about' && in_array($op, array('hallOfFame'))) {

			define('HALLOFFAME_PLUGIN_NAME', $this->getName());
			define('HANDLER_CLASS', 'HallOfFameHandler');
			$this->import('HallOfFameHandler');

			return true;
		} 
		return false;
	}

	function getDisplayName() {
		return __('plugins.generic.hallOfFame.displayName');
	}

	function getDescription() {
		return __('plugins.generic.hallOfFame.description');
	}

}

?>
