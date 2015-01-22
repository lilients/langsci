{**
 * controllers/tab/settings/affiliation/form/affiliationForm.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Contact management form.
 *
 *}

<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#affiliationForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="affiliationForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="saveFormData" tab="affiliationAndSupport"}">
	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="affiliationFormNotification"}
	{fbvFormArea id="sponsorsFormArea" class="border" title="manager.setup.sponsors"}
		{fbvFormSection description="manager.setup.sponsors.description"}
			{url|assign:sponsorGridUrl router=$smarty.const.ROUTE_COMPONENT component="grid.settings.sponsor.sponsorGridHandler" op="fetchGrid" escape=false}
			{load_url_in_div id="sponsorGridDiv" url=$sponsorGridUrl}
			{fbvElement type="textarea" multilingual=true id="sponsorNote" value=$sponsorNote rich=true label="manager.setup.sponsors.note"}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormArea id="contributorsFormArea" class="border" title="manager.setup.contributors"}
		{fbvFormSection description="manager.setup.contributors.description"}
			{url|assign:contributorGridUrl router=$smarty.const.ROUTE_COMPONENT component="grid.settings.contributor.ContributorGridHandler" op="fetchGrid" escape=false}
			{load_url_in_div id="contributorGridDiv" url=$contributorGridUrl}
			{fbvElement type="textarea" multilingual=true id="contributorNote" value=$contributorNote rich=true label="manager.setup.contributors.note"}
		{/fbvFormSection}
	{/fbvFormArea}

	{fbvFormButtons id="affiliationFormSubmit" submitText="common.save" hideCancel=true}
</form>
