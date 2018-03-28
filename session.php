<?php

$lifetime = 60 * 60;//one hour session
session_set_cookie_params($lifetime, '/'); //lifetime, path, domain, secure, httponly
session_start();

?>