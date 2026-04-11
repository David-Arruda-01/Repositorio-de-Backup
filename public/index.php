<?php
require_once "../app/application.php";

use Fmk\Utils\Request;

session_start();

echo Request::exec();
