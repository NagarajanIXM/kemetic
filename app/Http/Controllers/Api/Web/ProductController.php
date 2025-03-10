<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Mixins\Cashback\CashbackRules;
use App\Models\AdvertisingBanner;
use App\Models\Api\Product;
use App\Models\Follow;
use App\Models\ProductCategory;
use App\Models\ProductSelectedSpecification;
use Illuminate\Http\Request;
use App\Models\ProductOrder;
use Exception;

class ProductController extends Controller
{
    public function index(Request $request)
    {

        $data = $request->all();
        $user = apiAuth();
        // print_r($user);die;

        // Default limit and offset values
        $limit = (int) $request->input('limit', 10); // Default limit is 10
        $offset = (int) $request->input('offset', 0); // Default offset is 0

        // Base query
        $query = Product::where('products.status', Product::$active)
            ->where('ordering', true)
            ->where('price', '!=', 0)
            ->orderBy('id', 'desc');

        // Apply any additional filters
        $query = $this->handleFilters($request, $query);

        // Get total count for pagination metadata
        $totalCount = $query->count();

        // Apply limit and offset
        $products = $query->skip($offset)->take($limit)->get();
        // print_r(count($products));die;

        foreach ($products as $key => $product) {
            if ($user && $product->checkUserHasBought($user)) {
                $product->purchaseStatus = true;
            }

            $seller = $product->creator;
            $followers = $seller->followers();

            $authUserIsFollower = false;
            if ($user) {
                // $authUserIsFollower = $followers->where('follower', auth()->id())
                $authUserIsFollower = $followers->where('follower', $user->id)
                    ->where('status', Follow::$accepted)
                    ->isNotEmpty();
            }

            $product->creator->userFollowerStatus = $authUserIsFollower;
        }

        if (!empty($data['category_id'])) {
            $selectedCategory = ProductCategory::where('id', $data['category_id'])->first();
        }

        return apiResponse2(
            1,
            'retrieved',
            trans('api.public.retrieved'),
            [
                'products' => ProductResource::collection($products),
                'limit' => $limit,
                'offset' => $offset,
                'count' => count($products),
            ]
        );
    }

    public function handleFilters(Request $request, $query, $isRewardProducts = false)
    {
        $search = $request->get('search', null);
        $isFree = $request->get('free', null);
        $isFreeShipping = $request->get('free_shipping', null);
        $withDiscount = $request->get('discount', null);
        $sort = $request->get('sort', null);
        $type = $request->get('type', null);
        $options = $request->get('options', null);
        $categoryId = (int) $request->get('category_id', null);
        $filterOption = $request->get('filter_option', null);

        if (!empty($search)) {
            $query->whereTranslationLike('title', '%' . $search . '%');
        }

        if (!empty($isFree) and $isFree == true) {
            $query->where(function ($qu) {
                $qu->whereNull('price')
                    ->orWhere('price', '0');
            });
        }

        if (!empty($isFreeShipping) and $isFreeShipping == true) {
            $query->where(function ($qu) {
                $qu->whereNull('delivery_fee')
                    ->orWhere('delivery_fee', '0');
            });
        }

        if (!empty($withDiscount) and $withDiscount == true) {
            $query->whereHas('discounts', function ($query) {
                $query->where('status', 'active')
                    ->where('start_date', '<', time())
                    ->where('end_date', '>', time());
            });
        }

        if (!empty($type) and count($type)) {
            $query->whereIn('type', $type);
        }

        if (!empty($options) and count($options)) {
            if (in_array('only_available', $options)) {
                $query->where(function ($query) {
                    $query->where('unlimited_inventory', true)
                        ->orWhereHas('productOrders', function ($query) {
                            $query->havingRaw('products.inventory > sum(quantity)')
                                ->whereNotNull('sale_id')
                                ->whereNotIn('status', [ProductOrder::$canceled, ProductOrder::$pending])
                                ->groupBy('product_id');
                        });
                });
            }

            if (in_array('with_point', $options)) {
                $query->whereNotNull('point');
            }
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($filterOption) and is_array($filterOption)) {
            $productIdsFilterOptions = ProductSelectedFilterOption::whereIn('filter_option_id', $filterOption)
                ->pluck('product_id')
                ->toArray();

            $productIdsFilterOptions = array_unique($productIdsFilterOptions);

            $query->whereIn('products.id', $productIdsFilterOptions);
        }

        if (!empty($sort)) {
            if ($sort == 'expensive') {
                if ($isRewardProducts) {
                    $query->orderBy('point', 'desc');
                } else {
                    $query->orderBy('price', 'desc');
                }
            }

            if ($sort == 'inexpensive') {
                if ($isRewardProducts) {
                    $query->orderBy('point', 'asc');
                } else {
                    $query->orderBy('price', 'asc');
                }
            }

            if ($sort == 'bestsellers') {
                $query->leftJoin('product_orders', function ($join) {
                    $join->on('products.id', '=', 'product_orders.product_id')
                        ->whereNotNull('product_orders.sale_id')
                        ->whereNotIn('product_orders.status', [ProductOrder::$canceled, ProductOrder::$pending]);
                })
                    ->select('products.*', DB::raw('sum(product_orders.quantity) as salesCounts'))
                    ->groupBy('product_orders.product_id')
                    ->orderBy('salesCounts', 'desc');
            }

            if ($sort == 'best_rates') {
                $query->leftJoin('product_reviews', function ($join) {
                    $join->on('products.id', '=', 'product_reviews.product_id');
                    $join->where('product_reviews.status', 'active');
                })
                    ->whereNotNull('rates')
                    ->select('products.*', DB::raw('avg(rates) as rates'))
                    ->groupBy('product_reviews.product_id')
                    ->orderBy('rates', 'desc');
            }
        }

        return $query;
    }

    public function show($id)
    {
        $user = apiAuth();

        $product = Product::where('status', Product::$active)
            ->where('id', $id)
            ->with([
                'selectedSpecifications' => function ($query) {
                    $query->where('status', ProductSelectedSpecification::$Active);
                    $query->with(['specification']);
                },
                'comments' => function ($query) {
                    $query->where('status', 'active');
                    $query->whereNull('reply_id');
                    $query->with([
                        'replies' => function ($query) {
                            $query->where('status', 'active');
                        }
                    ]);
                    $query->orderBy('created_at', 'desc');
                },
                'files' => function ($query) {
                    $query->where('status', 'active');
                    $query->orderBy('order', 'asc');
                },
                'reviews' => function ($query) {
                    // $query->where('status', 'active');
                    $query->with([
                        'comments' => function ($query) {
                            $query->where('status', 'active');
                        },
                    ]);
                },
            ])
            ->first();

        if (empty($product)) {
            abort(404);
        }

        $selectableSpecifications = $product->selectedSpecifications->where('allow_selection', true)
            ->where('type', 'multi_value');
        $selectedSpecifications = $product->selectedSpecifications->where('allow_selection', false);
        $seller = $product->creator;

        $cashbackRules = null;
        if (!empty($product->price) and getFeaturesSettings('cashback_active') and (empty($user) or !$user->disable_cashback)) {
            $cashbackRulesMixin = new CashbackRules($user);
            $cashbackRules = $cashbackRulesMixin->getRules('store_products', $product->id, $product->type, $product->category_id, $product->creator_id);
        }
        $product->cashbackRules = $cashbackRules;

        $resource = new ProductResource($product);
        $resource->show = true;
        return apiResponse2(
            1,
            'retrieved',
            trans('api.public.retrieved'),
            [
                'product' => $resource,

            ]
        );
    }


    public function getSortData()
    {
        $data = ['newest', 'expensive', 'inexpensive', 'best_rates', 'bestsellers'];
        return apiResponse2(
            1,
            'retrieved',
            trans('api.public.retrieved'),
            $data
        );
    }
}
