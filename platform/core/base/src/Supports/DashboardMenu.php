<?php

namespace Botble\Base\Supports;

use BaseHelper;
use Botble\Menu\Models\MenuNode;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;
use RuntimeException;
use URL;

class DashboardMenu
{
    /**
     * Get all registered links from package
     * @var array
     */
    protected $links = [];

    /**
     * Add link
     * @param array $options
     * @return $this
     */
    public function registerItem(array $options): self
    {
        if (isset($options['children'])) {
            unset($options['children']);
        }

        $defaultOptions = [
            'id'          => '',
            'priority'    => 99,
            'parent_id'   => null,
            'name'        => '',
            'icon'        => null,
            'url'         => '',
            'children'    => [],
            'permissions' => [],
            'active'      => false,
        ];

        $options = array_merge($defaultOptions, $options);
        $id = $options['id'];

        if (!$id && !app()->runningInConsole() && app()->isLocal()) {
            $calledClass = isset(debug_backtrace()[1]) ?
                debug_backtrace()[1]['class'] . '@' . debug_backtrace()[1]['function']
                :
                null;
            throw new RuntimeException('Menu id not specified: ' . $calledClass);
        }

        if (isset($this->links[$id]) && $this->links[$id]['name'] && !app()->runningInConsole() && app()->isLocal()) {
            $calledClass = isset(debug_backtrace()[1]) ?
                debug_backtrace()[1]['class'] . '@' . debug_backtrace()[1]['function']
                :
                null;
            throw new RuntimeException('Menu id already exists: ' . $id . ' on class ' . $calledClass);
        }

        if (isset($this->links[$id])) {

            $options['children'] = array_merge($options['children'], $this->links[$id]['children']);
            $options['permissions'] = array_merge($options['permissions'], $this->links[$id]['permissions']);

            $this->links[$id] = array_replace($this->links[$id], $options);

            return $this;
        }

        if ($options['parent_id']) {
            if (!isset($this->links[$options['parent_id']])) {
                $this->links[$options['parent_id']] = ['id' => $options['parent_id']] + $defaultOptions;
            }

            $this->links[$options['parent_id']]['children'][] = $options;

            $permissions = array_merge($this->links[$options['parent_id']]['permissions'], $options['permissions']);
            $this->links[$options['parent_id']]['permissions'] = $permissions;
        } else {
            $this->links[$id] = $options;
        }

        return $this;
    }

    /**
     * @param array|string $id
     * @param null $parentId
     * @return $this
     */
    public function removeItem($id, $parentId = null): self
    {
        if ($parentId && !isset($this->links[$parentId])) {
            return $this;
        }

        $id = is_array($id) ? $id : func_get_args();
        foreach ($id as $item) {
            if (!$parentId) {
                Arr::forget($this->links, $item);
            } else {
                foreach ($this->links[$parentId]['children'] as $key => $child) {
                    if ($child['id'] === $item) {
                        Arr::forget($this->links[$parentId]['children'], $key);
                        break;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Rearrange links
     * @return Collection
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getAll(): Collection
    {
        $custom_menus = CustomDashboardMenu::where('status', 1)->whereNull('parent_id')->get()->toArray();
        $new_menus = [];
        foreach ($custom_menus as $custom_menu) {
            $new_menus[$custom_menu['id']] = $custom_menu;
        }
        $this->links = $new_menus;

//        $custom_menus = MenuNode::where('menu_id', 6)->where('parent_id', 0)->get()->toArray();
//        $new_menus = [];
//        foreach ($custom_menus as $custom_menu) {
//            $url =  $custom_menu['url'] != '/' ? url('/admin', $custom_menu['url']) : url('/admin');
//
//            $new_menus[$custom_menu['plugin_id']]['id'] = $custom_menu['plugin_id'];
//            $new_menus[$custom_menu['plugin_id']]['priority'] = $custom_menu['position'];
//            $new_menus[$custom_menu['plugin_id']]['parent_id'] = null;
//            $new_menus[$custom_menu['plugin_id']]['name'] = $custom_menu['title'];
//            $new_menus[$custom_menu['plugin_id']]['icon'] = $custom_menu['icon_font'];
//            $new_menus[$custom_menu['plugin_id']]['url'] = urldecode($url);
//            $new_menus[$custom_menu['plugin_id']]['permissions'] = json_decode($custom_menu['permissions']);
//            $new_menus[$custom_menu['plugin_id']]['active'] = false;
//            $new_menus[$custom_menu['plugin_id']]['children'] = count($custom_menu['children']) ? $this->setDashboardMenu($custom_menu['plugin_id'], $custom_menu['children']) : [];
//        }
//        $this->links = $new_menus;
        // dd($this->links);


        $currentUrl = URL::full();

        $prefix = request()->route()->getPrefix();
        if (!$prefix || $prefix === BaseHelper::getAdminPrefix()) {
            $uri = explode('/', request()->route()->uri());
            $prefix = end($uri);
        }

        $routePrefix = '/' . $prefix;

        if (setting('cache_admin_menu_enable', true) && Auth::check()) {
            $cacheKey = md5('cache-dashboard-menu-' . Auth::user()->getKey());
            if (!cache()->has($cacheKey)) {
                $links = $this->links;
                cache()->forever($cacheKey, $links);
            } else {
                $links = cache($cacheKey);
            }
        } else {
            $links = $this->links;
        }

        foreach ($links as $key => &$link) {
            if ($link['permissions'] && !Auth::user()->hasAnyPermission($link['permissions'])) {
                Arr::forget($links, $key);
                continue;
            }

            $link['active'] = $currentUrl == $link['url'] || (Str::contains($link['url'], $routePrefix) && !in_array($routePrefix, ['//', '/' . BaseHelper::getAdminPrefix()]));
            if (!count($link['children'])) {
                continue;
            }

            $link['children'] = collect($link['children'])->sortBy('priority')->toArray();

            foreach ($link['children'] as $subKey => $subMenu) {
                if ($subMenu['permissions'] && !Auth::user()->hasAnyPermission($subMenu['permissions'])) {
                    Arr::forget($link['children'], $subKey);
                    continue;
                }

                if ($currentUrl == $subMenu['url'] || Str::contains($currentUrl, $subMenu['url'])) {
                    $link['children'][$subKey]['active'] = true;
                    $link['active'] = true;
                }
            }
        }

        $menus = collect($links)->sortBy('priority');


        foreach ($menus as $menu) {
            $check = CustomDashboardMenu::where('menu_id', $menu['id'])->first();
            if (!$check) {
                $explode = explode('/admin/', $menu['url']);
                $last_word = isset($explode[1]) && !in_array($explode[1], ['admin', '#']) ? $explode[1] : '/';
                $dt = [
                    'menu_id' => $menu['id'],
                    'priority' => $menu['priority'],
                    'name' => $menu['name'],
                    'icon' => $menu['icon'],
                    'url' => $last_word,
                    'permissions' => json_encode($menu['permissions']),
                    'status' => 1,
                ];
                CustomDashboardMenu::create($dt);
                if (count($menu['children'])) {
                    foreach ($menu['children'] as $child) {
                        $checkC = CustomDashboardMenu::where('menu_id', $child['id'])->first();
                        if (!$checkC) {
                            $explode = explode('/admin/', $child['url']);
                            $last_word = isset($explode[1]) && !in_array($explode[1], ['admin', '#']) ? $explode[1] : '/';
                            $dtc = [
                                'menu_id' => $child['id'],
                                'priority' => $child['priority'],
                                'parent_id' => $child['parent_id'],
                                'name' => $child['name'],
                                'icon' => $child['icon'],
                                'url' => $last_word,
                                'permissions' => json_encode($child['permissions']),
                                'status' => 1,
                            ];
                            CustomDashboardMenu::create($dtc);
                        }
                    }
                }
            }
        }


//        foreach ($menus as $menu) {
//            $check = MenuNode::where('plugin_id', $menu['id'])->first();
//            if (!$check) {
//                $explode = explode('/admin/', $menu['url']);
//                $last_word = isset($explode[1]) && !in_array($explode[1], ['admin', '#']) ? $explode[1] : '/';
//                $dt = [
//                    'menu_id' => 6,
//                    'plugin_id' => $menu['id'],
//                    'position' => $menu['priority'],
//                    'title' => !is_array(trans($menu['name'])) ? trans($menu['name']) : $menu['name'],
//                    'icon_font' => $menu['icon'],
//                    'url' => $last_word,
//                    'permissions' => json_encode($menu['permissions']),
//                    'has_child' => count($menu['children']) ? 1 : 0,
//                    //'status' => 1,
//                ];
//                $parentMenu = MenuNode::create($dt);
//                if (count($menu['children'])) {
//                    foreach ($menu['children'] as $child) {
//                        $checkC = MenuNode::where('plugin_id', $child['id'])->first();
//                        if (!$checkC) {
//                            $explode = explode('/admin/', $child['url']);
//                            $last_word = isset($explode[1]) && !in_array($explode[1], ['admin', '#']) ? $explode[1] : '/';
//                            $dtc = [
//                                'menu_id' => 6,
//                                'plugin_id' => $child['id'],
//                                'position' => $child['priority'],
//                                'parent_id' => $parentMenu->id,
//                                'title' => !is_array(trans($child['name'])) ? trans($child['name']) : $child['name'],
//                                'icon_font' => $child['icon'],
//                                'url' => $last_word,
//                                'permissions' => json_encode($child['permissions']),
//                                //'status' => 1,
//                            ];
//                            MenuNode::create($dtc);
//                        }
//                    }
//                }
//            }
//        }


        return $menus;
    }

    public function setDashboardMenu($parentId, $children)
    {
        $menus = [];
        foreach ($children as $child) {
            $url = $child['url'] != '/' ? url('/admin', $child['url']) : url('/admin');

            $child_menu['id'] = $child['plugin_id'];
            $child_menu['priority'] = $child['position'];
            $child_menu['parent_id'] = $parentId;
            $child_menu['name'] = $child['title'];
            $child_menu['icon'] = $child['icon_font'];
            $child_menu['url'] = urldecode($url);
            $child_menu['permissions'] = json_decode($child['permissions']);
            $child_menu['active'] = false;
            $child_menu['children'] = count($child['children']) ? $this->setDashboardMenu($child['plugin_id'], $child['children']) : [];

            $menus[] = $child_menu;
        }
        return $menus;
    }

}
