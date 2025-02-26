<?php

namespace App\Controller;

use App\Entity\CompanyInformation;
use App\Entity\OrderSet;
use App\Entity\OrderSummary;
use App\Entity\Product;
use App\Entity\UserInformation;
use App\Form\CompanyInformationType;
use App\Form\OrderInformationType;
use App\Form\UserInformationType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShoppingCartController extends AbstractController
{
    #[Route("/shoppingcart", name:"shoppingcart")]
    public function cart(): Response
    {
        return $this->render('shoppingCart.html.twig', [
        ]);
    }

    #[Route("/cart/add/{id}", name: "add_cart")]
    public function cartAdd(string $id, Request $request): Response
    {
        $cart = $this->getCart();

        $product = $this->entityManager->find(Product::class, $id);

        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen', 'main');
        }

        if (isset($cart['products'][$id])) {
            $cart['products'][$id]++;
        } else {
            $cart['products'][$id] = 1;
        }

        $cart['total'] += $product->getPrice(); 

        $this->saveCart($cart);

        if ($request->query->has('toCart')) {
            return $this->redirectToRoute('shoppingcart');
        }

        return $this->redirectToRoute('tag', [
            'id' => $product->getTag()->getId()
        ]);
    }

    #[Route("/cart/remove/{id}", name: "remove_cart")]
    public function cartRemove(string $id): Response
    {
        $cart = $this->getCart();

        $product = $this->entityManager->find(Product::class, $id);

        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen', 'main');
        }

        if (isset($cart['products'][$id])) {

            $cart['products'][$id]--;

            if ($cart['products'][$id] == 0) {
                unset($cart['products'][$id]);
            }

            $cart['total'] -= $product->getPrice();

            $this->saveCart($cart);
        }

        return $this->redirectToRoute('shoppingcart');
    }

    #[Route("/cart/completeRemove/{id}", name: "complete_remove_cart")]
    public function completeRemove(string $id): Response
    {
        $cart = $this->getCart();

        $product = $this->entityManager->find(Product::class, $id);

        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen', 'main');
        }

        if (isset($cart['products'][$id])) {

            $quantity = $cart['products'][$id];

                unset($cart['products'][$id]);

            $cart['total'] -= $product->getPrice() * $quantity;
            
            $this->saveCart($cart);
        }

        return $this->redirectToRoute('shoppingcart');
    }

    #[Route("/cart/clear", name: "clear_cart")]
    public function clearCart(string $id): Response
    {
        $this->saveCart([
            'products' => [],
            'total' => 0
        ]);

        return $this->redirectToRoute('main');
    }

    #[Route("/shoppingcart-info", name:"shoppingcart_info")]
    public function cartInfo(Request $request): Response
    {

        $form = $this->createForm(OrderInformationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();            

            $session = $this->requestStack->getSession();
            $session->set('userInformation', $formData['userInformation']);

            $companyInformation = $formData['companyInformation'];
            if ($companyInformation !== null) {
                // TODO: dalsi osetreni company
                $session->set('companyInformation', $formData['companyInformation']);
            }

            return $this->redirectToRoute('shoppingcart_summary');
        }

        return $this->render('shoppingCartInfo.html.twig', [
            'form' => $form
        ]);
    }

    #[Route("/shoppingcart-summary", name:"shoppingcart_summary")]
    public function cartSummary(Request $request): Response
    {
        $session = $this->requestStack->getSession();
        $userInformation = $session->get('userInformation');

        if ($userInformation === null) {
            return $this->flashRedirect('error', 'Nebyly zadány informace k platbě', "shoppingcart_info");
        }

        $form = $this->createFormBuilder()
            ->add('submit', SubmitType::class, ['label' => 'Potvrdit objednávku'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $totalPrice = 0;

            $orderSummary = new OrderSummary(
                $totalPrice,
                $userInformation
            );

            foreach ($this->getCart()['products'] as $id => $quantity) {
                $product = $this->entityManager->find(Product::class, $id);
                if ($product === null) {
                    return $this->flashRedirect('error', 'Produkt nebyl nalezen', 'clearCart');
                }

                $orderSet = new OrderSet($quantity, $product->getPrice());

                $orderSummary->addOrderSet($orderSet);
                $product->addOrderSet($orderSet);

                $this->entityManager->persist($orderSet);
            }

            $companyInformation = $session->get('companyInformation');
            if ($companyInformation && $companyInformation->getCompanyName() !== null && $companyInformation->getIco() !== null && $companyInformation->getDic() !== null) {
                $companyInformation->setOrderSummary($orderSummary);
                $this->entityManager->persist($companyInformation);
            }

            $userInformation->setOrderSummary($orderSummary);

            $this->entityManager->persist($userInformation);
            $this->entityManager->persist($orderSummary);

            $this->entityManager->flush();

            return $this->flashRedirect('notice', 'Objednávka byla dokončena.', 'main');
        }

        return $this->render('shoppingCartSummary.html.twig', [
            'form' => $form,
            'userInformation' => $userInformation
        ]);
    }
}