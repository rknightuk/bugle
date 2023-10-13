<?php

if (!function_exists('formatForMeta')) {
    function formatForMeta($content)
    {
        return str_replace('"', '\'', html_entity_decode(strip_tags($content)));
    }
}
