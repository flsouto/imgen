<?php
$config = require(__DIR__."/config.php");
require_once($config['imagick_path']);
use FlSouto\Imagick;

if(empty($argv[1])){
    die("usage: cmd <widthxheight>\n");
}

$dims = explode('x',$argv[1]);
$dims_str = $argv[1];

$amount = $argv[2] ?? 100;

function gen(){

    global $dims,$dims_str;

    $src = __DIR__."/db/";
    $img = Imagick::select($src."*/"."*.jpg");
    $final_w = $dims[0];
    $final_h = $dims[1];

    $pick_w = $final_w / 2;
    $pick_h = $final_h / 2;

    if(rand(0,1)){
        $a = $img->pick($pick_w,$pick_h);
    } else {
        $a = $img->pick($pick_h,$pick_w);
        $a->rotate(rand(0,1) ? 90 : 270);
    }
    if(rand(0,1)){
        $b = $a()->flip(rand(0,1));
    } else {
        $b = $a()->flop(rand(0,1));
    }
    $top = $a->add($b);
    $bottom = $top()->rotate(180);
    $top->add($bottom, 1);
    $top->colorize('rgb('.implode(',',[rand(0,100),rand(0,100),rand(0,100)]).')', 80);
    return $top;
}

$out_dir = __DIR__."/tmp/".$dims_str."/";
if(!is_dir($out_dir)){
    mkdir($out_dir, 0777, true);
}

for($i=1;$i<=$amount;$i++){

    echo "Generating $i of $amount...\n";
    $a = gen();
    if(rand(0,1)){
        $b = gen();
        $a->mix($b,'blend');
    }

    $j=1;
    while($a->colorspace()=='Gray'){
        $a->colorize('rand',rand(50,100));
        $j++;
        if($i>10){
            continue 2;
        }
    }

    $a->sfactor('2x2,1x1,1x1');
    if(implode('x',$a->size()) != $dims_str){
        die('dimensions failed');
    }
    $hash = $a->hash();
    $img = "$out_dir/$hash.jpg";
    echo "Saving $img\n";
    $a->save($img);
}
