<?php

// default landing page
addRoute('index.php', 'home.php', REQUEST_PAGE);

addRoute('user/authenticate', 'user.php', REQUEST_JSON, EXCLUDE_AUTH);
addRoute('user/logout', 'user.php', REQUEST_JSON, EXCLUDE_AUTH);

addRoute('login', 'login.php', REQUEST_PAGE, EXCLUDE_AUTH);
addRoute('home', 'home.php', REQUEST_PAGE);
