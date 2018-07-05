<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/ad", name="ads")
 */
class AdController extends Controller
{
    /**
     * @Route("/", name="_index")
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    /**
     * @Route("/new", name="_create")
     * @Security("is_granted('ROLE_USER')")
     */
    public function form(Request $request, ObjectManager $manager, Ad $ad = null, $slug = null) {
        $ad = new Ad();

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success', 
                "L'annonce a été créée avec succès ! <strong>Bravo !</strong>"
            );
            
            return $this->redirectToRoute('ads_show', ['slug' => $ad->getSlug()]);
        }

        return $this->render('ad/new.html.twig', [
            'adForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="_edit")
     * @Security("is_granted('ROLE_USER') and user === ad.getOwner()", message="Vous ne pouvez pas modifier une annonce qui n'est pas la votre !")
     */
    public function edit(Request $request, ObjectManager $manager, Ad $ad, string $slug) {
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            
            $this->addFlash(
                'success', 
                "L'annonce <a href='{$this->generateUrl('ads_show', ['slug' => $ad->getSlug()])}'>{$ad->getTitle()}</a> a été modifiée avec succès !"
            );

            if($ad->getSlug() !== $slug) {
                return $this->redirectToRoute('ads_edit', ['slug' => $ad->getSlug()]);
            }
        }

        return $this->render('ad/edit.html.twig', [
            'adForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/{slug}/delete", name="_delete")
     * @Security("is_granted('ROLE_USER') and ad.getOwner() === user")
     */
    public function delete(Ad $ad, string $slug, ObjectManager $manager){
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            'success', 
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !"
        );
        
        return $this->redirectToRoute('ads_index');
    }

    /**
     * @Route("/{slug}", name="_show")
     */
    public function show(Ad $ad) {
        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }

}