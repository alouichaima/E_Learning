<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Enseignant;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class EnseignantController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

   
    /**
     * @Route("/enseignant" , name="app_enseignant")
     
     */
    
    // Cette fonction retourne tout les forums et creer un nouveau forum
  public function index(Request $request,ManagerRegistry $doctrine,Enseignant $enseignant=null){
      //Initialisation des paramètres
      $entityManager = $doctrine->getManager();

      $allenseignant=$doctrine->getRepository(Enseignant::class)->findAll();
        //dd($allenseignant);


      if($enseignant==null){
          $enseignant = new Enseignant();
      }
          // Creation du formulaire
          $form=$this->createFormBuilder($enseignant)
          // Configuration des paramètre du formulaire
                  ->add('email',EmailType::class)
                  ->add('password',PasswordType::class)
                  ->add('nom',TextType::class)
                  ->add('prenom',TextType::class)
                  ->add('image', FileType::class,array('data_class'=> null, 'label' => 'Image','label' => false ))
                  ->add('enregistrer',SubmitType::class)
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
               
            }   $enseignant->setImage($fileName);


                      $entityManager->persist($enseignant);
                      $entityManager->flush();
                      return $this->redirectToRoute('app_enseignant');
                  }


      return $this->render('enseignant/index.html.twig', [
          'allenseignant' => $allenseignant,
          'form' =>$form->createView(),
          'request' =>$request
          
      ]);

  }

  /**
     * @Route("/delete_enseignant/{id}" , name="delete_enseignant")
     */
    public function deleteEnseignant($id,ManagerRegistry $doctrine) {

        $em = $this->getDoctrine()->getManager();
        $user=$doctrine->getRepository(Enseignant::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'Il n y aucun enseignant avec l id suivant: ' . $id
            );
        }

        $em->remove($user);
        $em->flush();
    
        return $this->redirect($this->generateUrl('app_enseignant'));

    }

    /**
     * @Route("getInfoEnseignant/{id}", name="getInfoEnseignant")
     */
    public function getInfoEnseignant($id)
    // ici on récupère toute les infos de l'enseignant en fct de l'id passé en paramtre
    {
        try{

            $user = $this->em->getRepository(Enseignant::class)->getOneEnseignant((int)$id);
            

            return $this->json($user[0],Response::HTTP_OK);
        }catch(Exception $ex){
            return $this->json($ex->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

     /**
     * @Route("codeEditEnseignant", name="codeEditEnseignant")
     */
    public function codeEditEnseignant(Request $request)
    {
        try{
            $data = json_decode($request->getContent());

            $user = $this->em->find(Enseignant::class,(int)$data->id);
            $user->setEmail($data->email);
            //$user->setPassword($data->password);
            $user->setNom($data->nom);
            $user->setPrenom($data->prenom);
            $user->setImage($data->image);
           

            $this->em->persist($user);
            $this->em->flush();

            return $this->json("success",Response::HTTP_OK);
        }catch(Exception $ex){
            return $this->json($ex->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

}
