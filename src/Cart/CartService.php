<?php

namespace App\Cart;

use App\Cart\CartItem;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    protected function getCart(): array
    {
        return $this->session->get('cart', []); //tableau vide si cart n'existe pas
    }

    protected function saveCart(array $cart)
    {
        $this->session->set('cart', $cart);
    }

    public function empty()
    {
        $this->saveCart([]);
    }

    public function add(int $id)
    {
        //1. Retrouver le panier dans la session (sous la forme d'un tableau)
        //2. S'il n'existe pas encore, prendre un tableau vide
        $cart = $this->getCart();

        //3. Voir si le produit ($id) existe dans le tableau
        //4. Si c'est le cas, simplement augmenter la quantité
        //5. Sinon ajouter le produit avec la quantité 1
        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }

        $cart[$id]++;

        //6. Enregistrer le panier mis à jour dans la session
        $this->saveCart($cart);
    }

    public function remove(int $id)
    {
        $cart = $this->getCart();

        unset($cart[$id]);

        $this->saveCart($cart);
    }

    public function decrement(int $id)
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            return;
        }

        if ($cart[$id] === 1) { //Je pensais que ca planterait...
            $this->remove($id);
            return;
        }

        $cart[$id]--;

        $this->saveCart($cart);
        //Je pensais qu'il manquait un return...
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue; //Passer à l'itération suivante de la boucle
            }

            $total += $product->getPrice() * $qty;
        }

        return $total;
    }

    /**
     * @return CartItem[]
     */
    public function getDetailedCartItems(): array
    {
        $detailedCart = [];

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }

        return $detailedCart;
    }
}
