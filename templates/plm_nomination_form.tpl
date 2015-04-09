<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<div id="plm-nomination-form">
  <div id="maintenance" >
    <!-- BEGIN cancel -->
    <div id="cancel" style="">
      <h3 style="text-align: left;">Nomination Withdrawal Request</h3>
      {START_FORM}
      {SUBMIT}
      {END_FORM}
    </div>
    <!-- END cancel -->
    <!-- BEGIN resend -->
    <div id="resend" style="">
      <h3 style="text-align: left;">Resend Notification Emails</h3>
      {START_FORM}
      {USERS_1_LABEL}
      {USERS_1}
      {USERS_2_LABEL}
      {USERS_2}<br />
      {USERS_3_LABEL}
      {USERS_3}
      {USERS_4_LABEL}
      {USERS_4}<br />
      {SUBMIT}
      {END_FORM}
    </div>
    <!-- END resend -->
  </div>
{START_FORM}
<div class="title"><h2>{AWARD_TITLE}</h2></div>
<ol type="i">
<li>
<h3>Nominee Information</h3>
  <table>
<tr>
  <th class="req">
{NOMINEE_FIRST_NAME_LABEL}
  </th>
  <td>
{NOMINEE_FIRST_NAME}
  </td>
</tr>
<tr>
  <th>
{NOMINEE_MIDDLE_NAME_LABEL}
  </th>
  <td>
{NOMINEE_MIDDLE_NAME}
  </td>
</tr>
<tr>
  <th class="req">
{NOMINEE_LAST_NAME_LABEL}
  </th>
  <td>
{NOMINEE_LAST_NAME}
  </td>
</tr>
<tr>
  <th class="req">
{NOMINEE_EMAIL_LABEL}
  </th>
  <td>
{NOMINEE_EMAIL}
  </td>
</tr>
<tr>
  <th>
{NOMINEE_POSITION_LABEL}
  </th>
  <td>
{NOMINEE_POSITION}
  </td>
</tr>
<tr>
  <th>
{NOMINEE_DEPARTMENT_MAJOR_LABEL}
  </th>
  <td>
{NOMINEE_DEPARTMENT_MAJOR}
  </td>
</tr>
<tr>
  <th>
{NOMINEE_YEARS_LABEL}
  </th>
  <td>
{NOMINEE_YEARS}
  </td>
</tr>
  </table>
</li>

<hr>

<li>
<div>
  <h3> Please choose the appropriate category: </h3>
  <ul style="list-style-image: none; list-style-type: none; margin-left:-12px;">
    <li>
    {CATEGORY_1}<b>Student Leader</b>- one who has provided distinguished leadership above that of other student leaders
    </li>
    <li>
    {CATEGORY_2}<b>Student Development Educator within the Division of Student Development</b>- for meritorious leadership in his or her work to enrich the quality of student life and learning.
    </li>
    <li>
    {CATEGORY_3}<b>Faculty Member</b> - one who has provided meritorious leadership through his or her work with student clubs or organizations, or work that enriches the quality of student life and learning outside of the classroom.
    </li>
    <li>
    {CATEGORY_4} <b>Employee of Appalachian State University</b>- one who has shown that he or she has provided meritorious leadership which has significantly enriched the quality of student life and learning.
    </li>
  </ul>
</div>
</li>

<hr>

<li>
<div>
  <h3>References</h3>
  <p><b>Contact information for three references must be included for this nomination.</b> These references will be sent a link to submit letters of recommendation which should include relevant information that gives examples of meritorious leadership and qualities that exemplifies that the nominee has gone above and beyond what is expected in the course of their normal responsibilities.</p>
  <ol>

  <li id="ref_1" style="margin-bottom: 3em;">
  <table>
    <tr>
      <tr>
      <th class="req">
      {REFERENCE_FIRST_NAME_1_LABEL}
      </th>
      <td>
      {REFERENCE_FIRST_NAME_1}
      </td>
      </tr>
      <tr>
      <th>
      {REFERENCE_MIDDLE_NAME_1_LABEL}
      </th>
      <td>
      {REFERENCE_MIDDLE_NAME_1}
      </td>
      </tr>
      <tr>
      <th class="req">
      {REFERENCE_LAST_NAME_1_LABEL}
      </th>
      <td>
      {REFERENCE_LAST_NAME_1}
      </td>
      </tr>
      <tr>
      <th>
      {REFERENCE_DEPARTMENT_1_LABEL}
      </th>
      <td>
      {REFERENCE_DEPARTMENT_1}
      </td>
      </tr>
      <tr>
      <th class="req">
      {REFERENCE_PHONE_1_LABEL}
      </th>
      <td>
      {REFERENCE_PHONE_1}
      </td>
      </tr>
      <tr>
      <th class="req">
      {REFERENCE_EMAIL_1_LABEL}
      </th>
      <td>
      {REFERENCE_EMAIL_1}
      </td>
      </tr>
      <tr>
      <th>
      {REFERENCE_RELATIONSHIP_1_LABEL}
      </th>
      <td>
      {REFERENCE_RELATIONSHIP_1}
      </td>
      </tr>
    </tr>
  </table>
  </li>

  <li id="ref_2" style="margin-bottom: 3em;">
  <table>
    <tr>
      <tr>
      <th class="req">
      {REFERENCE_FIRST_NAME_2_LABEL}
      </th>
      <td>
      {REFERENCE_FIRST_NAME_2}
      </td>
      </tr>
      <tr>
      <th>
      {REFERENCE_MIDDLE_NAME_2_LABEL}
      </th>
      <td>
      {REFERENCE_MIDDLE_NAME_2}
      </td>
      </tr>
      <tr>
      <th class="req">
      {REFERENCE_LAST_NAME_2_LABEL}
      </th>
      <td>
      {REFERENCE_LAST_NAME_2}
      </td>
      </tr>
      <tr>
      <th>
      {REFERENCE_DEPARTMENT_2_LABEL}
      </th>
      <td>
      {REFERENCE_DEPARTMENT_2}
      </td>
      </tr>
      <tr>
      <th class="req">
      {REFERENCE_PHONE_2_LABEL}
      </th>
      <td>
      {REFERENCE_PHONE_2}
      </td>
      </tr>
      <tr>
      <th class="req">
      {REFERENCE_EMAIL_2_LABEL}
      </th>
      <td>
      {REFERENCE_EMAIL_2}
      </td>
      </tr>
      <tr>
      <th>
      {REFERENCE_RELATIONSHIP_2_LABEL}
      </th>
      <td>
      {REFERENCE_RELATIONSHIP_2}
      </td>
      </tr>
    </tr>
  </table>
  </li>

  <li id="ref_3">
  <table>
    <tr>
      <tr>
      <th class="req">
      {REFERENCE_FIRST_NAME_3_LABEL}
      </th>
      <td>
      {REFERENCE_FIRST_NAME_3}
      </td>
      </tr>
      <tr>
      <th>
      {REFERENCE_MIDDLE_NAME_3_LABEL}
      </th>
      <td>
      {REFERENCE_MIDDLE_NAME_3}
      </td>
      </tr>
      <tr>
      <th class="req">
      {REFERENCE_LAST_NAME_3_LABEL}
      </th>
      <td>
      {REFERENCE_LAST_NAME_3}
      </td>
      </tr>
      <tr>
      <th>
      {REFERENCE_DEPARTMENT_3_LABEL}
      </th>
      <td>
      {REFERENCE_DEPARTMENT_3}
      </td>
      </tr>
      <tr>
      <th class="req">
      {REFERENCE_PHONE_3_LABEL}
      </th>
      <td>
      {REFERENCE_PHONE_3}
      </td>
      </tr>
      <tr>
      <th class="req">
      {REFERENCE_EMAIL_3_LABEL}
      </th>
      <td>
      {REFERENCE_EMAIL_3}
      </td>
      </tr>
      <tr>
      <th>
      {REFERENCE_RELATIONSHIP_3_LABEL}
      </th>
      <td>
      {REFERENCE_RELATIONSHIP_3}
      </td>
      </tr>
    </tr>
  </table>
  </li>

  </ol>
</div>
</li>

<hr>

<li>
<h3>Statement</h3>
<p>    A statement from nominator must be included that supports this nomination for the Plemmons<br />
       Leadership Award. This statement should address the following:<br />
       a) Work and involvement with students outside the classroom<br />
       b) Role or roles that have had an impact on the life of students on campus<br />
       c) Meritorious involvement- why does this person stand above others?<br />
</p>
<p>{STATEMENT}</p>
</li>

<hr>

<li>
<h3>Nominator Information</h3>
<table>
  <tr>
    <tr>
      <th class="req">
      {NOMINATOR_FIRST_NAME_LABEL}
      </th>
      <td>
      {NOMINATOR_FIRST_NAME}
      </td>
    </tr>
    <tr>
      <th>
      {NOMINATOR_MIDDLE_NAME_LABEL}
      </th>
      <td>
      {NOMINATOR_MIDDLE_NAME}
      </td>
    </tr>
    <tr>
      <th class="req">
      {NOMINATOR_LAST_NAME_LABEL}
      </th>
      <td>
      {NOMINATOR_LAST_NAME}
      </td>
    </tr>
    <tr>
      <th class="req">
      {NOMINATOR_ADDRESS_LABEL}
      </th>
      <td>
      {NOMINATOR_ADDRESS}
      </td>
    </tr>
    <tr>
      <th class="req">
      {NOMINATOR_PHONE_LABEL}
      </th>
      <td>
      {NOMINATOR_PHONE}
      </td>
    </tr>
    <tr>
      <th class="req">
      {NOMINATOR_EMAIL_LABEL}
      </th>
      <td>
      {NOMINATOR_EMAIL}
      </td>
    </tr>
    <tr>
      <th>
      {NOMINATOR_RELATIONSHIP_LABEL}
      </th>
      <td>
      {NOMINATOR_RELATIONSHIP}
      </td>
    </tr>
  </tr>
</table>
</li>
</ol>
{SUBMIT}
</div>
{END_FORM}
</div>
