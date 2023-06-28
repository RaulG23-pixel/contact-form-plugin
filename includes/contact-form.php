<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


add_action("rest_api_init", "create_rest_endpoint");
add_shortcode("contact","show_contact_form");
add_action('init', 'register_scripts');
add_action('wp_enqueue_scripts', 'enqueue_scripts');

function show_contact_form(){
    include MY_PLUGIN_PATH . "includes/templates/contact-form.php";
}

function register_scripts(){
    $css = plugins_url('css/contact.css',__FILE__);
    $js = plugins_url('js/contact-form.js',__FILE__);

    wp_register_style('contact-form-css',$css);
    wp_register_script('contact-form-scripts', $js);
}

function enqueue_scripts(){
    wp_enqueue_style('contact-form-css');
    wp_enqueue_script('contact-form-scripts');
}

function create_rest_endpoint(){
    register_rest_route("v1/contact-form","submit",array(
        "methods" => "POST",
        "callback" => "handle_enquiry",
    ));
}

function handle_enquiry($data){
    $params = $data->get_params();

    if(!wp_verify_nonce($params["_wpnonce"],"wp_rest")){
        return new WP_REST_Response("Message not sent", 422);
    }

    $fields = array_filter($params,function($key){
        return $key !== "_wpnonce" && $key!== "_wp_http_referer";  
    }, ARRAY_FILTER_USE_KEY);
    
    $headers = [];
    $headers[] = "from: {{$params['email']}}";
    $headers[] = "to: <raul@gmail.com>";

    $message = '';
    $message = "Message has been sent from {$params['name']} <br></br>";

    foreach($fields as $label => $value){
        $message .= ucfirst($label) .': ' . $value .'<br>';
    }
   
    function mailtrap(PHPMailer $phpmailer) {
        $phpmailer->isSMTP();
        $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = 2525;
        $phpmailer->Username = '801b93a0129ac3';
        $phpmailer->Password = 'b86f71c2b39769';

        return $phpmailer;
    }

    try {
        $mail = new PHPMailer(true);
        $phpmailer = mailtrap($mail);
        $phpmailer->setFrom($params["email"], $params["name"]);
        $phpmailer->addAddress("raulx12@gmail.com");
        $phpmailer->Subject = 'Test';
        $phpmailer->Body = $message;
        $mail->isHTML(true);

        $phpmailer->send();
        echo 'Message has been sent';
    } catch (\Throwable $e) {
        return $e->getMessage();
    }
    


      
}