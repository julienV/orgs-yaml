<?php
namespace App\Entity;


class Organisation implements \JsonSerializable
{
	private string $name;
	private string $description;

	/**
	 * @var User[]
	 */
	private ?array $users;

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param   string  $name
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * @param   string  $description
	 */
	public function setDescription(string $description): void
	{
		$this->description = $description;
	}

	/**
	 * @return User[]
	 */
	public function getUsers(): array
	{
		return $this->users;
	}

	/**
	 * @param   User[]  $users
	 */
	public function setUsers(array $users): void
	{
		$this->users = $users;
	}

	public function jsonSerialize() : array
	{
		return [
			'name' => $this->name,
			'description' => $this->description,
			'users' => $this->users ?? [],
		];
	}

	public function toArray() : array
	{
		return [
			'name' => $this->name,
			'description' => $this->description,
			'users' => $this->users ? array_map(function(User $user) {
				return $user->toArray();
			}, $this->users) : [],
		];
	}

	public static function fromArray(array $data) : self
	{
		$instance = new static;
		$instance->setName($data['name']);
		$instance->setDescription($data['description']);

		if (!empty($data['users']))
		{
			$users = array_map(
				function(array $data) {
					return User::fromArray($data);
				},
				$data['users']
			);

			$instance->setUsers($users);
		}

		return $instance;
	}
}
