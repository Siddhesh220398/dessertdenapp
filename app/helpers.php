<?php

function getPermissions($user_type = 'normal'){
    $permissions = array();

    if($user_type == 'admin'){
        $permissions = [
            1 =>[                                                   // Dashboard
                'permissions' => 'access'
            ],
            2 =>[                                                   // Manage Banners
                'permissions' => 'access,add,delete'
            ],
            3 =>[                                                   // Manage Cities
                'permissions' => 'access,add,edit,delete'
            ],
            4 =>[                                                   // Manage Delivery times
                'permissions' => 'access,add,edit,delete'
            ],
            5 =>[                                                   // Manage Categories
                'permissions' => 'access,add,edit,delete'
            ],
            6 =>[                                                   // Manage Sub Category
                'permissions' => 'access,add,edit,delete'
            ],
            7 =>[                                                   // Manage Franchise
                'permissions' => 'access,add,edit,view,delete'
            ],
            8 =>[                                                   // Manage Franchises Price
                'permissions' => 'access,add,edit,delete'
            ],
            9 =>[                                                   // Manage Flavour
                'permissions' => 'access,add,edit,delete'
            ],
            10 =>[                                                   // Manage Product
                'permissions' => 'access,add,edit,view,delete'
            ],
            11 =>[                                                   // Manage Price
                'permissions' => 'access,add,edit,delete'
            ],
            12 =>[                                                   // Manage Price of Paper
                'permissions' => 'access,add,edit,delete'
            ],
             13 =>[                                                   // Manage Staff
                 'permissions' => 'access,add,edit,view,delete'
             ],
             14 =>[                                                   // Manage Coupon
                 'permissions' => 'access,add,edit,view,delete'
             ],
             15 =>[                                                   // Manage Order
                 'permissions' => 'access,edit,view,delete'
             ],
             16 =>[                                                   // Manage User
                 'permissions' => 'access,edit,view,delete'
             ],
            17 => [                                                   // Manage User
                'permissions' => 'access,edit,view,delete'
            ],

        ];
    }

    return $permissions;
}

function getdistributorsPermissions($user_type = 'normal'){
    $permissions = array();

    if($user_type == 'distributor'){
        $permissions = [
            1 =>[                                                   // Dashboard
                'permissions' => 'access'
            ],


        ];
    }

    return $permissions;
}

function getURL($url = ''){
    return ((!preg_match("~^(?:f|ht)tps?://~i", $url) && !empty($url)) ? '//' : '') . $url;
}

function sendPushMessage($token, $message = '', $data = ['user_id' => 0]){
    $content = ["en" => $message];

    $fields = [
        'app_id' => "89a0babc-d9e6-4e10-bf95-e7672e716b71",
        'include_player_ids' => [$token],
        'data' => $data,
        'large_icon' =>"ic_launcher_round.png",
        'contents' => $content,
        'android_channel_id'=> "88e467d5-c128-47c0-8eb3-aff74c216d31"
    ];

    $fields = json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8',
                                               'Authorization: Basic N2QyYWYxYWItZjQ4OS00MzQ3LTk2NGQtNWIyMjE5YzJmODhl']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $response = curl_exec($ch);
    curl_close($ch);

    return true;
}
