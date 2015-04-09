var HighlightFields = function(fields){
    for ( var i in fields) {
        $("#nomination_form_"+fields[i]).parent().addClass("missing-field");
    }
};