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
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    function add(Request $request)
    {
    	if(!Permissions::hasPermission('product_categories', 'create'))
    	{
    		$request->session()->flash('error', 'Permission denied.');
    		return redirect()->route('admin.dashboard');
    	}

    	if($request->isMethod('post'))
    	{
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

	        if(!$validator->fails())
	        {
	        	$category = ProductCategories::create($data);
	        	if($category)
	        	{
	        		$request->session()->flash('success', 'Product category created successfully.');
	        		return redirect()->route('admin.products.categories');
	        	}
	        	else
	        	{
	        		$request->session()->flash('error', 'Category could not be save. Please try again.');
		    		return redirect()->back()->withErrors($validator)->withInput();
	        	}
		    }
		    else
		    {
		    	$request->session()->flash('error', 'Please provide valid inputs.');
		    	return redirect()->back()->withErrors($validator)->withInput();
		    }
		}

		$categories = ProductCategories::getAll(
			[
				'product_categories.id',
				'product_categories.title'
			],
			[
				'status' => 1,
			],
			'product_categories.title desc'
		);

	    return view("admin/products/categories/add",[
			'categories' => $categories
		]);
    }

    function edit(Request $request, $id)
    {
    	if(!Permissions::hasPermission('product_categories', 'update'))
    	{
    		$request->session()->flash('error', 'Permission denied.');
    		return redirect()->route('admin.dashboard');
    	}

    	$category = ProductCategories::get($id);
    	if($category)
    	{
	    	if($request->isMethod('post'))
	    	{
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

		        if(!$validator->fails())
		        {
		        	unset($data['_token']);

	        		/** IN CASE OF SINGLE UPLOAD **/
		        	if(isset($data['image']) && $data['image'])
		        	{
		        		$oldImage = $category->image;
		        	}
		        	else
		        	{
		        		unset($data['image']);

		        	}
		        	/** IN CASE OF SINGLE UPLOAD **/
		        	if(ProductCategories::modify($id, $data))
		        	{
		        		/** IN CASE OF SINGLE UPLOAD **/
		        		if(isset($oldImage) && $oldImage)
		        		{
		        			FileSystem::deleteFile($oldImage);
		        		}
		        		/** IN CASE OF SINGLE UPLOAD **/

		        		$request->session()->flash('success', 'Product category updated successfully.');
		        		return redirect()->route('admin.products.categories');
		        	}
		        	else
		        	{
		        		$request->session()->flash('error', 'Category could not be save. Please try again.');
			    		return redirect()->back()->withErrors($validator)->withInput();
		        	}
			    }
			    else
			    {
			    	$request->session()->flash('error', 'Please provide valid inputs.');
			    	return redirect()->back()->withErrors($validator)->withInput();
			    }
			}

			$categories = ProductCategories::getAll(
				[
					'product_categories.id',
					'product_categories.title'
				],
				[
					'status' => 1,
				],
				'product_categories.title desc'
			);
		    return view("admin/products/categories/edit", [
		    		'categories' => $categories,
					'category' => $category
	    		]);
		}
		else
		{
			abort(404);
		}
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
    	if(!Permissions::hasPermission('product_categories', 'delete'))
    	{
    		$request->session()->flash('error', 'Permission denied.');
    		return redirect()->route('admin.dashboard');
    	}

    	$product = ProductCategories::find($id);
    	if($product->delete())
    	{
    		$request->session()->flash('success', 'Category deleted successfully.');
    		return redirect()->route('admin.products.categories');
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
