var Collapse = function(num, id, PHPWS_SOURCE_HTTP){
    this.num = num;
    this.isOpen = true;

    this.open = "<img src='"+PHPWS_SOURCE_HTTP+"mod/plm/img/arrow_down.png'/> Nomination "+this.num;
    this.closed = "<img src='"+PHPWS_SOURCE_HTTP+"mod/plm/img/arrow_right.png'/> Nomination "+this.num;

    this.nom = ".nomination#"+num;
    this.toggle = "#nom-toggle-"+num;

    this.id = id;

    var me = this;
    
    this.toggleNom = function(){
        $(this.nom).slideToggle('fast', function(){
            me.isOpen = !me.isOpen;
            me.toggleToggle();
        });

    }
    
    this.toggleToggle = function(){
        if(this.isOpen){
            $(this.toggle).html(this.open);
        } else {
            $(this.toggle).html(this.closed);
        }
    }

    // Hide/Show nomination details when clicked
    $(this.toggle).click(function(){
        me.toggleNom();
    });
}