<?php

defaultRoute('auth', REQUEST_PAGE);

addRoute('sign-in', 'auth', REQUEST_PAGE, EXCLUDE_AUTH);
addRoute('guest/authenticate', 'auth', REQUEST_JSON, EXCLUDE_AUTH);

addRoute('home', 'home', REQUEST_PAGE);

addRoute('user/all', 'user', REQUEST_JSON);