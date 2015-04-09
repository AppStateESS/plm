<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#dialog").hide();
        $("#{START_DATE_ID}").datepicker();
        $("#{END_DATE_ID}").datepicker();
        $(".help-icon").click(function(){
            $("#dialog").dialog();

        });
    });
</script>

<h2>Period Settings</h2>
<h3>Current period is <b>{CURRENT_PERIOD_YEAR}</b>.</h3>

<div id="dialog" title="Start/End Date">
  <ul>
    <li>Nominations will be accepted starting at 12:00am (midnight) on the selected start date.</li>
    <li>Nominations will no longer be accepted starting at 12:00am (midnight) on the selected end date.</li>
  </ul>
</div>

{START_FORM}
<table>
  <tr>
    <th>{NOMINATION_PERIOD_START_LABEL}</th>
    <td>{NOMINATION_PERIOD_START}</td>
    <td rowspan="2"><img class="help-icon" src="{HELP_ICON}"></td>
  </tr>

  <tr>
    <th>{NOMINATION_PERIOD_END_LABEL}</th>
    <td>{NOMINATION_PERIOD_END}</td>
  </tr>

  <tr>
    <th>Rollover</th>
    <td>Next period is {NEXT_PERIOD} [{ROLLOVER_LINK}]</td>
  </tr>

  <tr>
    <th>{ROLLOVER_EMAIL_LABEL}</th>
    <td>{ROLLOVER_EMAIL}</td>
  </tr>
</table>
{SUBMIT}
{END_FORM}
