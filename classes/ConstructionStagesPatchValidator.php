<?php

class ConstructionStagesPatchValidator {    
    /**
     * validate
     *
     * @param  mixed $patch
     * @return void
     */
    public static function validate(ConstructionStagesPatch $patch) {
        $validStatuses = ["NEW", "PLANNED", "DELETED"];
        if (!empty($patch->status) && !in_array($patch->status, $validStatuses)) {
            throw new Exception("Status must be one of NEW, PLANNED or DELETED");
        }
    }
}
