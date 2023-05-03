<?php
require('router.php');
$data=['method'=>"",'text'=>''];
Route::$base='/php-router';
Route::get('/home','includable.php');
Route::post('/post',function(){
    $GLOBALS['data']['method']='post';
    $GLOBALS['data']['text']=$_POST['text'];
});
if(isset($data) and !empty($data['text'])){
    echo json_encode($data);
}
?>
