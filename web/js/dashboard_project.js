$(function() 
{
    $("#list-bugs a, #list-todo a").on("mouseover", function() {
        $(this).children("button").show();
    });
    
    $("#list-bugs a, #list-todo a").on("mouseout", function() {
        $(this).children("button").hide();
    });
    
    $("a").on("click", "#btn_close", function(event) {
        var item = $(this).parent();
        item.slideUp(function() {
            
            var panel = item.parent().parent();
            var type;
            if (panel.hasClass('panel-info')) {
                type = "TODO";
            }
            if (panel.hasClass('panel-danger')) {
                type = "BUG";
            }
            
            $.ajax(
                {url: item.children("#btn_close").children("span").text(),
                 success: function(data) {
                    $(".list-group:last").before(item);
            
                    var todoNode = $("#list-projects .active .badge");
                    var numberTodo = parseInt(todoNode.text());
                    numberTodo--;
                    if (numberTodo==0) {
                        todoNode.remove();
                    } else {
                        todoNode.text(numberTodo);
                    }
                    
                    item.children("button").remove();  
                    
                    if (type=="BUG") {
                        item.addClass("list-group-item-danger");
                    }
                    if (type=="TODO") {
                        item.addClass("list-group-item-info");
                    }           
                    
                    item.fadeIn();
                 },
                 error: function() {
                    item.fadeIn();
                 }, type: 'GET'});
        });
        
        return false;
    });
});
