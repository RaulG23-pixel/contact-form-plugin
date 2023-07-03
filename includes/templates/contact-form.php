<div id="form_success" style="background-color: green; color: white;">
</div>

<div id="form_failed" style="background-color: red; color: white;">
</div>

<form action="#" id="enquiry_form" class="enquiry_form">
    <?php wp_nonce_field('wp_rest');?>

    <div class="form_section">
        <label for="name">Name</label>
        <input type="text" name="name" id="name">
    </div>
    <div class="form_section">
        <label for="email">Email</label>
        <input type="email" name="email" id="email">
    </div>
    <div class="form_section">
        <label for="name">Name</label>
        <textarea name="message" id="message" cols="30" rows="10"></textarea>
    </div>
    <input type="submit" value="Enviar">
    
</form>
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<script>
    jQuery(document).ready(function(x){
    jQuery("#enquiry_form").submit(function(x){
        x.preventDefault();

        let form = jQuery(this);
        let url = "<?php echo get_rest_url(null,'v1/contact-form/submit'); ?>";
        jQuery.ajax({
            type: "POST",
            url: url,
            data: form.serialize(),
            success: function(res){
                jQuery("#form_success").html(res).fadeIn();
                
            },
            error: function(){
                jQuery("#form_error").html("There has been an error on sending the message").fadeIn();
            }
        });

    });
})
</script>