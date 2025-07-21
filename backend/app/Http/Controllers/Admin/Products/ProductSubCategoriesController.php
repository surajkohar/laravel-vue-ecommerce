<?php
namespace App\Http\Controllers\Admin\Products;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\Permissions;
use Illuminate\Validation\Rule;
use App\Http\Controllers\ApiController;
use App\Models\Admin\ProductCategories;
use App\Models\Admin\ProductSubCategories;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\Log;
use Symfony\Contracts\Service\Attribute\Required;

class ProductSubCategoriesController extends ApiController
{

    public function index(Request $request)
    {
        try {
            $where = $this->setFilters($request);
            $listing = ProductSubCategories::getListing($request, $where);

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

    function setFilters($request)
    {
         $where = [];

            if ($request->get('search')) {
                $search = $request->get('search');
                $search = '%' . $search . '%';
                $where[] = ['(product_sub_categories.title LIKE ? or owner.name LIKE ?)', [$search, $search]];
            }

            // Date range filter
            if ($createdFrom = $request->get('createdFrom')) {
                $where[] = ['product_sub_categories.created >= ?', date('Y-m-d', strtotime($createdFrom))];
            }


            if ($createdTo = $request->get('createdTo')) {
                $where[] = ['product_sub_categories.created <= ?', date('Y-m-d 23:59:59', strtotime($createdTo))];
            }

            // Category filter
            if ($categoryIds = $request->get('category')) {
                $categoryIds = array_map('intval', (array)$categoryIds);
                $where[] = 'product_sub_categories.parent_id IN (' . implode(',', $categoryIds) . ')';
            }

            // Admin filter
            if ($adminIds = $request->get('admins')) {
                $adminIds = array_map('intval', (array)$adminIds);
                $where[] = 'product_sub_categories.created_by IN (' . implode(',', $adminIds) . ')';
            }

            return $where;
    }

    function add(Request $request)
    {
                Log::info('Request Data:', $request->all());
        try {
            if ($request->isMethod('post')) {
                $data = $request->toArray();
                unset($data['_token']);

                $validator = Validator::make(
                    $request->toArray(),
                    [
                        'title' => [
                            'required',
                            Rule::unique('product_sub_categories')->whereNull('deleted_at')
                        ],
                        'category_ids' =>[
                            'Required'
                        ],
                        'description' => [
                            'nullable'
                        ]

                    ]
                );

                if (!$validator->fails()) {
                    $category = ProductSubCategories::create($data);
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
        $subcategory = ProductSubCategories::get($id);
        $categories = ProductCategories::all();
        if ($request->isMethod('put')) {
            $data = $request->toArray();
            $validator = Validator::make(
                $request->toArray(),
                [
                    'title' => [
                        'required',
                        Rule::unique('product_sub_categories')->ignore($subcategory->id)->whereNull('deleted_at'),
                    ],
                    'category_ids' => [
                        'Required'
                    ],
                    'description' => [
                        'nullable'
                    ]
                ]
            );

            if (!$validator->fails()) {
                unset($data['_token']);
                if (ProductSubCategories::modify($id, $data)) {
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
            'subcategory' => $subcategory,
            'categories' => $categories
        ]);
    }

    function view(Request $request, $id)
    {

    	$page = ProductSubCategories::get($id);
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
            $product = ProductSubCategories::find($id);
            if ($product->delete()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Sub Category deleted successfully'
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

    public function bulkActions(Request $request, $action)
    {
        Log::info('Request Data:', $request->all());

        $ids = $request->get('ids');
        if (is_array($ids) && !empty($ids)) {
            switch ($action) {
                case 'delete':
                    $deleted = ProductSubCategories::removeAll($ids);
                    $message = $deleted . ' records have been deleted.';
                    break;
                case 'archive':
                    // Implement archive logic here
                    $message = count($ids) . ' records have been archived.';
                    break;
                default:
                    return Response()->json([
                        'status' => 'error',
                        'message' => 'Invalid action provided.',
                    ], 400);
            }

            return Response()->json([
                'status' => 'success',
                'message' => $message,
            ], 200);
        } else {
            return Response()->json([
                'status' => 'error',
                'message' => 'Please select at least one record.',
            ], 200);
        }
    }
}
