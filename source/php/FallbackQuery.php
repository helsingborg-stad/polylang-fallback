<?php

namespace pplfallback;

class FallbackQuery
{

    public $fallbackLanguages = null;
    public $onlyMainQuery = false;

    public function __construct()
    {
        if (is_admin()) {
            return;
        }

        //Set filterable default values
        $this->fallbackLanguages = (array) apply_filters('PolylangFallback/fallbackLanguages', array("en", "sv"));
        $this->onlyMainQuery = (bool) apply_filters('PolylangFallback/onlyMainQuery', false);

        //Do fallback
        add_action('pre_get_posts', array($this, 'fallbackToLanguage'));
    }

    /**
     * Fallback to defined language(s) if the main language isen't present
     * @param object $query current instance of wp query
     * @return void
     */

    public function fallbackToLanguage($query)
    {

        //If class is set to only fallback on main query, return false if it's not this one.
        if ($onlyMainQuery && !$query->is_main_query()) {
            return false;
        }

        //Fallback procedure in order of the fallback array
        if (!is_a($query->queried_object, 'WP_Post')) {
            if (isset($query->query)) {
                if (is_array($this->fallbackLanguages) && !empty($this->fallbackLanguages)) {
                    remove_action('pre_get_posts', array($this, 'fallbackToLanguage'));

                    foreach ($this->fallbackLanguages as $fallbackLanguage) {
                        $query->query['lang'] = (string) $fallbackLanguage;

                        $query = new \WP_Query((array) json_decode(json_encode($query->query), true));

                        if (is_a($query->queried_object, 'WP_Post')) {
                            global $wp_query;
                            $wp_query = $query;
                            break;
                        }
                    }

                    add_action('pre_get_posts', array($this, 'fallbackToLanguage'));
                } else {
                    wp_die("Error: Yo havent defined any fallback language for polylang fallback functionality.");
                }
            }
        }
    }
}
