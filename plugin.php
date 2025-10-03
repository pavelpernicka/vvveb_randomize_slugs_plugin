<?php

/*
Name: Randomize slugs
Slug: randomize-slugs
Category: content
Url: https://pernicka.cz/vvveb_randomize
Description: Add random 5-character prefix to slugs
Author: pavelpernicka
Version: 0.2
Thumb: random-slugs.svg
Author url: https://pernicka.cz
*/

use Vvveb\System\Event;

if (! defined('V_VERSION')) {
    die('Invalid request!');
}

if (! function_exists('slugify_randomized')) {
    function slugify_randomized($text) {
        if (! $text) {
            return $text;
        }

        // keep randomized part
        if (preg_match('/^([a-zA-Z0-9]{5})-(.+)$/', $text, $matches)) {
            $random = $matches[1];
            $text   = $matches[2];

            // normalize accents
            if (class_exists('Normalizer')) {
                $text = Normalizer::normalize($text, Normalizer::FORM_D);
            }

            // remove accents
            $text = preg_replace('/\p{Mn}/u', '', $text);

            // replace non-word chars and spaces with hyphen
            $text = preg_replace('/([^\w]+|\s+)/u', '-', $text);

            // collapse multiple hyphens
            $text = preg_replace('/-+/', '-', $text);

            // trim leading/trailing hyphens
            $text = trim($text, '-');

            return strtolower($random . '-' . $text);
        }

        if (class_exists('Normalizer')) {
            $text = Normalizer::normalize($text, Normalizer::FORM_D);
        }
        $text = preg_replace('/\p{Mn}/u', '', $text);
        $text = preg_replace('/([^\w]+|\s+)/u', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        $text = trim($text, '-');

        return strtolower($text);
    }
}


class RandomizeSlugsPlugin {
    function randomize($text) {
        return slugify_randomized($text);
    }

    function app() {
        // post component
        Event::on('Vvveb\Component\Post', 'results', __CLASS__, function ($results = false) {
            if ($results) {
                $randomSlug = $this->randomize($results['slug']);
                $results['url'] = str_replace($results['slug'], $randomSlug, $results['url']);
                $results['slug'] = $randomSlug;
            }
            return [$results];
        });

        // posts component
        Event::on('Vvveb\Component\Posts', 'results', __CLASS__, function ($results = false) {
            if (isset($results['post'])) {
                foreach ($results['post'] as &$post) {
                    $randomSlug = $this->randomize($post['slug']);
                    $post['url'] = str_replace($post['slug'], $randomSlug, $post['url']);
                    $post['slug'] = $randomSlug;
                }
            }
            return [$results];
        });
    }

    function __construct() {
        if (APP == 'app') {
            $this->app();
        }
    }
}

$randomizeSlugsPlugin = new RandomizeSlugsPlugin();
