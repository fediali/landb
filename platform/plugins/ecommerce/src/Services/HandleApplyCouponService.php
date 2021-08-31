<?php

namespace Botble\Ecommerce\Services;

use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Repositories\Interfaces\DiscountInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Cart;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use OrderHelper;

class  HandleApplyCouponService
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
     * PublicController constructor.
     * @param DiscountInterface $discountRepository
     * @param ProductInterface $productRepository
     */
    public function __construct(DiscountInterface $discountRepository, ProductInterface $productRepository)
    {
        $this->discountRepository = $discountRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param string $coupon
     * @return array
     */
    public function execute(string $coupon)
    {
        $token = OrderHelper::getOrderSessionToken();

        if (!$token) {
            $token = OrderHelper::getOrderSessionToken();
        }

        $sessionData = OrderHelper::getOrderSessionData($token);

        $couponCode = trim($coupon);

        $discount = $this->getCouponData($couponCode, $sessionData);

        if (empty($discount)) {
            return [
                'error'   => true,
                'message' => trans('plugins/ecommerce::discount.invalid_coupon'),
            ];
        }

        if ($discount->target === 'customer') {
            $discountCustomers = $discount->customers()->pluck('customer_id')->all();
            if (!auth('customer')->check() ||
                !in_array(auth('customer')->user()->id, $discountCustomers)
            ) {
                return [
                    'error'   => true,
                    'message' => trans('plugins/ecommerce::discount.invalid_coupon'),
                ];
            }
        }

        if (!$discount->can_use_with_promotion && Arr::get($sessionData, 'promotion_discount_amount')) {
            return [
                'error'   => true,
                'message' => trans('plugins/ecommerce::discount.cannot_use_same_time_with_other_discount_program'),
            ];
        }
/*UPDATE BY TALHA*/
        /*$cartItems = Cart::instance('cart')->content();*/
        $cart = Order::where('id', auth('customer')->user()->getUserCart())->with(['products' => function ($query) {
          $query->with(['product']);
        }])->first();
        $couponDiscountAmount = 0;
        $isFreeShipping = false;

        if ($discount->type_option == 'shipping') {
            $sessionData['is_free_ship'] = true;
            $isFreeShipping = true;
            OrderHelper::setOrderSessionData($token, $sessionData);
        } elseif ($discount->type_option === 'amount' && $discount->discount_on === 'per-order') {
            $couponDiscountAmount = $discount->value;
        } else {
            switch ($discount->type_option) {
                case 'amount':
                    switch ($discount->target) {
                        case 'amount-minimum-order':
                            /*if ($discount->min_order_price <= Cart::instance('cart')->rawTotal()) {*/
                            if ($discount->min_order_price <= $cart->sub_total) {
                                $couponDiscountAmount += $discount->value;
                            }
                            break;
                        case 'all-orders':
                            $couponDiscountAmount += $discount->value;
                            break;
                        case 'product-variant' || 'category':
                          foreach ($discount->products as $p_product){
                            foreach ($cart->products as $c_product){
                              if($p_product->id == $c_product->product_id){
                                $couponDiscountAmount += $c_product->qty * $discount->value ;
                              }
                            }
                          }
                          break;
                        default:
                            /*if (Cart::instance('cart')->count() >= $discount->product_quantity) {*/
                            if ($cart->products->count() >= $discount->product_quantity) {
                                $couponDiscountAmount += $discount->value;
                            }
                            break;
                    }
                    break;
                case 'percentage':
                    switch ($discount->target) {
                        case 'amount-minimum-order':
                            /*if ($discount->min_order_price <= Cart::instance('cart')->rawTotal()) {*/
                            if ($discount->min_order_price <= $cart->sub_total) {
                                /*$couponDiscountAmount = Cart::instance('cart')->rawTotal() * $discount->value / 100;*/
                                $couponDiscountAmount = $cart->sub_total * $discount->value / 100;
                            }
                            break;
                        case 'all-orders':
                            /*$couponDiscountAmount = Cart::instance('cart')->rawTotal() * $discount->value / 100;*/
                            $couponDiscountAmount = $cart->sub_total * $discount->value / 100;
                            break;
                        case 'product-variant' || 'category':
                          foreach ($discount->products as $p_product){
                            foreach ($cart->products as $c_product){
                              if($p_product->id == $c_product->product_id){
                                $couponDiscountAmount = ($c_product->qty * $c_product->price) * $discount->value / 100;
                              }
                            }
                          }
                          break;
                        default:
                            /*if (Cart::instance('cart')->count() >= $discount->product_quantity) {*/
                            if ($cart->products->count() >= $discount->product_quantity) {
                                /*$couponDiscountAmount += Cart::instance('cart')->rawTotal() * $discount->value / 100;*/
                                $couponDiscountAmount += $cart->sub_total * $discount->value / 100;
                            }
                            break;
                    }
                    break;
                case 'same-price':
                    foreach ($cart->products as $item) {
                        if (in_array($discount->target, ['specific-product', 'product-variant']) &&
                            in_array($item->product_id, $discount->products()->pluck('product_id')->all())
                        ) {
                            $couponDiscountAmount += $item->price - $discount->value;
                        } elseif ($product = $this->productRepository->findById($item->id)) {
                            $productCollections = $product
                                ->productCollections()
                                ->pluck('ec_product_collections.id')
                                ->all();

                            $discountProductCollections = $discount
                                ->productCollections()
                                ->pluck('ec_product_collections.id')
                                ->all();

                            if (!empty(array_intersect($productCollections, $discountProductCollections))) {
                                $couponDiscountAmount += $item->price - $discount->value;
                            }
                        }
                    }
            }
        }

        if ($couponDiscountAmount < 0) {
            $couponDiscountAmount = 0;
        }

        Arr::set($sessionData, 'coupon_discount_amount', $couponDiscountAmount);
        OrderHelper::setOrderSessionData($token, $sessionData);

        session()->put('applied_coupon_code', $couponCode);

        return [
            'error' => false,
            'data'  => [
                'discount_amount'  => $couponDiscountAmount,
                'is_free_shipping' => $isFreeShipping,
            ],
        ];
    }

    /**
     * @param string $couponCode
     * @param array $sessionData
     * @return mixed
     */
    public function getCouponData($couponCode, $sessionData)
    {
        $couponCode = trim($couponCode);

        $discount = $this->discountRepository
            ->getModel()
            ->where('code', $couponCode)
            ->where('type', 'coupon')
            ->where('start_date', '<=', now())
            ->where(function ($query) use ($sessionData) {
                /**
                 * @var Builder $query
                 */
                $query
                    ->where(function ($sub) {
                        /**
                         * @var Builder $sub
                         */
                        return $sub
                            ->whereIn('type_option', ['amount', 'percentage'])
                            ->where(function ($subSub) {
                                /**
                                 * @var Builder $subSub
                                 */
                                return $subSub
                                    ->whereNull('end_date')
                                    ->orWhere('end_date', '>=', now());
                            });
                    })
                    ->orWhere(function ($sub) use ($sessionData) {
                        /**
                         * @var Builder $sub
                         */
                        return $sub
                            ->where('type_option', 'shipping')
                            ->where('value', '>=', Arr::get($sessionData, 'shipping_amount', 0))
                            ->where(function ($subSub) use ($sessionData) {
                                /**
                                 * @var Builder $subSub
                                 */
                                return $subSub
                                    ->whereNull('target')
                                    ->orWhere('target', 'all-orders');
                            });
                    })
                    ->orWhere(function ($sub) {
                        /**
                         * @var Builder $sub
                         */
                        return $sub
                            ->where('type_option', 'same-price')
                            ->whereIn('target', ['group-products', 'specific-product', 'product-variant']);
                    });
            })
            ->where(function ($query) {
                /**
                 * @var Builder $query
                 */
                return $query
                    ->whereNull('quantity')
                    ->orWhereRaw('quantity > total_used');
            })
            ->first();

        return $discount;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function applyCouponWhenCreatingOrderFromAdmin(Request $request)
    {
        $couponCode = trim($request->input('coupon_code'));

        $sessionData = [
            'shipping_amount' => $request->input('shipping_amount'),
            'state'           => $request->input('state'),
        ];

        $discount = $this->getCouponData($couponCode, $sessionData);

        if (empty($discount)) {
            return [
                'error'   => true,
                'message' => trans('plugins/ecommerce::discount.invalid_coupon'),
            ];
        }

        if ($discount->target == 'customer') {
            $discountCustomers = $discount->customers()->pluck('customer_id')->all();
            if (!in_array($request->input('customer_id'), $discountCustomers)) {
                return [
                    'error'   => true,
                    'message' => trans('plugins/ecommerce::discount.invalid_coupon'),
                ];
            }
        }

        if (!$discount->can_use_with_promotion && Arr::get($sessionData, 'promotion_discount_amount')) {
            return [
                'error'   => true,
                'message' => trans('plugins/ecommerce::discount.cannot_use_same_time_with_other_discount_program'),
            ];
        }

        $couponDiscountAmount = 0;
        $isFreeShipping = false;

        if ($discount->type_option == 'shipping') {
            $sessionData['is_free_ship'] = true;
            $isFreeShipping = true;
        } elseif ($discount->type_option === 'amount' && $discount->discount_on === 'per-order') {
            $couponDiscountAmount = $discount->value;
        } else {
            switch ($discount->type_option) {
                case 'amount':
                    switch ($discount->target) {
                        case 'amount-minimum-order':
                            if ($discount->min_order_price <= $request->input('sub_total')) {
                                foreach ($request->input('product_ids', []) as $cartId => $item) {
                                    $couponDiscountAmount += $discount->value;
                                }
                            }
                            break;
                        case 'all-orders':
                            foreach ($request->input('product_ids', []) as $cartId => $item) {
                                $couponDiscountAmount += $discount->value;
                            }
                            break;
                        default:
                            if (count($request->input('product_ids', [])) >= $discount->product_quantity) {
                                foreach ($request->input('product_ids', []) as $cartId => $item) {
                                    $couponDiscountAmount += $discount->value;
                                }
                            }
                            break;
                    }
                    break;
                case 'percentage':
                    switch ($discount->target) {
                        case 'amount-minimum-order':
                            if ($discount->min_order_price <= $request->input('sub_total')) {
                                $couponDiscountAmount = $request->input('sub_total') * $discount->value / 100;
                            }
                            break;
                        case 'all-orders':
                            $couponDiscountAmount = $request->input('sub_total') * $discount->value / 100;
                            break;
                        default:
                            if (count($request->input('product_ids', [])) >= $discount->product_quantity) {
                                $couponDiscountAmount += $request->input('sub_total') * $discount->value / 100;
                            }
                            break;
                    }
                    break;
                case 'same-price':
                    foreach ($request->input('product_ids', []) as $cartId => $item) {
                        if (in_array($discount->target, ['specific-product', 'product-variant']) &&
                            in_array($item->id, $discount->products()->pluck('product_id')->all())
                        ) {
                            $couponDiscountAmount += $item->price - $discount->value;
                        } else {
                            $product = $this->productRepository->findById($item->id);
                            if ($product) {
                                $productCollections = $product
                                    ->productCollections()
                                    ->pluck('ec_product_collections.id')
                                    ->all();

                                $discountProductCollections = $discount
                                    ->productCollections()
                                    ->pluck('product_collection_id')
                                    ->all();

                                if (!empty(array_intersect($productCollections, $discountProductCollections))) {
                                    $couponDiscountAmount += $item->price - $discount->value;
                                }
                            }
                        }
                    }
            }
        }

        if ($couponDiscountAmount < 0) {
            $couponDiscountAmount = 0;
        }

        return [
            'error' => false,
            'data'  => [
                'discount_amount'  => $couponDiscountAmount,
                'is_free_shipping' => $isFreeShipping,
            ],
        ];
    }


}
