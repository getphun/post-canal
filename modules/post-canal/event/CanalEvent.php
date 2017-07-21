<?php
/**
 * Post canal events
 * @package post-canal
 * @version 0.0.1
 * @upgrade false
 */

namespace PostCanal\Event;

class CanalEvent{
    
    static function general($object, $old=null){
        $dis = \Phun::$dispatcher;
        $page = $dis->router->to('sitePostCanalSingle', ['slug'=>$object->slug]);
        $dis->cache->removeOutput($page);
    }
    
    static function created($object){
        self::general($object);
    }
    
    static function updated($object, $old=null){
        self::general($object, $old);
    }
    
    static function deleted($object){
        self::general($object);
    }
}