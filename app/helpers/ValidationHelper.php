<?php
class ValidationHelper {
    
    public static function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? '';
            
            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;
                
                $error = self::validateRule($field, $value, $ruleName, $ruleParam, $data);
                if ($error) {
                    $errors[] = $error;
                    break; // Stop on first error for this field
                }
            }
        }
        
        return $errors;
    }
    
    private static function validateRule($field, $value, $rule, $param, $allData) {
        $fieldName = ucfirst(str_replace('_', ' ', $field));
        
        switch ($rule) {
            case 'required':
                return empty(trim($value)) ? "{$fieldName} is required" : null;
                
            case 'min':
                return strlen($value) < $param ? "{$fieldName} must be at least {$param} characters" : null;
                
            case 'max':
                return strlen($value) > $param ? "{$fieldName} cannot exceed {$param} characters" : null;
                
            case 'email':
                return !filter_var($value, FILTER_VALIDATE_EMAIL) ? "Invalid email format" : null;
                
            case 'alpha_num':
                return !preg_match('/^[a-zA-Z0-9_]+$/', $value) ? "{$fieldName} can only contain letters, numbers and underscores" : null;
                
            case 'confirmed':
                $confirmField = $field . '_confirmation';
                return $value !== ($allData[$confirmField] ?? '') ? "{$fieldName} confirmation does not match" : null;
                
            default:
                return null;
        }
    }
}