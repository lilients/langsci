{**
 * plugins/generic/registerPage/templates/sign.tpl
 *
 * Copyright (c) 2014 Carola Fanselow Freie Universität Berlin
 * Distributed under the GNU GPL v2.
 *
 *}

{strip}
    {if !$contentOnly}
        {include file="common/header.tpl"}
    {/if}
{/strip}





<div class="LSP">
<div class="registerPage">

<form method="post" action="validation">

    <p class="intro">{translate key="plugins.generic.registerPage.intro1"}
        <a class="privacyStatement" href="#privacyStatement">
            {translate key="plugins.generic.registerPage.privacyStatement"}</a>
    </p>

    <div>
        <div class="input">
            <p>{translate key="plugins.generic.registerPage.label.academicTitle"}</p>
          <input id="inputAcademicTitle" name="inputAcademicTitle" value="{$academicTitle}" type="text">
        </div>
        <div class="input">
            <p>{translate key="plugins.generic.registerPage.label.firstName"}</p>
          <input id="inputFirstName" name="inputFirstName" value="{$firstName}" type="text">
        </div>
        <div>
            <p>{translate key="plugins.generic.registerPage.label.lastName"}</p>
          <input id="inputLastName" name="inputLastName" value="{$lastName}" type="text">
        </div>
    </div>

    <div>
        <div>
            <p>{translate key="plugins.generic.registerPage.label.username"}</p>
          <input id="inputUsername" name="inputUsername" value="{$username}" type="text">
        </div>
    </div>

    <div>
        <div class="input">
            <p>{translate key="plugins.generic.registerPage.label.password1"}</p>
          <input id="inputPassword"  type="password" name="inputPassword" value="{$password}" type="text">
        </div>
        <div>
            <p>{translate key="plugins.generic.registerPage.label.password2"}</p>
          <input id="inputPassword2"  type="password" name="inputPassword2" value="{$password2}" type="text">
        </div>
    </div>

    <div>
        <div class="input">
            <p>{translate key="plugins.generic.registerPage.label.affiliation"}</p>
          <input id="inputAffiliation" name="inputAffiliation" value="{$affiliation}" type="text">
        </div>
        <div>
            <p>{translate key="plugins.generic.registerPage.label.emailAddress"}</p>
          <input id="inputEmail" name="inputEmail" value="{$email}" type="text">
        </div>
    </div>

    <div class="input">
            <p>Personal homepage URL</p>
          <input id="inputUserUrl" name="inputUserUrl" value="{$userUrl}" type="text">
    </div>
    <div>
        <p>{translate key="plugins.generic.registerPage.label.country"}</p>
        <section>
            <select id="selectCountry" name="selectCountry">
              <optgroup>
                    <option></option>
                    {foreach item=c from=$countries }
                        {if $c==$country}
                            <option class="inputOption" selected>{$c}</option>
                        {else}
                            <option class="inputOption">{$c}</option>
                        {/if}
                    {/foreach}
                </optgroup>
            </select>
        </section>
    </div>


    {if $emptyAcademicTitle}<p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredAcademicTitle"}</p>{/if}
    {if $emptyFirstName}<p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredFirstName"}</p>{/if}
    {if $emptyLastName}<p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredLastName"}</p>{/if}
    {if $emptyUsername}<p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredUsername"}</p>{/if}
    {if not $usernameUnique}<p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredUsernameUnique"}</p>{/if}
    {if $emptyPassword}<p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredPassword"}</p>{/if}
    {if $emptyPassword2}<p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredPassword2"}</p>{/if}
    {if $emptyAffiliation}<p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredAffiliation"}</p>{/if}
    {if $emptyEmail}<p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredEmailAddress"}</p>{/if}
    {if not $emailUnique}<p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredEmailUnique"}</p>{/if}

    {if not $emailValid && not $emptyEmail}<p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredEmailValid"}</p>{/if}
    {if not $urlOkay}<p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredUrlValid"}</p>{/if}
    {if not $passwordsMatch && not $emptyPassword && not $emptyPassword2}
        <p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredPasswordsMatch"}</p>{/if}
    {if not $usernameAlphanumeric && not $emptyUsername}
        <p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredUsernameAlphanumeric"}</p>{/if}
    {if not $password6Characters && $passwordsMatch && not $emptyPassword && not $emptyPassword2}
        <p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredPassword6Characters"}</p>{/if}

    <div class="footnotes">
        <p class="footnote">{translate key="plugins.generic.registerPage.footnote0"}</p>
        <p class="footnote">{translate key="plugins.generic.registerPage.footnote1"}</p>
        <p class="footnote">{translate key="plugins.generic.registerPage.footnote2"}</p>
    </div>


    {include
        file="../plugins/generic/registerPage/templates/subscriptionDetails.tpl"
    }

    {if !$captchaCorrect}

        <p class="intro">{translate key="plugins.generic.registerPage.intro4"}</p>
        <p>{translate key="plugins.generic.registerPage.captcha"}</p>
        {if $displayCaptchaRequired}
           <p id="requiredCaptcha">{translate key="plugins.generic.registerPage.captchaError"}</p>
        {/if}
        <p>{$captchaQuestion} <input id="inputCaptcha" name="inputCaptcha" type="text"></p>
        <input class="checkbox" type="checkbox" style="display:none" id="checkboxCaptchaCorrect" name="checkboxCaptchaCorrect">
    {else}
        <input class="checkbox" type="checkbox" style="display:none" id="checkboxCaptchaCorrect" name="checkboxCaptchaCorrect" checked="true">
    {/if}
    <input type="hidden" name="captchaQuestion" value="{$captchaQuestion}">
    <input type="hidden" name="captchaSolution" value="{$captchaSolution}">

    <p class="intro">{translate key="plugins.generic.registerPage.intro5"}</p>
    <input class="checkbox" type="checkbox" id="checkboxConfirmation" name="checkboxConfirmation">
        {translate key="plugins.generic.registerPage.checkboxConfirmation"}<br>

    <a href="{$pressUrl}">Cancel</a>
    <input id="buttonRegister" value="Register" type="submit">

</form> {** action="check" *}

    <div id="privacyStatement">
        <h3>{translate key="plugins.generic.registerPage.privacyStatement"}</h3>
        <p>{translate key="plugins.generic.registerPage.contentPrivacyStatement"}</p>
    </div>

</div> {**class="LSP" *}
</div> {**class="registerPage" *}

<script language="JavaScript" type="text/javascript">

   var issetCheckboxSupporter = "{$issetCheckboxSupporter}";
   var issetCheckboxReader = "{$issetCheckboxReader}";
   var issetCheckboxAuthor = "{$issetCheckboxAuthor}";
   var issetCheckboxVolumeEditor = "{$issetCheckboxVolumeEditor}";
   var issetCheckboxReviewer = "{$issetCheckboxReviewer}";
   var issetCheckboxProofreader = "{$issetCheckboxProofreader}";
   var issetCheckboxTypesetter = "{$issetCheckboxTypesetter}";
   var issetCheckboxNewsletter = "{$issetCheckboxNewsletter}";
   var issetCheckboxML = "{$issetCheckboxML}";
   var issetCheckboxConfirmation = "{$issetCheckboxConfirmation}";
   var issetCheckboxEnglish = "{$issetCheckboxEnglish}";
   var issetCheckboxGerman = "{$issetCheckboxGerman}";
   var issetCheckboxFrench = "{$issetCheckboxFrench}";
   var issetCheckboxOther = "{$issetCheckboxOther}";

    {literal}
        document.getElementById("checkboxSupporter").checked = issetCheckboxSupporter;
        document.getElementById("checkboxReader").checked = issetCheckboxReader;
        document.getElementById("checkboxAuthor").checked = issetCheckboxAuthor;
        document.getElementById("checkboxVolumeEditor").checked = issetCheckboxVolumeEditor;
        document.getElementById("checkboxConfirmation").checked = issetCheckboxConfirmation;
        document.getElementById("checkboxReviewer").checked = issetCheckboxReviewer;
        document.getElementById("checkboxProofreader").checked = issetCheckboxProofreader;
        document.getElementById("checkboxTypesetter").checked = issetCheckboxTypesetter;
        document.getElementById("checkboxNewsletter").checked = issetCheckboxNewsletter;
        document.getElementById("checkboxML").checked = issetCheckboxML;
        document.getElementById("checkboxEnglish").checked = issetCheckboxEnglish;
        document.getElementById("checkboxGerman").checked = issetCheckboxGerman;
        document.getElementById("checkboxFrench").checked = issetCheckboxFrench;
        document.getElementById("checkboxOther").checked = issetCheckboxOther;

        if (issetCheckboxReviewer) {
            document.getElementById("reviewerQuestions").style.display = 'block';
        }
    {/literal}

</script>


{strip}
        {include file="common/footer.tpl"}
{/strip}
