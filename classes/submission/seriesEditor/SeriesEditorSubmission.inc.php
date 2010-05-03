<?php

/**
 * @file classes/submission/seriesEditor/SeriesEditorSubmission.inc.php
 *
 * Copyright (c) 2003-2010 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SeriesEditorSubmission
 * @ingroup submission
 * @see SeriesEditorSubmissionDAO
 *
 * @brief SeriesEditorSubmission class.
 */

// $Id$


import('classes.monograph.Monograph');

class SeriesEditorSubmission extends Monograph {

	/** @var array ReviewAssignments of this monograph */
	var $reviewAssignments;

	/** @var array IDs of ReviewAssignments removed from this monograph */
	var $removedReviewAssignments;

	/** @var array the editor decisions of this monograph */
	var $editorDecisions;

	/** @var array the revisions of the editor file */
	var $editorFileRevisions;

	/** @var array the revisions of the author file */
	var $authorFileRevisions;

	/** @var array the revisions of the revised copyedit file */
	var $copyeditFileRevisions;

	/**
	 * Constructor.
	 */
	function SeriesEditorSubmission() {
		parent::Monograph();
		$this->reviewAssignments = array();
		$this->removedReviewAssignments = array();
	}

	/**
	 * Add a review assignment for this monograph.
	 * @param $reviewAssignment ReviewAssignment
	 */
	function addReviewAssignment($reviewAssignment) {
		if ($reviewAssignment->getMonographId() == null) {
			$reviewAssignment->setMonographId($this->getMonographId());
		}

		if (isset($this->reviewAssignments[$reviewAssignment->getReviewType()][$reviewAssignment->getRound()])) {
			$roundReviewAssignments = $this->reviewAssignments[$reviewAssignment->getReviewType()][$reviewAssignment->getRound()];
		} else {
			$roundReviewAssignments = Array();
		}
		array_push($roundReviewAssignments, $reviewAssignment);

		return $this->reviewAssignments[$reviewAssignment->getReviewType()][$reviewAssignment->getRound()] = $roundReviewAssignments;
	}

	/**
	 * Add an editorial decision for this monograph.
	 * @param $editorDecision array
	 * @param $reviewType int
	 * @param $round int
	 */
	function addDecision($editorDecision, $reviewType, $round) {
		if (isset($this->editorDecisions[$reviewType][$round]) && is_array($this->editorDecisions[$reviewType][$round])) {
			array_push($this->editorDecisions[$reviewType][$round], $editorDecision);
		}
		else $this->editorDecisions[$reviewType][$round] = Array($editorDecision);		
	}

	/**
	 * Remove a review assignment.
	 * @param $reviewId ID of the review assignment to remove
	 * @return boolean review assignment was removed
	 */
	function removeReviewAssignment($reviewId) {
		$found = false;

		if ($reviewId != 0) {
			// FIXME maintain a hash for quicker get/remove
			$reviewAssignments = array();
			$empty = array();
			foreach ($this->reviewAssignments as $reviewType => $reviewRounds)  {
				foreach ($reviewRounds as $round => $junk )  {
					$roundReviewAssignments = $this->reviewAssignments[$reviewType][$round];
					foreach ( $roundReviewAssignments as $assignment ) {
						if ($assignment->getReviewId() == $reviewId) {
							array_push($this->removedReviewAssignments, $reviewId);
							$found = true;
						} else {
							array_push($reviewAssignments, $assignment);
						}
					}
					$this->reviewAssignments[$reviewType][$round] = $reviewAssignments;
					$reviewAssignments = $empty;
				}
			}
		}
		return $found;
	}

	/**
	 * Updates an existing review assignment.
	 * @param $reviewAssignment ReviewAssignment
	 */
	function updateReviewAssignment($reviewAssignment) {
		$reviewAssignments = array();
		$roundReviewAssignments = $this->reviewAssignments[$reviewAssignment->getReviewType()][$reviewAssignment->getRound()];
		for ($i=0, $count=count($roundReviewAssignments); $i < $count; $i++) {
			if ($roundReviewAssignments[$i]->getReviewId() == $reviewAssignment->getReviewId()) {
				array_push($reviewAssignments, $reviewAssignment);
			} else {
				array_push($reviewAssignments, $roundReviewAssignments[$i]);
			}
		}
		$this->reviewAssignments[$reviewAssignment->getReviewType()][$reviewAssignment->getRound()] = $reviewAssignments;
	}

	/**
	 * Get the submission status. Returns one of the defined constants
	 * (STATUS_INCOMPLETE, STATUS_ARCHIVED, STATUS_PUBLISHED,
	 * STATUS_DECLINED, STATUS_QUEUED_UNASSIGNED, STATUS_QUEUED_REVIEW,
	 * or STATUS_QUEUED_EDITING). Note that this function never returns
	 * a value of STATUS_QUEUED -- the three STATUS_QUEUED_... constants
	 * indicate a queued submission.
	 * NOTE that this code is similar to getSubmissionStatus in
	 * the AuthorSubmission class and changes should be made there as well.
	 */
	function getSubmissionStatus() {
		$status = $this->getStatus();
		if ($status == STATUS_ARCHIVED || $status == STATUS_PUBLISHED ||
		    $status == STATUS_DECLINED) return $status;

		// The submission is STATUS_QUEUED or the author's submission was STATUS_INCOMPLETE.
		if ($this->getSubmissionProgress()) return (STATUS_INCOMPLETE);

		// The submission is STATUS_QUEUED. Find out where it's queued.
		$editAssignments = $this->getEditAssignments();
		if (empty($editAssignments))
			return (STATUS_QUEUED_UNASSIGNED);

		$decisions = $this->getDecisions();
		$decision = is_array($decisions) ? array_pop($decisions) : null;
		if (!empty($decision)) {
			$latestDecision = array_pop($decision);
			if ($latestDecision['decision'] == SUBMISSION_EDITOR_DECISION_ACCEPT) {
				return STATUS_QUEUED_EDITING;
			}
		}
		return STATUS_QUEUED_REVIEW;
	}

	/**
	 * Get/Set Methods.
	 */

	/**
	 * Get edit assignments for this monograph.
	 * @return array
	 */
	function &getEditAssignments() {
		$editAssignments =& $this->getData('editAssignments');
		return $editAssignments;
	}

	/**
	 * Set edit assignments for this monograph.
	 * @param $editAssignments array
	 */
	function setEditAssignments($editAssignments) {
		return $this->setData('editAssignments', $editAssignments);
	}

	//
	// Review Assignments
	//

	/**
	 * Get review assignments for this monograph.
	 * @return array ReviewAssignments
	 */
	function &getReviewAssignments($reviewType = null, $round = null) {
		if ($reviewType == null) {
			return $this->reviewAssignments;
		} elseif ($round == null) {
			return $this->reviewAssignments[$reviewType];
		} else {
			return $this->reviewAssignments[$reviewType][$round];
		}
	}

	/**
	 * Set review assignments for this monograph.
	 * @param $reviewAssignments array ReviewAssignments
	 */
	function setReviewAssignments($reviewAssignments, $reviewType, $round) {
		return $this->reviewAssignments[$reviewType][$round] = $reviewAssignments;
	}

	/**
	 * Get the IDs of all review assignments removed..
	 * @return array int
	 */
	function &getRemovedReviewAssignments() {
		return $this->removedReviewAssignments;
	}

	//
	// Editor Decisions
	//

	/**
	 * Get editor decisions.
	 * @return array
	 */
	function getDecisions($reviewType = null, $round = null) {
		if ($reviewType == null) {
			return $this->editorDecisions;
		} elseif ($round == null) {
			if (isset($this->editorDecisions[$reviewType])) return $this->editorDecisions[$reviewType];
		} else {
			if (isset($this->editorDecisions[$reviewType][$round])) return $this->editorDecisions[$reviewType][$round];
		}

		return null;
		
	}

	/**
	 * Set editor decisions.
	 * @param $editorDecisions array
	 * @param $reviewType int
	 * @param $round int
	 */
	function setDecisions($editorDecisions, $reviewType, $round) {
		$this->editorDecisions[$reviewType][$round] = $editorDecisions;
	}

	// 
	// Files
	//	

	/**
	 * Get submission file for this monograph.
	 * @return MonographFile
	 */
	function &getSubmissionFile() {
		$returner =& $this->getData('submissionFile');
		return $returner;
	}

	/**
	 * Set submission file for this monograph.
	 * @param $submissionFile MonographFile
	 */
	function setSubmissionFile($submissionFile) {
		return $this->setData('submissionFile', $submissionFile);
	}

	/**
	 * Get revised file for this monograph.
	 * @return MonographFile
	 */
	function &getRevisedFile() {
		$returner =& $this->getData('revisedFile');
		return $returner;
	}

	/**
	 * Set revised file for this monograph.
	 * @param $submissionFile MonographFile
	 */
	function setRevisedFile($revisedFile) {
		return $this->setData('revisedFile', $revisedFile);
	}

	/**
	 * Get review file.
	 * @return MonographFile
	 */
	function &getReviewFile() {
		$returner =& $this->getData('reviewFile');
		return $returner;
	}

	/**
	 * Set review file.
	 * @param $reviewFile MonographFile
	 */
	function setReviewFile($reviewFile) {
		return $this->setData('reviewFile', $reviewFile);
	}

	/**
	 * Get all editor file revisions.
	 * @return array MonographFiles
	 */
	function getEditorFileRevisions($reviewType = null, $round = null) {
		if ($reviewType == null) {
			return $this->editorFileRevisions;
		} elseif ( $round == null ) {
			return $this->editorFileRevisions[$reviewType];
		} else {
			return $this->editorFileRevisions[$reviewType][$round];
		}
	}

	/**
	 * Set all editor file revisions.
	 * @param $editorFileRevisions array MonographFiles
	 */
	function setEditorFileRevisions($editorFileRevisions, $reviewType, $round) {
		return $this->editorFileRevisions[$reviewType][$round] = $editorFileRevisions;
	}

	/**
	 * Get all author file revisions.
	 * @return array MonographFiles
	 */
	function getAuthorFileRevisions($reviewType = null, $round = null) {
		if ($reviewType == null) {
			return $this->authorFileRevisions;
		} elseif ( $round == null ) {
			return $this->authorFileRevisions[$reviewType];
		} else {
			return $this->authorFileRevisions[$reviewType][$round];
		}
	}

	/**
	 * Set all author file revisions.
	 * @param $authorFileRevisions array MonographFiles
	 */
	function setAuthorFileRevisions($authorFileRevisions, $reviewType, $round) {
		return $this->authorFileRevisions[$reviewType][$round] = $authorFileRevisions;
	}

	/**
	 * Get post-review file.
	 * @return MonographFile
	 */
	function &getEditorFile() {
		$returner =& $this->getData('editorFile');
		return $returner;
	}

	/**
	 * Set post-review file.
	 * @param $editorFile MonographFile
	 */
	function setEditorFile($editorFile) {
		return $this->setData('editorFile', $editorFile);
	}

	//
	// Review Rounds
	//

	/**
	 * Get review file revision.
	 * @return int
	 */
	function getReviewRevision() {
		return $this->getData('reviewRevision');
	}

	/**
	 * Set review file revision.
	 * @param $reviewRevision int
	 */
	function setReviewRevision($reviewRevision) {
		return $this->setData('reviewRevision', $reviewRevision);
	}

	//
	// Comments
	//

	/**
	 * Get most recent editor decision comment.
	 * @return MonographComment
	 */
	function getMostRecentEditorDecisionComment() {
		return $this->getData('mostRecentEditorDecisionComment');
	}

	/**
	 * Set most recent editor decision comment.
	 * @param $mostRecentEditorDecisionComment MonographComment
	 */
	function setMostRecentEditorDecisionComment($mostRecentEditorDecisionComment) {
		return $this->setData('mostRecentEditorDecisionComment', $mostRecentEditorDecisionComment);
	}

	/**
	 * Get most recent copyedit comment.
	 * @return MonographComment
	 */
	function getMostRecentCopyeditComment() {
		return $this->getData('mostRecentCopyeditComment');
	}

	/**
	 * Set most recent copyedit comment.
	 * @param $mostRecentCopyeditComment MonographComment
	 */
	function setMostRecentCopyeditComment($mostRecentCopyeditComment) {
		return $this->setData('mostRecentCopyeditComment', $mostRecentCopyeditComment);
	}

	/**
	 * Get most recent layout comment.
	 * @return MonographComment
	 */
	function getMostRecentLayoutComment() {
		return $this->getData('mostRecentLayoutComment');
	}

	/**
	 * Set most recent layout comment.
	 * @param $mostRecentLayoutComment MonographComment
	 */
	function setMostRecentLayoutComment($mostRecentLayoutComment) {
		return $this->setData('mostRecentLayoutComment', $mostRecentLayoutComment);
	}

	/**
	 * Get most recent proofread comment.
	 * @return MonographComment
	 */
	function getMostRecentProofreadComment() {
		return $this->getData('mostRecentProofreadComment');
	}

	/**
	 * Set most recent proofread comment.
	 * @param $mostRecentProofreadComment MonographComment
	 */
	function setMostRecentProofreadComment($mostRecentProofreadComment) {
		return $this->setData('mostRecentProofreadComment', $mostRecentProofreadComment);
	}

	/**
	 * Get the galleys for an monograph.
	 * @return array MonographGalley
	 */
	function &getGalleys() {
		$galleys =& $this->getData('galleys');
		return $galleys;
	}

	/**
	 * Set the galleys for a monograph.
	 * @param $galleys array MonographGalley
	 */
	function setGalleys(&$galleys) {
		return $this->setData('galleys', $galleys);
	}

	/**
	 * Return array mapping editor decision constants to their locale strings.
	 * (Includes default mapping '' => "Choose One".)
	 * @return array decision => localeString
	 */
	function &getEditorDecisionOptions() {
		static $editorDecisionOptions = array(
			'' => 'common.chooseOne',
			SUBMISSION_EDITOR_DECISION_ACCEPT => 'editor.monograph.decision.accept',
			SUBMISSION_EDITOR_DECISION_PENDING_REVISIONS => 'editor.monograph.decision.pendingRevisions',
			SUBMISSION_EDITOR_DECISION_RESUBMIT => 'editor.monograph.decision.resubmit',
			SUBMISSION_EDITOR_DECISION_DECLINE => 'editor.monograph.decision.decline'
		);
		return $editorDecisionOptions;
	}

	/**
	 * Get the CSS class for highlighting this submission in a list, based on status.
	 * @return string
	 */
	function getHighlightClass() {
		$signoffDao =& DAORegistry::getDAO('SignoffDAO');
		$highlightClass = 'highlight';
		$overdueSeconds = 60 * 60 * 24 * 14; // Two weeks

		// Submissions that are not still queued (i.e. published) are not highlighted.
		if ($this->getStatus() != STATUS_QUEUED) return null;

		// Awaiting assignment.
		$editAssignments = $this->getEditAssignments();
		if (empty($editAssignments)) return $highlightClass;

		$press =& Request::getPress();
		// Sanity check
		if (!$press || $press->getId() != $this->getId()) return null;

		// Check whether it's in review or editing.
		$inEditing = false;
		$decisionsEmpty = true;
		$lastDecisionDate = null;
		$decisions = $this->getDecisions();
		$decision = is_array($decisions) ? array_pop($decisions) : null;
		if (!empty($decision)) {
			$latestDecision = array_pop($decision);
			if (is_array($latestDecision)) {
				if ($latestDecision['decision'] == SUBMISSION_EDITOR_DECISION_ACCEPT) $inEditing = true;
				$decisionsEmpty = false;
				$lastDecisionDate = strtotime($latestDecision['dateDecided']);
			}
		}

		if ($inEditing) {
			// ---
			// --- Highlighting conditions for submissions in editing
			// ---

			// COPYEDITING

			// First round of copyediting
			$initialSignoff = $signoffDao->build('SIGNOFF_COPYEDITING_INITIAL', ASSOC_TYPE_MONOGRAPH, $this->getMonographId());
			$dateCopyeditorNotified = $initialSignoff->getDateNotified() ?
				strtotime($initialSignoff->getDateNotified()) : 0;
			$dateCopyeditorUnderway = $initialSignoff->getDateUnderway() ?
				strtotime($initialSignoff->getDateUnderway()) : 0;
			$dateCopyeditorCompleted = $initialSignoff->getDateCompleted() ?
				strtotime($initialSignoff->getDateCompleted()) : 0;
			$dateCopyeditorAcknowledged = $initialSignoff->getDateAcknowledged() ?
				strtotime($initialSignoff->getDateAcknowledged()) : 0;
			$dateLastCopyeditor = max($dateCopyeditorNotified, $dateCopyeditorUnderway);

			// If the Copyeditor has not been notified, highlight.
			if (!$dateCopyeditorNotified) return $highlightClass;

			// Check if the copyeditor is overdue on round 1
			if (	$dateLastCopyeditor &&
				!$dateCopyeditorCompleted &&
				$dateLastCopyeditor + $overdueSeconds < time()
			) return $highlightClass;

			// Check if acknowledgement is overdue for CE round 1
			if ($dateCopyeditorCompleted && !$dateCopyeditorAcknowledged) return $highlightClass;

			// Second round of copyediting
			$authorSignoff = $signoffDao->build('SIGNOFF_COPYEDITING_AUTHOR', ASSOC_TYPE_MONOGRAPH, $this->getMonographId());
			$dateCopyeditorAuthorNotified = $authorSignoff->getDateNotified() ?
				strtotime($authorSignoff->getDateNotified()) : 0;
			$dateCopyeditorAuthorUnderway = $authorSignoff->getDateUnderway() ?
				strtotime($authorSignoff->getDateUnderway()) : 0;
			$dateCopyeditorAuthorCompleted = $authorSignoff->getDateCompleted() ?
				strtotime($authorSignoff->getDateCompleted()) : 0;
			$dateCopyeditorAuthorAcknowledged = $authorSignoff->getDateAcknowledged() ?
				strtotime($authorSignoff->getDateAcknowledged()) : 0;
			$dateLastCopyeditorAuthor = max($dateCopyeditorAuthorNotified, $dateCopyeditorAuthorUnderway);

			// Check if round 2 is awaiting notification.
			if ($dateCopyeditorAcknowledged && !$dateCopyeditorAuthorNotified) return $highlightClass;

			// Check if acknowledgement is overdue for CE round 2
			if ($dateCopyeditorAuthorCompleted && !$dateCopyeditorAuthorAcknowledged) return $highlightClass;

			// Check if author is overdue on CE round 2
			if (	$dateLastCopyeditorAuthor &&
				!$dateCopyeditorAuthorCompleted &&
				$dateLastCopyeditorAuthor + $overdueSeconds < time()
			) return $highlightClass;

			// Third round of copyediting
			$finalSignoff = $signoffDao->build('SIGNOFF_COPYEDITING_FINAL', ASSOC_TYPE_MONOGRAPH, $this->getMonographId());
			$dateCopyeditorFinalNotified = $finalSignoff->getDateNotified() ?
				strtotime($finalSignoff->getDateNotified()) : 0;
			$dateCopyeditorFinalUnderway = $finalSignoff->getDateUnderway() ?
				strtotime($finalSignoff->getDateUnderway()) : 0;
			$dateCopyeditorFinalCompleted = $finalSignoff->getDateCompleted() ?
				strtotime($finalSignoff->getDateCompleted()) : 0;
			$dateLastCopyeditorFinal = max($dateCopyeditorFinalNotified, $dateCopyeditorUnderway);

			// Check if round 3 is awaiting notification.
			if ($dateCopyeditorAuthorAcknowledged && !$dateCopyeditorFinalNotified) return $highlightClass;

			// Check if copyeditor is overdue on round 3
			if (	$dateLastCopyeditorFinal &&
				!$dateCopyeditorFinalCompleted &&
				$dateLastCopyeditorFinal + $overdueSeconds < time()
			) return $highlightClass;

			// Check if acknowledgement is overdue for CE round 3
			if ($dateCopyeditorFinalCompleted && !$dateCopyeditorFinalAcknowledged) return $highlightClass;

			// LAYOUT EDITING
			$layoutSignoff = $signoffDao->build('SIGNOFF_LAYOUT', ASSOC_TYPE_MONOGRAPH, $this->getMonographId());
			$dateLayoutNotified = $layoutSignoff->getDateNotified() ?
				strtotime($layoutSignoff->getDateNotified()) : 0;
			$dateLayoutUnderway = $layoutSignoff->getDateUnderway() ?
				strtotime($layoutSignoff->getDateUnderway()) : 0;
			$dateLayoutCompleted = $layoutSignoff->getDateCompleted() ?
				strtotime($layoutSignoff->getDateCompleted()) : 0;
			$dateLayoutAcknowledged = $layoutSignoff->getDateAcknowledged() ?
				strtotime($layoutSignoff->getDateAcknowledged()) : 0;
			$dateLastLayout = max($dateLayoutNotified, $dateLayoutUnderway);

			// Check if Layout Editor needs to be notified.
			if ($dateLastCopyeditorFinal && !$dateLayoutNotified) return $highlightClass;

			// Check if layout editor is overdue
			if (	$dateLastLayout &&
				!$dateLayoutCompleted &&
				$dateLastLayout + $overdueSeconds < time()
			) return $highlightClass;

			// Check if acknowledgement is overdue for layout
			if ($dateLayoutCompleted && !$dateLayoutAcknowledged) return $highlightClass;

			// PROOFREADING

			// First round of proofreading
			$authorSignoff = $signoffDao->build('SIGNOFF_PROOFREADING_AUTHOR', ASSOC_TYPE_MONOGRAPH, $this->getMonographId());
			$dateAuthorNotified = $authorSignoff->getDateNotified() ?
				strtotime($authorSignoff->getDateNotified()) : 0;
			$dateAuthorUnderway = $authorSignoff->getDateUnderway() ?
				strtotime($authorSignoff->getDateUnderway()) : 0;
			$dateAuthorCompleted = $authorSignoff->getDateCompleted() ?
				strtotime($authorSignoff->getDateCompleted()) : 0;
			$dateAuthorAcknowledged = $authorSignoff->getDateAcknowledged() ?
				strtotime($authorSignoff->getDateAcknowledged()) : 0;
			$dateLastAuthor = max($dateNotified, $dateAuthorUnderway);

			// Check if the author is awaiting proofreading notification.
			if ($dateLayoutAcknowledged && !$dateAuthorNotified) return $highlightClass;

			// Check if the author is overdue on round 1 of proofreading
			if (	$dateLastAuthor &&
				!$dateAuthorCompleted &&
				$dateLastAuthor + $overdueSeconds < time()
			) return $highlightClass;

			// Check if acknowledgement is overdue for proofreading round 1
			if ($dateAuthorCompleted && !$dateAuthorAcknowledged) return $highlightClass;

			// Second round of proofreading
			$proofreaderSignoff = $signoffDao->build('SIGNOFF_PROOFREADING_PROOFREADER', ASSOC_TYPE_MONOGRAPH, $this->getMonographId());
			$dateProofreaderNotified = $proofreaderSignoff->getDateNotified() ?
				strtotime($proofreaderSignoff->getDateNotified()) : 0;
			$dateProofreaderUnderway = $proofreaderSignoff->getDateUnderway() ?
				strtotime($proofreaderSignoff->getDateUnderway()) : 0;
			$dateProofreaderCompleted = $proofreaderSignoff->getDateCompleted() ?
				strtotime($proofreaderSignoff->getDateCompleted()) : 0;
			$dateProofreaderAcknowledged = $proofreaderSignoff->getDateAcknowledged() ?
				strtotime($proofreaderSignoff->getDateAcknowledged()) : 0;
			$dateLastProofreader = max($dateProofreaderNotified, $dateProofreaderUnderway);

			// Check if the proofreader is awaiting notification.
			if ($dateAuthorAcknowledged && !$dateProofreaderNotified) return $highlightClass;

			// Check if acknowledgement is overdue for proofreading round 2
			if ($dateProofreaderCompleted && !$dateProofreaderAcknowledged) return $highlightClass;

			// Check if proofreader is overdue on proofreading round 2
			if (	$dateLastProofreader &&
				!$dateProofreaderCompleted &&
				$dateLastProofreader + $overdueSeconds < time()
			) return $highlightClass;

			// Third round of proofreading
			$layoutEditorSignoff = $signoffDao->build('SIGNOFF_PROOFREADING_LAYOUT', ASSOC_TYPE_MONOGRAPH, $this->getMonographId());
			$dateLayoutEditorNotified = $layoutEditorSignoff->getDateNotified() ?
				strtotime($layoutEditorSignoff->getDateNotified()) : 0;
			$dateLayoutEditorUnderway = $layoutEditorSignoff->getDateUnderway() ?
				strtotime($layoutEditorSignoff->getDateUnderway()) : 0;
			$dateLayoutEditorCompleted = $layoutEditorSignoff->getDateCompleted() ?
				strtotime($layoutEditorSignoff->getDateCompleted()) : 0;
			$dateLastLayoutEditor = max($dateLayoutEditorNotified, $dateCopyeditorUnderway);

			// Check if the layout editor is awaiting notification.
			if ($dateProofreaderAcknowledged && !$dateLayoutEditorNotified) return $highlightClass;

			// Check if proofreader is overdue on round 3 of proofreading
			if (	$dateLastLayoutEditor &&
				!$dateLayoutEditorCompleted &&
				$dateLastLayoutEditor + $overdueSeconds < time()
			) return $highlightClass;

			// Check if acknowledgement is overdue for proofreading round 3
			if ($dateLayoutEditorCompleted && !$dateLayoutEditorAcknowledged) return $highlightClass;
		} else {
			// ---
			// --- Highlighting conditions for submissions in review
			// ---
			$reviewAssignments =& $this->getReviewAssignments($this->getCurrentRound());
			if (is_array($reviewAssignments) && !empty($reviewAssignments)) {
				$allReviewsComplete = true;
				foreach ($reviewAssignments as $i => $junk) {
					$reviewAssignment =& $reviewAssignments[$i];

					// If the reviewer has not been notified, highlight.
					if ($reviewAssignment->getDateNotified() === null) return $highlightClass;

					// Check review status.
					if (!$reviewAssignment->getCancelled() && !$reviewAssignment->getDeclined()) {
						if (!$reviewAssignment->getDateCompleted()) $allReviewsComplete = false;

						$dateReminded = $reviewAssignment->getDateReminded() ?
							strtotime($reviewAssignment->getDateReminded()) : 0;
						$dateNotified = $reviewAssignment->getDateNotified() ?
							strtotime($reviewAssignment->getDateNotified()) : 0;
						$dateConfirmed = $reviewAssignment->getDateConfirmed() ?
							strtotime($reviewAssignment->getDateConfirmed()) : 0;

						// Check whether a reviewer is overdue to confirm invitation
						if (	!$reviewAssignment->getDateCompleted() &&
							!$dateConfirmed &&
							!$press->getSetting('remindForInvite') &&
							max($dateReminded, $dateNotified) + $overdueSeconds < time()
						) return $highlightClass;

						// Check whether a reviewer is overdue to complete review
						if (	!$reviewAssignment->getDateCompleted() &&
							$dateConfirmed &&
							!$press->getSetting('remindForSubmit') &&
							max($dateReminded, $dateConfirmed) + $overdueSeconds < time()
						) return $highlightClass;
					}

					unset($reviewAssignment);
				}
				// If all reviews are complete but no decision is recorded, highlight.
				if ($allReviewsComplete && $decisionsEmpty) return $highlightClass;

				// If the author's last file upload hasn't been taken into account in
				// the most recent decision or author/editor correspondence, highlight.
				$comment = $this->getMostRecentEditorDecisionComment();
				$commentDate = $comment ? strtotime($comment->getDatePosted()) : 0;
				$authorFileRevisions = $this->getAuthorFileRevisions($this->getCurrentRound());
				$authorFileDate = null;
				if (is_array($authorFileRevisions) && !empty($authorFileRevisions)) {
					$authorFile = array_pop($authorFileRevisions);
					$authorFileDate = strtotime($authorFile->getDateUploaded());
				}
				if (	($lastDecisionDate || $commentDate) &&
					$authorFileDate &&
					$authorFileDate > max($lastDecisionDate, $commentDate)
				) return $highlightClass;
			}
		}
		return null;
	}
}

?>
