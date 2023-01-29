<?php
namespace App\Entity;

class User implements \JsonSerializable
{
	private string $name;
	private string $password;

	/**
	 * @var string[]
	 */
	private array $roles;

	/**
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	/**
	 * @param   string  $password
	 */
	public function setPassword(string $password): void
	{
		$this->password = $password;
	}

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
	 * @return array
	 */
	public function getRoles(): array
	{
		return $this->roles;
	}

	/**
	 * @param   array  $roles
	 */
	public function setRoles(array $roles): void
	{
		$this->roles = $roles;
	}

	public function jsonSerialize() : array
	{
		return [
			'name' => $this->name,
			'password' => $this->password,
			'roles' => $this->roles,
		];
	}

	public static function fromArray(array $data) : self
	{
		$instance = new static;
		$instance->setName($data['name']);
		$instance->setPassword($data['password']);
		$instance->setRoles($data['role']);

		return $instance;
	}
}
