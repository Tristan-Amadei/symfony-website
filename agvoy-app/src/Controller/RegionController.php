<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Region;

/**
 * Controleur Region
 * @Route("/region")
 */
class RegionController extends AbstractController
{
    /**
     * @Route("/list", name="public_region_index", methods="GET")
     */
    public function region_index():Response
    {
        $em = $this->getDoctrine()->getManager();
        $regions = $em->getRepository('App:Region')->findAll();

        dump($regions);

        return $this->render('region/index.html.twig', [
            'regions' => $regions ]
        );
    }

    /**
     * Displays the rooms available in a region
     *
     * @Route("/{id}", name="public_region_show", requirements={ "id": "\d+"}, methods="GET")
     */
    public function region_show(Region $region):Response
    {
        $em = $this->getDoctrine()->getManager();
        $regions = $em->getRepository(Region::class)->findAll();

        dump($regions);

        return $this->render('region/show.html.twig', 
        ['region' => $region, 'rooms' => $region->getRooms()]);
    }    
}
