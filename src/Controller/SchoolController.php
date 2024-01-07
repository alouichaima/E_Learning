<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Enseignant;
use App\Entity\Classe;
use App\Entity\Apprenant;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class SchoolController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/school', name: 'app_school')]
    public function index(Request $request,ManagerRegistry $doctrine,Classe $classe=null): Response
    {
        $entityManager = $doctrine->getManager();
  
        $allclasse=$doctrine->getRepository(Classe::class)->findAll();
        $enseignant = $doctrine->getRepository(Enseignant::class)->findAll();
        $apprenant = $doctrine->getRepository(Apprenant::class)->findAll();
        
        
  
        if($classe==null){
            $classe = new Classe();
        }
            // Creation du formulaire
            $form=$this->createFormBuilder($classe)
            // Configuration des paramètre du formulaire
                    ->add('nom',TextType::class)
                    ->add('image',FileType::class,array('data_class'=> null, 'label' => 'Image','label' => false ))
                    ->add('id_enseignant',EntityType::class, [
                        'class'=> Enseignant::class,
                        'choice_label'=>'nom',
                        'expanded'=>false,
                        'multiple'=>false
                    ])
                    ->add('id_apprenant',EntityType::class, [
                        'class'=> Apprenant::class,
                        'choice_label'=>'nom',
                        'expanded'=>false,
                        'multiple'=>false
                    ])
                    
                    ->getForm();
  
                    $form->handleRequest($request);
  
                    if($form->isSubmitted() && $form->isValid()){
                        $file = $form->get('image')->getData();
            
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();
                    try {
                        $file->move(
                            $this->getParameter('images_directory'),
                            $fileName
                        );
                    } catch (FileException $e) {
                    }   $classe->setImage($fileName);
                    
                        $entityManager->persist($classe);
                        $entityManager->flush();
                        return $this->redirectToRoute('app_school');
                    }
               
  
        return $this->render('school/index.html.twig', [
            'allclasse' => $allclasse,
            'enseignant' => $enseignant,
            'apprenant' => $apprenant,
            'form' =>$form->createView(),
            
            
        ]);
      
    }

      /**
     * @Route("getInfoClasse/{id}", name="getInfoClasse")
     */
    public function getInfoClasse($id):Response
    // ici on récupère toute les infos de la question enseignant en fct de l'id passé en paramtre
    {
        try{
            $user = $this->em->find(Classe::class,$id);
            $data=[
                "id"=>$user->getId(),
                "nom"=>$user->getNom(),
                "image"=>$user->getImage(),
                "id_enseignant"=>$user->getIdEnseignant()->getId(),
                "id_apprenant"=>$user->getIdApprenant()->getId()
            ];
            
            return $this->json($data);
        }catch(Exception $ex){
            return $this->json($ex->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("codeEditClasse/{id}", name="codeEditClasse")
     */
    public function codeEditClasse(Request $request,int $id) :Response
    {
        try
        {
            $user = $this->em->find(Classe::class,$id);
            
            
            $enseignant=$this->em->find(Enseignant::class,$request->request->get("id_enseignant"));
            $apprenant=$this->em->find(Apprenant::class,$request->request->get("id_apprenant"));
            
            //dump($request->request->get("nom"));
            $user->setNom($request->request->get("nom"));
            $user->setIdEnseignant($enseignant);
            $user->setIdApprenant($apprenant);
            $user->setImage($request->request->get("image"));
          
            $this->em->persist($user);
            $this->em->flush();

            return $this->json("success",Response::HTTP_OK);
        }
        catch(Exception $ex)
        {
            return $this->json($ex->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/delete_classe/{id}" , name="delete_classe")
     */
    public function deleteClasse($id,ManagerRegistry $doctrine) {

        $em = $this->getDoctrine()->getManager();
        $user=$doctrine->getRepository(Classe::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'Il n y aucune classe avec l id suivant: ' . $id
            );
        }

        $em->remove($user);
        $em->flush();
    
        return $this->redirect($this->generateUrl('app_school'));

    }
}
