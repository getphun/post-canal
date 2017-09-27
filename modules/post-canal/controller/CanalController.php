<?php
/**
 * Canal controller
 * @package post-canal
 * @version 0.0.1
 * @upgrade false
 */

namespace PostCanal\Controller;
use PostCanal\Meta\Canal;
use PostCanal\Model\PostCanal as PCanal;
use PostCanal\Model\PostCanalChain as PTChain;
use Post\Model\Post;

class CanalController extends \SiteController
{
    public function indexAction(){
        // serve only if it's allowed to be served
        if(!$this->setting->post_canal_index_enable)
            return $this->show404();
        
        $page = $this->req->getQuery('page', 1);
        $rpp  = 12;
        
        $cache= 60*60*24*7;
        if($page > 1 || is_dev())
            $cache = null;
        
        $canals = PCanal::get([], $rpp, $page, 'created DESC');
        if(!$canals)
            return $this->show404();
        
        $canals = \Formatter::formatMany('post-canal', $canals, false, ['user']);
        $params = [
            'canals' => $canals,
            'index' => new \stdClass(),
            'pagination' => [],
            'total' => PCanal::count()
        ];
        
        $params['index']->meta = Canal::index();
        
        // pagination
        if($params['total'] > $rpp)
            $params['pagination'] = calculate_pagination($page, $rpp, $params['total']);
        
        $this->respond('post/canal/index', $params, $cache);
    }
    
    public function singleAction(){
        $slug = $this->param->slug;
        
        $canal = PCanal::get(['slug'=>$slug], false);
        if(!$canal){
            if(module_exists('slug-history'))
                $this->slug->goto('post-canal', $slug, 'sitePostCanalSingle');
            return $this->show404();
        }
            
        $page = $this->req->getQuery('page', 1);
        $rpp = 12;
        
        $cache = 60*60*24*7;
        if($page > 1 || is_dev())
            $cache = null;
        
        $canal = \Formatter::format('post-canal', $canal, false, ['user']);
        $params = [
            'canal' => $canal,
            'posts' => [],
            'pagination' => [],
            'total' => Post::countX(['canal'=>$canal->id, 'status'=>4])
        ];
        
        // pagination
        if($params['total'] > $rpp)
            $params['pagination'] = calculate_pagination($page, $rpp, $params['total']);
        
        $posts = Post::getX(['canal'=>$canal->id, 'status'=>4], $rpp, $page, 'created DESC');
        if($posts)
            $params['posts'] = \Formatter::formatMany('post', $posts, false, false);
        
        $params['canal']->meta = Canal::single($canal);
        
        $this->respond('post/canal/single', $params, $cache);
    }
}