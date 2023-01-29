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

	public function deleteOrganisationByName(string $name) : void
	{
		if (!$this->getOrganisationByName($name)) {
			throw new \InvalidArgumentException('not found');
		}

		$orgs = array_filter(
			$this->getOrganisationsList(),
			function (Organisation $org) use ($name) {
				return $org->getName() !== $name;
			}
		);

		$this->write($orgs);
	}

	public function updateOneOrganisation(string $name, Organisation $data) : Organisation
	{
		if (!$this->getOrganisationByName($name)) {
			throw new \InvalidArgumentException('not found');
		}

		$orgs = $this->getOrganisationsList();

		foreach ($orgs as &$org)
		{
			if ($org->getName() == $name) {
				$org = $data;
			}
		}

		$this->write($orgs);

		return $data;
	}

	public function createOrganisation(Organisation $data) : Organisation
	{
		$orgs = $this->getOrganisationsList();
		$orgs[] = $data;

		$this->write($orgs);

		return $data;
	}

	/**
	 * @param   Organisation[]  $organisations
	 *
	 * @return void
	 */
	private function write(array $organisations)
	{
		$data = [
			'organizations' => array_values(array_map(
				function (Organisation $org) {
					return $org->toArray();
				},
				$organisations
			))
		];

		$yaml = Yaml::dump($data, 5, 4);

		file_put_contents($this->dataFilePath, $yaml);
	}
}
