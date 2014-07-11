<?php

abstract class FormHelper {
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

    public function addField($field) {
        $output;

            switch ($field['type']) {
                case 'select':
                    $output =  '<select name="'. $field['group'] .'['. $field['id'] .']">';
                        foreach ($field['options'] as $key => $value) {
                            $selected = $field['value'] === $value ? 'selected="selected"' : '';
                            $output .= '<option value="'. $value .'" '. $selected .'>'. $key .'</option>';
                        }
                    $output .=  '</select>';
                break;
                case 'checkbox':
                    $field['value']  = !empty($field['value']) ? '1' : '0';
                    $output = '<input name="'. $field['group'] .'['. $field['id'] .']" type="'. $field['type'] .'" value="1" '. checked($field['value'], 1, 0) .'  />';
                break;
                case 'text':
                default:
                    $output = '<input name="'. $field['group'] .'['. $field['id'] .']" type="'. $field['type'] .'" value="'. $field['value'] .'" />';
                break;
            }

        return $output;
    }
}