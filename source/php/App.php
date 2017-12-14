<?php

namespace pplfallback;

class App
{
    public function __construct()
    {
        /* Don't redirect URLs in 'wrong' language so that we can
        * have article in one language and interface in another
        */
        add_filter('pll_check_canonical_url', '__return_false');

        /* Do a simple fallback query, if not post is found.
        */
        new FallbackQuery();
    }
}
