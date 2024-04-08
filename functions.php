<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


function load_custom_plugin_scripts()
{
    wp_enqueue_style('styles', plugins_url('css/styles.css', __FILE__));
    wp_enqueue_script('scripts', plugins_url('js/script.js', __FILE__), array('jquery'), '1.0', true);
}

add_action('admin_enqueue_scripts', 'load_custom_plugin_scripts');



function getPecents($value, $full)
{
    return number_format($value / $full * 100, 2);
}


function page_stats_admin_page()
{
    ob_start();

    $args = array(
        'post_type' => 'maps',
        'order' => 'DSC',
        'posts_per_page' => -1,
    );
    $all_maps = get_posts($args);

    $all_maps_count = 0;
    $parent_maps_count = 0;
    $single_maps_count = 0;
    $empty_maps_count = 0;
    $full_maps_count = 0;
    $maps_without_category = 0;
    $maps_with_translated_seo = 0;

    $district_relates = 0;
    $max_categories_number = 0;
    $min_categories_number = PHP_INT_MAX;

    $jpeg_links_count = 0;
    $nav_links_count = 0;

    $map_with_jpeg_download = 0;
    $map_with_nav_download = 0;


    foreach ($all_maps as $map) {
        $all_maps_count++;

        $children = get_children(array(
            'post_parent' => $map->ID,
            'post_type' => 'maps',
        ));

        if (count($children) > 0) {
            $parent_maps_count++;
        } else {
            $single_maps_count++;


            if (get_field('empty_map_page', $map->ID)) {
                $empty_maps_count++;
            }

            if (get_field('added_content', $map->ID)) {
                $full_maps_count++;
            }

            if (get_field('translated_seo-attributes', $map->ID)) {
                $maps_with_translated_seo++;
            }

            $map_template = get_page_template_slug($map->ID);

            // single map
            if (get_field('format_jpeg', $map->ID) && $map_template == 'templates/maps-regular-post-template.php') {
                $jpeg_links_count++;
                $map_with_jpeg_download++;
            }

            if (get_field('format_nav', $map->ID) && $map_template == 'templates/maps-regular-post-template.php') {
                $nav_links_count++;
                $map_with_nav_download++;
            }

            // multiple map
            $multimap = get_field('maps', $map->ID);
            if ($multimap && $map_template == 'templates/maps-multiple-post-template.php') {

                $flag_jpeg = false;
                $flag_nav = false;

                foreach ($multimap as $single_element) {

                    if ($single_element['format_jpeg']) {
                        $jpeg_links_count++;

                        if (!$flag_jpeg) {
                            $flag_jpeg = true;
                            $map_with_jpeg_download++;
                        }
                    }

                    if ($single_element['format_nav']) {
                        $nav_links_count++;

                        if (!$flag_nav) {
                            $flag_nav = true;
                            $map_with_nav_download++;
                        }
                    }
                }
            }

            $categories = get_the_category($map->ID);

            if (empty($categories)) {
                $maps_without_category++;
            } else {
                $categories_sum = count($categories);
                $district_relates += $categories_sum;

                if ($categories_sum > $max_categories_number) {
                    $max_categories_number = $categories_sum;
                }

                if ($categories_sum < $min_categories_number) {
                    $min_categories_number = $categories_sum;
                }
            }
        }
    }

    $maps_without_category_count = $maps_without_category - $empty_maps_count - $parent_maps_count;


    $districts_count = 0;
    $districts_with_translated_seo = 0;
    $additional_content = 0;

    $all_download_links = $jpeg_links_count + $nav_links_count;

    $args = array(
        'post_type' => 'page',
        'order' => 'DSC',
        'posts_per_page' => -1,
    );

    $pages = get_posts($args);

    foreach ($pages as $page) {

        $parent_id = wp_get_post_parent_id($page->ID);

        if (!$parent_id) {
            continue;
        }

        $districts_count++;

        if (get_field('translated_seo-attributes', $page->ID)) {
            $districts_with_translated_seo++;
        }

        if (get_field('additional_non-translated_content', $page->ID)) {
            $additional_content++;
        }
    }

?>


    <div class="freemap-stats-loader-container">
        <div class="freemap-stats-loader"></div>
    </div>

    <h1 class="freemap-stats-h1">Freemap Site General Statistics:</h1>

    <div class="freemap-stats-container">
        <div class="freemap-stats-container-block1">
            <h3>Total amount of maps: <?php echo $all_maps_count; ?></h3>
            <br>
            <h3>Parent maps from them: <?php echo $parent_maps_count ?></h3>
            <h3>Single maps from them: <?php echo $single_maps_count ?></h3>
        </div>
        <div class="freemap-stats-container-block2">
            <h3>Maps with content: <?php echo $full_maps_count; ?> / <?php echo getPecents($full_maps_count, $single_maps_count) ?>%</h3>
            <span class="freemap-stats-progressbar"><span class="freemap-stats-progressbar-fill" style="width: <?php echo getPecents($full_maps_count, $single_maps_count) ?>%"></span></span>

            <h3>Maps without content: <?php echo $empty_maps_count; ?> / <?php echo getPecents($empty_maps_count, $single_maps_count) ?>%</h3>
            <span class="freemap-stats-progressbar"><span class="freemap-stats-progressbar-fill-bad" style="width: <?php echo getPecents($empty_maps_count, $single_maps_count) ?>%"></span></span>

            <h3>Maps with content and without any district/category: <?php echo $maps_without_category_count; ?> / <?php echo getPecents($maps_without_category_count, $full_maps_count) ?>%</h3>
            <span class="freemap-stats-progressbar"><span class="freemap-stats-progressbar-fill-bad" style="width: <?php echo getPecents($maps_without_category_count, $full_maps_count) ?>%"></span></span>

            <h3>Maps with translated seo-attributes: <?php echo $maps_with_translated_seo; ?> / <?php echo getPecents($maps_with_translated_seo, $full_maps_count); ?>%</h3>
            <span class="freemap-stats-progressbar"><span class="freemap-stats-progressbar-fill" style="width: <?php echo getPecents($maps_with_translated_seo, $full_maps_count) ?>%"></span></span>

        </div>
        <div class="freemap-stats-container-block3">
            <h3>Total amount of districts: <?php echo $districts_count; ?></h3>
            <br>
            <h3>Total amount of district-map relationships: <?php echo $district_relates; ?></h3>
            <h3>Max number of district-map relationships on map: <?php echo $max_categories_number; ?></h3>
            <h3>Min number of district-map relationships on map: <?php echo $min_categories_number; ?></h3>
        </div>
        <div class="freemap-stats-container-block4">

            <h3>Districts with translated seo-attributes: <?php echo $districts_with_translated_seo; ?> / <?php echo getPecents($districts_with_translated_seo, $districts_count); ?>%</h3>
            <span class="freemap-stats-progressbar"><span class="freemap-stats-progressbar-fill" style="width: <?php echo getPecents($districts_with_translated_seo, $districts_count) ?>%"></span></span>

            <h3>Districts with non-translated additional content: <?php echo $additional_content; ?> / <?php echo getPecents($additional_content, $districts_count); ?>%</h3>
            <span class="freemap-stats-progressbar"><span class="freemap-stats-progressbar-fill-bad" style="width: <?php echo getPecents($additional_content, $districts_count) ?>%"></span></span>

        </div>
        <div class="freemap-stats-container-block5">
            <h3>Total amount of buttons for download: <?php echo $all_download_links; ?></h3>
            <br>
            <h3>Total amount of img buttons for download: <?php echo $jpeg_links_count; ?></h3>
            <h3>Total amount of .ozf2 & .map buttons for download: <?php echo $nav_links_count; ?></h3>
        </div>
        <div class="freemap-stats-container-block6">
            <h3>Total amount of single maps with jpeg download buttons: <?php echo $map_with_jpeg_download; ?> / <?php echo getPecents($map_with_jpeg_download, $full_maps_count) ?>%</h3>
            <span class="freemap-stats-progressbar"><span class="freemap-stats-progressbar-fill" style="width: <?php echo getPecents($map_with_jpeg_download, $full_maps_count) ?>%"></span></span>

            <h3>Total amount of single maps with .ozf2 & .map download buttons: <?php echo $map_with_nav_download; ?> / <?php echo getPecents($map_with_nav_download, $full_maps_count) ?>%</h3>
            <span class="freemap-stats-progressbar"><span class="freemap-stats-progressbar-fill" style="width: <?php echo getPecents($map_with_nav_download, $full_maps_count) ?>%"></span></span>
        </div>



    <?php
    $content = ob_get_clean();
    echo $content;
}








function maps_without_districts_admin_page()
{
    ob_start();
    ?>

        <div class="freemap-stats-loader-container">
            <div class="freemap-stats-loader"></div>
        </div>

        <h1 class="freemap-stats-h1">List of maps without any district (category):</h1>
        <div><button class="freemap-stats-hide-all">HIDE ALL</button><button class="freemap-stats-show-all active">SHOW ALL</button></div>

        <ol>

            <?php

            $parent_maps = get_posts(array(
                'post_type' => 'maps',
                'order' => 'DSC',
                'posts_per_page' => -1,
                'post_parent' => 0,
            ));

            $parent_map_ids = wp_list_pluck($parent_maps, 'ID');

            $maps = get_posts(array(
                'post_type' => 'maps',
                'order' => 'DSC',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'category',
                        'operator' => 'NOT EXISTS',
                    ),
                ),
                'meta_query' => array(
                    array(
                        'key' => 'empty_map_page',
                        'value' => '0',
                        'compare' => '=',
                    ),
                )
            ));

            $group_parent_id = null;
            $flag = false;

            foreach ($maps as $map) :

                if (in_array($map->ID, $parent_map_ids)) {
                    continue;
                }

                $parent_id = wp_get_post_parent_id($map->ID);
                if ($group_parent_id != $parent_id) {
                    $group_parent_id = $parent_id;
                    if ($flag) {
                        echo '</div>';
                    }
                    $flag = true;
                    echo '<span class="freemap-stats-h2-container"><h2 class="freemap-stats-h2">' . get_the_title($parent_id) . '</h2><button class="freemap-stats-list-selector" data-state="hide">HIDE LIST</button></span>';
                    echo '<div class="freemap-stats-li-group">';
                }


            ?>

                <li class="freemap-stats-li">
                    <a href="<?php echo get_permalink($map->ID); ?>" target="_blank">
                        <?php echo $map->post_title; ?>
                    </a>
                </li>


            <?php endforeach; ?>

        </ol>



    <?php
    $content = ob_get_clean();
    echo $content;
}








function origin_seo_maps_admin_page()
{
    ob_start();
    ?>

        <div class="freemap-stats-loader-container">
            <div class="freemap-stats-loader"></div>
        </div>

        <h1 class="freemap-stats-h1">List of maps with origin seo-attributes:</h1>
        <div><button class="freemap-stats-hide-all">HIDE ALL</button><button class="freemap-stats-show-all active">SHOW ALL</button></div>

        <ol>

            <?php
            $maps = get_posts(array(
                'post_type' => 'maps',
                'order' => 'DSC',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => 'translated_seo-attributes',
                        'value' => '0',
                        'compare' => '=',
                    ),
                )
            ));

            $group_parent_id = null;
            $flag = false;

            foreach ($maps as $map) :
                $parent_id = wp_get_post_parent_id($map->ID);
                if ($group_parent_id != $parent_id) {
                    $group_parent_id = $parent_id;

                    if ($flag) {
                        echo '</div>';
                    }
                    $flag = true;
                    echo '<span class="freemap-stats-h2-container"><h2 class="freemap-stats-h2">' . get_the_title($parent_id) . '</h2><button class="freemap-stats-list-selector" data-state="hide">HIDE LIST</button></span>';
                    echo '<div class="freemap-stats-li-group">';
                }
            ?>

                <li class="freemap-stats-li">
                    <a href="<?php echo get_permalink($map->ID); ?>" target="_blank">
                        <?php echo $map->post_title; ?>
                    </a>
                </li>


            <?php endforeach; ?>

        </ol>



    <?php
    $content = ob_get_clean();
    echo $content;
}








function origin_seo_pages_admin_page()
{
    ob_start();
    ?>

        <div class="freemap-stats-loader-container">
            <div class="freemap-stats-loader"></div>
        </div>

        <h1 class="freemap-stats-h1">List of pages with origin seo-attributes:</h1>

        <ol class="freemap-stats-margin-top-50">

            <?php
            $maps = get_posts(array(
                'post_type' => 'page',
                'order' => 'DSC',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => 'translated_seo-attributes',
                        'value' => '0',
                        'compare' => '=',
                    ),
                )
            ));

            foreach ($maps as $map) :
            ?>

                <li>
                    <a href="<?php echo get_permalink($map->ID); ?>" target="_blank">
                        <?php echo $map->post_title; ?>
                    </a>
                </li>


            <?php endforeach; ?>

        </ol>



    <?php
    $content = ob_get_clean();
    echo $content;
}













function empty_maps_admin_page()
{
    ob_start();
    ?>

        <div class="freemap-stats-loader-container">
            <div class="freemap-stats-loader"></div>
        </div>

        <h1 class="freemap-stats-h1">List of temporary empty maps:</h1>
        <div><button class="freemap-stats-hide-all">HIDE ALL</button><button class="freemap-stats-show-all active">SHOW ALL</button></div>

        <ol>

            <?php
            $maps = get_posts(array(
                'post_type' => 'maps',
                'order' => 'DSC',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => 'empty_map_page',
                        'value' => '1',
                        'compare' => '=',
                    ),
                )
            ));


            $group_parent_id = null;
            $flag = false;

            foreach ($maps as $map) :
                $parent_id = wp_get_post_parent_id($map->ID);
                if ($group_parent_id != $parent_id) {
                    $group_parent_id = $parent_id;

                    if ($flag) {
                        echo '</div>';
                    }
                    $flag = true;
                    echo '<span class="freemap-stats-h2-container"><h2 class="freemap-stats-h2">' . get_the_title($parent_id) . '</h2><button class="freemap-stats-list-selector" data-state="hide">HIDE LIST</button></span>';
                    echo '<div class="freemap-stats-li-group">';
                }
            ?>

                <li class="freemap-stats-li">
                    <a href="<?php echo get_permalink($map->ID); ?>" target="_blank">
                        <?php echo $map->post_title; ?>
                    </a>
                </li>


            <?php endforeach; ?>

        </ol>



    <?php
    $content = ob_get_clean();
    echo $content;
}
