<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Categorie;
class CategorieController extends AbstractController
{
    
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/categorie', name: 'app_categorie')]
    public function index(Request $request,ManagerRegistry $doctrine,Categorie $categorie=null)
    {
        
        //Initialisation des paramètres
        $entityManager = $doctrine->getManager();
  
        $allcategorie=$doctrine->getRepository(Categorie::class)->findAll();
  
  
        if($categorie==null){
            $categorie = new Categorie();
        }
            // Creation du formulaire
            $form=$this->createFormBuilder($categorie)
            // Configuration des paramètre du formulaire
                    
                    ->add('nom',TextType::class)
                    ->add('enregistrer',SubmitType::class)
                    ->getForm();
  
                    $form->handleRequest($request);
  
                    if($form->isSubmitted() && $form->isValid()){
                        $entityManager->persist($categorie);
                        $entityManager->flush();
                        return $this->redirectToRoute('app_categorie');
                    }
  
  
        return $this->render('categorie/index.html.twig', [
            'allcategorie' => $allcategorie,
            'form' =>$form->createView(),
            'request' =>$request
            
        ]);
  
    }

    /**
     * @Route("/delete_categorie/{id}" , name="delete_categorie")
     */
    public function deleteCategorie($id,ManagerRegistry $doctrine) {

        $em = $this->getDoctrine()->getManager();
        $user=$doctrine->getRepository(Categorie::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'Il n y aucune categorie avec l id suivant: ' . $id
            );
        }

        $em->remove($user);
        $em->flush();
    
        return $this->redirect($this->generateUrl('app_categorie'));

    }

    /**
     * @Route("getInfoCategorie/{id}", name="getInfoCategorie")
     */
    public function getInfoCategorie($id)
    // ici on récupère toute les infos de l'enseignant en fct de l'id passé en paramtre
    {
        try{

            $user = $this->em->getRepository(Categorie::class)->getOneCategorie((int)$id);
            

            return $this->json($user[0],Response::HTTP_OK);
        }catch(Exception $ex){
            return $this->json($ex->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("codeEditCategorie", name="codeEditCategorie")
     */
    public function codeEditCategorie(Request $request)
    {
        try{
            $data = json_decode($request->getContent());

            $user = $this->em->find(Categorie::class,(int)$data->id);
            $user->setNom($data->nom);
            
           

            $this->em->persist($user);
            $this->em->flush();

            return $this->json("success",Response::HTTP_OK);
        }catch(Exception $ex){
            return $this->json($ex->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

}
