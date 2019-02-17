
(function($) {
    //var baseUrl = "https://suite.social/coder/call/bsp//";
	var baseUrl = "http://train.social.com//";

    // Admin Login

    $("#loginForm").submit(function(e) {
        $.ajax({
            type : "POST",
            url : baseUrl+'ajax.php',
            data : $(this).serialize(),
            success : function(response) {
                if(response.status =='TRUE'){
                    window.location.href = baseUrl+'admin.php';
                } else {
                    alert('INVALID CREDENTIALS.');
                }
            }
        });
        e.preventDefault(); return false;
    });

    // Admin Logout

    $("#logoutForm").submit(function(e) {
        $.ajax({
            type : "POST",
            url : baseUrl+'ajax.php',
            data : $(this).serialize(),
            success : function(response) {
                if(response.status){
                    window.location.href = baseUrl;
                }
            }
        });
        e.preventDefault(); return false;
    });


    /*
    *  Delete Contact from Contact list.
    */

    $('.deleteContact').click(function(e){
        var confrm = confirm("Are you sure you want to delete contact ?");
        if(confrm){
            var pid = $(this).data('id');
            $.ajax({
                type : "POST",
                url : baseUrl+'ajax.php',
                data : {
                    "action" : "delete_contact",
                    "id" : pid
                },
                success : function(data) {
                    if(data.status){
                        window.location.reload();
                    } else {
                        alert('Contact not found.');
                    }
                }
            });
        }
        e.preventDefault(); return false;
    });

	$( ".updateContact" ).click(function() {
    //dataid=$(this).attr("data-id");
		dataid=$(this).data("id");
		
		dataimg = $(".selimg[data-id = '" + dataid + "']").data("value");
		dataname = $(".selname[data-id = '" + dataid + "']").data("value");
		dataphone = $(".selphone[data-id = '" + dataid + "']").data("value");
		
		$("#id").val(dataid);
		$("#contact_name").val(dataname);
		$("#contact_phone").val(dataphone);
		
		$("#cmdUpdate").prop("disabled", false);
		$("#cmdCreate").prop("disabled", true);
		
   
  });
	$("#cmdUpdate").click(function() {
		var confrm = confirm("Are you sure you want to update contact ?");
        if(confrm){
            var pid = $("#id").val();
			
			dataname = $("#contact_name").val();
			dataphone = $("#contact_phone").val();
			
            $.ajax({
                type : "POST",
                url : baseUrl+'ajax.php',
                data : {
                    "action" : "update_contact",
                    "id" : pid,
					"name":dataname,
					"phone":dataphone,
                },
                success : function(data) {
                    if(data.status){
                        window.location.reload();
                    } else {
                        alert('Contact not found.');
                    }
                }
            });
        }
        e.preventDefault(); return false;
	});
    $('.numberPad').click(function(){
        var existingNo = $('.dialedNo').html();
        existingNo.replace(/[^0-9]/, '');
        existingNo += $(this).data('number');
        $('.dialedNo').html(existingNo);
        filterContacts(existingNo);
    });


    /*
    * Remove Number On click button.
    */
    $('.dialedErase').click(function(){
        var existingNo = $('.dialedNo').html();
        var newStr = existingNo.substring(0, existingNo.length-1);
        newStr.replace(/[^0-9]/, '');
        $('.dialedNo').html(newStr);
        filterContacts(newStr);
    });

    /*
    * Url Validation.
    */
    function formatUrl(url){
        var httpString = 'http://'
            , httpsString = 'https://'
            ;
        if (url.substr(0, httpString.length) !== httpString && url.substr(0, httpsString.length) !== httpsString)
            url = httpString + url;
        return url;
    }

    /*
     * Filter Contact form Contact list.
    *  Function : filterContacts().
    *            Parameter : Number
    */

    function filterContacts(number) {

        $("#contactList > li").each(function() {
            if ($(this).find('.contactDetail .contactNo').text().search(number) > -1) {
                $(this).show();
            }
            else {
                $(this).hide();
            }
        });
    }
    /*
    * Add New User Contact In Json.
    * */

    $( "#form_submit" ).submit(function( event ) {
        if($("#contact_url").val() == '' || $("#contact_phone").val() == ''  || $("#contact_name").val() == ''){
            alert("please add contact number,name,and url");
            return false;
        }
        //if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test($("#contact_url").val())){
        //} else {
            //alert("invalid URL");
            //return false;
        //}
        $.ajax({
            type : "POST",
            url : baseUrl+'ajax.php',
            data : {
                "action" : "check_contact",
                "phone" : $("#contact_phone").val()
            },
            success : function(data) {
                if(data.status =='TRUE'){
                    alert('Contact all ready exist.');
                    return false;
                } else {

                }
            }
        });
    });

    /*
    * Check Image Validation.
    * */

    $("#file").on('change', function(event) {

        var fileInput = document.getElementById('file');
        var filePath = fileInput.value;
        var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
        if(!allowedExtensions.exec(filePath)){
            alert('Please upload file having extensions .jpeg/.jpg/.png/.gif only.');
            fileInput.value = '';
            return false;
        }
    });

})(jQuery);
// End of use strict
