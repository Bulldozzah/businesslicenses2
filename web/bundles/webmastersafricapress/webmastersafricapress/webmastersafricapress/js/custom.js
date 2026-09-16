jQuery(document).ready(function(){ 
	tfuse_custom_form();
    tfuse_feedback_form();
});

function tfuse_custom_form(){ 
	
}



function tfuse_feedback_form(){
    var my_error;
    var url = jQuery("input[name=temp_url]").attr('value');
    jQuery("#send_f").bind("click", function(){

        my_error = false;
        jQuery(".feedback_ajax_form input, .feedback_ajax_form textarea, .feedback_ajax_form radio, .feedback_ajax_form select").each(function(i)
        {
            var surrounding_element = jQuery(this);
            var value               = jQuery(this).attr("value");
            var check_for 			= jQuery(this).attr("id");
            var required 			= jQuery(this).hasClass("required");

            if(check_for == "email_f"){
                surrounding_element.removeClass("error valid");
                baseclases = surrounding_element.attr("class");
                if(!value.match(/^\w[\w|\.|\-]+@\w[\w|\.|\-]+\.[a-zA-Z]{2,4}$/)){
                    surrounding_element.attr("class",baseclases).addClass("error");
                    my_error = true;
                }else{
                    surrounding_element.attr("class",baseclases).addClass("valid");
                }
            }

            if(check_for == "name_f"){
                surrounding_element.removeClass("error valid");
                baseclases = surrounding_element.attr("class");
                if(value == "" || value == "Name*"){
                    surrounding_element.attr("class",baseclases).addClass("error");
                    my_error = true;
                }else{
                    surrounding_element.attr("class",baseclases).addClass("valid");
                }
            }

            if(required && check_for != "email_f" && check_for != "name_f" ){
                surrounding_element.removeClass("error valid");
                baseclases = surrounding_element.attr("class");
                if(value == ""){
                    surrounding_element.attr("class",baseclases).addClass("error");
                    my_error = true;
                }else{
                    surrounding_element.attr("class",baseclases).addClass("valid");
                }
            }


            if(jQuery(".feedback_ajax_form input, .feedback_ajax_form textarea, .feedback_ajax_form radio, .feedback_ajax_form select").length  == i+1){
                if(my_error == false){
                    jQuery(".feedback_ajax_form").slideUp(400);

                    var $datastring = "ajax=true";
                    jQuery(".feedback_ajax_form input, .feedback_ajax_form textarea, .feedback_ajax_form radio, .feedback_ajax_form select").each(function(i)
                    {
                        var $name = jQuery(this).attr('name');
                        var $value = encodeURIComponent(jQuery(this).attr('value'));
                        $datastring = $datastring + "&" + $name + "=" + $value;
                    });


                    jQuery(".feedback_ajax_form #send_f").fadeOut(100);

                    jQuery.ajax({
                        type: "POST",
                        url: "./sendmail.php",
                        data: $datastring,
                        success: function(response){
                            jQuery(".feedback_ajax_form").before("<div class='ajaxresponse_f' style='display: none;'></div>");
                            jQuery(".ajaxresponse_f").html(response).slideDown(400);
                            jQuery(".feedback_ajax_form #send_f").fadeIn(400);
                            jQuery(".feedback_ajax_form input, .feedback_ajax_form textarea, .feedback_ajax_form radio, .feedback_ajax_form select").val("");
                        }
                    });
                }
            }

        });
        return false;
    });
}
