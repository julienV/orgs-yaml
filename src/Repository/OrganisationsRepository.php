<?php
namespace App\Repository;

use App\Entity\Organisation;
use App\Entity\User;
use App\Service\DataProvider;

class OrganisationsRepository
{
	private DataProvider $dataProvider;

	public function  __construct(DataProvider $dataProvider)
	{
		$this->dataProvider = $dataProvider;
	}

	/**
	 * @return Organisation[]
	 */
	public function findAll() : array
	{
		return $this->dataProvider->getOrganisationsList();
	}

	public function findOneByName(string $name) : ?Organisation
	{
		return $this->dataProvider->getOrganisationByName($name);
	}
}
