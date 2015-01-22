<?php
/**
 * @file controllers/grid/files/submissionDocuments/SubmissionDocumentsFilesGridDataProvider.inc.php
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2000-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SubmissionDocumentsFilesGridDataProvider
 * @ingroup controllers_grid_files_submissionDocuments
 *
 * @brief The base data provider for the submission documents library files grid.
 */

import('lib.pkp.classes.controllers.grid.CategoryGridDataProvider');

class SubmissionDocumentsFilesGridDataProvider extends CategoryGridDataProvider {

	/**
	 * Constructor
	 */
	function SubmissionDocumentsFilesGridDataProvider() {
		parent::CategoryGridDataProvider();
	}

	/**
	 * @copydoc GridDataProvider::getAuthorizationPolicy()
	 */
	function getAuthorizationPolicy($request, $args, $roleAssignments) {
		import('classes.security.authorization.SubmissionAccessPolicy');
		$policy = new SubmissionAccessPolicy($request, $args, $roleAssignments, 'submissionId');
		return $policy;
	}

	//
	// Getters and Setters
	//

	/**
	 * Get the authorized submission.
	 * @return Submission
	 */
	function &getSubmission() {
		return $this->getAuthorizedContextObject(ASSOC_TYPE_SUBMISSION);
	}

	/**
	 * @copydoc GridDataProvider::getRequestArgs()
	 */
	function getRequestArgs() {
		$submission = $this->getSubmission();
		return array(
			'submissionId' => $submission->getId(),
		);
	}

	/**
	 * @copydoc CategoryGridHandler::getCategoryData()
	 */
	function getCategoryData(&$fileType, $filter = null) {

		// Retrieve all library files for the given submission document category.
		$submission = $this->getSubmission();
		import('lib.pkp.classes.context.LibraryFile');
		$libraryFileDao = DAORegistry::getDAO('LibraryFileDAO'); /* @var $libraryFileDao LibraryFileDAO */
		$libraryFiles = $libraryFileDao->getBySubmissionId($submission->getId(), $fileType);

		return $libraryFiles->toAssociativeArray();
	}
}

?>
