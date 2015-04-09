var Nom = function(num, id, winner, PHPWS_SOURCE_HTTP){
    var awardIcon  = PHPWS_SOURCE_HTTP+"mod/plm/img/tango/actions/list-add-green.png";
    var removeIcon = PHPWS_SOURCE_HTTP+"mod/plm/img/tango/actions/list-remove-red.png";
    var awardHint  = "Click to set as winner";
    var removeHint = "Click to remove winner status";
    var me = this;

    this.nomManager = ".nom-manager.nom-"+num;
    this.icon = this.nomManager+" #icon";
    this.helpText = this.nomManager+" #help-text";
    this.winner = winner;
    this.id = id;

    // Insert and hide text
    $(this.helpText).html(this.winner ? removeHint : awardHint);
    $(this.helpText).hide();

    // Post request to server. Set nomination to loser/winner
    // Swap icons and helper text
    this.toggleWinner = function() {
        var icon = me.winner ? awardIcon : removeIcon;
        var status = me.winner ? 0 : 1;

        $.post('index.php', {'module': 'plm', 'action': 'SetWinnerStatus', 'id': me.id, 'status': status}, 
               function(data){
                   if(!data){
                       alert("Cannot change winner status");
                   } else {
                       
                       // Fade icons
                       $(me.icon).fadeOut('fast',function(){
                           $(me.icon).attr('src', icon);
                       });
                       $(me.icon).fadeIn('fast');

                       // Change winner status
                       me.winner = !me.winner;
                       // Change helper text
                       $(me.helpText).html(me.winner ? removeHint : awardHint);
                   }
               }, 'json');
    }

    // Set to winner when icon is clicked
    $(this.nomManager).click(function(){
        me.toggleWinner();
    });

    // Fade help text in/out
    $(this.nomManager).mouseenter(function() {
        $(me.helpText).fadeIn('fast');
    });
    $(this.nomManager).mouseleave(function() {
        $(me.helpText).fadeOut('fast');
    });
}