<?php

class SanitizeService
{

    //--------------------------------------------------------------------------
    // htmlspecialchars — Convert special characters to HTML entities
    public static function SanitizeProperty($stringTobeSanitized)
    {
       return htmlspecialchars($stringTobeSanitized, ENT_QUOTES, 'UTF-8');
    }

    //--------------------------------------------------------------------------
    // Sanitize specified properties of the object
    public static function SanitizeObjectsProperties($object, $propertiesToSanitize)
    {   
       $sanitizedObject = json_decode('{}');
       
       // Sanitizes object properties values
       foreach ($propertiesToSanitize as $property) {
            $sanitizedObject->{"$property"} = SanitizeService::SanitizeProperty($object->{"$property"});
        }

        return $sanitizedObject;
    }

    //--------------------------------------------------------------------------
    // htmlspecialchars — Convert special characters to HTML entities
    // Method is equal to SanitizeProperty
    public static function SanitizeString($stringTobeSanitized)
    {
       return htmlspecialchars($stringTobeSanitized, ENT_QUOTES, 'UTF-8');
    }

}
?>