$( document ).ready( function() {  
  
  $( ".selupdate" ).click(function() {
    //dataid=$(this).attr("data-id");
    dataid=$(this).data("id");
 
    dataname = $(".selname[data-id = '" + dataid + "']").data("value");
    dataemail = $(".selemail[data-id = '" + dataid + "']").data("value");
    datanote = $(".selnote[data-id = '" + dataid + "']").data("value");
    $("#id").val(dataid);
    $("#name").val(dataname);
    $("#email").val(dataemail);
    $("#note").val(datanote);
    $("#cmdUpdate").prop("disabled", false);
    $("#cmdCreate").prop("disabled", true);
   
  });

  show_page = function(actionurl)
  {
    request = $.ajax({
      url: actionurl,
      type: "post",
      data: {
        name: $("#name").val(),
        email: $("#email").val(),
        note: $("#note").val(),
        csrfmiddlewaretoken: $('input[name=csrfmiddlewaretoken]').val()
      },
      success: function (response) {
        // you will get response from your php page (what you echo or print)                 
        $("#entirepage").html(response);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        //console.log(textStatus, errorThrown);
      }

    });
    event.preventDefault();
  }

  $("#cmdCreate").click(function() {
    show_page("new");
  });
 
  $( ".seldelete" ).click(function() {
    dataid=$(this).data("id");
    show_page("delete/" + dataid);
  });

  $("#cmdUpdate").click(function() {
    show_page("edit/" + dataid);
  });

});

