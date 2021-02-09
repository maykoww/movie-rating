<?php 

require_once("templates/header.php");

if($userData) {
    $userDao->destroyToken();
}