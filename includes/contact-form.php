<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


add_action("rest_api_init", "create_rest_endpoint");
add_shortcode("contact","show_contact_form");
add_action('init', 'register_scripts');
add_action('init', 'create_submissions_page');
add_action('wp_enqueue_scripts', 'enqueue_scripts');
add_action('add_meta_boxes','create_meta_box');

//Create a meta box for Submissions
function create_meta_box(){
    add_meta_box('custom_contact_form','Submission','display_submission','submission');
}

function display_submission(){
    $postMetas = get_post_meta(get_the_ID());
    echo "<ul>";
    unset($postMetas["_edit_lock"]);
    foreach($postMetas as $key => $value){
        echo "<li><strong>" . ucfirst($key) . "</strong>" . ': ' . $value[0] . "</li>";
    }
    echo "</ul>";
}


function create_submissions_page(){
    //Atributes for submissions page
    $args = [
        'public' => true,
        'has_archive' => true,
        'labels' => [
            'name' => 'Submissions',
            'singular_name' => 'Submission'
        ],
        'supports' => false,
    ];

    register_post_type('submission',$args);
}

function show_contact_form(){
    include MY_PLUGIN_PATH . "includes/templates/contact-form.php";
}

function register_scripts(){
    $css = plugins_url('css/contact.css',__FILE__);
    wp_register_style('contact-form-css',$css);
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

    $postArgs = [
            'post_title' => $params["name"],
            'post_type' => 'Submission'
        ];
        
    $post_id = wp_insert_post($postArgs);

    foreach($fields as $label => $value){
        $message .= ucfirst($label) .': ' . $value .'<br>';
        add_post_meta($post_id,$label,$value);
    }
   
    function mailtrap(PHPMailer $phpmailer) {
        $phpmailer->isSMTP();
        $phpmailer->Host = $_ENV['MAIL_HOST'];
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = $_ENV['MAIL_PORT'];;
        $phpmailer->Username = $_ENV['MAIL_USERNAME'];;
        $phpmailer->Password = $_ENV['MAIL_PASSWORD'];

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
        return new WP_REST_Response("Message has been sent", 200);

    } catch (\Throwable $e) {
        return new WP_REST_Response("There was an error sending the email", 500);
    }
      
}