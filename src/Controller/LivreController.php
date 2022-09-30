<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Entity\User;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class LivreController extends AbstractController
{
    /**
     * @Route("/showlivres", name="livres")
     */
    public function index(ManagerRegistry $doctrine)
    {
        //$livres = new Livre();
        $repo = $doctrine->getRepository(Livre::class);
        $livres = $repo->findAll();

        return $this->render('livre/index.html.twig', [
            "livres" => $livres
        ]);
    }

    /**
     * @Route("/modifierLivre/{id}", name="modifierLivre")
     * @Route("/ajoutLivre",name="ajoutlivre")
     */
    public function add(ManagerRegistry $doctrine, Request $reqesut, UserInterface $user, Livre $livre=null)
    {
        if(!$livre){
            $livre = new Livre();
        }
       

        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($reqesut);
        if ($form->isSubmitted() && $form->isValid()) {
            $livre->setUser($user);
            $om  = $doctrine->getManager();
            $om->persist($livre);
            $om->flush();
            return $this->redirectToRoute("livres");
        }
        $mode = false;
        if($livre->getId() != null){
            $mode = true;
        }
        return $this->render('livre/add.html.twig', [
            
            "formulairelivre" => $form->createView(),
                "mode" =>$mode
        ]);
    }
    /**
     * @Route("/removelivre/{id}",name="removelivre")
     */
    public function remove(ManagerRegistry $doctrine, Livre $livre){
        $om = $doctrine->getManager();
        $om->remove($livre);
        $om->flush();
        return $this->redirectToRoute("livres");
    }

}
