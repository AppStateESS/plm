    <script type="text/javascript">
    $(document).ready(function(){
        $('.view-email').click(function(){
            $.get('index.php', {'module':'plm', 'view':'EmailView', 'id':this.id, 'ajax':true},
                  function(data){
                      $("#dialog").dialog({autoOpen : false, show: 'blind', hide: 'blind', width: 640});
                      $("#dialog").html(data);
                      $("#dialog").dialog('open');
                  });
        });
    });
</script>