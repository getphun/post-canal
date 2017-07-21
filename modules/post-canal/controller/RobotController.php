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
    private function feed($type='xml'){
        if(!module_exists('robot'))
            return $this->show404();
        
        if($type === 'json' && !$this->config->robot['json'])
            return $this->show404();
        
        $feed_router = $type === 'xml' ? 'sitePostCanalFeedXML' : 'sitePostCanalFeedJSON';
        $feed_host   = $this->setting->post_canal_index_enable ? 'sitePostCanal' : 'siteHome';
        
        $feed = (object)[
            'url'         => $this->router->to($feed_router),
            'description' => hs($this->setting->post_canal_index_meta_description),
            'updated'     => null,
            'host'        => $this->router->to($feed_host),
            'title'       => hs($this->setting->post_canal_index_meta_title)
        ];
        
        $pages = Robot::feed();
        $this->robot->feed($feed, $pages, $type);
    }
    
    private function feedSingle($slug, $type='xml'){
        if(!module_exists('robot'))
            return $this->show404();
        
        if($type === 'json' && !$this->config->robot['json'])
            return $this->show404();
        
        $canal = PCanal::get(['slug'=>$slug], false);
        if(!$canal)
            return $this->show404();
        
        $canal = \Formatter::format('post-canal', $canal, false);
        
        $feed_router = $type === 'xml' ? 'sitePostCanalSingleFeedXML' : 'sitePostCanalSingleFeedJSON';
        
        $feed = (object)[
            'url'         => $this->router->to($feed_router, ['slug'=>$canal->slug]),
            'description' => hs($canal->meta_description->value != '' ? $canal->meta_description : $canal->about),
            'updated'     => null,
            'host'        => $canal->page,
            'title'       => hs($canal->name)
        ];
        
        $pages = Robot::feedPost($canal);
        $this->robot->feed($feed, $pages, $type);
    }
    
    public function feedXmlAction(){
        $this->feed('xml');
    }
    
    public function feedJsonAction(){
        $this->feed('json');
    }
    
    public function feedSingleXmlAction(){
        $this->feedSingle($this->param->slug, 'xml');
    }
    
    public function feedSingleJsonAction(){
        $this->feedSingle($this->param->slug, 'json');
    }
}