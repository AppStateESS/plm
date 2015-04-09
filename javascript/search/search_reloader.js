//requires jquery

var Reloader = function(div, content){
    this.div     = div;
    this.content = content;
    var me = this;

    this.callback = function(){
        $.get('index.php?'+$(this).serialize(), {'ajax': true},
              function(data){
                  $(me.content).html(data);
              });
        return false;
    }

    $(this.div).submit(this.callback);
};