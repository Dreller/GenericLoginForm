<?php  

function createIcon($result){
    return '<i class="large '.($result?'check circle outline':'circle').' middle aligned icon" style="color: '.($result?'green':'red').';"></i>';
}
function settingSet($section, $setting, &$array){
    $result = Array(
        "pass" => "",
        "note" => ""
    );

    if( !isset($array[$section][$setting]) ){
        $result["pass"] = false;
        $result["note"] = "Setting not set!  Section $section, Item $setting";
    }else{
        $result["pass"] = true;
        $result["note"] = "$section/$setting is set to <strong>".$array[$section][$setting]."</strong>";
    }

    return $result;
}

# Read config file 
    $config = parse_ini_file('.login.config', TRUE);

    $tests = Array(
        "set!Application Name!Application!Name",
        "set!Redirection page for successful logon!Application!RedirectPage"
    );


# Perform tests
    foreach($tests as $test){
        $x = explode("!", $test);

        switch($x[0]){
            case "set":
                $res = settingSet($x[2], $x[3], $config);
                break;
        }

        $icon = createIcon($res["pass"]);
        $desc = $res["note"];

        echo '<div class="item">'.$icon.'<div class="content"><div class="header">'.$x[1].'</div><div class="description">'.$desc.'</div></div></div>';
    }


        ?>