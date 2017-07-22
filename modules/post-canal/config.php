<?php
/**
 * post-canal config file
 * @package post-canal
 * @version 0.0.1
 * @upgrade true
 */

return [
    '__name' => 'post-canal',
    '__version' => '0.0.1',
    '__git' => 'https://github.com/getphun/post-canal',
    '__files' => [
        'modules/post-canal/config.php' => [
            'install',
            'remove',
            'update'
        ],
        'modules/post-canal/_db' => [
            'install',
            'remove',
            'update'
        ],
        'modules/post-canal/model' => [
            'install',
            'remove',
            'update'
        ],
        'modules/post-canal/library' => [
            'install',
            'remove',
            'update'
        ],
        'modules/post-canal/meta' => [
            'install',
            'remove',
            'update'
        ],
        'modules/post-canal/controller/RobotController.php' => [
            'install',
            'remove',
            'update'
        ],
        'modules/post-canal/controller/CanalController.php' => [
            'install',
            'remove'
        ],
        'modules/post-canal/event' => [
            'install',
            'remove'
        ],
        'theme/site/post/canal' => [
            'install',
            'remove'
        ]
    ],
    '__dependencies' => [
        'post',
        'formatter',
        'site',
        'site-meta',
        '/db-mysql',
        '/robot'
    ],
    '_services' => [],
    '_autoload' => [
        'classes' => [
            'PostCanal\\Model\\PostCanal' => 'modules/post-canal/model/PostCanal.php',
            'PostCanal\\Library\\Robot' => 'modules/post-canal/library/Robot.php',
            'PostCanal\\Meta\\Canal' => 'modules/post-canal/meta/Canal.php',
            'PostCanal\\Controller\\RobotController' => 'modules/post-canal/controller/RobotController.php',
            'PostCanal\\Controller\\CanalController' => 'modules/post-canal/controller/CanalController.php',
            'PostCanal\\Event\\CanalEvent' => 'modules/post-canal/event/CanalEvent.php'
        ],
        'files' => []
    ],
    '_routes' => [
        'site' => [
            'sitePostCanalFeedXML' => [
                'rule' => '/post/canal/feed.xml',
                'handler' => 'PostCanal\\Controller\\Robot::feedXml'
            ],
            'sitePostCanalFeedJSON' => [
                'rule' => '/post/canal/feed.json',
                'handler' => 'PostCanal\\Controller\\Robot::feedJson'
            ],
            'sitePostCanal' => [
                'rule' => '/post/canal',
                'handler' => 'PostCanal\\Controller\\Canal::index'
            ],
            'sitePostCanalSingleFeedXML' => [
                'rule' => '/post/canal/:slug/feed.xml',
                'handler' => 'PostCanal\\Controller\\Robot::feedSingleXml'
            ],
            'sitePostCanalSingleFeedJSON' => [
                'rule' => '/post/canal/:slug/feed.json',
                'handler' => 'PostCanal\\Controller\\Robot::feedSingleJson'
            ],
            'sitePostCanalSingle' => [
                'rule' => '/post/canal/:slug',
                'handler' => 'PostCanal\\Controller\\Canal::single'
            ]
        ]
    ],
    'events' => [
        'post-canal:created' => [
            'post-canal' => 'PostCanal\\Event\\CanalEvent::created'
        ],
        'post-canal:updated' => [
            'post-canal' => 'PostCanal\\Event\\CanalEvent::updated'
        ],
        'post-canal:deleted' => [
            'post-canal' => 'PostCanal\\Event\\CanalEvent::deleted'
        ]
    ],
    'formatter' => [
        'post-canal' => [
            'name' => 'text',
            'about' => 'text',
            'updated' => 'date',
            'created' => 'date',
            'user' => [
                'type' => 'object',
                'model' => 'User\\Model\\User'
            ],
            'page' => [
                'type' => 'router',
                'params' => [
                    'for' => 'sitePostCanalSingle'
                ]
            ],
            'meta_title' => 'text',
            'meta_description' => 'text'
        ],
        'post-category' => [
            'canal' => [
                'type' => 'object',
                'model' => 'PostCanal\\Model\\PostCanal',
                'format' => 'post-canal'
            ]
        ],
        'post' => [
            'canal' => [
                'type' => 'object',
                'model' => 'PostCanal\\Model\\PostCanal',
                'format' => 'post-canal'
            ]
        ]
    ],
    'robot' => [
        'sitemap' => [
            'post-canal' => 'PostCanal\\Library\\Robot::sitemap'
        ],
        'feed' => [
            'post-canal' => 'PostCanal\\Library\\Robot::feed'
        ]
    ]
];