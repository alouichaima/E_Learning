<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Knp\Component\Pager\PaginatorInterface; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/user" , name="app_user")
     
     */
    public function index(ManagerRegistry $doctrine,User $user=null,Request $request): Response
    {

        $alluser=$doctrine->getRepository(User::class)->findAll();

        $entityManager = $doctrine->getManager();

        if($user==null){
            $user = new User();
        }
            // Creation du formulaire
            $form=$this->createFormBuilder($user)
            // Configuration des paramètre du formulaire
                    ->add('email',EmailType::class)
                    ->add('roles',ChoiceType::class, [
                        'choices' => [
                            'Utilisateur' => 'ROLE_USER',
                            'Administrateur' => 'ROLE_ADMIN'
                        ],
                        'expanded' => true,
                        'multiple' => true,
                        'label' => 'Rôles' 
                    ])
                    ->add('nom',TextType::class)
                    ->add('password',PasswordType::class)
                    ->add('prenom',TextType::class)
                    ->add('enregistrer',SubmitType::class)
                    ->getForm();
    
                    $form->handleRequest($request);
    
                    if($form->isSubmitted() && $form->isValid()){
                        $entityManager->persist($user);
                        $entityManager->flush();
                        return $this->redirectToRoute('app_user');
                    }

                  

        return $this->render('user/index.html.twig', [
            'alluser' => $alluser,
            'form' =>$form->createView()
            
        ]);
    }

    /**
     * @Route("/delete/{id}" , name="delete")
     */
    public function deleteUser($id,ManagerRegistry $doctrine) {

        $em = $this->getDoctrine()->getManager();
        $user=$doctrine->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'Il n y aucun utilisateur avec l id suivant: ' . $id
            );
        }

        $em->remove($user);
        $em->flush();
    
        return $this->redirect($this->generateUrl('app_user'));

    }

    /**
     * @Route("getInfoUser/{id}", name="getInfoUser")
     */
    public function getInfoUser($id)
    {
        try{

            $user = $this->em->getRepository(User::class)->getOneUser((int)$id);
            dd($user);
            return $this->json($user[0],Response::HTTP_OK);
        }catch(Exception $ex){
            return $this->json($ex->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

     /**
     * @Route("codeEditUser", name="codeEditUser")
     */
    public function codeEditUser(Request $request)
    {
        try{
            $data = json_decode($request->getContent());
                                
            $user = $this->em->find(User::class,(int)$data->id);
            $user->setEmail($data->email);
            //$user->setRoles($data->roles);
            $user->setNom($data->nom);
            //$user->setPassword($data->password);
            $user->setPrenom($data->prenom);
           

            $this->em->persist($user);
            $this->em->flush();

            return $this->json("success",Response::HTTP_OK);
        }catch(Exception $ex){
            return $this->json($ex->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }
           

}
