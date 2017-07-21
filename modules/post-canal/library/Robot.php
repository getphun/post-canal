<?php
/**
 * Robot provider
 * @package post-canal
 * @version 0.0.1
 * @upgrade true
 */

namespace PostCanal\Library;
use PostCanal\Model\PostCanal as PCanal;
use Post\Model\Post;

class Robot
{
    static function _getCanals(){
        // get all pages that is updated last 2 days
        $last2days = date('Y-m-d H:i:s', strtotime('-2 days'));
        
        $canals = PCanal::get([
            'updated >= :updated',
            'bind' => [
                'updated' => $last2days
            ]
        ], true);
        
        if(!$canals)
            return false;
        
        return \Formatter::formatMany('post-canal', $canals, false, ['user']);
    }
    
    static function feed(){
        $result = [];
        
        $canals = self::_getCanals();
        
        if(!$canals)
            return $result;
        
        foreach($canals as $canal){
            $desc = $canal->meta_description->safe;
            if(!$desc)
                $desc = $canal->about->chars(160);
            
            $result[] = (object)[
                'author'      => hs($canal->user->fullname),
                'description' => $desc,
                'page'        => $canal->page,
                'published'   => $canal->created->format('c'),
                'updated'     => $canal->updated->format('c'),
                'title'       => $canal->name->safe
            ];
        }
        
        return $result;
    }
    
    static function feedPost($canal){
        $result = [];
        
        $last2days = date('Y-m-d H:i:s', strtotime('-2 days'));
        
        $posts = Post::getX([
            'canal'    => $canal->id,
            'status'   => 4,
            'updated'  => ['__op', '>=', $last2days]
        ]);
        
        if(!$posts)
            return $result;
        
        $posts = \Formatter::formatMany('post', $posts, false, ['content', 'user', 'category']);
        
        foreach($posts as $post){
            $desc = $post->meta_description->safe;
            if(!$desc)
                $desc = $post->content->chars(160);
            
            $row = (object)[
                'author'      => hs($post->user->fullname),
                'description' => $desc,
                'page'        => $post->page,
                'published'   => $post->created->format('r'),
                'updated'     => $post->updated->format('c'),
                'title'       => $post->title->safe
            ];
            
            if($post->category){
                $row->categories = [];
                foreach($post->category as $cat)
                    $row->categories[] = $cat->name->safe;
            }
            
            $result[] = $row;
        }
        
        return $result;
    }
    
    static function sitemap(){
        $result = [];
        
        $canals = self::_getCanals();
        
        if(!$canals)
            return $result;
        
        $last_update = null;
        foreach($canals as $canal){
            $result[] = (object)[
                'url'       => $canal->page,
                'lastmod'   => $canal->updated->format('Y-m-d'),
                'changefreq'=> 'daily',
                'priority'  => 0.4
            ];
            
            if(is_null($last_update))
                $last_update = $canal->updated;
            elseif($last_update < $canal->updated)
                $last_update = $canal->updated;
        }
        
        $dis = \Phun::$dispatcher;
        if($dis->setting->post_canal_index_enable){
            $result[] = (object)[
                'url'       => $dis->router->to('sitePostCanal'),
                'lastmod'   => $last_update->format('Y-m-d'),
                'changefreq'=> 'monthly',
                'priority'  => 0.3
            ];
        }
        
        return $result;
    }
}