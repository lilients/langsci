{**
 * plugins/generic/registerPage/templates/subscriptionDetails.tpl
 *
 * Copyright (c) 2014 Carola Fanselow Freie Universität Berlin
 * Distributed under the GNU GPL v2.
 *
 *
 *}

<script language="JavaScript" type="text/javascript">
{literal}
    function showReviewerQuestions() {
        if (document.getElementById("checkboxReviewer").checked == false){
            document.getElementById("reviewerQuestions").style.display = 'none';
        } else {
            document.getElementById("reviewerQuestions").style.display = 'block';
        }
    }
{/literal}
</script>


    <p class="intro">{translate key="plugins.generic.registerPage.intro2"}</p>

    {translate|assign:"supporter_text" key="plugins.generic.registerPage.checkboxSupporter"}
    {translate|assign:"author_text" key="plugins.generic.registerPage.checkboxAuthor"}
    {translate|assign:"volumeEditor_text" key="plugins.generic.registerPage.checkboxVolumeEditor"}

    <input class="checkbox" type="checkbox" id="checkboxSupporter" name="checkboxSupporter"/> {eval var=$supporter_text}<br>
    <input class="checkbox" type="checkbox" id="checkboxReader" name="checkboxReader"/>
        {translate key="plugins.generic.registerPage.checkboxReader"}<br>
    <input class="checkbox" type="checkbox" id="checkboxAuthor" name="checkboxAuthor"/>
        {eval var=$author_text}<br>
    <input class="checkbox" type="checkbox" id="checkboxVolumeEditor" name="checkboxVolumeEditor"/>
        {eval var=$volumeEditor_text}<br>
    <input class="checkbox" type="checkbox" id="checkboxReviewer" name="checkboxReviewer" onclick="showReviewerQuestions()"/>
        {translate key="plugins.generic.registerPage.checkboxReviewer"}<br>


    <div id="reviewerQuestions">
       <div id="reviewerQuestionText">
          <p>{translate key="plugins.generic.registerPage.primaryArea"}</p>
          <p>{translate key="plugins.generic.registerPage.secondaryArea"}</p>
          <p>{translate key="plugins.generic.registerPage.tertiaryArea"}</p>
          <p>{translate key="plugins.generic.registerPage.reviewLanguage"}</p>
        </div>

        <section>
            <select id="selectArea1" name="selectArea1">
              <optgroup>
                    <option></option>
                    {foreach item=a from=$areas}
                        {if $a==$area1}
                            <option class="inputOption" selected>{$a}</option>
                        {else}
                            <option class="inputOption">{$a}</option>
                        {/if}
                    {/foreach}
                </optgroup>
            </select>
        </section>

        <section>
            <select id="selectArea2" name="selectArea2">
              <optgroup>
                    <option></option>
                    {foreach item=a from=$areas}
                        {if $a==$area2}
                            <option class="inputOption" selected>{$a}</option>
                        {else}
                            <option class="inputOption">{$a}</option>
                        {/if}
                    {/foreach}
                </optgroup>
            </select>
        </section>

        <section>
            <select id="selectArea3" name="selectArea3">
              <optgroup>
                    <option></option>
                    {foreach item=a from=$areas}
                        {if $a==$area3}
                            <option class="inputOption" selected>{$a}</option>
                        {else}
                            <option class="inputOption">{$a}</option>
                        {/if}
                    {/foreach}
                </optgroup>
            </select>
        </section>

        <div id="checkboxexReviewLanguages">
            <input class="checkbox" type="checkbox" id="checkboxEnglish" name="checkboxEnglish"/>
                {translate key="plugins.generic.registerPage.checkboxEnglish"}<br>
            <input class="checkbox" type="checkbox" id="checkboxGerman" name="checkboxGerman"/>
                {translate key="plugins.generic.registerPage.checkboxGerman"}<br>
            <input class="checkbox" type="checkbox" id="checkboxFrench" name="checkboxFrench"/>
                {translate key="plugins.generic.registerPage.checkboxFrench"}<br>
            <input class="checkbox" type="checkbox" id="checkboxOther" name="checkboxOther"/>
                {translate key="plugins.generic.registerPage.checkboxOther"}
            <input id="inputOtherLanguage" name="inputOtherLanguage" value="{$otherLanguage}" type="text">
        </div>

        {if $emptyArea1}<p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredArea1"}</p>{/if}
        {if not $languageOkay}<p class="fieldRequired">{translate key="plugins.generic.registerPage.requiredLanguage"}</p>{/if}

    </div> {**end div: reviewerQuestions**}


    <input  type="checkbox" id="checkboxProofreader" name="checkboxProofreader"><span class="checkboxtext">
        {translate key="plugins.generic.registerPage.checkboxProofreader"}</span><br>
    <input  type="checkbox" id="checkboxTypesetter" name="checkboxTypesetter"><span class="checkboxtext">
        {translate key="plugins.generic.registerPage.checkboxTypesetter"}</span>

    <p class="intro">{translate key="plugins.generic.registerPage.intro3"}</p>


    <input class="checkbox" type="checkbox" id="checkboxNewsletter" name="checkboxNewsletter">
        {translate key="plugins.generic.registerPage.checkboxNewsletter"}<br>
    <input class="checkbox" type="checkbox" id="checkboxML" name="checkboxML">
        {translate key="plugins.generic.registerPage.checkboxML"}<br>

