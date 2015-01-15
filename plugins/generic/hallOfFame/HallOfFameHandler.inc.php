<?php

/**
 * @file plugins/generic/hallOfFame/HallOfFameHandler.inc.php
 *
 * Copyright (c) Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class HallOfFameHandler
 *
 * Find the content and display the appropriate page
 *
 */

import('classes.handler.Handler');
import('plugins.generic.hallOfFame.HallOfFameDAO');


class HallOfFameHandler extends Handler {	

	function hallOfFame($args, $request) {
	
		$templateMgr = $this->getTemplateManager($request)	;
		//$templateMgr->assign('printReviewer',$this->getUserSubmissionList("Reviewer"));
		$templateMgr->assign('printProofreader',$this->getUserSubmissionList("Typesetter"));
		$templateMgr->assign('printTypesetter',$this->getUserSubmissionList("Proofreader"));
		$templateMgr->assign('pageTitle', 'plugins.generic.title.halloffame');
		$templateMgr->assign('introText', 'plugins.generic.intro.halloffame');

		$this->assignOmpUrl($templateMgr,'about');
		$this->displayTemplate($templateMgr,'page.tpl');
	}

	function assignOmpUrl($templateMgr,$cut) {
 		$host = "http://$_SERVER[HTTP_HOST]";
		$shortUri = substr($_SERVER[REQUEST_URI],1,strpos($_SERVER[REQUEST_URI], $cut)-1);
		$nativeUri = substr($shortUri ,0,strpos($shortUri, '/'));
		if (!$nativeUri=="") {
			$nativeUri = $nativeUri . "/";
		}
		$templateMgr->assign('ompUrl',$host ."/".$nativeUri);	
	}


	function getUserSubmissionList($userGroup) {

		$submissionTitles = array();
		$usernames = array();
		$usernames_second = array();
		$numberOfItems = array();

		$hallOfFameDAO = new HallOfFameDAO;

		if ($userGroup=="Reviewer") {
			$Ids = $hallOfFameDAO ->getReviews();
		} else {		
			$Ids = $hallOfFameDAO->getPressAssistant($userGroup);
		}

		for ($row = 0; $row< sizeof($Ids); ++$row) {

			$submissionTitle = $hallOfFameDAO ->getSubmissionTitle($Ids[$row][0]);
			$username = $hallOfFameDAO ->getUserName($Ids[$row][1]);	

			$pos = array_search($username[0]." ".$username[1], $usernames);
			if ($pos||$pos===0) {
				$numberOfItems[$pos] = $numberOfItems[$pos] + 1;
				$submissionTitles[$pos] .= "<li>" . $submissionTitle ."</li>";
			} else {
				$usernames[] = $username[0]." ".$username[1];
				$usernames_second[] = $username[1];
				$numberOfItems[] = 1;
				$submissionTitles[] = "<li>" . $submissionTitle ."</li>";
			}
			array_multisort($numberOfItems,SORT_DESC, $usernames_second,$usernames,$submissionTitles);
		}

		// format
		$returner = "<h3>".$userGroup."</h3><br><table>";

		for ($row = 0; $row< sizeof($submissionTitles); ++$row) {
				$numberOfStars = $numberOfItems[$row];
				$numberOfStarsInARow = 10;
				$widthOneStar=15;
				$widthStars1=$numberOfStarsInARow*$widthOneStar;
				$heightStars1=floor($numberOfStars/$numberOfStarsInARow)*$widthOneStar;
				$widthStars2=$numberOfStars%$numberOfStarsInARow*$widthOneStar;
				$heightStars2=$widthOneStar;
				$heightStarsContainer = $heightStars1+$heightStars2;
				$returner .= '<tr>';
				$returner .= '<td class="topleft"><button class="showSubmissions" 
									id="'.$usernames[$row]."_img".'" type="button" onclick="showSubmissions(\''.$usernames[$row].'\')"></button>'.$usernames[$row]."</td>";
				$returner .= '<td><div width: 150px; height: '.$heightStarsContainer.'px;">
										<div id="stars1" style="width:'.$widthStars1.'px;height:'.$heightStars1.'px;"></div>
										<div id="stars2" style="width:'.$widthStars2.'px;height:'.$heightStars2.'px;"></div>
										</div>
									<td>';
				$returner .= '</tr>';
				$returner .= '<tr><td class="topbottom"><ul id="'.$usernames[$row].'">'. $submissionTitles[$row].  "</ul></td></tr>";
		}
		$returner .= "</table>";
		return $returner;

	}

	function getTemplateManager($request)	{
		$this->validate();
		$press = $request->getPress();
		$this->setupTemplate($request, $press);
		$contentOnly = $request->getUserVar('contentOnly');
		$templateMgr = TemplateManager::getManager($request);
		$server_path = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";       
		$templateMgr->assign('ompUrl',substr($server_path,0,strpos($server_path, 'index.php')));	
		return $templateMgr;
	}

	function displayTemplate($templateMgr,$filename) {
		$hallOfFamePlugin = PluginRegistry::getPlugin('generic', HALLOFFAME_PLUGIN_NAME);
		$templateMgr->display($hallOfFamePlugin->getTemplatePath().$filename);
	}

}

?>
