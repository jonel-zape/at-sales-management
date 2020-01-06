<?php

defaultRoute('auth', REQUEST_PAGE);

addRoute('sign-in', 'auth', REQUEST_PAGE, EXCLUDE_AUTH);
addRoute('guest/authenticate', 'auth', REQUEST_JSON, EXCLUDE_AUTH);
addRoute('auth/logout', 'auth', REQUEST_PAGE);

addRoute('home', 'home', REQUEST_PAGE);

addRoute('product', 'product', REQUEST_PAGE);
addRoute('product/find', 'product', REQUEST_JSON);
addRoute('product/create', 'product', REQUEST_PAGE);
addRoute('product/edit/$', 'product', REQUEST_PAGE);
addRoute('product/delete', 'product', REQUEST_JSON);
addRoute('product/save', 'product', REQUEST_JSON);
addRoute('product/autonCompleteSearch', 'product', REQUEST_JSON);

addRoute('purchase', 'purchase', REQUEST_PAGE);
addRoute('purchase/create', 'purchase', REQUEST_PAGE);
addRoute('purchase/find', 'purchase', REQUEST_JSON);
addRoute('purchase/save', 'purchase', REQUEST_JSON);
addRoute('purchase/edit/$', 'purchase', REQUEST_PAGE);
addRoute('purchase/delete', 'purchase', REQUEST_JSON);
addRoute('purchase/details', 'purchase', REQUEST_JSON);

addRoute('sales', 'sales', REQUEST_PAGE);
addRoute('sales/create', 'sales', REQUEST_PAGE);

addRoute('user/all', 'user', REQUEST_JSON);