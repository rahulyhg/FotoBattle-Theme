<?php
/**
 * Route
 * 
 * Resister VotingImages
 * 
 * 
 * @package JobstairsVoting
 * @subpackage Voting_App
 * @since 1.0.0
 */

/**
 * Resister Scores API's
 */
function votingRegisterScores() {
    register_rest_route('voting', 'score', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'votingScoreResults'
    ));
    register_rest_route('voting', 'score/(?P<score>[0-9-]+)', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'votingHighScoreResultsPaging',
        'args' => array(
            'score' => array( 
                'validate_callback' => function( $param, $request, $key ) {
                    return sanitize_text_field( $param );
                }
            )
        )
    ));
    register_rest_route('voting', 'share/(?P<share>[a-zA-Z0-9-]+)', array(
      'methods' => WP_REST_SERVER::READABLE,
      'callback' => 'votingGetSharedImage',
      'args' => array(
          'share' => array( 
              'validate_callback' => function( $param, $request, $key ) {
                  return sanitize_text_field( $param );
              }
          )
      )
    ));
    register_rest_route('voting', 'like/(?P<like>[a-zA-Z0-9-]+)', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'votingGetLikedImage',
        'args' => array(
            'like' => array( 
                'validate_callback' => function( $param, $request, $key ) {
                    return sanitize_text_field( $param );
                }
            )
        )
    ));
    register_rest_route('voting', 'manage', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'votingCreateLike'
    ));
}
add_action('rest_api_init', 'votingRegisterScores');

/**
 * Voting Score Results
 */
function votingScoreResults($data) {
    $ipaddress = $_SERVER['REMOTE_ADDR'];
    $scoreQuery = New WP_Query(array(
        'posts_per_page' => 10,
        'post_status' => 'publish',
        'post_type' => array('image'),
        'orderby' => 'title',
        'order' => 'DESC'
    ));
    $scores = array(
        'items' => array()
    );
    while($scoreQuery->have_posts()) {
        $scoreQuery->the_post(); 
        if (get_post_type() == 'image') {
            $id = get_the_ID();
            $countdown = get_count_down();
            $timestamp = time();
            $day = 60; // 86400
            $slug = get_post_field('post_name', $id);
            $image = 'https://fotobattle.roberteberhard.com/images/' . $slug . '.jpg';
            /**
             * Retrieve likes
             */
            $likeCounts = new WP_Query(array(
                'post_type' => array('like'),
                'meta_query' => array(
                    array(
                        'key' => 'liked_image_id',
                        'compare' => '=',
                        'value' => $id
                    )
                )
            ));
            $likes = $likeCounts->found_posts;
            /**
             * Retrieve actives
             */
            $activeCounts = new WP_Query(array(
                'post_type' => array('like'),
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'liked_image_id',
                        'compare' => '=',
                        'value' => $id
                    ),
                    array(
                        'key' => 'liked_image_timestamp',
                        'compare' => '>=',
                        'value' => $timestamp - $day,
                        'type' => 'NUMERIC'
                    ),
                    array(
                        'key' => 'liked_client_ip',
                        'compare' => '=',
                        'value' => $ipaddress
                    )
                )
            ));
            $active = $activeCounts->found_posts; // Is image restricted for 24 hours (active = yes)
            /**
             * Retrieve clicks
             */
            $clickCounts = new WP_Query(array(
                'post_type' => array('like'),
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'liked_image_timestamp',
                        'compare' => '>=',
                        'value' => $timestamp - $day, // CHANGE TO DAY
                        'type' => 'NUMERIC'
                    ),
                    array(
                        'key' => 'liked_client_ip',
                        'compare' => '=',
                        'value' => $ipaddress
                    )
                )
            ));
            $clicks = $clickCounts->found_posts; // Clicks in the last 24 hours (limit clicks after 10)
            $start = $paged;
            $end = $scoreQuery->max_num_pages;
            $start = 2;
            $end = $scoreQuery->max_num_pages;
            /**
             * Build data array
             */
            array_push($scores['items'], array(
                'id' => $id,
                'timestamp' => $timestamp,
                'countdown' => $countdown,
                'image' => $image,
                'likes' => (int)$likes,
                'selected' => 0,
                'active' => (int)$active,
                'clicks' => (int)$clicks,
                'start'=> $start,
                'end'=> $end
            ));
        }
    }
    wp_reset_postdata();
    return rest_ensure_response($scores);
}


/**
 * Voting HighScore Results
 */
function votingHighScoreResultsPaging($data) {
    $paged = (int)$data['score'];
    $ipaddress = $_SERVER['REMOTE_ADDR'];
    $scoreQuery = New WP_Query(array(
        'paged' => $paged,
        'posts_per_page' => 10,
        'post_type' => array('image'),
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'DESC'
    ));
    $scores = array(
        'items' => array()
    );
    while($scoreQuery->have_posts()) {
        $scoreQuery->the_post(); 
        if (get_post_type() == 'image') {
            $id = get_the_ID();
            $countdown = get_count_down();
            $timestamp = time();
            $day = 60; // 86400
            $slug = get_post_field('post_name', $id);
            $image = 'https://fotobattle.roberteberhard.com/images/' . $slug . '.jpg';
            /**
             * Retrieve likes
             */
            $likeCounts = new WP_Query(array(
                'post_type' => array('like'),
                'meta_query' => array(
                    array(
                        'key' => 'liked_image_id',
                        'compare' => '=',
                        'value' => $id
                    )
                )
            ));
            $likes = $likeCounts->found_posts;
            /**
             * Retrieve actives
             */
            $activeCounts = new WP_Query(array(
                'post_type' => array('like'),
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'liked_image_id',
                        'compare' => '=',
                        'value' => $id
                    ),
                    array(
                        'key' => 'liked_image_timestamp',
                        'compare' => '>=',
                        'value' => $timestamp - $day,
                        'type' => 'NUMERIC'
                    ),
                    array(
                        'key' => 'liked_client_ip',
                        'compare' => '=',
                        'value' => $ipaddress
                    )
                )
            ));
            $active = $activeCounts->found_posts; // Is image restricted for 24 hours (active = yes)
            /**
             * Retrieve clicks
             */
            $clickCounts = new WP_Query(array(
                'post_type' => array('like'),
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'liked_image_timestamp',
                        'compare' => '>=',
                        'value' => $timestamp - $day,
                        'type' => 'NUMERIC'
                    ),
                    array(
                        'key' => 'liked_client_ip',
                        'compare' => '=',
                        'value' => $ipaddress
                    )
                )
            ));
            $clicks = $clickCounts->found_posts; // Clicks in the last 24 hours (limit clicks after 10)
            $start = $paged;
            $end = $scoreQuery->max_num_pages;
            /**
             * Build data array
             */
            array_push($scores['items'], array(
                'id' => $id,
                'timestamp' => $timestamp,
                'countdown' => $countdown,
                'image' => $image,
                'likes' => (int)$likes,
                'selected' => 0,
                'active' => (int)$active,
                'clicks' => (int)$clicks,
                'start'=> $start,
                'end'=> $end
            ));
        }
    }
    wp_reset_postdata();
    return rest_ensure_response($scores);
}

/**
 * Voting Get Shared image
 */
function votingGetSharedImage($data) {
    global $post;
    $items = [];
    $ipaddress = $_SERVER['REMOTE_ADDR'];
    if(isset($data['share'])) {
        $imageQuery = New WP_Query(array(
            'post_status' => 'publish',
            'post_type' => array('image'),
            'post__in' => array($data['share']),
            'order_by' => 'post__in'
        ));
        while($imageQuery->have_posts()){
            $imageQuery->the_post();
            if (get_post_type() == 'image') {
                $id = get_the_ID();
                $countdown = get_count_down();
                $timestamp = time();
                $day = 60; // 86400
                $slug = get_post_field('post_name', $id);
                $image = 'https://fotobattle.roberteberhard.com/images/' . $slug . '.jpg';
                /**
                 * Retrieve likes
                 */
                $likeCounts = new WP_Query(array(
                    'post_type' => array('like'),
                    'meta_query' => array(
                        array(
                            'key' => 'liked_image_id',
                            'compare' => '=',
                            'value' => $id
                        )
                    )
                ));
                $likes = $likeCounts->found_posts;
                /**
                 * Retrieve actives
                 */  
                $activeCounts = new WP_Query(array(
                    'post_type' => array('like'),
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'liked_image_id',
                            'compare' => '=',
                            'value' => $id
                        ),
                        array(
                            'key' => 'liked_image_timestamp',
                            'compare' => '>=',
                            'value' => $timestamp - $day,
                            'type' => 'NUMERIC'
                        ),
                        array(
                            'key' => 'liked_client_ip',
                            'compare' => '=',
                            'value' => $ipaddress
                        )
                    )
                ));
                $active = $activeCounts->found_posts; // Is image restricted for 24 hours (active = yes)
                /**
                 * Retrieve clicks
                 */               
                $clickCounts = new WP_Query(array(
                    'post_type' => array('like'),
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'liked_image_timestamp',
                            'compare' => '>=',
                            'value' => $timestamp - $day,
                            'type' => 'NUMERIC'
                        ),
                        array(
                            'key' => 'liked_client_ip',
                            'compare' => '=',
                            'value' => $ipaddress
                        )
                    )
                ));
                $clicks = $clickCounts->found_posts; // Clicks in the last 24 hours (limit clicks after 10)
                /**
                 * Build data array
                 */
                array_push($items, array(
                    'id' => $id,
                    'timestamp' => $timestamp,
                    'countdown' => $countdown,
                    'image' => $image,
                    'likes' => (int)$likes,
                    'active' => (int)$active,
                    'clicks' => (int)$clicks
                ));
            }
        };
        wp_reset_postdata();
        return rest_ensure_response($items);
    }
    return $items;
}
/**
 * Voting Get Liked image
 */
function votingGetLikedImage($data) {
    global $post;
    $items = [];
    $ipaddress = $_SERVER['REMOTE_ADDR'];
    if(isset($data['like'])) {
        $imageQuery = New WP_Query(array(
            'post_status' => 'publish',
            'post_type' => array('image'),
            'post__in' => array($data['like']),
            'order_by' => 'post__in'
        ));
        while($imageQuery->have_posts()){
            $imageQuery->the_post();
            if (get_post_type() == 'image') {
                $id = get_the_ID();
                $countdown = get_count_down();
                $timestamp = time();
                $day = 60; // 86400
                $slug = get_post_field('post_name', $id);
                $image = 'https://fotobattle.roberteberhard.com/images/' . $slug . '.jpg';
                /**
                 * Retrieve likes
                 */
                $likeCounts = new WP_Query(array(
                    'post_type' => array('like'),
                    'meta_query' => array(
                        array(
                            'key' => 'liked_image_id',
                            'compare' => '=',
                            'value' => $id
                        )
                    )
                ));
                $likes = $likeCounts->found_posts;
                /**
                 * Retrieve actives
                 */  
                $activeCounts = new WP_Query(array(
                    'post_type' => array('like'),
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'liked_image_id',
                            'compare' => '=',
                            'value' => $id
                        ),
                        array(
                            'key' => 'liked_image_timestamp',
                            'compare' => '>=',
                            'value' => $timestamp - $day, // CHANGE TO DAY
                            'type' => 'NUMERIC'
                        ),
                        array(
                            'key' => 'liked_client_ip',
                            'compare' => '=',
                            'value' => $ipaddress
                        )
                    )
                ));
                $active = $activeCounts->found_posts; // Is image restricted for 24 hours (active = yes)
                /**
                 * Retrieve clicks
                 */                
                $clickCounts = new WP_Query(array(
                    'post_type' => array('like'),
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'liked_image_timestamp',
                            'compare' => '>=',
                            'value' => $timestamp - $day, // CHANGE TO DAY
                            'type' => 'NUMERIC'
                        ),
                        array(
                            'key' => 'liked_client_ip',
                            'compare' => '=',
                            'value' => $ipaddress
                        )
                    )
                ));
                $clicks = $clickCounts->found_posts; // Clicks in the last 24 hours (limit clicks after 10)
                /**
                 * Build data array
                 */
                array_push($items, array(
                    'id' => $id,
                    'timestamp' => $timestamp,
                    'countdown' => $countdown,
                    'image' => $image,
                    'likes' => (int)$likes,
                    'active' => (int)$active,
                    'clicks' => (int)$clicks
                ));
            }
        };
        wp_reset_postdata();
        return rest_ensure_response($items);
    }
    return $items;
}

/**
 * Create Voting Like
 * hello@roberteberhard.com
 * xLFw)hCUM@9dw@yr7Z4yst3s
 */
function votingCreateLike($data) {
    $id = sanitize_text_field($data['imageId']);
    $ip = $_SERVER['REMOTE_ADDR'];
    /**
     * Insert like data when the client with the ip addess
     * and timestamp.
     */
    if (get_post_type($id) == 'image') {
        $imageObj = wp_insert_post(array(
            'post_title' => 'Image-' . $id,
            'post_type' => 'like',
            'post_status' => 'publish',
            'meta_input' => array(
                'liked_image_id' => $id,
                'liked_image_timestamp' => time(),
                'liked_client_ip' => $ip
            )
        ));
        return $id; // special: return image id to update instead of like id
    } else {
        die();
    }
}

/**
 * Get Days to Voting Start Date (Count Down)
 */
function get_count_down() {
    $today = time();
    $start_day = mktime(0,0,0,5,8,2019); // 0/0/0/month/day/year
    $diff_days = ($start_day - $today);
    $days = (int)($diff_days/86400);
    $days = $days > 0 ? $days : 0;
    return $days;
}