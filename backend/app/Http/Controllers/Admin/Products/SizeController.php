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
use Illuminate\Validation\Rule;
use App\Libraries\FileSystem;
use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\ApiController;
use App\Models\Admin\Colours;
use App\Models\Admin\Orders;
use App\Models\Admin\Settings;
use App\Models\Admin\Sizes;
use App\Models\Admin\StaffDocuments;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Size;

class SizeController extends ApiController
{

    function index(Request $request, $slug = null)
    {

		$sizes = Sizes::all();

        return response()->json([
            'status' => true,
            'sizes' => $sizes
        ],200);
    }

    function filters(Request $request)
    {
		$admins = [];
		$adminIds = Sizes::distinct()->whereNotNull('created_by')->pluck('created_by')->toArray();
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
    'sizes' => 'required|array',
    'sizes.*.name' => 'required|string|max:255',
]);


        Sizes::truncate();
        foreach ($request->sizes as $size) {
            $capitalizedSize = strtoupper($size['name']);
            Sizes::create(['size_title' => $capitalizedSize]);
        }


        return response()->json([
            'status' =>  true,
            'message' => 'Sizes added successfully!'
        ], 200);
    }

    function delete(Request $request, $id)
    {
    	$size = Sizes::find($id);

    if (!$size) {
        return response()->json([
            'status' => false,
            'message' => 'Size not found.'
        ], 404);
    }

    try {
        $size->delete();

        return response()->json([
            'status' => true,
            'message' => 'Size deleted successfully.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Failed to delete size.'
        ], 500);
    }
    }

}
