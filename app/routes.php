<?php

defaultRoute( 'home', REQUEST_PAGE);

addRoute('sign-in', 'auth', REQUEST_PAGE, EXCLUDE_AUTH);

addRoute('user/all', 'user', REQUEST_JSON);