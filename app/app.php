<?php

session_start();
ob_start();

require 'system/router.php';
require 'routes.php';

route();
