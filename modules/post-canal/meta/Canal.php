<?php
/**
 * Meta provider
 * @package post-canal
 * @version 0.0.1
 * @upgrade true
 */

namespace PostCanal\Meta;

class Canal
{
    static function index(){
        $dis = \Phun::$dispatcher;
        
        $page = $dis->req->getQuery('page', 1);
        
        $base_url   = $dis->router->to('siteHome');
        
        $meta_title = $dis->setting->post_canal_index_meta_title;
        $meta_desc  = $dis->setting->post_canal_index_meta_description;
        $meta_keys  = $dis->setting->post_canal_index_meta_keywords;
        $meta_url   = $dis->router->to('sitePostCanal');
        $meta_image = $base_url . 'theme/site/static/logo/500x500.png';
        
        if($page && $page > 1){
            $meta_title = sprintf('Page %s %s', $page, $meta_title);
            $meta_desc  = sprintf('Page %s %s', $page, $meta_desc);
            $meta_url   = $meta_url . '?page=' . $page;
        }
        
        $index = (object)[
            '_schemas' => [],
            '_metas'   => [
                'title'         => $meta_title,
                'canonical'     => $meta_url,
                'description'   => $meta_desc,
                'keywords'      => $meta_keys,
                'image'         => $meta_image,
                'type'          => 'website'
            ]
        ];
        
        // my rss feed?
        if(module_exists('robot'))
            $index->_metas['feed'] = $dis->router->to('sitePostCanalFeed');
        
        // Schema
        $schema = [
            '@context'      => 'http://schema.org',
            '@type'         => 'CollectionPage',
            'name'          => $meta_title,
            'description'   => $meta_desc,
            'publisher'     => $dis->meta->schemaOrganization(),
            'url'           => $meta_url,
            'image'         => $meta_image
        ];
        
        $index->_schemas[] = $schema;
        
        return $index;
    }
    
    static function single($canal){
        $dis = \Phun::$dispatcher;
        
        $base_url = $dis->router->to('siteHome');
        
        $meta_desc  = $canal->meta_description->safe;
        if(!$meta_desc)
            $meta_desc = $canal->about->chars(160);
        $meta_image = $base_url . 'theme/site/static/logo/500x500.png';
        $meta_url   = $canal->page;
        $meta_title = $canal->meta_title->safe;
        $meta_keys  = $canal->meta_keywords;
        if(!$meta_title)
            $meta_title = $canal->name->safe;
        
        $page = $dis->req->getQuery('page', 1);
        if($page && $page > 1){
            $meta_title = sprintf('Page %s %s', $page, $meta_title);
            $meta_desc  = sprintf('Page %s %s', $page, $meta_desc);
            $meta_url   = $meta_url . '?page=' . $page;
        }
        
        // metas
        $single = (object)[
            '_schemas' => [],
            '_metas'   => [
                'title'         => $meta_title,
                'canonical'     => $meta_url,
                'description'   => $meta_desc,
                'keywords'      => $meta_keys,
                'image'         => $meta_image,
                'type'          => 'website'
            ]
        ];
        
        // my rss feed?
        if(module_exists('robot'))
            $single->_metas['feed'] = $dis->router->to('sitePostCanalSingleFeed', ['slug'=>$canal->slug]);
        
        // schemas 
        $schema = [
            '@context'      => 'http://schema.org',
            '@type'         => 'CollectionPage',
            'name'          => $canal->name,
            'description'   => $meta_desc,
            'publisher'     => $dis->meta->schemaOrganization(),
            'url'           => $meta_url,
            'image'         => $meta_image
        ];
        $single->_schemas[] = $schema;
        
        // schema breadcrumbs
        $second_item = [
            '@type' => 'ListItem',
            'position' => 2,
            'item' => [
                '@id' => $base_url . '#post',
                'name' => 'Post'
            ]
        ];
        if(module_exists('post'))
            $second_item['item']['name'] = $dis->setting->post_index_meta_title;
        
        if($dis->setting->post_canal_index_enable){
            $second_item = [
                '@type' => 'ListItem',
                'position' => 2,
                'item' => [
                    '@id' => $dis->router->to('sitePostCanal'),
                    'name' => $dis->setting->post_canal_index_meta_title
                ]
            ];
        }
        
        $schema = [
            '@context'  => 'http://schema.org',
            '@type'     => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'item' => [
                        '@id' => $base_url,
                        'name' => $dis->config->name
                    ]
                ],
                $second_item
            ]
        ];
        
        $single->_schemas[] = $schema;
        
        return $single;
    }
}