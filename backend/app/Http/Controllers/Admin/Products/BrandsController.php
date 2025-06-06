<?php

/**
 * Pages Class
 *
 * @package    PagesController
 * @version    Release: 1.0.0
 * @since      Class available since Release 1.0.0
 */


namespace App\Http\Controllers\Admin\Products;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\Permissions;
use App\Models\Admin\Admins;
use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\ApiController;
use App\Models\Admin\Brands;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BrandsController extends ApiController
{

    function index(Request $request, $slug = null)
    {

		$sizes = Brands::all();

        return response()->json([
            'status' => true,
            'brands' => $sizes
        ],200);
    }

    function filters(Request $request)
    {
		$admins = [];
		$adminIds = Brands::distinct()->whereNotNull('created_by')->pluck('created_by')->toArray();
		if($adminIds)
		{
	    	$admins = Admins::getAll(
	    		[
	    			'admins.id',
	    			'admins.first_name',
	    			'admins.last_name',
	    			'admins.status',
	    		],
	    		[
	    			'admins.id in ('.implode(',', $adminIds).')'
	    		],
	    		'concat(admins.first_name, admins.last_name) desc'
	    	);
	    }
    	return [
	    	'admins' => $admins
    	];
    }

    function add(Request $request)
{
    Log::info($request->all());

    $request->validate([
        'brands' => 'required|array',
        'brands.*.name' => 'required|string|max:255',
    ]);

    Brands::truncate(); // clears table

    foreach ($request->brands as $brand) {
        $capitalizedName = strtoupper($brand['name']);
        Brands::create(['title' => $capitalizedName]); // use correct column name
    }

    return response()->json([
        'status' => true,
        'message' => 'Brands added successfully!'
    ], 200);
}


    function delete(Request $request, $id)
    {
    	$size = Brands::find($id);

    if (!$size) {
        return response()->json([
            'status' => false,
            'message' => 'Brands not found.'
        ], 404);
    }

    try {
        $size->delete();

        return response()->json([
            'status' => true,
            'message' => 'Record deleted successfully.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Failed to delete record.'
        ], 500);
    }
    }

}
