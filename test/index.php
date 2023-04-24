<?php

use BRdev\Cookie\Cookie;

require __DIR__."/../vendor/autoload.php";

// KEY UNIQUE  
$cookie = new Cookie("BRDEV", 3600);

if(true){
    //SET COOKIE STRING OR INT
    $cookie->set("pswd","mypassword");
    //GET COOKIE
    echo $cookie->pswd ."<br><br>";
}

if(false){
    if($cookie->has("user")){
    
        echo "Name: ".$cookie->user["name"]. "<br>";
        echo "Age: ".$cookie->user["age"]. "<br>";
        echo "Genre: ".$cookie->user["genre"]. "<br>";

    }else{
        //SET COOKIE ARRAY
        $cookie->set("user",[
            "name"  => "Stefan Salvatore",
            "age"   => "18",
            "genre" => "Male"
        ]);
    };
}

if(false){
    $cookie->delete("user");
}

if(false){
    $cookie->deleteAll();
}
