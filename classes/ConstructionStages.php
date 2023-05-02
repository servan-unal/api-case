<?php

class ConstructionStages
{
	private $db;

	public function __construct()
	{
		$this->db = Api::getDb();
	}

	public function getAll()
	{
		$stmt = $this->db->prepare("
			SELECT
				ID as id,
				name, 
				strftime('%Y-%m-%dT%H:%M:%SZ', start_date) as startDate,
				strftime('%Y-%m-%dT%H:%M:%SZ', end_date) as endDate,
				duration,
				durationUnit,
				color,
				externalId,
				status
			FROM construction_stages
		");
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getSingle($id)
	{
		$stmt = $this->db->prepare("
			SELECT
				ID as id,
				name, 
				strftime('%Y-%m-%dT%H:%M:%SZ', start_date) as startDate,
				strftime('%Y-%m-%dT%H:%M:%SZ', end_date) as endDate,
				duration,
				durationUnit,
				color,
				externalId,
				status
			FROM construction_stages
			WHERE ID = :id
		");
		$stmt->execute(['id' => $id]);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function post(ConstructionStagesCreate $data)
	{
		$stmt = $this->db->prepare("
			INSERT INTO construction_stages
			    (name, start_date, end_date, duration, durationUnit, color, externalId, status)
			    VALUES (:name, :start_date, :end_date, :duration, :durationUnit, :color, :externalId, :status)
			");
		$stmt->execute([
			'name' => $data->name,
			'start_date' => $data->startDate,
			'end_date' => $data->endDate,
			'duration' => $data->duration,
			'durationUnit' => $data->durationUnit,
			'color' => $data->color,
			'externalId' => $data->externalId,
			'status' => $data->status,
		]);
		return $this->getSingle($this->db->lastInsertId());
	}

	
	/**
	 * delete
	 *
	 * @param  mixed $id
	 * @return void
	 */
	public function delete($id)
	{
		$sql = "UPDATE construction_stages SET status = 'DELETED' WHERE ID = :id";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':id', $id);
		$stmt->execute();

		// Return the updated resource
		return $this->getSingle($id);
	}

	
	/**
	 * @param ConstructionStagesPatch $data
	 * 
	 * @return [type]
	 */
	public function patch(ConstructionStagesPatch $data)
{

	$id = $data->id;
    $updates = [];
    $params = ['id' => $id];
    $validator = new ConstructionStagesPatchValidator();
	try {
    $validator->validate($data);
    // Data is valid
	} catch (ValidationException $e) {
    // Handle validation errors
    $errors = $e->getErrors();
}

    if (!empty($data->name)) {
        $updates[] = 'name = :name';
        $params['name'] = $data->name;
    }
    
    if (!empty($data->startDate)) {
        $updates[] = 'start_date = :start_date';
        $params['start_date'] = $data->startDate;
    }
    
    if (!empty($data->endDate)) {
        $updates[] = 'end_date = :end_date';
        $params['end_date'] = $data->endDate;
    }
    
    if (!empty($data->durationUnit)) {
        $updates[] = 'durationUnit = :durationUnit';
        $params['durationUnit'] = $data->durationUnit;
    }
    
    if (!empty($data->color)) {
        $updates[] = 'color = :color';
        $params['color'] = $data->color;
    }
    
    if (!empty($data->externalId)) {
        $updates[] = 'externalId = :externalId';
        $params['externalId'] = $data->externalId;
    }
    
    if (!empty($data->status)) {
        $updates[] = 'status = :status';
        $params['status'] = $data->status;
    }
    
    if (empty($updates)) {
        // Nothing to update
        return $this->getSingle($id);
    }
    
    $sql = 'UPDATE construction_stages SET ' . implode(', ', $updates) . ' WHERE ID = :id';
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    
    // Return the updated resource
    return $this->getSingle($id);
	}
}