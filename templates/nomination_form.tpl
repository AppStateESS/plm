<div id="nomination-nomination-form">
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

<h1 class="title">{AWARD_TITLE}</h1>

This nomination period will end on <b>{PERIOD_END}</b>.
<p>

<h2>Nominee Information</h2>

<table>
<!-- BEGIN NOMINEE_BANNER_ID -->
  <tr>
    <td class="req">{NOMINEE_BANNER_ID_LABEL}</td>
    <td>{NOMINEE_BANNER_ID}</td>
  </tr>
<!-- END NOMINEE_BANNER_ID -->
  <tr>
    <td class="req">{NOMINEE_FIRST_NAME_LABEL}</td>
    <td>{NOMINEE_FIRST_NAME}</td>
  </tr>
  <tr>
    <td>{NOMINEE_MIDDLE_NAME_LABEL}</td>
    <td>{NOMINEE_MIDDLE_NAME}</td>
  </tr>
  <tr>
    <td class="req">{NOMINEE_LAST_NAME_LABEL}</td>
    <td>{NOMINEE_LAST_NAME}</td>
  </tr>
  <tr>
    <td class="req">{NOMINEE_EMAIL_LABEL}</td>
    <td>{NOMINEE_EMAIL}@appstate.edu</td>
  </tr>
<!-- BEGIN NOMINEE_ASUBOX -->
  <tr>
    <td class="req">{NOMINEE_ASUBOX_LABEL}</td>
    <td>{NOMINEE_ASUBOX}</td>
  </tr>
<!-- END NOMINEE_ASUBOX -->
<!-- BEGIN NOMINEE_PHONE -->
  <tr>
    <td class="req">{NOMINEE_PHONE_LABEL}</td>
    <td>{NOMINEE_PHONE}</td>
  </tr>
<!-- END NOMINEE_PHONE -->
<!-- BEGIN NOMINEE_POSITION -->
  <tr>
    <td>{NOMINEE_POSITION_LABEL}</td>
    <td>{NOMINEE_POSITION}</td>
  </tr>
<!-- END NOMINEE_POSITION -->
<!-- BEGIN NOMINEE_DEPARTMENT_MAJOR -->
  <tr>
    <td>{NOMINEE_DEPARTMENT_MAJOR_LABEL}</td>
    <td>{NOMINEE_DEPARTMENT_MAJOR}</td>
  </tr>
<!-- END NOMINEE_DEPARTMENT_MAJOR -->
<!-- BEGIN NOMINEE_GPA -->
  <tr>
    <td class="req">{NOMINEE_GPA_LABEL}</td>
    <td>{NOMINEE_GPA}</td>
  </tr>
<!-- END NOMINEE_GPA -->
<!-- BEGIN NOMINEE_CLASS -->
  <tr>
    <td class="req">{NOMINEE_CLASS_LABEL}</td>
    <td>{NOMINEE_CLASS}</td>
  </tr>
<!-- END NOMINEE_CLASS -->
<!-- BEGIN NOMINEE_YEARS -->
  <tr>
    <td>{NOMINEE_YEARS_LABEL}</td>
    <td>{NOMINEE_YEARS}</td>
  </tr>
<!-- END NOMINEE_YEARS -->
<!-- BEGIN NOMINEE_RESPONSIBILITY -->
  <tr>
    <td colspan="2">Have you ever been found responsible or accepted responsibility for violating ASU's (or another school's) policies, or any law or regulation?</td>
  </tr>
  <tr>
    <td>{NOMINEE_RESPONSIBILITY_1} {NOMINEE_RESPONSIBILITY_1_LABEL}<br />{NOMINEE_RESPONSIBILITY_2} {NOMINEE_RESPONSIBILITY_2_LABEL}</td>
  </tr>
<!-- END NOMINEE_RESPONSIBILITY -->
</table>

<!-- BEGIN CATEGORY -->
<hr>

<h2> Please choose group you would like to apply to: </h2>
<ul style="list-style-image: none; list-style-type: none; margin-left:-12px;">
  <li>
    {CATEGORY_1}
    Student Conduct Board
  </li>
  
  <li>
    {CATEGORY_2}
    Academic Integrity Board
  </li>
  
  <li>
    {CATEGORY_3}
    Both / Either (Student Conduct and/or Academic Integrity Board)
  </li>
</ul>
<!-- END CATEGORY -->
<!-- BEGIN REFERENCES_OVERALL -->
<hr>

<h2>References</h2>
  <p>
  <b>Contact information for {NUM_REFS} reference(s) must be included for this application.</b>
  These references will be sent a link to submit letters of recommendation which should include relevant information that gives examples of your leadership ability, dependability, integrity, self-confidence, maturity, and communication skills as it relates to your abilities to serve on one of the student boards.</p>
  
  <ol>
    <!-- BEGIN REFERENCES_REPEAT -->
    <li>
      <table>
<!-- BEGIN REFERENCE_FIRST_NAME -->
        <tr>
          <td class="req">{REFERENCE_FIRST_NAME__LABEL}</td>
          <td>{REFERENCE_FIRST_NAME_}</td>
        </tr>
<!-- END REFERENCE_FIRST_NAME -->
<!-- BEGIN REFERENCE_LAST_NAME -->
        <tr>
          <td class="req">{REFERENCE_LAST_NAME__LABEL}</td>
          <td>{REFERENCE_LAST_NAME_}</td>
        </tr>
<!-- END REFERENCE_LAST_NAME -->
<!-- BEGIN REFERENCE_DEPARTMENT -->
        <tr>
          <td>{REFERENCE_DEPARTMENT__LABEL}</td>
          <td>{REFERENCE_DEPARTMENT_}</td>
        </tr>
<!-- END REFERENCE_DEPARTMENT -->
<!-- BEGIN REFERENCE_PHONE -->
        <tr>
          <td class="req">{REFERENCE_PHONE__LABEL}</td>
          <td>{REFERENCE_PHONE_}</td>
        </tr>
<!-- END REFERENCE_PHONE -->
<!-- BEGIN REFERENCE_EMAIL -->
        <tr>
          <td class="req">{REFERENCE_EMAIL__LABEL}</td>
          <td>{REFERENCE_EMAIL_}</td>
        </tr>
<!-- END REFERENCE_EMAIL -->
<!-- BEGIN REFERENCE_RELATIONSHIP -->
        <tr>
          <td>{REFERENCE_RELATIONSHIP__LABEL}</td>
          <td>{REFERENCE_RELATIONSHIP_}</td>
        </tr>
<!-- END REFERENCE_RELATIONSHIP -->
      </table>
    </li>
    <!-- END REFERENCES_REPEAT -->
  </ol>

<!-- END REFERENCES_OVERALL -->

<!-- BEGIN STATEMENT -->
<hr>

<h2>Resume & Short Answer</h2>
<p>Please <a href="{FILES_DIR}mod/nomination/files/StudentConductApplicationQuestions.doc">download this document</a> (please right-click and select "save link as...") and insert your answers to the short-answer questions directly into the document. Then, attach your resume as the last page of the document and upload the document using the button below. Please save your document as a PDF file, if possible. 
</p>
<p>{STATEMENT}</p>
<!-- END STATEMENT -->

<!-- BEGIN NOMINATOR_OVERALL -->
<hr>

<h2>Nominator Information</h2>
<table>
<!-- BEGIN NOMINATOR_FIRST_NAME -->
  <tr>
    <td class="req">{NOMINATOR_FIRST_NAME_LABEL}</td>
    <td>{NOMINATOR_FIRST_NAME}</td>
  </tr>
<!-- END NOMINATOR_FIRST_NAME -->
<!-- BEGIN NOMINATOR_MIDDLE_NAME -->
  <tr>
    <td>{NOMINATOR_MIDDLE_NAME_LABEL}</td>
    <td>{NOMINATOR_MIDDLE_NAME}</td>
  </tr>
<!-- END NOMINATOR_MIDDLE_NAME -->
<!-- BEGIN NOMINATOR_LAST_NAME -->
  <tr>
    <td class="req">{NOMINATOR_LAST_NAME_LABEL}</td>
    <td>{NOMINATOR_LAST_NAME}</td>
  </tr>
<!-- END NOMINATOR_LAST_NAME -->
<!-- BEGIN NOMINATOR_ADDRESS -->
  <tr>
    <td class="req">{NOMINATOR_ADDRESS_LABEL}</td>
    <td>{NOMINATOR_ADDRESS}</td>
  </tr>
<!-- END NOMINATOR_ADDRESS -->
<!-- BEGIN NOMINATOR_PHONE -->
  <tr>
    <td class="req">{NOMINATOR_PHONE_LABEL}</td>
    <td>{NOMINATOR_PHONE}</td>
  </tr>
<!-- END NOMINATOR_PHONE -->
<!-- BEGIN NOMINATOR_EMAIL -->
  <tr>
    <td class="req">{NOMINATOR_EMAIL_LABEL}</td>
    <td>{NOMINATOR_EMAIL}@appstate.edu</td>
  </tr>
<!-- END NOMINATOR_EMAIL -->
<!-- BEGIN NOMINATOR_RELATIONSHIP -->
  <tr>
    <td>{NOMINATOR_RELATIONSHIP_LABEL}</td>
    <td>{NOMINATOR_RELATIONSHIP}</td>
  </tr>
<!-- END NOMINATOR_RELATIONSHIP -->
</table>
<!-- END NOMINATOR_OVERALL -->
<hr>
<div>
  <p>
    In order for you to be considered for the Student Conduct Board and/or Academic Integrity Board, you must be a student in good academic standing (GPA of 2.5 or above) and good conduct standing (not currently on probation) within the Appalachian community. You must also attest that all of the information provided is accurate to the best of your knowledge. By submitting this form, it will give the Office of Student Conduct staff permission to check your records and grades.
  </p>
</div>

{SUBMIT}
{END_FORM}
</div>
