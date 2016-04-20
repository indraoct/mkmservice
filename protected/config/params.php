<?php

// application-level parameters that can be accessed
        // using Yii::app()->params['paramName']
        return array(
                // this is used in contact page
                'adminEmail'=>'webmaster@example.com',
                'wsdl_iptv'=> dirname(__FILE__).DIRECTORY_SEPARATOR."../wsdl/BOSS.wsdl",
                'access_iptv' => "IPTVWS234",
                'url_service_ott' => "172.16.160.180:9346",
                'access_ott' => "OTTWS234",
                'token_moviebay_service' => "kambingjantan23",
                "prefix_moviebay" => "MBAY",
                "product_code_moviebayfree" => "MOVIEBAYFREE",
                "auth_username_ottplaymedia" => "moviebay",
                "auth_password_ottplaymedia" => "Movi3bay",
                "url_ottplaymedia" => "http://10.9.35.21/crmrestful/server/api/Userdataott",
                "suffix_ottplaymedia" => "04",
                "suffix_samsung" =>"@samsung.co.id",
                "token_samsung_service" => "kambingjantan24",
        );
