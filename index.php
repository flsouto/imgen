<?php

$dirs = glob(__DIR__."/*/*[0-9]x[0-9]*/");

foreach($dirs as &$dir){
    $dir = str_replace(__DIR__."/","",$dir);
}

$d = $_REQUEST['d'] ?? null;

?>

<?php if(!$d) : ?>
    <b>Select a directory:</b><br/>
    <?php foreach($dirs as $d) : ?>
        - <a href="?d=<?php echo $d; ?>"><?php echo $d ?></a><br/>
    <?php endforeach; ?>
<?php else : ?>
    <?php
        $imgs = glob($d."*.jpg");
        $i = $_REQUEST['i'] ?? 0;
        $img = $imgs[$i];
        $save_to = $_REQUEST['save_to'] ?? 'db, new';
    ?>
    <a href="?">Home</a> &raquo;
    <b>Browsing <?php echo $d; ?></b><br/>
    Save to: <input value="<?php echo $save_to ?>" style="width:120px" /> &nbsp; Copy to: <input style="width:120px;" />
    <br/>
    <?php echo $i+1; ?> of <?php echo count($imgs); ?>
    &nbsp; [s]ave [d]iscard [c]opy [f]orward [b]ackward
    <br/>
    <input value="<?php echo __DIR__."/".$img; ?>" onfocus="this.select()" style="border:none;width:700px;" /><br/>
    <img src="<?php echo $img ?>" style="width:480px" />
<?php endif; ?>
