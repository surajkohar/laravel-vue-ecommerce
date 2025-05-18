<?php

/**
 * Pages Class
 *
 * @package    PagesController
 * @version    Release: 1.0.0
 * @since      Class available since Release 1.0.0
 */


namespace App\Http\Controllers\Admin;

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
    	// if(!Permissions::hasPermission('sizes', 'update'))
    	// {
    	// 	$request->session()->flash('error', 'Permission denied.');
    	// 	return redirect()->route('admin.dashboard');
    	// }

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
            'sizes.*' => 'string|max:255',
        ]);

        Sizes::truncate();
        foreach ($request->sizes as $size) {
            $capitalizedSize = strtoupper($size); // Store as uppercase
            Sizes::create(['size_title' => $capitalizedSize]); // Assuming your sizes table has a 'name' column
        }

        return response()->json(['message' => 'Sizes added successfully!'], 201);
    }

    function delete(Request $request, $id)
    {
    	if(!Permissions::hasPermission('sizes', 'delete'))
    	{
    		$request->session()->flash('error', 'Permission denied.');
    		return redirect()->route('admin.dashboard');
    	}

    	$admin = Sizes::find($id);
    	if($admin->delete())
    	{
    		$request->session()->flash('success', 'Staff deleted successfully.');
    		return redirect()->route('admin.size');
    	}
    	else
    	{
    		$request->session()->flash('error', 'Staff could not be delete.');
    		return redirect()->route('admin.size');
    	}
    }

}
