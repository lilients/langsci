{**
 * plugins/generic/usageStats/templates/usageStatsSettingsForm.tpl
 *
 * Copyright (c) 2013 Simon Fraser University Library
 * Copyright (c) 2003-2013 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Usage statistics plugin management form.
 *
 *}
<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#usageStatsSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="usageStatsSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="plugin" category="generic" plugin=$pluginName verb="save"}">

	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="usageStatsSettingsFormNotification"}

	{fbvFormArea id="usageStatsLogging" title="plugins.generic.usageStats.settings.logging"}
		{fbvFormSection for="createLogFiles" list=true description="plugins.generic.usageStats.settings.createLogFiles.description"}
			{fbvElement type="checkbox" id="createLogFiles" value="1" checked=$createLogFiles label="plugins.generic.usageStats.settings.createLogFiles"}
		{/fbvFormSection}
		{fbvFormSection title="plugins.generic.usageStats.settings.logParseRegex" description="plugins.generic.usageStats.settings.logParseRegex.description"}
			{fbvElement type="text" id="accessLogFileParseRegex" value=$accessLogFileParseRegex"}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormButtons id="usageStatsSettingsFormSubmit" submitText="common.save" hideCancel=true}
</form>