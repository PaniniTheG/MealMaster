<?php
function getSite($site)
{
    if(isset($_GET['site'])){
        include_once('mealmaster_web/auth/scripts/'.$_GET['site'].'.php');
    } else{
        include_once('mealmaster_web/auth/scripts/'.$site.'.php');
    }
}