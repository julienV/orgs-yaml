<?php
namespace App\Controller;

use App\Repository\OrganisationsRepository;
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
		$found = $this->organisationsRepository->findOneByName($name);

		if (!$found) {
			return new JsonResponse(new NotFoundHttpException('This organisation does not exist'), 404);
		}

		return new JsonResponse($found);
	}
}
