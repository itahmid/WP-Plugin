<?php

abstract class FormHelper {
    public function isAssoc($array) {
        return is_array($array) && array_diff_key($array, array_keys(array_keys($array)));
    }

    public function addFields($fields, $data) {
        $fields = $this->isAssoc($fields) ? array($fields) : $fields;
        $output;

        foreach ($fields as $field) {
            switch ($field['type']) {
                default:
                    $value  = !empty($data[$field['id']]) ? $data[$field['id']] : '';
                    $output = '<input name="'. $field['group'] .'['. $field['id'] .']" type="text" value="'. $value .'" />';
                break;
            }
        }

        return $output;
    }
}