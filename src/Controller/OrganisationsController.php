<?php
namespace App\Controller;

use App\Entity\Organisation;
use App\Entity\User;
use App\Repository\OrganisationsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class OrganisationsController extends AbstractController
{
	private OrganisationsRepository $organisationsRepository;

	public function __construct(OrganisationsRepository $organisationsRepository)
	{
		$this->organisationsRepository = $organisationsRepository;
	}

	/**
	 * @Route("/organisations", methods={"GET"})
	 */
	public function getOrganisations() : JsonResponse
	{
		return new JsonResponse([
			'organisations' => $this->organisationsRepository->findAll()
		]);
	}

	/**
	 * @Route("/organisations/{name}", methods={"GET"})
	 */
	public function getOrganisation(string $name) : JsonResponse
	{
		$found = $this->organisationsRepository->findOneByName(urldecode($name));

		if (!$found) {
			return new JsonResponse(new NotFoundHttpException('This organisation does not exist'), 404);
		}

		return new JsonResponse($found);
	}

	/**
	 * @Route("/organisations/{name}", methods={"DELETE"})
	 */
	public function deleteOrganisation(string $name) : JsonResponse
	{
		try
		{
			$this->organisationsRepository->deleteOneByName(urldecode($name));

			return new JsonResponse(null, 204);
		}
		catch (\InvalidArgumentException $exception)
		{
			return new JsonResponse(new NotFoundHttpException('This organisation does not exist'), 404);
		}
		catch (\Exception $exception)
		{
			return new JsonResponse(new \HttpException('Something went wrong'), 500);
		}
	}

	/**
	 * @Route("/organisations/{name}", methods={"PUT"})
	 */
	public function updateOrganisation(string $name, Request $request)
	{
		$organisation = $this->organisationsRepository->findOneByName(urldecode($name));

		if (!$organisation) {
			return new JsonResponse(new NotFoundHttpException('This organisation does not exist'), 404);
		}

		$data = json_decode($request->getContent(), true);

		if (!empty($data['name']))
		{
			$organisation->setName($data['name']);
		}

		if (!empty($data['description']))
		{
			$organisation->setDescription($data['description']);
		}

		if (isset($data['users']))
		{
			$users = array_map(
				function(array $data)
				{
					return User::fromArray($data);
				},
				$data['users']
			);
			$organisation->setUsers($users);
		}

		try
		{
			$this->organisationsRepository->updateOneOrganisation(urldecode($name), $organisation);

			return new JsonResponse($organisation);
		}
		catch (\Exception $e)
		{
			return new JsonResponse($e, $e->getCode());
		}
	}


	/**
	 * @Route("/organisations", methods={"POST"})
	 */
	public function createOrganisation(Request $request)
	{
		$organisation = new Organisation();

		$data = json_decode($request->getContent(), true);

		if (!empty($data['name']))
		{
			$organisation->setName($data['name']);
		}

		if (!empty($data['description']))
		{
			$organisation->setDescription($data['description']);
		}

		if (isset($data['users']))
		{
			$users = array_map(
				function(array $data)
				{
					return User::fromArray($data);
				},
				$data['users']
			);
			$organisation->setUsers($users);
		}

		try
		{
			$this->organisationsRepository->createOrganisation($organisation);

			return new JsonResponse($organisation, 201);
		}
		catch (\Exception $e)
		{
			return new JsonResponse($e, $e->getCode());
		}
	}
}
