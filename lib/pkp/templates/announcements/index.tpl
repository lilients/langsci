{**
 * templates/announcements/index.tpl
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Public announcements page.
 *}
{strip}
{assign var="pageTitle" value="announcement.announcements"}
{include file="common/header.tpl"}
{/strip}

{include file="announcements/announcements.tpl"}

{include file="common/footer.tpl"}
