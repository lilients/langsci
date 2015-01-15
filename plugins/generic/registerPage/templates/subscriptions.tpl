{**
 * plugins/generic/registerPage/templates/subscriptions.tpl
 *
 * Copyright (c) 2014 Carola Fanselow Freie Universität Berlin
 * Distributed under the GNU GPL v2. 
 *
 *
 *}

{strip}
	{if !$contentOnly}
		{include file="common/header.tpl"}
	{/if}
{/strip}


<script language="JavaScript" type="text/javascript">

{literal}			
	function showReviewerQuestions() {
		if (!checkboxReviewer.checked){
			document.getElementById("reviewerQuestions").style.display = 'none';

		} else {
			document.getElementById("reviewerQuestions").style.display = 'block';

		}
	}

{/literal}
</script> 


<div class="LSP">
<div class="registerPage">

<form method="post" action="saveSubscriptions">

	{include
		file="../plugins/generic/registerPage/templates/subscriptionDetails.tpl"
	}
	
	<input id="buttonSaveSubscriptionChanges" name="buttonSaveSubscriptionChanges" value="Save Changes" type="submit">
	<br><a href="{$ompUrl}">Cancel</a>

</form> {** action="check" *}


</div> {**class="LSP" *}
</div> {**class="registerPage" *}

<script language="JavaScript" type="text/javascript">
	var userGroups = {$userGroups|@json_encode}; 
	var subscriptions = {$subscriptions|@json_encode}; 
   var issetCheckboxEnglish = "{$issetCheckboxEnglish}";
   var issetCheckboxGerman = "{$issetCheckboxGerman}";
   var issetCheckboxFrench = "{$issetCheckboxFrench}";
   var issetCheckboxOther = "{$issetCheckboxOther}";

	{literal}

		document.getElementById("checkboxSupporter").checked = $.inArray('Supporter',userGroups)+1;
		document.getElementById("checkboxReader").checked = $.inArray('Reader',userGroups)+1;
		document.getElementById("checkboxAuthor").checked = $.inArray('Author',userGroups)+1;
		document.getElementById("checkboxVolumeEditor").checked = $.inArray('Volume Editor',userGroups)+1;

		document.getElementById("checkboxReviewer").checked = $.inArray('Reviewer',userGroups)+1;
		document.getElementById("checkboxProofreader").checked = $.inArray('Proofreader',userGroups)+1;
		document.getElementById("checkboxTypesetter").checked = $.inArray('Typesetter',userGroups)+1;

		document.getElementById("checkboxNewsletter").checked = $.inArray('Newsletter',subscriptions)+1;

		document.getElementById("checkboxML").checked = $.inArray('General Mailing List',subscriptions)+1;
		document.getElementById("checkboxEnglish").checked = issetCheckboxEnglish;
		document.getElementById("checkboxGerman").checked = issetCheckboxGerman;
		document.getElementById("checkboxFrench").checked = issetCheckboxFrench;
		document.getElementById("checkboxOther").checked = issetCheckboxOther;
		if ($.inArray('Reviewer',userGroups)+1) {	
			document.getElementById("reviewerQuestions").style.display = 'block';
		}
	{/literal}

</script>


{strip}
		{include file="common/footer.tpl"}
{/strip}

