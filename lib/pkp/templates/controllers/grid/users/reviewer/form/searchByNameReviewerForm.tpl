{**
 * templates/controllers/grid/user/reviewer/form/searchByNameReviewerForm.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Search By Name and assignment reviewer form
 *
 *}
<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#searchByNameReviewerForm').pkpHandler('$.pkp.controllers.grid.users.reviewer.form.AddReviewerFormHandler',
			{ldelim}
				templateUrl: "{url|escape:'javascript' router=$smarty.const.ROUTE_COMPONENT component='grid.users.reviewer.ReviewerGridHandler' op='fetchTemplateBody' stageId=$stageId reviewRoundId=$reviewRoundId submissionId=$submissionId escape=false}"
			{rdelim}
		);
	{rdelim});
</script>

<form class="pkp_form" id="searchByNameReviewerForm" method="post" action="{url op="updateReviewer"}" >
	{fbvFormSection title="user.role.reviewer" for="reviewer"}
		{url|assign:autocompleteUrl op="getReviewersNotAssignedToSubmission" submissionId=$submissionId stageId=$stageId reviewRoundId=$reviewRoundId escape=false}
		{fbvElement type="autocomplete" autocompleteUrl=$autocompleteUrl id="reviewerId" name="reviewer" value=$userNameString disableSync=true}
	{/fbvFormSection}
	<div class="action_links">
		{foreach from=$reviewerActions item=action}
			{include file="linkAction/linkAction.tpl" action=$action contextId="searchByNameReviewerForm"}
		{/foreach}
	</div>

	{include file="controllers/grid/users/reviewer/form/reviewerFormFooter.tpl"}

	{fbvFormButtons submitText="editor.submission.addReviewer"}
</form>

