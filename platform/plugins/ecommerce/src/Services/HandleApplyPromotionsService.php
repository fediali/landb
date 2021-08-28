<?php

namespace Botble\Ecommerce\Services;

use Botble\Ecommerce\Models\Discount;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Repositories\Interfaces\DiscountInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Cart;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use OrderHelper;

class HandleApplyPromotionsService
{
    /**
     * @var DiscountInterface
     */
    protected $discountRepository;

    /**
     * @var ProductInterface
     */
    protected $productRepository;

    /**
     * HandleApplyPromotionsService constructor.
     * @param DiscountInterface $discountRepository
     * @param ProductInterface $productRepository
     */
    public function __construct(DiscountInterface $discountRepository, ProductInterface $productRepository)
    {
        $this->discountRepository = $discountRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param string $token
     * @return float
     */
    public function execute($token = null)
    {
        $promotions = $this->discountRepository->getAvailablePromotions();

        $promotionDiscountAmount = 0;

        $cart = Order::where('id', auth('customer')->user()->getUserCart())->with(['products' => function ($query) {
          $query->with(['product']);
        }])->first();

        foreach ($promotions as $promotion) {
            /**
             * @var Discount $promotion
             */
            switch ($promotion->type_option) {
                case 'amount':
                    switch ($promotion->target) {
                        case 'amount-minimum-order':
                            if ($promotion->min_order_price <= $cart->sub_total) {
                                $promotionDiscountAmount += $promotion->value;
                            }
                            break;
                        case 'all-orders':
                            $promotionDiscountAmount += $promotion->value;
                            break;
                        case 'product-variant' || 'category':
                            foreach ($promotion->products as $p_product){
                              foreach ($cart->products as $c_product){
                                if($p_product->id == $c_product->product_id){
                                  $promotionDiscountAmount += $c_product->qty * $promotion->value;
                                }
                              }
                            }
                            break;
                        default:
                            if ($cart->products->count() >= $promotion->product_quantity) {
                                $promotionDiscountAmount += $promotion->value;
                            }
                            break;
                    }
                    break;
                case 'percentage':
                    switch ($promotion->target) {
                        case 'amount-minimum-order':
                            if ($promotion->min_order_price <= $cart->sub_total) {
                                $promotionDiscountAmount += $cart->sub_total * $promotion->value / 100;
                            }
                            break;
                        case 'all-orders':
                            $promotionDiscountAmount += $cart->sub_total * $promotion->value / 100;
                            break;
                        case 'product-variant' || 'category':
                            foreach ($promotion->products as $p_product){
                              foreach ($cart->products as $c_product){
                                if($p_product->id == $c_product->product_id){
                                  $promotionDiscountAmount += ($c_product->qty * $c_product->price) * $promotion->value / 100;
                                }
                              }
                            }
                            break;
                        default:
                            if ($cart->products->count() >= $promotion->product_quantity) {
                                $promotionDiscountAmount += $cart->sub_total * $promotion->value / 100;
                            }
                            break;
                    }
                    break;
                case 'same-price':
                    if ($promotion->product_quantity > 1 && $cart->products->count() >= $promotion->product_quantity) {
                        foreach ($cart->products as $item) {
                            if ($item->qty >= $promotion->product_quantity) {
                                if (in_array($promotion->target, ['specific-product', 'product-variant']) &&
                                    in_array($item->id, $promotion->products()->pluck('product_id')->all())
                                ) {
                                    $promotionDiscountAmount += ($item->price - $promotion->value) * $item->qty;
                                } elseif ($product = $this->productRepository->findById($item->id)) {
                                    $productCollections = $product
                                        ->productCollections()
                                        ->pluck('ec_product_collections.id')->all();

                                    $discountProductCollections = $promotion
                                        ->productCollections()
                                        ->pluck('ec_product_collections.id')
                                        ->all();

                                    if (!empty(array_intersect($productCollections,
                                        $discountProductCollections))) {
                                        $promotionDiscountAmount += ($item->price - $promotion->value) * $item->qty;
                                    }
                                }
                            }
                        }
                    }
                    break;
            }
        }

        if (!$token) {
            $token = OrderHelper::getOrderSessionToken();
        }

        Arr::set($sessionData, 'promotion_discount_amount', $promotionDiscountAmount);
        OrderHelper::setOrderSessionData($token, $sessionData);

        return $promotionDiscountAmount;
    }
}
