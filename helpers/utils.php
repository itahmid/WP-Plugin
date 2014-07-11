<?php

abstract class UtilsHelper {
    public function dier($something) {
        die(
            var_dump(
                $something
            )
        );
    }

    public function vard($something) {
        var_dump(
            $something
        );
    }

    public function isAssoc($array) {
        return is_array($array) && array_diff_key($array, array_keys(array_keys($array)));
    }

    public function echoOutput($content, $echo) {
        if ($echo) {
            echo $content;
        }

        return $content;
    }
}