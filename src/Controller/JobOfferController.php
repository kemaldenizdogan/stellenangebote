<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Company;
use App\Entity\JobOffer;
use App\Service\RequestHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobOfferController extends AbstractController
{
    /**
     * @Route("/", name="job_offer.index")
     */
    public function index(Request $request, EntityManagerInterface $em, RequestHandler $requestHandler): Response
    {
        $filter_params = [
            'job_offer' => isset($request->get('filters')['job_offer']) ? $request->get('filters')['job_offer'] : null,
            'category_id' => isset($request->get('filters')['category_id']) ? $request->get('filters')['category_id'] : null,
            'company_id' => isset($request->get('filters')['company_id']) ? $request->get('filters')['company_id'] : null,
        ];

        $order_params = [
            'job_offer' => [
                'link' => $this->generateUrl('job_offer.index', $requestHandler->generateOrderLinkData('job_offer')),
                'direction' => $requestHandler->getOrderFlag('job_offer')
            ],
            'category' => [
                'link' => $this->generateUrl('job_offer.index', $requestHandler->generateOrderLinkData('category')),
                'direction' => $requestHandler->getOrderFlag('category')
            ],
            'company' => [
                'link' => $this->generateUrl('job_offer.index', $requestHandler->generateOrderLinkData('company')),
                'direction' => $requestHandler->getOrderFlag('company')
            ]
        ];

        $jobOffers = $em->getRepository(JobOffer::class)->findByRequest($filter_params, $order_params);

        $categories = $em->getRepository(Category::class)->findByHavingJobOffer();

        $companies = $em->getRepository(Company::class)->findByHavingJobOffer();

        return $this->render('job_offer/index.html.twig', compact('jobOffers', 'categories', 'companies', 'filter_params', 'order_params'));
    }
}
