<script type="text/javascript" src="{PHPWS_SOURCE_HTTP}mod/plm/javascript/nomCollapse/collapse.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    var noms = {noms};
    for(var i in noms){
        var nom = noms[i];
        var n = new Collapse(nom.NUM, nom.ID, "{PHPWS_SOURCE_HTTP}");
    }
});
</script>
