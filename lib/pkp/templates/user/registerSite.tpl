{**
 * templates/user/registerSite.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Site registration.
 *}
{strip}
{include file="common/header.tpl"}
{/strip}
<div id="contexts">
{iterate from=contexts item=context}
	{if !$notFirstContext}
		{translate key="user.register.selectContext"}:
		<ul>
		{assign var=notFirstContext value=1}
	{/if}
	<li><a href="{url context=$context->getPath() page="user" op="register"}">{$context->getLocalizedName()|escape}</a></li>
{/iterate}
{if $contexts->wasEmpty()}
	{translate key="user.register.noContexts"}
{else}
	</ul>
{/if}
</div>
{include file="common/footer.tpl"}
