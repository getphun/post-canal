<?php
/**
 * Static page robot provider
 * @package post-canal
 * @version 1.0.0
 */

namespace PostCanal\Controller;
use PostCanal\Library\Robot;
use PostCanal\Model\PostCanal as PCanal;

class RobotController extends \SiteController
{
    public function feedAction(){
        if(!module_exists('robot'))
            return $this->show404();
        
        $feed_host   = $this->setting->post_canal_index_enable ? 'sitePostCanal' : 'siteHome';
        
        $feed = (object)[
            'url'         => $this->router->to('sitePostCanalFeed'),
            'description' => hs($this->setting->post_canal_index_meta_description),
            'updated'     => null,
            'host'        => $this->router->to($feed_host),
            'title'       => hs($this->setting->post_canal_index_meta_title)
        ];
        
        $pages = Robot::feed();
        $this->robot->feed($feed, $pages);
    }
    
    public function feedSingleAction(){
        if(!module_exists('robot'))
            return $this->show404();
        
        $slug = $this->param->slug;
        
        $canal = PCanal::get(['slug'=>$slug], false);
        if(!$canal)
            return $this->show404();
        
        $canal = \Formatter::format('post-canal', $canal, false);
        
        $feed = (object)[
            'url'         => $this->router->to('sitePostCanalSingleFeed', ['slug'=>$canal->slug]),
            'description' => hs($canal->meta_description->value != '' ? $canal->meta_description : $canal->about),
            'updated'     => null,
            'host'        => $canal->page,
            'title'       => hs($canal->name)
        ];
        
        $pages = Robot::feedPost($canal);
        $this->robot->feed($feed, $pages);
    }
}