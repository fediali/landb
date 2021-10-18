<?php

namespace Theme\Landb\Http\Controllers;

use App\Http\Controllers\Controller;

use BaseHelper;
use Botble\Page\Models\Page;
use Botble\SimpleSlider\Models\SimpleSlider;
use Botble\Theme\Events\RenderingSingleEvent;
use Theme\Landb\Repositories\ProductsRepository;
use Response;
use SeoHelper;
use SiteMapManager;
use SlugHelper;
use Theme;

class LandbController extends Controller
{
    private $productRepo;
    protected $homeSliderKey = 'home-slider';

    public function __construct(ProductsRepository $productsRepo)
    {
        $this->productRepo = $productsRepo;
    }

    public function getIndex()
    {
        $data = [
            'home_featured' => $this->productRepo->getProductsByParams(['is_featured' => true, 'limit' => 15, 'array' => true]),
            'latest_collection' => $this->productRepo->getProductsByParams(['latest' => true, 'limit' => 20, 'array' => true]),
            'slider' => $this->getHomeSlideshow()
        ];
        return Theme::scope('index', $data)->render();
    }

    public function orderSuccess()
    {
        return Theme::scope('orderSuccess', [])->render();
    }

    public function getHomeSlideshow()
    {
        $data = SimpleSlider::where('key', $this->homeSliderKey)->with(['sliderItems'])->first();
        return $data;
    }

    public function getView($key = null)
    {
        if (empty($key)) {
            return $this->getIndex();
        }

        $slug = SlugHelper::getSlug($key, '');

        if (!$slug) {
            abort(404);
        }

        if (defined('PAGE_MODULE_SCREEN_NAME')) {
            if ($slug->reference_type == Page::class && BaseHelper::isHomepage($slug->reference_id)) {
                return redirect()->to('/');
            }
        }

        $result = apply_filters(BASE_FILTER_PUBLIC_SINGLE_DATA, $slug);

        if (isset($result['slug']) && $result['slug'] == 'faqs') {
            $faq = Theme::partial('short-codes.faqs');

            $result['data']['page']->content = str_replace('[faqs][/faqs]', $faq, $result['data']['page']->content);
        }

        if (isset($result['slug']) && $result['slug'] !== $key) {
            return redirect()->route('public.single', $result['slug']);
        }

        event(new RenderingSingleEvent($slug));

        if (!empty($result) && is_array($result)) {
            return Theme::scope($result['view'], $result['data'])->render();
        }

        abort(404);
    }

}
