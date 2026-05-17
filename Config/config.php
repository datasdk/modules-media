<?php

    return [
        
        'name' => 'Media',

        'admin' => [

            "navigationbar" => [
                
                "group" => "Images",

                "sorting" => 600,

                "link" => ["name" => "Media", "icon" => "fas fa-image", "link" => "media.index", "new_window" => false ],
    /*
                "submenu" => [
                    ['icon'=> 'fas fa-photo-video','name' => 'Images', 'link' => 'media.index'],
                ]
                */
            
            ],
   
        ],

        'allowed_file_extensions' => [
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp','jfif',
            'mp3', 'wav', 'ogg', 'flac',
            'mp4', 'avi', 'mov', 'wmv', 'mkv',
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        ],

    ];
 
