<?php
/**
 * Created by PhpStorm.
 * User: TONY
 * Date: 2016-10-18
 * Time: 20:21
 */

header("Cache-Control: no-cache, must-revalidate");
session_start();
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {//The SESSION does not exist
    die ("<script type=\"text/javascript\">window.top.location='login.php';</script>");
}

?>