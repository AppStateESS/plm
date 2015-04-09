//requires jquery

var DocumentSelector = function(orig, title, input, close){
    this.orig     = orig;
    this.title    = title;
    this.input    = input;
    this.close    = close;
    this.typeDisp = $(this.orig).find(".type-disp");
    var me = this;

    this.doChange = function(){
        var length = $(me.input).val().length;

        if(length != 0){
            me.doCheck();
        } else {
            $(me.title).html('No file');
            $(me.typeDisp).html('empty');
            return;
        }
        
        try{
            var name = /^(\w*).*$/.exec($(me.input)[0].files[0].fileName)[1];
        }
        catch(err){
            $(me.title).html('No File');
            return;
        }
        $(me.title).html(name);
    }

    this.doClose = function(){
        $(me.input).val('');
        me.doChange();
    }

    this.doCheck = function(){
        var name = $(me.input).val();
        try{
            var type = /\.(\w*)$/.exec($(me.input)[0].files[0].fileName)[1];
        }
        catch(err){
            return;
        }
        var mime = $(me.input)[0].files[0].type;
        var size = $(me.input)[0].files[0].size;

        $.post('index.php', {'module': 'nomination', 'action': 'CheckFile', 'type': mime, 'size': size},
               function(data){
                   if(data != 1){
                       me.doClose();
                   } else {
                       $(me.typeDisp).html(type);
                   }
               },'json');
    }

    /* Setup */
    $(this.input).change(me.doChange);
    //$(this.close).click(me.doClose);
    $(this.orig).click(function(){$(me.input).click();});
};