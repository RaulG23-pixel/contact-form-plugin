<?php

/**
 * Plugin name: Contact
 * Description: Plugin made by myself
 * Version: 1.0.0
 * Text domain: /options/plugins
 * Author: Raul Gonzalez
 * **/

 if ( !defined("ABSPATH")){
    die("you cannot be here");
 }

 if(!class_exists("ContactPlugin")){

    class ContactPlugin{

        public function __construct()
        {
            define("MY_PLUGIN_PATH", plugin_dir_path(__FILE__));
            require_once(MY_PLUGIN_PATH . "/vendor/autoload.php");
        }

        public function initialize(){
            include_once MY_PLUGIN_PATH . "/includes/utilities.php";
            include_once MY_PLUGIN_PATH . "/includes/option-fields.php";
            include_once MY_PLUGIN_PATH . "/includes/contact-form.php";

            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
            $dotenv->load();

        }
    }
    
    $contactPlugin = new ContactPlugin();

    $contactPlugin->initialize();
 }