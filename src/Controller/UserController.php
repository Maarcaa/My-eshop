<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * Pour l'enregistrement d'un nouvel utilisateur, nous ne pouvons insérer le mdp en clair en BDD.
     * Pour cela, Symfony nous fournit un outil pour hasher (encrypter) le password.
     * Pour l'utiliser, nous avons jute à l'injecter comme dépendance (de notre fonction).
     * L'injection de dépendance se fait entre les parenthèses de la fonction.
     * 
     * @Route ("/inscription", name="user_register", methods={"GET|POST"})
     */
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        # On créé une nouvelle instance de notre class/entité User
        $user = new User();

        $form = $this->createForm(UserFormType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            # Nous settons les propriétés qui ne sont pas dans le form et donc auto-hydratées.
            # Les propriétés createdAt, et updatedAt attendent
            $user->setCreatedAt(new DateTime());
            $user->setUpdatedAt(new DateTime());
            # Pour assurer un rôle utilisateur à tous les utilisateurs, on set le rôle également.
            $user->setRoles(['ROLE_USER']);

            # On récupère la valeur de l'input password dans le formulaire
            $plainPassword = $form->get('password')->getData();

            # On re set le password du user en le hachant.
            #Pour hacher on utilise l'outil de hashage qu'on a injecté dans notre action $passwordHasher
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $plainPassword
                )
            );

            #Notre user est correctement setter, on peut 
            $entityManager->persist($user);
            $entityManager->flush();

            # Grâce à la méthode addflash () affiche et stock message twig dans la session destinés à l'utilisateur en front, il prend toujours 2 paramêtres, l'action et le message, le résultat est sous forme de 2 TABLEAUX 1 tableau pour action et 1 tableau pour le message, c'est pourquoi nous tuiliserons 2 boucles FOR pour l'affichage dans le fichier _flashes.html.twig
            $this->addFlash('success', 'Vous êtes inscrit avec succès');

            # On peut enfin return et rediriger l'utilisateur là ou on le souhaite
            return $this->redirectToRoute('app_login');
        } # end if()

        # On rend la vue qui contient le formulaire d'inscription
        return $this->render("user/register.html.twig", [
            'form_register' => $form->createView()
        ]);
    }# end function register()
}# end class
