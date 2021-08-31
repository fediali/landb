<?php

namespace Theme\Landb\Http\Controllers;

use App\Http\Controllers\Controller;

/*use Botble\Theme\Theme;*/

use App\Models\User;
use BaseHelper;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationItemInterface;
use Botble\Page\Models\Page;
use Botble\Page\Services\PageService;
use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Botble\Theme\Events\RenderingSingleEvent;
use Botble\Theme\Events\RenderingHomePageEvent;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Botble\Timeline\Models\Timeline;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Theme\Landb\Repositories\ProductsRepository;
use Response;
use SeoHelper;
use SiteMapManager;
use SlugHelper;
use Theme;
use Botble\Base\Enums\BaseStatusEnum;

/*use Botble\Theme\Http\Controllers\PublicController;*/

class ProductsController extends Controller
{
    private $productRepo;
    protected $slugRepository;

    public function __construct(ProductsRepository $productsRepo, SlugInterface $slugRepository)
    {
        $this->productRepo = $productsRepo;
        $this->slugRepository = $slugRepository;
    }

    public function getIndex()
    {

        $data = [
            'products' => $this->productRepo->getProductsByParams(['latest' => true, 'paginate' => true, 'array' => true])
        ];
        //dd($data['products']);
        if (request()->ajax()) {
            return response()->json(['products' => $this->getProductsListingHtml($data['products']), 'count' => count($data['products'])]);
        }
        return Theme::scope('products', $data)->render();
    }

    public function productsByCategory($category)
    {
        $slug = SlugHelper::getSlug($category, '');
        if ($slug) {
            return redirect()->route('public.single', $category);
        }
        $data = [
            'products' => $this->productRepo->getProductsByParams(['latest' => true, 'paginate' => true, 'array' => true, 'category_slug' => $category])
        ];


        if (request()->ajax()) {
            return response()->json(['products' => $this->getProductsListingHtml($data['products']), 'count' => count($data['products'])]);
        }

        return Theme::scope('products', $data)->render();
    }

    public function getDetails($slug, Request $request)
    {

        /*$data = [
            'product' => $this->productRepo->getProductsByParams(['first' => true, 'slug' => $slug, 'category' => true])
        ];
        if(!$data['product']){
          abort('404');
        }*/

        $slug = $this->slugRepository->getFirstBy([
            'key'            => $slug,
            'reference_type' => Product::class,
            'prefix'         => SlugHelper::getPrefix(Product::class),
        ]);

        if (!$slug) {
            abort(404);
        }

        $condition = [
            'ec_products.id'     => $slug->reference_id,
            'ec_products.status' => BaseStatusEnum::$PRODUCT['Active'],
        ];

        if (Auth::check() && request()->input('preview')) {
            Arr::forget($condition, 'status');
        }

        $data['product'] = get_products([
            'condition' => $condition,
            'take'      => 1,
            'with'      => [
                'defaultProductAttributes',
                'slugable',
                'tags',
                'tags.slugable'
            ],
        ]);

        if (!$data['product']) {
            abort(404);
        }
        $data['productVariations'] = $data['product']->variations()->with(['product'])->orderBy('is_default', 'desc')->get();
        foreach ($data['productVariations'] as $key => $variation) {
            if ($variation->product->quantity < 1) {
                $data['productVariations']->forget($key);
            }
        }
        //dd($data['product']->variations()->get());
        //dd($data['product']->price);
        if ($request->ajax()) {
            $values = $data['productVariations']->values();
            $data['productVariations'] = $values->all();
            return response()->json(['product' => $data['product'], 'productVariations' => $data['productVariations']], 200);
        } else {
            return Theme::scope('product', $data)->render();
        }
    }

    public function timeline(Request $request, $id = null)
    {
        $user = User::where('id', $id)->first();

        $tz = Carbon::now('America/Chicago')->toDateString();
        $date = Carbon::createFromFormat('Y-m-d', $tz)->toDateString();
        $product = Timeline::where('date', $date)->first();
        return Theme::scope('timeline', ['product' => $product, 'user' => $user])->render();
    }

    public function searchProducts(Request $request)
    {
        $keyword = $request->keyword;

        if (!empty($keyword)) {
            if (is_numeric($keyword)) {
                $data = [
                    'products' => $this->productRepo->getProductsByParams(['latest' => true, 'price_search' => $keyword, 'paginate' => true, 'array' => true])
                ];

            } else {
                $data = [
                    'products' => $this->productRepo->getProductsByParams(['latest' => true, 'name_search' => $keyword, 'paginate' => true, 'array' => true])
                ];
            }
            //dd($data['products']->hasPages());
            if (request()->ajax()) {
                return response()->json(['products' => $this->getProductsListingHtml($data['products']), 'count' => count($data['products'])]);
            }
            return Theme::scope('products', $data)->render();
        } else {
            return redirect()->route('public.products');
        }
    }

    public function getProductsListingHtml($products)
    {
        $html = '';
        foreach ($products as $product) {
            $html .= Theme::partial('product-card', ['product' => $product, 'col' => '4']);
        }
        return $html;
    }
}
