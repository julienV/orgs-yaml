<?php
namespace App\Service;

use App\Entity\Organisation;
use Symfony\Component\Yaml\Yaml;

class DataProvider
{
	private string $dataFilePath;

	public function __construct(string $dataFilePath)
	{
		$this->dataFilePath= $dataFilePath;
	}

	/**
	 * @return Organisation[]
	 */
	public function getOrganisationsList() : array
	{
		$data = Yaml::parseFile($this->dataFilePath);
		$results = [];

		if (!empty($data['organizations'])) {
			return array_map(
				function(array $data) {
					return Organisation::fromArray($data);
				},
				$data['organizations']
			);
		}

		return $results;
	}

	public function getOrganisationByName(string $name) : ?Organisation
	{
		foreach ($this->getOrganisationsList() as $org)
		{
			if ($org->getName() === $name) {
				return $org;
			}
		}

		return null;
	}
}
