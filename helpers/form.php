<?php

abstract class FormHelper extends UtilsHelper {
    public function addField($field, $echo = true) {
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

        return $this->echoOutput($output, $echo);
    }

    public function addView($template, $data, $echo = true) {
        $output = file_get_contents($template, true);

        return $this->echoOutput($output, $echo);
    }
}