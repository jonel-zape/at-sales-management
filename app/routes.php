<?php

defaultRoute('auth', REQUEST_PAGE);

addRoute('sign-in', 'auth', REQUEST_PAGE, EXCLUDE_AUTH);
addRoute('guest/authenticate', 'auth', REQUEST_JSON, EXCLUDE_AUTH);
addRoute('auth/logout', 'auth', REQUEST_PAGE);

addRoute('home', 'home', REQUEST_PAGE);
addRoute('home/summary', 'home', REQUEST_JSON);

addRoute('product', 'product', REQUEST_PAGE);
addRoute('product/find', 'product', REQUEST_JSON);
addRoute('product/create', 'product', REQUEST_PAGE);
addRoute('product/edit/$', 'product', REQUEST_PAGE);
addRoute('product/delete', 'product', REQUEST_JSON);
addRoute('product/save', 'product', REQUEST_JSON);
addRoute('product/autonCompleteSearch', 'product', REQUEST_JSON);
addRoute('product/receivedAutoCompleteSearch', 'product', REQUEST_JSON);

addRoute('purchase', 'purchase', REQUEST_PAGE);
addRoute('purchase/create', 'purchase', REQUEST_PAGE);
addRoute('purchase/find', 'purchase', REQUEST_JSON);
addRoute('purchase/save', 'purchase', REQUEST_JSON);
addRoute('purchase/edit/$', 'purchase', REQUEST_PAGE);
addRoute('purchase/delete', 'purchase', REQUEST_JSON);
addRoute('purchase/details', 'purchase', REQUEST_JSON);
addRoute('purchase/autonCompleteSearchInvoice', 'purchase', REQUEST_JSON);

addRoute('sales', 'sales', REQUEST_PAGE);
addRoute('sales/find', 'sales', REQUEST_JSON);
addRoute('sales/create', 'sales', REQUEST_PAGE);
addRoute('sales/save', 'sales', REQUEST_JSON);
addRoute('sales/edit/$', 'sales', REQUEST_PAGE);
addRoute('sales/details', 'sales', REQUEST_JSON);

addRoute('payment', 'payment', REQUEST_PAGE);
addRoute('payment/find', 'payment', REQUEST_JSON);
addRoute('payment/pay', 'payment', REQUEST_JSON);

addRoute('settings', 'settings', REQUEST_PAGE);
addRoute('settings/loginAttempt', 'settings', REQUEST_JSON);
addRoute('settings/loginAttemptCount', 'settings', REQUEST_JSON);
addRoute('settings/clearLoginAttemptCount', 'settings', REQUEST_JSON);
addRoute('settings/updateUsername', 'settings', REQUEST_JSON);
addRoute('settings/updatePassword', 'settings', REQUEST_JSON);
addRoute('settings/clearData', 'settings', REQUEST_JSON);

addRoute('user/all', 'user', REQUEST_JSON);