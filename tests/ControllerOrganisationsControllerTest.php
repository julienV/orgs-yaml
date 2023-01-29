<?php

namespace App\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerOrganisationsControllerTest extends WebTestCase
{
	protected function setUp() : void
	{
		$data = <<<YAMl
organizations:
  -
    name: "Facebook"
    description: "Facebook [ˈfeɪsbʊk] est une société américaine créée en 2004 par Mark Zuckerberg. Initialement concentrée sur le réseau social Facebook, la compagnie a racheté Instagram en 2012, ainsi que WhatsApp et Oculus VR en 2014"
    users:
      -
        name: Mark
        role: ["ADMIN", "CEO"]
        password: demo
      -
        name: Jean
        role: ["ADMIN", "CTO"]
        password: demo2
      -
        name: Pierre
        role: ["SALES"]
        password: demo3
  -
    name: "Google"
    description: "Google LLC /ˈguːgəl/ est une entreprise américaine de services technologiques fondée en 1998 dans la Silicon Valley, en Californie, par Larry Page et Sergey Brin, créateurs du moteur de recherche Google. C'est une filiale de la société Alphabet depuis août 2015"
    users:
      -
        name: Ive
        role: ["ADMIN", "CEO"]
        password: demo
      -
        name: Martin
        role: ["ADMIN", "CTO"]
        password: demo2
      -
        name: Jacques
        role: ["SALES"]
        password: demo3
  -
    name: "YouTube"
    description: "YouTube est un site web d’hébergement de vidéos et un média social sur lequel les utilisateurs peuvent envoyer, regarder, commenter, évaluer et partager des vidéos"
    users:
      -
        name: Nathalie
        role: ["ADMIN", "CEO"]
        password: demo
      -
        name: Martin
        role: ["ADMIN", "CTO"]
        password: demo2
      -
        name: Sophie
        role: ["SALES"]
        password: demo3

YAMl;
		file_put_contents(dirname(__DIR__) . '/organizations.test.yaml', $data);
	}

	public function testGetOrganisations() : void
	{
		$client = static::createClient();
		$client->request('GET', '/organisations');

		$response = $client->getResponse();
		$this->assertSame(200, $response->getStatusCode());
		$responseData = json_decode($response->getContent(), true);

		self::assertEquals(3, count($responseData['organisations']));
		self::assertEquals('Facebook', $responseData['organisations'][0]['name']);
	}

	public function testGetOrganisation() : void
	{
		$client = static::createClient();
		$client->request('GET', '/organisations/Facebook');

		$response = $client->getResponse();
		$this->assertSame(200, $response->getStatusCode());
		$responseData = json_decode($response->getContent(), true);

		self::assertEquals('Facebook', $responseData['name']);
	}

	public function testGetOrganisation404() : void
	{
		$client = static::createClient();
		$client->request('GET', '/organisations/someotherorg');

		$response = $client->getResponse();
		$this->assertSame(404, $response->getStatusCode());
	}

	public function testDeleteOrganisation() : void
	{
		$client = static::createClient();
		$client->request('GET', '/organisations/Google');
		$response = $client->getResponse();
		$this->assertSame(200, $response->getStatusCode());

		$client->request('DELETE', '/organisations/Google');
		$response = $client->getResponse();
		$this->assertSame(204, $response->getStatusCode());

		$client->request('GET', '/organisations/Google');
		$response = $client->getResponse();
		$this->assertSame(404, $response->getStatusCode());
	}

	public function testUpdateOrganisationDescription() : void
	{
		$client = static::createClient();
		$client->request('PUT', '/organisations/Google',
			[],
			[],
			['CONTENT_TYPE' => 'application/json'],
			json_encode(['description' => 'updated'])
		);
		$response = $client->getResponse();
		$this->assertSame(200, $response->getStatusCode());

		$client->request('GET', '/organisations/Google');
		$response = $client->getResponse();
		$this->assertSame(200, $response->getStatusCode());
		$responseData = json_decode($response->getContent(), true);

		self::assertEquals('updated', $responseData['description']);
	}

	public function testUpdateOrganisationName() : void
	{
		$client = static::createClient();
		$client->request('PUT', '/organisations/Google',
			[],
			[],
			['CONTENT_TYPE' => 'application/json'],
			json_encode(['name' => 'Goooooogle'])
		);
		$response = $client->getResponse();
		$this->assertSame(200, $response->getStatusCode());

		$client->request('GET', '/organisations/Goooooogle');
		$response = $client->getResponse();
		$this->assertSame(200, $response->getStatusCode());
		$responseData = json_decode($response->getContent(), true);

		self::assertEquals('Goooooogle', $responseData['name']);
	}

	public function testUpdateOrganisationUsers() : void
	{
		$client = static::createClient();
		$client->request('PUT', '/organisations/Google',
			[],
			[],
			['CONTENT_TYPE' => 'application/json'],
			json_encode(['users' => [
				[
					'name' => 'brian stuff',
					'password' => 'whereisbrian',
					'role' => ["SUPER AMIN", "BUDDY"]
				]
			]])
		);
		$response = $client->getResponse();
		$this->assertSame(200, $response->getStatusCode());

		$client->request('GET', '/organisations/Google');
		$response = $client->getResponse();
		$this->assertSame(200, $response->getStatusCode());
		$responseData = json_decode($response->getContent(), true);

		self::assertEquals('brian stuff', $responseData['users'][0]['name']);
		self::assertEquals('whereisbrian', $responseData['users'][0]['password']);
		self::assertEquals(["SUPER AMIN", "BUDDY"], $responseData['users'][0]['role']);
	}

	public function testUpdateOrganisationUsersEmpty() : void
	{
		$client = static::createClient();
		$client->request('PUT', '/organisations/Google',
			[],
			[],
			['CONTENT_TYPE' => 'application/json'],
			json_encode(['users' => []])
		);
		$response = $client->getResponse();
		$this->assertSame(200, $response->getStatusCode());

		$client->request('GET', '/organisations/Google');
		$response = $client->getResponse();
		$this->assertSame(200, $response->getStatusCode());
		$responseData = json_decode($response->getContent(), true);

		self::assertEmpty($responseData['users']);
	}


	public function testCreateOrganisation() : void
	{
		$client = static::createClient();
		$client->request('POST', '/organisations',
			[],
			[],
			['CONTENT_TYPE' => 'application/json'],
			json_encode([
				'name' => 'Test org',
				'description' => 'some description',
				'users' => [
					[
						'name' => 'brian stuff',
						'password' => 'whereisbrian',
						'role' => ["SUPER AMIN", "BUDDY"]
					]
				]]
			)
		);
		$response = $client->getResponse();
		$this->assertSame(201, $response->getStatusCode());

		$client->request('GET', '/organisations/' . urlencode('Test org'));
		$response = $client->getResponse();
		$this->assertSame(200, $response->getStatusCode());
		$responseData = json_decode($response->getContent(), true);

		self::assertEquals('brian stuff', $responseData['users'][0]['name']);
		self::assertEquals('whereisbrian', $responseData['users'][0]['password']);
		self::assertEquals(["SUPER AMIN", "BUDDY"], $responseData['users'][0]['role']);
	}
}
