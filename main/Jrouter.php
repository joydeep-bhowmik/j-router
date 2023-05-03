<?php

#
#
# J router
# https://github.com/joydeep-bhowmik/j-router
#
# (c) Joydeep Bhowmik
# https://github.com/joydeep-bhowmik/j-router/blob/main/LICENSE
#
# For the full license information, view the LICENSE file that was distributed
# with this source code.
#
#
class Route{
    public static $params=[];
    public static $queryParams=[];
    public static $base="";
    public static $data=[];
    protected static $routes=[];
    protected static $staticRoutes=[];
    public static function readyQueryParams(){
        parse_str(file_get_contents("php://input"),self::$queryParams);
        self::$queryParams=array_merge(self::$queryParams,$_POST,$_GET); 
    }
    public static function render($name,$content=null){
        if(isset($content)){
            route::$data[$name]=$content;
        }else{
            if(isset(route::$data[$name])) return route::$data[$name];
        }
    }
    public static function match($url,$pathname=null){
        $pathname=strtok($_SERVER["REQUEST_URI"], '?');
        $params = self::setparam($url, $pathname);
            if ($url == $pathname) {
                return true;
    
            } else if (count($params)!= 0) {
                {
                    echo $url.'<br>';
                 return true; 
                }
            }else{
                return false;
            }
    }
    protected static function similiarArr($arr1,$arr2){
        if(count(array_filter($arr1,'strlen'))==count(array_filter($arr2,'strlen'))){
            return true;
        }
        return false;
    }
    protected static function setparam ($drl, $url) {
        
        $r = [];
        $keys=explode("/",$drl);
        $values=explode("/",$url);
        // echo '<pre>';
        // print_r($keys);
        $pair=[];
        //if the url is a static url
        if(in_array($url, self::$staticRoutes)) return $pair;
        $match=false;
        if(self::similiarArr($keys,$values)){
            for($i=0;$i<count($keys);$i++){
                // if key and values are not equal,then they must be dynamic values
                if($keys[$i]!=$values[$i]){
                    if(preg_match('/{|}/',$keys[$i])) //if dynamic values
                    { $pair[preg_replace('/{|}/',"",$keys[$i])]=$values[$i];  
                        
                    }else{
                        //if not dynamic values
                        return $pair;
                    }
                }
                //echo $match;
            }
        }
        //print_r($pair);
        return $pair;
    }
    public static function for(array $methods,string $path,$callback){
        foreach($methods as $method){
            switch (strtolower($method)) {
                case 'post':
                    route::post($path,$callback);
                    break;
                case 'get':
                    route::get($path,$callback);
                    break;
                case 'put':
                    route::put($path,$callback);
                    break;
                case 'delete':
                    route::delete($path,$callback);
                    break;
                default:
                    route::any($path,$callback);
                    break;
            }
        }
    }
    public static function any(string $url,$callback ){
        $pathname=strtok($_SERVER["REQUEST_URI"], '?');
        $url=self::$base.$url;
        array_push(self::$routes,$url);
        //static routes
        if(!preg_match('/{|}/',$url)) {
            array_push(self::$staticRoutes,$url);
        }
        //404
        if($url==self::$base."*"){
            $routes = self::$routes;
            $results = [];
                for($i=0;$i<count($routes);$i++){
                    array_push($results,self::match($routes[$i]));
                }
                $results=array_unique($results);
                if(!in_array(true, $results)){
                    $callback();
                }
                return;
        }else{
        //normal
        $params=self::setparam($url,$pathname);

        if(self::match($url)){
            if(gettype($callback)=='string'){
                include($callback);
            }else{
                $callback($params);
                Route::$params=$params;
            }
        }
        }
    }
    public static function post(string $url,$callback){
        if($_SERVER['REQUEST_METHOD']=='POST'){
           self::any($url,$callback);
        }
    }
    public static function get(string $url,$callback){
        if($_SERVER['REQUEST_METHOD']=='GET'){
            self::any($url,$callback);
        }
    }
    public static function put(string $url,$callback){
        if($_SERVER['REQUEST_METHOD']=='PUT'){
            self::any($url,$callback);
        }
    }
    public static function delete(string $url,$callback){
        if($_SERVER['REQUEST_METHOD']=='DELETE'){
            self::any($url,$callback);
        }
    }
    public static function request($agrs){
        $url = $agrs['url'];
        $ch = curl_init();
        $data="";
        $success=$agrs['success'];
        $fail=$agrs['fail'];
        if(isset($agrs['data'])){
            $data=http_build_query($agrs['data']);
        }
        // Set query data here with the URL
        curl_setopt($ch, CURLOPT_URL, $url."?".$data); 
        if(isset($agrs['method']) and $agrs['method']=="POST"){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $content = trim(curl_exec($ch));
        if(curl_errno($ch)){
            $fail('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);
        $success($content);
    }
}
Route::readyQueryParams();
function g($str){
    return $GLOBALS[$str];
}
?>
