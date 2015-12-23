/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * by:mashihe
 */
 $(function(){
     $("tbody tr:even").css("background-color","#369456");

     var numTd=$("tbody td:even");
     numTd.click(function(){
         var tdObj=$(this);
        if(tdObj.children("input").length>0){
             return false;
         }
         var text=tdObj.html();
         var inputObj=$("<input type='text' />");
         inputObj.css("border-width","0");
         inputObj.css("font-size","16px");

         inputObj.width(tdObj.width());
         inputObj.val(tdObj.html());
         inputObj.css("background-color",tdObj.css("background-color"));
         tdObj.html("");
        inputObj.appendTo(tdObj);
         //inputObj.get(0).select();
         inputObj.trigger("focus").trigger("select");
         inputObj.click(function(){
             return false;
         });
         inputObj.keyup(function(event){
             var keycode=event.which;
            if(keycode==13){
                var inputValue=$(this).val();
                tdObj.html(inputValue);
            }
            if(keycode==27){
                 tdObj.html(text);
             }
         });
     });
});