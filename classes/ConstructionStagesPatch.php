<?php

class ConstructionStagesPatch
{
    public $id;
    public $name;
    public $startDate;
    public $endDate;
    public $durationUnit;
    public $color;
    public $externalId;
    public $status;
    
    /**
     * __construct
     *
     * @param  mixed $data
     * @return void
     */
    public function __construct($data)
    {
        if (is_object($data)) {
            $vars = get_object_vars($this);
            foreach ($vars as $name => $value) {
                if (isset($data->$name)) {
                    $this->$name = $data->$name;
                }
            }
        }
    }
}
