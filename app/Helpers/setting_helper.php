<?php

if (!function_exists('get_setting')) {
    /**
     * Get a system setting value by key
     */
    function get_setting($key, $default = '')
    {
        $db = \Config\Database::connect();
        $builder = $db->table('settings');
        $row = $builder->where('key', $key)->get()->getRow();

        return $row ? $row->value : $default;
    }
}
