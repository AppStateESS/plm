<script type="text/javascript" src="{PHPWS_SOURCE_HTTP}mod/plm/javascript/details/view.js"></script>
<script type='text/javascript'>
    $(document).ready(function(){
        $('.view-pending-nom').click(function(){
            
            // Reset any text changes done in past clicks
            $('.view-pending-nom').css('font-weight', 'normal');
            $('.view-pending-nom').css('font-style', 'normal');
            // Remind user what they have clicked
            $(this).css('font-weight', 'bold');
            $(this).css('font-style', 'italic');

            // Fetch nomination details from server
            var id = $(this).attr('id');
            $.get('index.php', {'module':'plm','view':'NominationView', 'id':id, ajax:true},
                   function(data){
                       // Slide details up, set the new data, slide back down
                       $('#nomination-view').fadeOut('fast', function(){
                           $('#nomination-view').html(data);});
                       $('#nomination-view').fadeIn('fast', function() {
                           var semaphore = new Semaphore(1);
                           // Create objects
                           $('.actor-details').each(function(index){
                               new Details(this, semaphore);
                           });

                       });

                   });
        });
    });
function checkDeny(){
    if(confirm('Deny the nominator\'s request to delete their nomination?')){
        document.forms['deny'].submit();
    } else {
        return;
    }
}

function checkApprove(){
    if(confirm('Approve the nominator\'s request to delete their nomination?')){
        document.forms['approve'].submit();
    } else {
        return;
    }
}
</script>
<h2>Pending Nomination Removal Requests</h2>
{START_DENY_FORM}{END_DENY_FORM}{START_APPRV_FORM}{END_APPRV_FORM}
<div id="pending-removal" style="display: inline-block;">
<table style="float: left;" >
  <tr>
    <th>Nominee Name
    </th>
    <th>Actions
    </th>
  </tr>
<!-- BEGIN listrows -->
  <tr>
    <td>{NAME}
    </td>
    <td><a class="view-pending-nom" id={NOMINATION}>View</a> | 
      <span onclick="checkApprove()"><a>{APPROVE}</a></span> | 
      <span onclick="checkDeny()"><a>{DENY}</a></span>
    </td>
  </tr>
<!-- END listrows -->
<!-- BEGIN EMPTY_MESSAGE -->
<tr>
  <td colspan="2"><i>{EMPTY_MESSAGE}</i></td>
</tr>
<!-- END EMPTY_MESSAGE -->
</table>

<div id="nomination-view">
</div>
</div>
<div align="center">
<b>{PAGE_LABEL}</b><br />
{PAGES}<br />
{LIMITS}
</div>
{END_FORM}
