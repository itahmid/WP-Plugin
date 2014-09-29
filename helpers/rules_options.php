<?php

class RulesOptionsHelper extends RulesHelper {
    public function option_post_type() {
        return array(
            'post'=>'Post',
            'page'=>'Page'
        );
    }

    public function option_post() {
        $output = array();
        $posts = get_posts(array('posts_per_page' => -1));

        foreach ($posts as $post) {
            $output[$post->ID] = $post->post_title;
        }

        return $output;
    }

    public function option_page() {
        $output = array();
        $pages = get_pages();

        foreach ($pages as $page) {
            $output[$page->ID] = $page->post_title;
        }

        return $output;
    }

    public function option_category() {
        $output = array();
        $categories = get_categories();

        foreach ($categories as $category) {
            $output[$category->cat_ID] = $category->name;
        }

        return $output;
    }

    public function option_template() {
        $output = array();
        $templates = wp_get_theme()->get_page_templates();

        if ($templates) {
            $output = ($templates);
        }

        return $output;
    }

    public function get($rule) {
        $options = array();

        switch($rule) {
            case 'post_type':
                $options = $this->option_post_type();
                break;
            case 'post':
                $options = $this->option_post();
                break;
            case 'category':
                $options = $this->option_category();
                break;
            case 'page':
                $options = $this->option_page();
                break;
            case 'template':
                $options = $this->option_template();
                break;
            default:
                $options = new stdClass();
                break;
        }

        return $options;
    }
}