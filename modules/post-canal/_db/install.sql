CREATE TABLE IF NOT EXISTS `post_canal` (
    `id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user` INTEGER NOT NULL,
    `name` VARCHAR(50) NOT NULL,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `about` TEXT,
    
    `meta_title` VARCHAR(100),
    `meta_description` TEXT,
    `meta_keywords` VARCHAR(200),
    
    `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO `site_param` ( `name`, `type`, `group`, `value` ) VALUES
    ( 'post_canal_index_enable', 4, 'Post Canal', '0' ),
    ( 'post_canal_index_meta_title', 1, 'Post Canal', 'Post Canals' ),
    ( 'post_canal_index_meta_description',  5, 'Post Canal', 'List of post canals' ),
    ( 'post_canal_index_meta_keywords', 1, 'Post Canal', '' );