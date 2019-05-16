<?php

function astra_child_enqueue_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('main-css', get_stylesheet_directory_uri() . '/style.css');
}

add_action('wp_enqueue_scripts', 'astra_child_enqueue_styles');


if (!function_exists('t5_show_short_content')) {

    /**
     * Print the whole content if the visible words are less than the excerpt length.
     *
     * @param  string $excerpt
     * @return string
     */
    function t5_show_short_content($excerpt)
    {

        global $post;

        $content = apply_filters('the_content', get_the_content());
        $content = str_replace(']]>', ']]&gt;', $content);

        $more = sprintf(
            '<a class="read-more" href="%1$s">%2$s &raquo;</a>',
            get_permalink(get_the_ID()),
            __('Read More', 'astrachild')
        );

        $excerpt = preg_replace("/\\[&hellip;\\]/", 'place here whatever you want to replace', $excerpt);

        if (has_excerpt($post->ID)) {
            $excerpt = $excerpt . $more;
        }

        return is_short_content($content) ? $content : $excerpt;
    }

    add_filter('the_excerpt', 't5_show_short_content');
}

if (!function_exists('is_short_content')) {
    /**
     * Is the passed string not longer than an excerpt?
     *
     * @param  string $content
     * @return boolean
     */
    function is_short_content($content = NULL)
    {
        NULL === $content && $content = get_the_content();
        // Get maximal excerpt length for this theme
        $max_word_length = apply_filters('excerpt_length', 55);

        // see 'Counting words in a post'
        // https://wordpress.stackexchange.com/a/52460/73
        $content_text    = trim(strip_tags($content));
        $content_words   = preg_match_all('~\s+~', "$content_text ", $m);

        return $content_words <= $max_word_length;
    }
}

if (!function_exists('modify_read_more_link')) {
    function custom_excerpt_more($more)
    {
        $more = sprintf(
            '&hellip; <br/> <br /><a class="read-more" href="%1$s">%2$s &raquo;</a>',
            get_permalink(get_the_ID()),
            __('Read More', 'astrachild')
        );
        return $more;
    }
    add_filter('excerpt_more', 'custom_excerpt_more');
}

/**
 * Function to get Content Read More Link of Post
 *
 * @since 1.2.7
 * @return html
 */
if (!function_exists('astra_child_the_content_more_link')) {

    /**
     * Filters the Read More link text.
     *
     * @param  string $more_link_element Read More link element.
     * @param  string $more_link_text Read More text.
     * @return html                Markup.
     */
    function astra_child_the_content_more_link($more_link_element = '', $more_link_text = '')
    {

        $enabled = apply_filters('astra_the_content_more_link_enabled', '__return_true');
        if ((is_admin() && !wp_doing_ajax()) || !$enabled) {
            return $more_link_element;
        }

        $more_link_text    = apply_filters('astra_the_content_more_string', __('Read More &raquo;', 'astra'));
        $read_more_classes = apply_filters('astra_the_content_more_link_class', array());

        $post_link = sprintf(
            esc_html('%s'),
            '<a class="' . esc_attr(implode(' ', $read_more_classes)) . '" href="' . esc_url(get_permalink()) . '"> ' . the_title('<span class="screen-reader-text">', '</span>', false) . $more_link_text . '</a>'
        );

        $more_link_element = ' <p class="ast-the-content-more-link"> ' . $post_link . '</p>';

        return apply_filters('astra_child_the_content_more_link', $more_link_element, $more_link_text);
    }
}
add_filter('the_content_more_link', 'astra_child_the_content_more_link', 12, 2);
