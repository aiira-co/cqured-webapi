<?php
/**
 * Configuration class for the API.
 * Toggle the $enableProdMode to show or hide CQured's error reporter.
 * Set Hearders & Corss Origin Settings.
 * Api's Router file path can be changed here too.
 */
class Config
{
    // General
    public $enableProdMode=false;

    public $offline = false;
    public $offline_message = 'This site is down for maintenance.<br />Please check back again soon.';
    public $display_offline_message = '1';
    public $offline_image = '';
    public $sitename = 'airCore';
    public $captcha = '0';
    public $list_limit = '20';
    public $access = '1';


    // Header & Cross Origin Setting
    public $allow_origin = "http://localhost:4200";
    public $allow_methods ='GET, POST, PUT, DELETE, OPTIONS';
    public $max_age='86400'; //cache for 1 day
    public $content_type='json'; //return a json or xml result

    // Routing
    public $secret = 'Pi1gS3vrtWvNq3O0';
    public $routerPath="./src/api/Router.php";
}
