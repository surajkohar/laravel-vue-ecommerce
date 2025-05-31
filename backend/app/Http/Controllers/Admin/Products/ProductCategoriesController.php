<?php

/**
 * ProductCategories Class
 *
 * @package    ProductCategoriesController


 * @version    Release: 1.0.0
 * @since      Class available since Release 1.0.0
 */


namespace App\Http\Controllers\Admin\Products;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\Settings;
use App\Models\Admin\Permissions;
use App\Models\Admin\AdminAuth;
use App\Libraries\General;
use App\Models\Admin\Admins;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Libraries\FileSystem;
use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\ApiController;
use App\Models\Admin\ProductCategories;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\Log;

class ProductCategoriesController extends ApiController
{

    public function index(Request $request)
    {
        try {

            $where = [];

            if ($request->get('search')) {
                $search = $request->get('search');
                $search = '%' . $search . '%';
                $where[] = ['(product_categories.title LIKE ? or owner.name LIKE ?)', [$search, $search]];
            }

            // Date range filter
            if ($createdFrom = $request->get('createdFrom')) {
                $where[] = ['product_categories.created >= ?', date('Y-m-d', strtotime($createdFrom))];
            }


            if ($createdTo = $request->get('createdTo')) {
                $where[] = ['product_categories.created <= ?', date('Y-m-d 23:59:59', strtotime($createdTo))];
            }

            // Category filter
            if ($categoryIds = $request->get('category')) {
                $categoryIds = array_map('intval', (array)$categoryIds);
                $where[] = 'product_categories.parent_id IN (' . implode(',', $categoryIds) . ')';
            }

            // Admin filter
            if ($adminIds = $request->get('admins')) {
                $adminIds = array_map('intval', (array)$adminIds);
                $where[] = 'product_categories.created_by IN (' . implode(',', $adminIds) . ')';
            }

            $listing = ProductCategories::getListing($request, $where);

            return response()->json([
                'status' => true,
                'page' => $listing
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Somethings went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    function add(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                $data = $request->toArray();
                unset($data['_token']);

                $validator = Validator::make(
                    $request->toArray(),
                    [
                        'title' => [
                            'required',
                            Rule::unique('product_categories')->whereNull('deleted_at')
                        ],
                        'description' => [
                            'nullable'
                        ]

                    ]
                );

                if (!$validator->fails()) {
                    $category = ProductCategories::create($data);
                    if ($category) {
                        return response()->json([
                            'status' => 'true',
                            'message' => 'Category saved successfully.'
                        ], 200);
                    } else {
                        return response()->json([
                            'status' => 'false',
                            'message' => 'Category could not be save. Please try again.'
                        ], 500);
                    }
                } else {
                     return response()->json([
                            'status' => 'false',
                            'errors' => $validator->errors(),
                            'message' => 'Validation error'
                    ], 422);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Somethings went wrong',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    function edit(Request $request, $id)
    {
        $category = ProductCategories::get($id);
        if ($request->isMethod('put')) {
            $data = $request->toArray();
            $validator = Validator::make(
                $request->toArray(),
                [
                    'title' => [
                        'required',
                        Rule::unique('product_categories')->ignore($category->id)->whereNull('deleted_at'),
                    ],
                    'description' => [
                        'nullable'
                    ]
                ]
            );

            if (!$validator->fails()) {
                unset($data['_token']);
                if (ProductCategories::modify($id, $data)) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Category saved successfully'

                    ]);
                } else {
                    return response()->json([
                        'status' => true,
                        'message' => 'Category could not be save. Please try again.'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'false',
                    'errors' => $validator->errors(),
                    'message' => 'Validation error'
                ], 422);
            }
        }

        return response()->json([
            'status' => true,
            'category' => $category
        ]);
    }

    function view(Request $request, $id)
    {
    	if(!Permissions::hasPermission('product_categories', 'listing'))
    	{
    		$request->session()->flash('error', 'Permission denied.');
    		return redirect()->route('admin.dashboard');
    	}

    	$page = ProductCategories::get($id);
    	if($page)
    	{
	    	return view("admin/products/categories/view", [
    			'page' => $page
    		]);
		}
		else
		{
			abort(404);
		}
    }

    function delete(Request $request, $id)
    {
        try {
            $product = ProductCategories::find($id);
            if ($product->delete()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Category saved successfully'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Somethings went wrong',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    function bulkActions(Request $request, $action)
    {
    	if( ($action != 'delete' && !Permissions::hasPermission('product_categories', 'update')) || ($action == 'delete' && !Permissions::hasPermission('product_categories', 'delete')) )
    	{
    		$request->session()->flash('error', 'Permission denied.');
    		return redirect()->route('admin.dashboard');
    	}

    	$ids = $request->get('ids');
    	if(is_array($ids) && !empty($ids))
    	{
    		switch ($action) {
    			case 'delete':
    				ProductCategories::removeAll($ids);
    				$message = count($ids) . ' records has been deleted.';
    			break;
    		}

    		$request->session()->flash('success', $message);

    		return Response()->json([
    			'status' => 'success',
	            'message' => $message,
	        ], 200);
    	}
    	else
    	{
    		return Response()->json([
    			'status' => 'error',
	            'message' => 'Please select atleast one record.',
	        ], 200);
    	}
    }
}
