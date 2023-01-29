<?php

namespace App\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerOrganisationsControllerTest extends WebTestCase
{
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
}
