<?php

/**
 * @file controllers/grid/users/reviewer/form/ReviewerForm.inc.php
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ReviewerForm
 * @ingroup controllers_grid_users_reviewer_form
 *
 * @brief Base Form for adding a reviewer to a submission.
 * N.B. Requires a subclass to implement the "reviewerId" to be added.
 */

import('lib.pkp.classes.form.Form');

class ReviewerForm extends Form {
	/** The submission associated with the review assignment **/
	var $_submission;

	/** The review round associated with the review assignment **/
	var $_reviewRound;

	/** An array of actions for the other reviewer forms */
	var $_reviewerFormActions;

	/** An array with all current user roles */
	var $_userRoles;

	/**
	 * Constructor.
	 * @param $submission Submission
	 * @param $reviewRound ReviewRound
	 */
	function ReviewerForm($submission, $reviewRound) {
		parent::Form('controllers/grid/users/reviewer/form/defaultReviewerForm.tpl');
		$this->setSubmission($submission);
		$this->setReviewRound($reviewRound);

		// Validation checks for this form
		$this->addCheck(new FormValidator($this, 'responseDueDate', 'required', 'editor.review.errorAddingReviewer'));
		$this->addCheck(new FormValidator($this, 'reviewDueDate', 'required', 'editor.review.errorAddingReviewer'));

		$this->addCheck(new FormValidatorPost($this));

		import('lib.pkp.classes.mail.SubmissionMailTemplate');
	}

	//
	// Getters and Setters
	//
	/**
	 * Get the submission Id
	 * @return int submissionId
	 */
	function getSubmissionId() {
		$submission = $this->getSubmission();
		return $submission->getId();
	}

	/**
	 * Get the submission
	 * @return Submission
	 */
	function getSubmission() {
		return $this->_submission;
	}

	/**
	 * Get the ReviewRound
	 * @return ReviewRound
	 */
	function getReviewRound() {
		return $this->_reviewRound;
	}

	/**
	 * Set the submission
	 * @param $submission Submission
	 */
	function setSubmission($submission) {
		$this->_submission = $submission;
	}

	/**
	 * Set the ReviewRound
	 * @param $reviewRound ReviewRound
	 */
	function setReviewRound($reviewRound) {
		$this->_reviewRound = $reviewRound;
	}

	/**
	 * Set a reviewer form action
	 * @param $action LinkAction
	 */
	function setReviewerFormAction($action) {
		$this->_reviewerFormActions[$action->getId()] = $action;
	}

	/**
	 * Set current user roles.
	 * @param $userRoles Array
	 */
	function setUserRoles($userRoles) {
		$this->_userRoles = $userRoles;
	}

	/**
	 * Get current user roles.
	 * @return $userRoles Array
	 */
	function getUserRoles() {
		return $this->_userRoles;
	}

	/**
	 * Get all of the reviewer form actions
	 * @return array
	 */
	function getReviewerFormActions() {
		return $this->_reviewerFormActions;
	}
	//
	// Overridden template methods
	//
	/**
	 * Initialize form data from the associated author.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function initData($args, $request) {
		$reviewerId = (int) $request->getUserVar('reviewerId');
		$context = $request->getContext();
		$reviewRound = $this->getReviewRound();
		$submission = $this->getSubmission();

		// The reviewer id has been set
		if (!empty($reviewerId)) {
			if ($this->_isValidReviewer($context, $submission, $reviewRound, $reviewerId)) {
				$userDao = DAORegistry::getDAO('UserDAO'); /* @var $userDao UserDAO */
				$reviewer = $userDao->getById($reviewerId);
				$this->setData('userNameString', sprintf('%s (%s)', $reviewer->getFullname(), $reviewer->getUsername()));
			}
		}

		// Get review assignment related data;
		$reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO');
		$reviewAssignment = $reviewAssignmentDao->getReviewAssignment($reviewRound->getId(), $reviewerId, $reviewRound->getRound());

		// Get the review method (open, blind, or double-blind)
		if (isset($reviewAssignment) && $reviewAssignment->getReviewMethod() != false) {
			$reviewMethod = $reviewAssignment->getReviewMethod();
		} else {
			// Set default value.
			$reviewMethod = SUBMISSION_REVIEW_METHOD_BLIND;
		}

		// Get the response/review due dates or else set defaults
		if (isset($reviewAssignment) && $reviewAssignment->getDueDate() != null) {
			$reviewDueDate = strftime(Config::getVar('general', 'date_format_short'), strtotime($reviewAssignment->getDueDate()));
		} else {
			$numWeeks = max((int) $context->getSetting('numWeeksPerReview'), 4);
			$reviewDueDate = strftime(Config::getVar('general', 'date_format_short'), strtotime('+' . $numWeeks . ' week'));
		}
		if (isset($reviewAssignment) && $reviewAssignment->getResponseDueDate() != null) {
			$responseDueDate = strftime(Config::getVar('general', 'date_format_short'), strtotime($reviewAssignment->getResponseDueDate()));
		} else {
			$numWeeks = max((int) $context->getSetting('numWeeksPerResponse'), 3);
			$responseDueDate = strftime(Config::getVar('general', 'date_format_short'), strtotime('+' . $numWeeks . ' week'));
		}

		// Get the currently selected reviewer selection type to show the correct tab if we're re-displaying the form
		$selectionType = (int) $request->getUserVar('selectionType');
		$stageId = $reviewRound->getStageId();

		$this->setData('submissionId', $this->getSubmissionId());
		$this->setData('stageId', $stageId);
		$this->setData('reviewMethod', $reviewMethod);
		$this->setData('reviewRoundId', $reviewRound->getId());
		$this->setData('reviewerId', $reviewerId);

		$context = $request->getContext();
		$templateKey = $this->_getMailTemplateKey($context);
		$template = new SubmissionMailTemplate($submission, $templateKey);
		if ($template) {
			$user = $request->getUser();
			$dispatcher = $request->getDispatcher();
			AppLocale::requireComponents(LOCALE_COMPONENT_PKP_REVIEWER); // reviewer.step1.requestBoilerplate
			$template->assignParams(array(
				'contextUrl' => $dispatcher->url($request, ROUTE_PAGE, $context->getPath()),
				'editorialContactSignature' => $user->getContactSignature(),
				'signatureFullName' => $user->getFullname(),
				'messageToReviewer' => __('reviewer.step1.requestBoilerplate'),
			));
		}
		$this->setData('personalMessage', $template->getBody() . "\n" . $context->getSetting('emailSignature'));
		$this->setData('responseDueDate', $responseDueDate);
		$this->setData('reviewDueDate', $reviewDueDate);
		$this->setData('selectionType', $selectionType);
	}

	/**
	 * Fetch
	 * @param $request PKPRequest
	 * @see Form::fetch()
	 */
	function fetch($request) {
		// Get the review method options.
		$reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO');
		$reviewMethods = $reviewAssignmentDao->getReviewMethodsTranslationKeys();
		$submission = $this->getSubmission();

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('reviewMethods', $reviewMethods);
		$templateMgr->assign('reviewerActions', $this->getReviewerFormActions());

		// Allow the default template
		$templateKeys[] = $this->_getMailTemplateKey($request->getContext());

		// Determine if the current user can use any custom templates defined.
		$user = $request->getUser();
		$roleDao = DAORegistry::getDAO('RoleDAO');

		$userRoles = $roleDao->getByUserId($user->getId(), $submission->getContextId());
		foreach ($userRoles as $userRole) {
			if (in_array($userRole->getId(), array(ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR, ROLE_ID_ASSISTANT))) {
				$emailTemplateDao = DAORegistry::getDAO('EmailTemplateDAO');
				$customTemplates = $emailTemplateDao->getCustomTemplateKeys(Application::getContextAssocType(), $submission->getContextId());
				$templateKeys = array_merge($templateKeys, $customTemplates);
				break;
			}
		}

		foreach ($templateKeys as $templateKey) {
			$template = new SubmissionMailTemplate($submission, $templateKey, null, null, null, false);
			$template->assignParams(array());
			$templates[$templateKey] = $template->getSubject();
		}

		$templateMgr->assign('templates', $templates);

		// Get the reviewer user groups for the create new reviewer/enroll existing user tabs
		$context = $request->getContext();
		$userGroupDao = DAORegistry::getDAO('UserGroupDAO'); /* @var $userGroupDao UserGroupDAO */
		$reviewRound = $this->getReviewRound();
		$reviewerUserGroups = $userGroupDao->getUserGroupsByStage($context->getId(), $reviewRound->getStageId(), false, false, ROLE_ID_REVIEWER);
		$userGroups = array();
		while($userGroup = $reviewerUserGroups->next()) {
			$userGroups[$userGroup->getId()] = $userGroup->getLocalizedName();
		}

		$this->setData('userGroups', $userGroups);
		return parent::fetch($request);
	}

	/**
	 * Assign form data to user-submitted data.
	 * @see Form::readInputData()
	 */
	function readInputData() {
		$this->readUserVars(array(
			'selectionType',
			'submissionId',
			'template',
			'personalMessage',
			'responseDueDate',
			'reviewDueDate',
			'reviewMethod',
			'skipEmail',
			'keywords',
			'interestsTextOnly',
			'reviewRoundId',
			'stageId',
			'selectedFiles',
		));

		$keywords = $this->getData('keywords');
		if ($keywords != null && is_array($keywords['interests'])) {
			// The interests are coming in encoded -- Decode them for DB storage
			$this->setData('interestsKeywords', array_map('urldecode', $keywords['interests']));
		}
	}

	/**
	 * Save review assignment
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function execute($args, $request) {
		$submission = $this->getSubmission();
		$context = $request->getContext();

		$currentReviewRound = $this->getReviewRound();
		$stageId = $currentReviewRound->getStageId();
		$reviewDueDate = $this->getData('reviewDueDate');
		$responseDueDate = $this->getData('responseDueDate');

		// Get reviewer id and validate it.
		$reviewerId = (int) $this->getData('reviewerId');

		if (!$this->_isValidReviewer($context, $submission, $currentReviewRound, $reviewerId)) {
			fatalError('Invalid reviewer id.');
		}

		$reviewMethod = (int) $this->getData('reviewMethod');

		import('lib.pkp.classes.submission.action.EditorAction');
		$editorAction = new EditorAction();
		$editorAction->addReviewer($request, $submission, $reviewerId, $currentReviewRound, $reviewDueDate, $responseDueDate, $reviewMethod);

		// Get the reviewAssignment object now that it has been added.
		$reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO'); /* @var $reviewAssignmentDao ReviewAssignmentDAO */
		$reviewAssignment = $reviewAssignmentDao->getReviewAssignment($currentReviewRound->getId(), $reviewerId, $currentReviewRound->getRound(), $stageId);
		$reviewAssignment->setDateNotified(Core::getCurrentDate());
		$reviewAssignment->setCancelled(0);
		$reviewAssignment->stampModified();
		$reviewAssignmentDao->updateObject($reviewAssignment);

		// Grant access for this review to all selected files.
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		import('lib.pkp.classes.submission.SubmissionFile'); // File constants
		$submissionFiles = $submissionFileDao->getLatestNewRevisionsByReviewRound($currentReviewRound, SUBMISSION_FILE_REVIEW_FILE);
		$selectedFiles = (array) $this->getData('selectedFiles');
		$reviewFilesDao = DAORegistry::getDAO('ReviewFilesDAO');
		foreach ($submissionFiles as $submissionFile) {
			if (in_array($submissionFile->getFileId(), $selectedFiles)) {
				$reviewFilesDao->grant($reviewAssignment->getId(), $submissionFile->getFileId());
			}
		}


		// Notify the reviewer via email.
		import('lib.pkp.classes.mail.SubmissionMailTemplate');
		$templateKey = $keywords = $this->getData('template');
		$mail = new SubmissionMailTemplate($submission, $templateKey, null, null, null, false);

		if ($mail->isEnabled() && !$this->getData('skipEmail')) {
			$userDao = DAORegistry::getDAO('UserDAO'); /* @var $userDao UserDAO */
			$reviewer = $userDao->getById($reviewerId);
			$user = $request->getUser();
			$mail->addRecipient($reviewer->getEmail(), $reviewer->getFullName());
			$mail->setBody($this->getData('personalMessage'));
			$dispatcher = $request->getDispatcher();

			// Set the additional arguments for the one click url
			$reviewUrlArgs = array('submissionId' => $this->getSubmissionId());
			if ($context->getSetting('reviewerAccessKeysEnabled')) {
				import('lib.pkp.classes.security.AccessKeyManager');
				$accessKeyManager = new AccessKeyManager();
				$expiryDays = $context->getSetting('numWeeksPerReview') + 4 * 7;
				$accessKey = $accessKeyManager->createKey($context->getId(), $reviewerId, $reviewAssignment->getId(), $expiryDays);
				$reviewUrlArgs = array_merge($reviewUrlArgs, array('reviewId' => $reviewAssignment->getId(), 'key' => $accessKey));
			}

			// Assign the remaining parameters
			$paramArray = array(
				'reviewerName' => $reviewer->getFullName(),
				'responseDueDate' => $responseDueDate,
				'reviewDueDate' => $reviewDueDate,
				'reviewerUserName' => $reviewer->getUsername(),
				'submissionReviewUrl' => $dispatcher->url($request, ROUTE_PAGE, null, 'reviewer', 'submission', null, $reviewUrlArgs)
			);
			$mail->assignParams($paramArray);
			$mail->send($request);
		}

		return $reviewAssignment;
	}


	//
	// Protected methods.
	//
	/**
	 * Get the link action that fetchs the search
	 * by name form content.
	 * @param $request Request
	 * @return LinkAction
	 */
	function getSearchByNameAction($request) {
		$reviewRound = $this->getReviewRound();

		$actionArgs['submissionId'] = $this->getSubmissionId();
		$actionArgs['stageId'] = $reviewRound->getStageId();
		$actionArgs['reviewRoundId'] = $reviewRound->getId();
		$actionArgs['selectionType'] = REVIEWER_SELECT_SEARCH_BY_NAME;

		return new LinkAction(
			'addReviewer',
			new AjaxAction($request->url(null, null, 'reloadReviewerForm', null, $actionArgs)),
			__('editor.submission.returnToSimpleSearch'),
			'return'
		);
	}


	//
	// Private helper methods
	//
	/**
	 * Check if a given user id is enrolled in reviewer user group.
	 * @param $context Context
	 * @param $submission Submission
	 * @param $reviewRound ReviewRound
	 * @param $reviewerId int
	 * @return boolean
	 */
	function _isValidReviewer($context, $submission, $reviewRound, $reviewerId) {
		$userDao = DAORegistry::getDAO('UserDAO'); /* @var $userDao UserDAO */
		$reviewerFactory = $userDao->getReviewersNotAssignedToSubmission($context->getId(), $submission->getId(), $reviewRound);
		$reviewersArray = $reviewerFactory->toAssociativeArray();
		if (array_key_exists($reviewerId, $reviewersArray)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get the email template key depending on if reviewer one click access is
	 * enabled or not.
	 *
	 * @param mixed $context Context
	 * @return int Email template key
	 */
	function _getMailTemplateKey($context) {
		$templateKey = 'REVIEW_REQUEST';
		if ($context->getSetting('reviewerAccessKeysEnabled')) {
			$templateKey = 'REVIEW_REQUEST_ONECLICK';
		}

		return $templateKey;
	}
}

?>
