<script type="text/javascript" src="{PHPWS_SOURCE_HTTP}mod/plm/javascript/nomWinner/nom.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    var noms = {noms};
    for(var i in noms){
        var nom = noms[i];
        var n = new Nom(nom.NUM, nom.ID, nom.WINNER, "{PHPWS_SOURCE_HTTP}");
    }
});
</script>
