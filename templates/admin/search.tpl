<h2>{TITLE}</h2>
<br />
{START_FORM}{QUERY}{SUBMIT}{END_FORM}
<br />
<div id="pager">
{PAGER}
</div>
<script type="text/javascript">
$(document).ready(function(){
    var s = new Reloader($("#search"), $("#pager"));
});
</script>
