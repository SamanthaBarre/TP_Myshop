<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Form\RegistrationFormType;
use App\Repository\ProduitRepository;
use App\Security\AppUserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class HomeController extends AbstractController{

    #[Route("/", name:"home_index")]
    public function index(ManagerRegistry $doctrine) :Response{
        $produit =$doctrine->getRepository(Produit::class)->findAll();
        return $this->render('front/index.html.twig', ['produits' => $produit]);
    }

    

    #[Route("/panier" , name:"home_panier")]
 public function panier(SessionInterface $session, ProduitRepository $produitRepository): Response
    {

        $panier = $session->get("panier", []);

        $panierData = [];

        foreach ($panier as $id => $quantite) {
            $panierData[] = [
                "produit" => $produitRepository->find($id),
                "quantite" => $quantite
            ];
            
        }

        $total = 0; 
        foreach($panierData as $item){
            $totalItem = $item["produit"]->getPrix() * $item["quantite"];
            $total += $totalItem;
        }
        return $this->render('front/panier.html.twig', ['items' => $panierData, 'total' => $total]);
    }



    #[Route("/add_panier/{id}" , name:"home_add_panier")]
    public function add($id, SessionInterface $session)
    {   
        $panier = $session->get('panier', []);

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }

        
        $session->set('panier', $panier);
        return $this->redirectToRoute("home_panier");
    }
    
    #[Route("/panier/delete/{id}", name:"home_delete")]
    public function delete($id, SessionInterface $session){
        $panier = $session->get('panier', []);

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute('home_panier');
    }
        
    #[Route("/remove_panier/{id}" , name:"home_remove")]
    public function remove($id, SessionInterface $session){   
        $panier = $session->get('panier', []);

        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id]--;
            }else{
                unset($panier[$id]);
            }           
        }
        $session->set('panier', $panier);
        return $this->redirectToRoute("home_panier");
    }

    #[Route("/delete/all", name:"home_delete_all")]
    public function deleteAll(SessionInterface $session){
        $session->remove('panier');
        return $this->redirectToRoute('home_panier');
    }
    #[Route("/mes-commandes", name:"home_compte")]
    public function compte(){ 
        $commande = new Commande();
        $user = $doctrine->get();
        if ($user -> is_authenticated()){
            return $this->render("front/compte.html.twig");
        }else{
            return $this->render("security/login.html.twig");
        }
       


    }

    #[Route("/inscription", name:"home_register")]
    public function inscrpiption(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppUserAuthenticator $authenticator, EntityManagerInterface $entityManager){

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

       // dd($form->isSubmitted() && $form->isValid());
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )

            );
            $user->setRoles(['ROLE_MEMBRE']);
            

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('front/inscription.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    }
    