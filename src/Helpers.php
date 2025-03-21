<?php

namespace Devsrealm\TonicsTemplateSystemExtensionBundle;

class Helpers
{
    /**
     * @param array $data
     * @return array
     */
    public static function HtmlSpecialCharsOnArrayValues(array $data): array
    {
        array_walk_recursive($data, function (&$value) {
            if (is_null($value)) {
                $value = '';
            }
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        });

        return $data;
    }

    /**
     * @param $data1
     * @param $data2
     * @return array
     */
    public static function MergeKeyIntersection($data1, $data2): array
    {
        return array_merge($data1, array_intersect_key($data2, $data1));
    }

    /**
     * @param callable|null $callback
     * @return void
     */
    public static function GarbageCollect(callable $callback = null)
    {
        if ($callback !== null) {
            $callback();
        }
        gc_collect_cycles();
    }
}