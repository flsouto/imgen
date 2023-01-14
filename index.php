<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
$dirs = glob(__DIR__."/*/*[0-9]x[0-9]*/");

foreach($dirs as &$dir){
    $dir = str_replace(__DIR__."/","",$dir);
}

$d = $_REQUEST['d'] ?? '';
$is_tmp = strstr($d,'tmp/');
?>

<?php if(!$d) : ?>
    <b>Select a directory:</b><br/>
    <?php foreach($dirs as $d) : ?>
        - <a href="?d=<?php echo $d; ?>"><?php echo $d ?></a><br/>
    <?php endforeach; ?>
<?php else : ?>
    <?php
        $imgs = glob($d."*.jpg");
        preg_match("/(\d+)x(\d+)/",$d, $m);
        $dims = $m[0];
        $i = $_REQUEST['i'] ?? 0;
        $save_to = $_REQUEST['save_to'] ?? 'db, '.date('Y-m-d');
        $copy_to = $_REQUEST['copy_to'] ?? '';

        switch($_REQUEST['a']??''){
            case 'forward' :
                $i++;
                if(!isset($imgs[$i])){
                    $i = 0;
                }
            break;
            case 'back' :
                $i--;
                if($i < 0){
                    $i = count($imgs)-1;
                }
            break;
            case 'copy' :
                foreach(explode(",", $copy_to) as $to_dir){
                    $to_dir = trim($to_dir);
                    $to_dir = __DIR__."/$to_dir/$dims/";
                    if(!is_dir($to_dir)){
                        mkdir($to_dir,0777,true);
                    }
                    copy($imgs[$i], $to_dir.basename($imgs[$i]));
                }
            break;
            case 'save' :
                foreach(explode(",", $save_to) as $to_dir){
                    $to_dir = trim($to_dir);
                    $to_dir = __DIR__."/$to_dir/$dims/";
                    if(!is_dir($to_dir)){
                        mkdir($to_dir,0777,true);
                    }
                    copy($imgs[$i], $to_dir.basename($imgs[$i]));
                }
                unlink($imgs[$i]);
                unset($imgs[$i]);
                $imgs = array_values($imgs);
            break;
            case 'delete' :
                unlink($imgs[$i]);
                unset($imgs[$i]);
                $imgs = array_values($imgs);
            break;
        }
        $img = $imgs[$i];
    ?>
    <a href="?">Home</a> &raquo;
    <b>Browsing <?php echo $d; ?></b>
    <form style="margin:0" action="?" method="GET" id="action-form">
        <?php if($is_tmp) : ?>
            Save to: <input name="save_to" value="<?php echo $save_to; ?>" style="width:120px" 
                onfocus="blocked = true"
                onblur="blocked = false"
            /> &nbsp;
        <?php endif; ?>
        <?php if(!$is_tmp) : ?>
            Copy to: <input name="copy_to" value="<?php echo $copy_to; ?>" style="width:120px" 
                onfocus="blocked = true"
                onblur="blocked = false"
             />
        <?php endif; ?>
        <input type=hidden name=a value="" />
        <input type=hidden name=i value=<?php echo $i ?> />
        <input type=hidden name=d value=<?php echo $d ?> />
    </form>
    <?php echo $i+1; ?> of <?php echo count($imgs); ?>
    &nbsp;
    <?php if($is_tmp) : ?>[s]ave<?php endif; ?>
    [d]elete <?php if(!$is_tmp) : ?>
    [c]opy <?php endif; ?>
    [f]orward
    [b]ackward
    <br/>
    <input value="<?php echo __DIR__."/".$img; ?>" onfocus="this.select()" style="border:none;width:700px;" /><br/>
    <img src="<?php echo $img ?>" style="width:480px" />
    <script>
        window.blocked = false;
        document.onkeydown = e => {
            if(blocked) return;
            const code2action = {
                <?php if($is_tmp) : ?>
                83 : 'save',
                <?php endif; ?>
                68 : 'delete',
                <?php if(!$is_tmp) : ?>
                67 : 'copy',
                <?php endif; ?>
                70 : 'forward',
                66 : 'back'
            }
            if(code2action[e.keyCode]){
                const form = document.getElementById('action-form')
                form.a.value = code2action[e.keyCode]
                form.submit()
            }
        }
    </script>
<?php endif; ?>
