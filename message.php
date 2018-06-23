<?php
include("./include/dbconfig.php");
use Cmfcmf\OpenWeatherMap;

$data = json_decode(file_get_contents('php://input'),true);
$content = $data["content"];
$user_key = $data["user_key"];

$sql = "insert into log(user_key, content) values('$user_key','$content')";
$mysqli->query($sql);

// 급식
if($content == "급식"){
echo <<< EOD
    {
        "message": {
            "text": "급식을 선택하세요."
        },
        "keyboard": { 
            "type": "buttons",
            "buttons": [
                "오늘 급식",
                "내일 급식",
                "처음으로"
            ]
        }
    }
EOD;
}

// 날씨
elseif($content == "날씨"){
    require 'vendor/autoload.php';
    $lang = 'ko';
    $units = 'metric';
    $owm = new OpenWeatherMap('8677921cc13beec7e1d9189f12e02993');
    $weather = $owm->getWeather('Seoul', $units, $lang);
    $weather_final=$weather->temperature;
    $weather_final=str_replace("&deg;C", "°C", $weather_final);
echo <<< EOD
    {
        "message": {
            "text": "날씨는 $weather_final 입니다."
        },
        "keyboard": { 
            "type": "buttons",
            "buttons": [
                "급식",
                "날씨",
                "코인",
                "자유대화"
            ]
        }
    }
EOD;
}

// 처음으로
elseif($content == "처음으로"){
echo <<< EOD
    {
        "message": {
            "text": "메인입니다."
        },
        "keyboard": { 
            "type": "buttons",
            "buttons": [
                "급식",
                "날씨",
                "코인",
                "자유대화"
            ]
        }
    }
EOD;
}

// 오늘 급식
elseif(strpos($content, "오늘") !== false && strpos($content, "급식") !== false){
    $url = 'http://juneyoung.kr/api/school-meal/meal_api.php?countryCode=stu.goe.go.kr&schulCode=J100004922&insttNm=교하고등학교&schulCrseScCode=4&schMmealScCode=2';
    $json=file_get_contents($url);
    $result=json_decode($json, true);
    $meal=$result['메뉴'];
echo <<< EOD
    {
        "message": {
            "text": "sss $meal"
        },
        "keyboard": { 
            "type": "buttons",
            "buttons": [
                "급식",
                "날씨",
                "코인",
                "자유대화"
            ]
        }
    }
EOD;
}

// 내일 급식
elseif(strpos($content, "내일") !== false && strpos($content, "급식") !== false){
echo <<< EOD
    {
        "message": {
            "text": "내일 급식은 {meal}입니다."
        },
        "keyboard": { 
            "type": "buttons",
            "buttons": [
                "급식",
                "날씨",
                "코인",
                "자유대화"
            ]
        }
    }
EOD;
}

elseif($content=="코인"){
echo <<< EOD
    {
        "message": {
            "text": "코인을 선택해주세요."
        },
        "keyboard": { 
            "type": "buttons",
            "buttons": [
               "비트코인",
               "이더리움",
               "리플",
               "처음으로"
            ]
        }
    }
EOD;
}

elseif($content=="비트코인"){
    $url="https://api.bithumb.com/public/ticker/BTC";
    $json=file_get_contents($url);
    $array=json_decode($json, true);
    $buy_price=$array['data']['buy_price'];
    $fluctate=$array['data']['24H_fluctate'];
    $fluctate_rate=$array['data']['24H_fluctate_rate'];
echo <<< EOD
    {
        "message": {
            "text": "비트코인\\n시세:$buy_price\\n변동가:$fluctate($fluctate_rate)"
        },
        "keyboard": { 
            "type": "buttons",
            "buttons": [
               "급식",
               "날씨",
               "코인",
               "자유대화"
            ]
        }
    }
EOD;
}

elseif($content=="이더리움"){
    $url="https://api.bithumb.com/public/ticker/ETH";
    $json=file_get_contents($url);
    $array=json_decode($json, true);
    $buy_price=$array['data']['buy_price'];
    $fluctate=$array['data']['24H_fluctate'];
    $fluctate_rate=$array['data']['24H_fluctate_rate'];
echo <<< EOD
    {
        "message": {
            "text": "이더리움\\n시세:$buy_price\\n변동가:$fluctate($fluctate_rate)"
        },
        "keyboard": { 
            "type": "buttons",
            "buttons": [
               "급식",
               "날씨",
               "코인",
               "자유대화"
            ]
        }
    }
EOD;
}

elseif($content=="리플"){
    $url="https://api.bithumb.com/public/ticker/XRP";
    $json=file_get_contents($url);
    $array=json_decode($json, true);
    $buy_price=$array['data']['buy_price'];
    $fluctate=$array['data']['24H_fluctate'];
    $fluctate_rate=$array['data']['24H_fluctate_rate'];
echo <<< EOD
    {
        "message": {
            "text": "리플\\n시세:$buy_price\\n변동가:$fluctate($fluctate_rate)"
        },
        "keyboard": { 
            "type": "buttons",
            "buttons": [
               "급식",
               "날씨",
               "코인",
               "자유대화"
            ]
        }
    }
EOD;
}

// 자유대화
if($content == "자유대화"){
echo <<< EOD
    {
        "message": {
            "text": "안녕?\\n(탈출하려면 <처음으로> 입력!)"
        }
    }
EOD;
}

elseif(strpos($content, "마이페이지") !== false){
    $query = "select * from user where user_key='$user_key'";
    $row = mysqli_fetch_assoc($mysqli->query($query));
    if(!$row){
        $sql = "insert into user(user_key) values('$user_key')";
        $mysqli->query($sql);
echo <<< EOD
    {
        "message": {
            "text": "처음 오신 것을 환영합니다!"
        },
        "keyboard": { 
            "type": "buttons",
            "buttons": [
                "급식",
                "날씨",
                "코인",
                "자유대화"
            ]
        }
    }
EOD;
    }else {
echo <<< EOD
    {
        "message": {
            "text": "다시와줘서 고마워요!"
        },
        "keyboard": { 
            "type": "buttons",
            "buttons": [
                "급식",
                "날씨",
                "코인",
                "자유대화"
            ]
        }
    }
EOD;
    }
}

else {
echo <<< EOD
    {
        "message": {
            "text": "개발중인 기능이거나 잘못된 입력입니다ㅠㅠ"
        }, 
        "keyboard": { 
            "type": "buttons",
            "buttons": [
                "급식",
                "날씨",
                "코인",
                "자유대화"
            ]
        }
    }
EOD;
}
?>