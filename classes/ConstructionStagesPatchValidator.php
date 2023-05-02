<?php

class ConstructionStagesPatchValidator {    
    /**
     * validate
     *
     * @param  mixed $patch
     * @return void
     */
    public static function validate(ConstructionStagesPatch $patch) {
        if (strlen($patch->name) > 255) {
            throw new Exception("Name cannot be longer than 255 characters");
        }

        if (!empty($patch->start_date) && !DateTime::createFromFormat(DateTime::ISO8601, $patch->start_date)) {
            throw new Exception("Start date must be a valid date&time in iso8601 format i.e. 2022-12-31T14:59:00Z");
        }

        if (!empty($patch->end_date)) {
            if (!DateTime::createFromFormat(DateTime::ISO8601, $patch->end_date)) {
                throw new Exception("End date must be a valid datetime in iso8601 format i.e. 2022-12-31T14:59:00Z");
            }

            $start = new DateTime($patch->start_date);
            $end = new DateTime($patch->end_date);
            if ($end <= $start) {
                throw new Exception("End date must be later than the start date");
            }
        }

        if (!empty($patch->start_date) && !empty($patch->end_date) && !empty($patch->durationUnit)) {
            $start = new DateTime($patch->start_date);
            $end = new DateTime($patch->end_date);
            $diff = $end->diff($start);
            switch ($patch->durationUnit) {
                case 'HOURS':
                    $duration = $diff->h + ($diff->days * 24);
                    break;
                case 'WEEKS':
                    $duration = ($diff->days + ($diff->y * 365) + ($diff->m * 30)) / 7 * 24;
                    break;
                case 'DAYS':
                default:
                    $duration = ($diff->days + ($diff->y * 365) + ($diff->m * 30)) * 24;
                    break;
            }
            $patch->duration = round($duration, 2);
        } elseif (empty($patch->duration)) {
            $patch->duration = null;
        }
        

        if (!empty($patch->color) && !preg_match('/^#[a-f0-9]{6}$/i', $patch->color)) {
            throw new Exception("Color must be a valid HEX color i.e. #FF0000");
        }

        if (!empty($patch->externalId) && strlen($patch->externalId) > 255) {
            throw new Exception("External ID cannot be longer than 255 characters");
        }

        $validStatuses = ["NEW", "PLANNED", "DELETED"];
        if (!empty($patch->status) && !in_array($patch->status, $validStatuses)) {
            throw new Exception("Status must be one of NEW, PLANNED or DELETED");
        }
    }
}
