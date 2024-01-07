<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AvisRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Avis;
use App\Entity\Cours;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class AvisController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/avis', name: 'app_avis')]
    /*public function index(AvisRepository $avisRepository): Response
    {
        return $this->render('avis/index.html.twig', [
            'avis' => $avisRepository->findAll(),
        ]);
    }*/

    /*public function index(Request $request,ManagerRegistry $doctrine){
        //Initialisation des paramètres
     
  
        $allavis=$doctrine->getRepository(Avis::class)->findAll();
        

        return $this->render('avis/index.html.twig', [
            'avis' => $allavis
            
        ]);
  
    }*/

    
   
    /**
     * @Route("create_new_avis", name="create_new_avis")
     */

    public function createAvis(Request $request,ManagerRegistry $doctrine,Avis $avis=null,AvisRepository $avisRepository){
        //Initialisation des paramètres
        $entityManager = $doctrine->getManager();
                $a=$avisRepository->findAll();
        if($avis==null){
            $avis = new Avis();
        }
            // Creation du formulaire
            $form=$this->createFormBuilder($avis)
            // Configuration des paramètre du formulaire
                        ->add('user_name',TextType::class)
                        ->add('user_rating',HiddenType::class)
                        ->add('user_review',TextType::class)
                        ->add('enregistrer',SubmitType::class)
                        ->getForm();
  
                    $form->handleRequest($request);
  
                    if($form->isSubmitted() && $form->isValid()){
                        $entityManager->persist($avis);
                        $entityManager->flush();
                        return $this->redirectToRoute('app_avis');
                    }
  
  
        return $this->render('avis/index.html.twig', [
            'form' =>$form->createView(),
            'avis' =>$a
            
        ]);
  
    }

    /**
     * @Route("/delete_avis/{id}" , name="delete_avis")
     */
    public function deleteAvis($id,ManagerRegistry $doctrine) {

        $em = $this->getDoctrine()->getManager();
        $user=$doctrine->getRepository(Avis::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'Il n y aucun apprenant avec l id suivant: ' . $id
            );
        }

        $em->remove($user);
        $em->flush();
    
        return $this->redirect($this->generateUrl('app_avis'));

    }

    /**
     * @Route("getInfoAvis/{id}", name="getInfoAvis")
     */
    public function getInfoAvis($id)
    // ici on récupère toute les infos de l'enseignant en fct de l'id passé en paramtre
    {
        try{

            $user = $this->em->getRepository(Avis::class)->getOneAvis((int)$id);
            

            return $this->json($user[0],Response::HTTP_OK);
        }catch(Exception $ex){
            return $this->json($ex->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("codeEditAvis", name="codeEditAvis")
     */
    public function codeEditAvis(Request $request)
    {
        try{
            $data = json_decode($request->getContent());

            $user = $this->em->find(Avis::class,(int)$data->id);
            $user->setUserName($data->user_name);
            $user->setUserRating($data->user_rating);
            $user->setUserReview($data->user_review);
           

            $this->em->persist($user);
            $this->em->flush();

            return $this->json("success",Response::HTTP_OK);
        }catch(Exception $ex){
            return $this->json($ex->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }


      

}
