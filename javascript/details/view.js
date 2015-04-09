
/*
 * Semaphore
 *
 *   Control access to a resource.  Only allow a limited number of
 * users to acquire a resource at a given time.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @package hms
 * @subpackage javascript
 */
var Semaphore = function(count){
    this.count = count;
    this.owner;

    this.acquire = function(owner){
        if(this.count > 0){
            this.count--;
            this.owner = owner;
            return true;
        }
        return false;
    }

    this.steal = function(theif){
        // Just try to steal it, not from anyone
        // in particular
        result = this.acquire(theif);
        if(!result){
            this.owner.hide()
            this.release();
            this.acquire(theif);
            return true;
        }
    }

    this.release = function(){
        this.count++;
        this.owner = null;
        return true;
    }
}

/**
 * Display details about a nominator or reference
 */
var Details = function(span, semaphore)
{
    this.span = span;
    this.type = $(span).attr('id').split('-')[0];
    this.id = $(span).attr('id').split('-')[1];
    this.view = (this.type == 'reference') ? 'ReferenceView' : 'NominatorView';
    this.semaphore = semaphore;
    this.dataIdent = "#"+this.type+"-"+this.id+" .details";
    this.cache = null;
    var me = this;

    // Show data
    this.show = function(){
        if(this.cache != null){
            this.semaphore.steal(this);
        } else {
            this.fetch();
        }
        $(this.cache).hide().appendTo(this.span).fadeIn(100);
        $(this.dataIdent).css("display", "inline");
    };

    // Get data from server
    this.fetch = function(){
        $.get('index.php', {'module':'plm', 'view': me.view,
                            'id':me.id, 'ajax':true},
              function(data){
                  me.semaphore.steal(me);
                  me.cache = data;
              });
    }

    this.hide = function(event){
        $(this.dataIdent).fadeOut(100, function(){
            $(me.dataIdent).remove();
        });
    };

    // Set mouseover events
    $(this.span).mouseenter(function(){
        // Dequeue any other events 
        $(".actor-details[id!=" +me.type+ "\-" +me.id+ "] > .details").dequeue();
        me.show();
    });

    $(this.span).mouseleave(function(e){ me.hide(e);});

    // Initialize cache
    this.fetch();
}