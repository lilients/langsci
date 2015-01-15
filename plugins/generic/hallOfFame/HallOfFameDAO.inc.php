<?php

/**
 * @file plugins/generic/hallOfFame/HallOfFameDAO.inc.php
 *
 * Copyright (c) 2000-2014 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class HallOfFameDAO
 *
 */

class HallOfFameDAO extends DAO {
	/**
	 * Constructor
	 */
	function HallOfFameDAO() {
		parent::DAO();
	}

	function getPressAssistant($user_group) {

		$result = $this->retrieve(
			'SELECT submission_id, user_id FROM stage_assignments WHERE user_group_id in
				(SELECT user_group_id FROM user_group_settings WHERE locale="en_US" AND setting_value="'.$user_group.'");'			
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;
		} else {
			$rownr=0;
			$typesetters = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$typesetters[$rownr][0] = $this->convertFromDB($row['submission_id']);
				$typesetters[$rownr][1] = $this->convertFromDB($row['user_id']);
				$rownr = $rownr + 1;				 
				$result->MoveNext();
			}
			$result->Close();
			return $typesetters;
		}
	}




	function getReviews() {

		$result = $this->retrieve(
			'SELECT submission_id, reviewer_id FROM review_assignments WHERE stage_id=3;'
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;
		} else {
			$rownr=0;
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$reviews[$rownr][0] = $this->convertFromDB($row['submission_id']);
				$reviews[$rownr][1] = $this->convertFromDB($row['reviewer_id']);
				$rownr = $rownr + 1;				 
				$result->MoveNext();
			}
			$result->Close();
			return $reviews;
		}
	}

	function getUserName($user_id) {

		$username = array();

		$result = $this->retrieve(
			'SELECT first_name, last_name FROM users WHERE user_id='.$user_id.';'
		);
		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;
		} else {
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$reviewername[] = $this->convertFromDB($row['first_name']);
				$reviewername[] = $this->convertFromDB($row['last_name']);			 
				$result->MoveNext();
			}
			$result->Close();
			return $reviewername;
		}
	}

	function getSubmissionTitle($submission_id) {

		$result1 = $this->retrieve(
			'SELECT setting_value FROM submission_settings WHERE locale="en_US" AND setting_name="title" AND submission_id='.$submission_id.';'
		);
		$result2 = $this->retrieve(
			'SELECT setting_value FROM submission_settings WHERE locale="en_US" AND setting_name="prefix" AND submission_id='.$submission_id.';'
		);

		if ($result1->RecordCount() == 0) {
			$returner = null;
			$result1->Close();
			$result2->Close();
			return $returner;
		} else {
		while (!$result1->EOF) {
				$row1 = $result1->getRowAssoc(false);
				$row2 = $result2->getRowAssoc(false);
				$title = $this->convertFromDB($row1['setting_value']);
				$prefix = $this->convertFromDB($row2['setting_value']);
				if (!$prefix=="") {
					$submissionTitle = $prefix . " ";
				} 
				$submissionTitle = $submissionTitle . $title;		 
				$result1->MoveNext();
			}
			$result1->Close();
			$result2->Close();
			return $submissionTitle;
		}
	}
}

?>
