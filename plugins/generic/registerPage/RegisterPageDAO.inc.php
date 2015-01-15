<?php

/**
 * @file plugins/generic/registerPage/RegisterPageDAO.inc.php
 *
 * Copyright (c) 2000-2014 Carola Fanselow, Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class RegisterPageDAO
 *
 * Class for Register Page DAO.
 * Operations for retrieving and modifying users
 */

class RegisterPageDAO extends DAO {
	/**
	 * Constructor
	 */
	function RegisterPageDAO() {
		parent::DAO();
	}


	/**
	 * Retrieve all usernames
	 * @return array
	 */
	function getUsernames() {
		$users = array();

		$result = $this->retrieve(
			'SELECT username FROM users;'
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;

		} else {
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$value = $this->convertFromDB($row['username']);
				$users[] = $value;
				$result->MoveNext();
			}
			$result->Close();

			return $users;
		}
	}

	/**
	 * Retrieve all emails
	 * @return array
	 */
	function getEmails() {
		$emails = array();

		$result = $this->retrieve(
			'SELECT email FROM users;'
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;

		} else {
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$value = $this->convertFromDB($row['email']);
				$emails[] = $value;
				$result->MoveNext();
			}
			$result->Close();

			return $emails;
		}
	}

	/**
	 * Retrieve user_group_id by user group name
	 * @return user_group_id
	 */
	function getUserGroupID($name) {

		$result = $this->retrieve(
			'SELECT user_group_id FROM user_group_settings WHERE setting_value="'.$name .'" AND locale="en_US";'
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;

		} else {
			$row = $result->getRowAssoc(false);
			$userGroupId = $this->convertFromDB($row['user_group_id']);
			$result->Close();

			return $userGroupId;
		};
	}

	/**
	 * Retrieve date_registered by user_id
	 * @return date_registered
	 */
	function getDateRegistered($user_id) {

		$result = $this->retrieve(
			'SELECT date_registered FROM users WHERE user_id='.$user_id
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;

		} else {
			$row = $result->getRowAssoc(false);
			$dateRegistered = $this->convertFromDB($row['date_registered']);
			$result->Close();

			return $dateRegistered;
		}
	}

	/**
	 * Retrieve email from tables users by user_id
	 * @return email
	 */
	function getEmail($user_id) {

		$result = $this->retrieve(
			'SELECT email FROM users WHERE user_id='.$user_id
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;

		} else {
			$row = $result->getRowAssoc(false);
			$email = $this->convertFromDB($row['email']);
			$result->Close();

			return $email;
		}
	}

	/**
	 * Retrieve next user id
	 * @return nextUserId
	 */
	function getNextUserId() {

		$result = $this->retrieve(
			'SELECT user_id FROM users WHERE user_id=(SELECT MAX(user_id) FROM users)'
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;

		} else {
			$row = $result->getRowAssoc(false);
			$nextUserId = $this->convertFromDB($row['user_id'])+1;
			$result->Close();

			return $nextUserId;
		};
	}

	/**
	 * Retrieve user_id by username from tables users
	 * @return user_id
	 */
	function getUserId($username) {

		$result = $this->retrieve(
			'SELECT user_id FROM users WHERE username="'.$username.'"'
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;

		} else {
			$row = $result->getRowAssoc(false);
			$userId = $this->convertFromDB($row['user_id']);
			$result->Close();

			return $userId;
		};

	}


	function getUserGroups($username) {

		$result = $this->retrieve(
			'SELECT setting_value FROM user_group_settings WHERE locale = "en_US" AND setting_name="name" AND
			user_group_id IN (SELECT user_group_id FROM user_user_groups WHERE user_id=(select user_id from users where username="'.$username.'"))'
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return array();
		} else {
			$userGroups = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$userGroups[]= $this->convertFromDB($row['setting_value']);				 
				$result->MoveNext();
			}
			$result->Close();
			return $userGroups;
		}
	}

	function getSubscriptions($username) {

		$result = $this->retrieve(
			'SELECT subscription FROM langsci_user_subscriptions WHERE user_id=(SELECT user_id FROM users WHERE username="'.$username.'")'
		);

		if ($result->RecordCount() == 0) {
			$result->Close();
			return array();
		} else {
			$subscriptions = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$subscriptions[]= $this->convertFromDB($row['subscription']);				 
				$result->MoveNext();
			}
			$result->Close();
			return $subscriptions;
		}
	}

	function getReviewAreas($username) {

		$result = $this->retrieve(
			'SELECT reviewarea FROM langsci_user_reviewareas WHERE user_id=(SELECT user_id FROM users WHERE username="'.$username.'")'
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;
		} else {
			$reviewAreas = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$reviewAreas[]= $this->convertFromDB($row['reviewarea']);				 
				$result->MoveNext();
			}
			$result->Close();
			return $reviewAreas;
		}
	}

	function getReviewLanguages($username) {

		$result = $this->retrieve(
			'SELECT reviewlanguage FROM langsci_user_reviewlanguages WHERE user_id=(SELECT user_id FROM users WHERE username="'.$username.'")'
		);

		if ($result->RecordCount() == 0) {
			$returner = null;
			$result->Close();
			return $returner;
		} else {
			$reviewLanguages = array();
			while (!$result->EOF) {
				$row = $result->getRowAssoc(false);
				$reviewLanguages[]= $this->convertFromDB($row['reviewlanguage']);				 
				$result->MoveNext();
			}
			$result->Close();
			return $reviewLanguages;
		}
	}


	function insertAffiliation($nextUserId,$affiliation) {

		$this->update(
			'INSERT INTO user_settings values('.$nextUserId.',"en_US","affiliation",0,0,"'.$affiliation.'","string")'
		);
	}

	function insertAcademicTitle($userId,$academicTitle) {

		$this->update(
			'INSERT INTO langsci_user_academic values('.$userId.',"'.$academicTitle.'")'
		);
	}

	function insertSubscription($userId,$type) {

		$this->update(
			'INSERT INTO langsci_user_subscriptions values('.$userId.',"'.$type.'")'
		);
	}

	function insertArea($userId,$rank,$area) {

		$this->update(
			'INSERT INTO langsci_user_reviewareas values('.$userId.','.$rank.',"'.$area.'")'
		);
	}

	function insertLanguage($userId,$language) {

		$this->update(
			'INSERT INTO langsci_user_reviewlanguages values('.$userId.',"'.$language.'")'
		);
	}

	function insertUser($userId,$username,$password,$firstName,$lastName,$email,$userUrl,$country) {

		$this->update(
			'INSERT INTO users (
				user_id, username, password, first_name, last_name, email, url, country, locales, date_registered,inline_help,date_last_login)
				values('.$userId.',"'.$username.'","'.$password.'","'.$firstName.'","'.$lastName.'","'.$email.'","'.$userUrl.'","'.
				$country.'","en_US",NOW(),0,0)'
		);
	}

	function insertUserGroup($userId,$userGroup) {

		$this->update(
			'INSERT INTO user_user_groups VALUES ((SELECT user_group_id FROM user_group_settings WHERE locale="en_US" AND setting_value="'.$userGroup.'"),'.$userId.')'
		);
	}

	function insertProminent($userId) {

		$this->update(
			'INSERT INTO langsci_user_prominent values('.$userId.',0)'
		);
	}

	function setDisabled($disabled,$disabledReason,$userId) {

		$this->update(
			'UPDATE users SET disabled='.$disabled. ',disabled_reason="'.$disabledReason.'" WHERE user_id='.$userId
		);
	}

	function deleteUserGroups($userId,$userGroups) {

		$this->update(
			'DELETE FROM user_user_groups WHERE user_id='.$userId.' AND user_group_id IN (SELECT user_group_id FROM user_group_settings WHERE locale="en_US" AND setting_value IN ('.$userGroups.'))'
		);
	}

	function deleteSubscriptions($userId){

		$this->update(
			'DELETE FROM langsci_user_subscriptions WHERE user_id='.$userId
		);
	}

	function deleteReviewer($userId){

		$this->update(
			'DELETE FROM user_user_groups WHERE user_id='.$userId.' AND user_group_id = (SELECT user_group_id FROM user_group_settings WHERE locale="en_US" AND setting_value="Reviewer")'
		);

		$this->update(
			'DELETE FROM langsci_user_reviewareas WHERE user_id='.$userId
		);

		$this->update(
			'DELETE FROM langsci_user_reviewlanguages WHERE user_id='.$userId
		);

	}


}

?>
