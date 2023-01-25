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

        //Init language code
        add_action('plugins_loaded', array($this, 'getDefultLanguageCode'), 5);

        //Set filterable default values
        add_action('plugins_loaded', function () {
            $this->fallbackLanguages = apply_filters('PolylangFallback/fallbackLanguages', array("en", $this->getDefultLanguageCode()));
            $this->onlyMainQuery = (bool) apply_filters('PolylangFallback/onlyMainQuery', false);
        }, 10);

        //Do fallback (single post)
        add_action('pre_get_posts', array($this, 'fallbackToLanguageSingle'));
    }

    /**
     * Fallback to defined language(s) if the main language isen't present (single post)
     * @param object $query current instance of wp query
     * @return void
     */
    public function fallbackToLanguageSingle($query)
    {

        //If class is set to only fallback on main query, return false if it's not this one.
        if ($this->onlyMainQuery && !$query->is_main_query()) {
            return false;
        }

        //Fallback procedure in order of the fallback array
        if (!is_a($query->queried_object, 'WP_Post')) {
            if (isset($query->query)) {
                if (is_array($this->fallbackLanguages) && !empty($this->fallbackLanguages)) {
                    remove_action('pre_get_posts', array($this, 'fallbackToLanguageSingle'));

                    foreach ($this->fallbackLanguages as $fallbackLanguage) {
                        $query->query['lang'] = (string) $fallbackLanguage;

                        $tempQuery = new \WP_Query((array) json_decode(json_encode($query->query), true));

                        if (is_a($tempQuery->queried_object, 'WP_Post')) {
                            if ($query->is_main_query()) {
                                global $wp_query;
                                $wp_query = $tempQuery;
                            }

                            $query = $tempQuery;

                            break;
                        }
                    }

                    add_action('pre_get_posts', array($this, 'fallbackToLanguageSingle'));
                } else {
                    wp_die("Error: Yo havent defined any fallback language for polylang fallback functionality.");
                }
            }
        }
    }

    public function getDefultLanguageCode()
    {
        return \pll_default_language();
    }
}
