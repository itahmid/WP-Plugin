<?php

class RulesHelper extends UtilsHelper {

    protected $priority = array(
        'all' => 1,
        'post_type' => 2,
        'category' => 3,
        'template' => 4,
        'post' => 5,
        'page' => 5
    );

    protected function phrase($param, $value, $visible) {
        $condition = false;

        switch($param) {
            case 'all':
                $condition = true;
                break;
            case 'post_type':
                $condition = get_post_type() === $value;
                break;
            case 'post':
                $condition = is_single($value);
                break;
            case 'category':
                $condition = is_category($value) || (is_single() && in_category($value));
                break;
            case 'page':
                $condition = is_page($value);
                break;
            case 'template':
                $condition = is_page_template($value);
                break;
        }

        return $condition && $visible;
    }

    public function test($data) {
        $results = array();

        foreach($data as $item) {
            $result = array(
                'priority' => $this->priority[$item['param']],
                'phrase' => $this->phrase($item['param'], $item['value'], $item['visible'])
            );

            array_push($results, $result);
        }

        uasort($results, array($this, 'SortById'));

        return $results[size($results) - 1]['phrase'];
    }

    public function getOptions($param) {
        $options = array();

        switch($param) {
            case 'post_type':
                $options = array(
                    'post'=>'Post',
                    'page'=>'Page'
                );
                break;
            case 'post':
                $posts = get_posts(array('posts_per_page' => -1));
                foreach ($posts as $post) {
                    $options[$post->ID] = $post->post_title;
                }
                break;
            case 'category':
                $categories = get_categories();
                foreach ($categories as $category) {
                    $options[$category->cat_ID] = $category->name;
                }
                break;
            case 'page':
                $pages = get_pages();
                foreach ($pages as $page) {
                    $options[$page->ID] = $parent .' '. $page->post_title;
                }
                break;
            case 'template':
                $templates = wp_get_theme()->get_page_templates();
                if ($templates) {
                    $options = ($templates);
                }
                break;
        }
    }
}