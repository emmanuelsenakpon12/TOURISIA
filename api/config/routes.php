<?php

return [
    'POST' => [
        '/api/register' => 'AuthController@register',
        '/api/login' => 'AuthController@login',
        '/api/offers' => 'OfferController@create',
        '/api/payments' => 'PaymentController@process',
        '/api/reservations' => 'ReservationController@create',
        '/api/admin/users/toggle' => 'AdminController@toggleUserStatus'
    ],
    'GET' => [
        '/api/offers' => 'OfferController@getAll',
        '/api/admin/users' => 'AdminController@users',
        '/api/user/reservations' => 'ReservationController@getUserReservations'
    ]
];
