<?php
/**
 * Created by PhpStorm.
 * User: wujunyuan
 * Date: 2017/8/18
 * Time: 9:56
 */

define("LOCK_FILE_PATH", "/tmp/lock");
if (!file_exists(LOCK_FILE_PATH)) {
    $fp = fopen(LOCK_FILE_PATH, "w");
    fclose($fp);
}

$fp = fopen(LOCK_FILE_PATH, "r");
if (!$fp) {
    $this->error('锁住了');
    return false;
}
flock($fp, LOCK_EX);




flock($fp, LOCK_UN);
fclose($fp);