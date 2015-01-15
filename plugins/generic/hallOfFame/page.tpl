{**
 * page.tpl
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

var path_part1 = "url(";
var ompUrl = "{$ompUrl}";
var path_part2 = path_part1.concat(ompUrl);
var path_arrowright = path_part2.concat("plugins/generic/hallOfFame/img/arrowright20.jpg)"); 
var path_arrowdown = path_part2.concat("plugins/generic/hallOfFame/img/arrowdown20.jpg)");

	{literal}
		function showSubmissions(name) {
			if (document.getElementById(name).style.display=="block") {
				document.getElementById(name).style.display="none";
				document.getElementById(name.concat('_img')).style.backgroundImage = path_arrowright;
			}
			else {
				document.getElementById(name).style.display="block";
				document.getElementById(name.concat('_img')).style.backgroundImage = path_arrowdown;
			}
		}
	{/literal}
</script> 



<div class="LSP">
<div class="hallOfFame">
<br>
<p>{translate key="plugins.generic.intro.halloffame"}</p>
{**$printReviewer*}
{$printProofreader}
{$printTypesetter}

</div> 
</div> 

{strip}
		{include file="common/footer.tpl"}
{/strip}
