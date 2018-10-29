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

  $("#cmdCreate").click(function() {

    request = $.ajax({
      url: "new",
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
  });
  // Variable to hold request
  var request; 
  $( ".seldelete" ).click(function() {
    //dataid=$(this).attr("data-id");
    dataid=$(this).data("id");
    // Abort any pending request
    if (request) {
        request.abort();
    }
  
    request = $.ajax({
      url: "delete/" + dataid,
      type: "post",
      data: {
        csrfmiddlewaretoken: $('input[name=csrfmiddlewaretoken]').val()
      },
      success: function (response) {
        $("#entirepage").html(response);
     },
     error: function(jqXHR, textStatus, errorThrown) {
        console.log(textStatus, errorThrown);
     }

    });
   
    // Prevent default posting of form - put here to work in case of errors
    event.preventDefault();
  });
  $("#cmdUpdate").click(function() {

    request = $.ajax({
      url: "edit/" + dataid,
      type: "post",
      data: {
        name: $("#name").val(),
        email: $("#email").val(),
        note: $("#note").val(),
        csrfmiddlewaretoken: $('input[name=csrfmiddlewaretoken]').val()
      },
      success: function (response) {
               
        $("#entirepage").html(response);
        
      },
    });
    event.preventDefault();
  });

});

