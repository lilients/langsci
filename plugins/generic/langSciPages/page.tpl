{**
 * page.tpl
 *
 * Copyright (c) 2014 Carola Fanselow Freie Universität Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * langSciPagesPlugin
 *
 *}

{strip}
	{if !$contentOnly}
		{include file="common/header.tpl"}
	{/if}
{/strip}

<div class="LSP">
<div class="LangSciPages">


	<p>
		{eval var=$content}
	</p>


</div>
</div>

{strip}
	{if !$contentOnly}
		{include file="common/footer.tpl"}
	{/if}
{/strip}
