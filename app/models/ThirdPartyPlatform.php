<?php

class ThirdPartyPlatform extends Eloquent {

    public static $data = [
        'Jocom' => false,
        'PGmall' => [
            1 => [
                "email" => "(pgmall@jocom.my)",
                "name" => "PGMall Jocom",
            ],
        ],
        'Lazada' => [
            1 => [
                "email" => "(elevenstreet@jocom.my)",
                "name" => "F&N",
            ],
            2 => [
                "email" => "(elevenstreet2@jocom.my)",
                "name" => "JOCOM",
            ],
            3 => [
                "email" => "(etika@jocom.my)",
                "name" => "ETIKA",
            ],
            4 => [
                "email" => "(yeos@jocom.my)",
                "name" => "YEOS"
            ], 
            5 => [
                "email" => "(starbucks@jocom.my)",
                "name" => "STARBUCKS"
            ],  
            6 => [
                "email" => "(pokka@jocom.my)",
                "name" => "POKKA",
            ],  
            7 => [
                "email" => "(ebfrozen@jocom.my)",
                "name" => "EBFROZEN",
            ],
            8 => [
                "email" => "(everbest@jocom.my)",
                "name" => "EVERBEST",
            ],  
            9 => [
                "email" => "(operation@jocom.my)",
                "name" => "JOCOM EXPRESS",
            ]
        ],
        'Shopee' => [
            1 => [
                "email" => "(shopee@jocom.my)",
                "name" => "Shopee Jocom",
            ],
            2 => [
                "email" => "(shopee@jocom.my)",
                "name" => "Shopee Coca-Cola",
            ],
            3 => [
                "email" => "(shopee@jocom.my)",
                "name" => "Shopee Yeo Hiap Seng",
            ],
            4 => [
                "email" => "(shopee@jocom.my)",
                "name" => "Shopee F&N"
            ],
            5 => [
                "email" => "(shopee.orientalfood@jocom.my)",
                "name" => "Shopee OrientalFoodMY"
            ],
            6 => [
                "email" => "(shopee.nikudo@jocom.my)",
                "name" => "Shopee NikudoSeafood",
            ],
            7 => [
                "email" => "(shopee.starbucks@jocom.my)",
                "name" => "Shopee Starbucks.OS",
            ],
            8 => [
                "email" => "(shopee.kawanfood@jocom.my)",
                "name" => "Shopee KawanFood",
            ],
            9 => [
                "email" => "(shopee.pokka@jocom.my)",
                "name" => "Shopee Pokka",
            ],
            10 => [
                "email" => "(etika@jocom.my)",
                "name" => "Shopee Etika",
            ],
            11 => [
                "email" => "(ebfrozen@jocom.my)",
                "name" => "Shopee Ebfrozen",
            ],
            12 => [
                "email" => "(everbest@jocom.my)",
                "name" => "Shopee Everbest",
            ],
        ],
        'PrestoMall/ElevenStreet' => [
            1 => [
                "email" => "(elevenstreet@jocom.my)",
                "name" => "PrestoMall Acc 1",
            ],
            2 => [
                "email" => "(elevenstreet2@jocom.my)",
                "name" => "PrestoMall Acc 2",
            ],
            3 => [
                "email" => "(elevenstreetfn@jocom.my)",
                "name" => "PrestoMall F&N",
            ],
            4 => [
                "email" => "(elevenstreetfn@jocom.my)",
                "name" => "Coca Cola"
            ], 
            5 => [
                "email" => "(spritzer@jocom.my)",
                "name" => "Spritzer"
            ],  
            6 => [
                "email" => "(cactus@jocom.my)",
                "name" => "Cactus",
            ],  
            7 => [
                "email" => "(fnncreameries@jocom.my)",
                "name" => "F&N Creamer",
            ],
            8 => [
                "email" => "(starbuck@jocom.my)",
                "name" => "Starbuck",
            ],  
            9 => [
                "email" => "(pokka@jocom.my)",
                "name" => "POKKA",
            ],
            10 => [
                "email" => "(yeos@jocom.my)",
                "name" => "Yeos",
            ],
            11 => [
                "email" => "(oriental@jocom.my)",
                "name" => "Oriental",
            ],
            12 => [
                "email" => "(kawanfood@jocom.my)",
                "name" => "KawanFood",
            ],
            13 => [
                "email" => "(nikudo@jocom.my)",
                "name" => "Nikudo",
            ],
            14 => [
                "email" => "(etika@jocom.my)",
                "name" => "Etika",
            ],
        ],
        'Qoo10' => [
            1 => [
                "email" => "(qoo10@jocom.my)",
                "name" => "Qoo10 Jocom MY & SG",
            ],
            2 => [
                "email" => "(qoo10@jocom.my)",
                "name" => "Qoo10 Singapore",
            ],
            3 => [
                "email" => "(qoo10@jocom.my)",
                "name" => "F&N",
            ]
        ],
    ];

    // // all those plateform/store info store on customer table
    // PrestoMall/ElevenStreet
    // acc-type="1" data-content="(elevenstreet@jocom.my)" PrestoMall Acc 1
    // acc-type="2" data-content="(elevenstreet2@jocom.my)" PrestoMall Acc 2
    // acc-type="3" data-content="(elevenstreetfn@jocom.my)" PrestoMall F&N
    // acc-type="4" data-content="(elevenstreetfn@jocom.my)" Coca Cola
    // acc-type="5" data-content="(spritzer@jocom.my)" Spritzer
    // acc-type="6" data-content="(cactus@jocom.my)" Cactus
    // acc-type="7" data-content="(fnncreameries@jocom.my)" F&N Creamer
    // acc-type="8" data-content="(starbuck@jocom.my)" Starbuck
    // acc-type="9" data-content="(pokka@jocom.my)" POKKA
    // acc-type="10" data-content="(yeos@jocom.my)" Yeos
    // acc-type="11" data-content="(oriental@jocom.my)" Oriental
    // acc-type="12" data-content="(kawanfood@jocom.my)" KawanFood
    // acc-type="13" data-content="(nikudo@jocom.my)" Nikudo
    // acc-type="14" data-content="(etika@jocom.my)" Etika

    // LAZADA
    // acc-type="1" title="11Street Account" data-content="(elevenstreet@jocom.my)" F&N
    // acc-type="2" title="11Street Account" data-content="(elevenstreet2@jocom.my)" JOCOM
    // acc-type="3" title="Lazada Account" data-content="(etika@jocom.my)" ETIKA
    // acc-type="4" title="Lazada Account" data-content="(yeos@jocom.my)" YEOS
    // acc-type="5" title="Lazada Account" data-content="(starbucks@jocom.my)" STARBUCKS
    // acc-type="6" title="Lazada Account" data-content="(pokka@jocom.my)" POKKA
    // acc-type="7" title="Lazada Account" data-content="(ebfrozen@jocom.my)" EBFROZEN
    // acc-type="8" title="Lazada Account" data-content="(everbest@jocom.my)" EVERBEST
    // acc-type="9" title="Lazada Account" data-content="(operation@jocom.my)" JOCOM EXPRESS

    // Qoo10
    // acc-type="1" data-content="(qoo10@jocom.my)" Qoo10 Jocom MY & SG
    // acc-type="2" data-content="(qoo10@jocom.my)" Qoo10 Singapore
    // acc-type="3" data-content="(qoo10@jocom.my)" F&N

    // SHOPEE
    // acc-type="1" data-content="(shopee@jocom.my)">Shopee Jocom
    // acc-type="2" data-content="(shopee@jocom.my)">Shopee Coca-Cola
    // acc-type="3" data-content="(shopee@jocom.my)">Shopee Yeo Hiap Seng
    // acc-type="4" data-content="(shopee@jocom.my)">Shopee F&N 
    // acc-type="5" data-content="(shopee.orientalfood@jocom.my)">Shopee OrientalFoodMY 
    // acc-type="6" data-content="(shopee.nikudo@jocom.my)">Shopee NikudoSeafood 
    // acc-type="7" data-content="(shopee.starbucks@jocom.my)">Shopee Starbucks.OS 
    // acc-type="8" data-content="(shopee.kawanfood@jocom.my)">Shopee KawanFood
    // acc-type="9" data-content="(shopee.pokka@jocom.my)">Shopee Pokka
    // acc-type="10" data-content="(etika@jocom.my)">Shopee Etika
    // acc-type="11" data-content="(ebfrozen@jocom.my)">Shopee Ebfrozen
    // acc-type="12" data-content="(everbest@jocom.my)">Shopee Everbest

    // PGMALL
    // acc-type="1" data-content="(pgmall@jocom.my)" PGMall Jocom

    // Astro Go Shop

    // Taobao

    // 1688
    
    // Tmall

    // Offline Sales
}