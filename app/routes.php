<?php

defaultRoute( 'home', REQUEST_PAGE);

addRoute('user/authenticate', 'user', REQUEST_JSON);
addRoute('user/all', 'user', REQUEST_JSON);