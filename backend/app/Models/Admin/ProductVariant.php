<?php

namespace App\Models\Admin;

use App\Models\AppModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Libraries\FileSystem;
use Illuminate\Support\Str;
use App\Libraries\General;
use Illuminate\Support\Facades\Log;

class ProductVariant extends AppModel
{
    protected $table = 'product_variants';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];


    /**
    * ProductVariant -> Product belongsToMany relation
    *
    * @return ProductVariant
    */
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

public function sizes()
{
    return $this->belongsToMany(Sizes::class, 'product_variant_sizes', 'variant_id', 'size_id')
                ->withPivot('price');
}


    public function images()
{
    return $this->hasMany(ProductVariantImage::class, 'variant_id');
}

    /**
    * Products -> Admins belongsTO relation
    *
    * @return Admins
    */
    public function owner()
    {
        return $this->belongsTo(Admins::class, 'created_by', 'id');
    }

    /**
    * Get resize images
    *
    * @return array
    */
    public function getResizeImagesAttribute()
    {
        return $this->image ? FileSystem::getAllSizeImages($this->image) : null;
    }

    /**
    * To search and get pagination listing
    * @param Request $request
    * @param $limit
    */

     public static function getListing(Request $request, array $where = [])
    {
        // Validate sorting parameters
        $orderBy = $request->get('sort_field', 'product_variants.id');
        $direction = in_array(strtolower($request->get('sort_direction')), ['asc', 'desc'])
            ? $request->get('sort_direction')
            : 'desc';

        // Pagination
        $page = max(1, (int)$request->get('page', 1));
        $limit = max(1, (int)$request->get('per_page', self::$paginationLimit));
        $offset = ($page - 1) * $limit;

        $query = ProductVariant::select([
                'product_variants.*',
                'owner.name as owner_first_name',
            ])
            ->leftJoin('users as owner', 'owner.id', '=', 'product_variants.created_by')
            ->orderBy($orderBy, $direction);

        // Apply where conditions
        foreach ($where as $condition) {
            if (is_array($condition)) {
                $query->whereRaw($condition[0], $condition[1]);
            } else {
                $query->whereRaw($condition);
            }
        }

        return $query->paginate($limit);
    }

    /**
    * To get all records
    * @param $where
    * @param $orderBy
    * @param $limit
    */
    public static function getAll($select = [], $where = [], $orderBy = 'product_variants.id desc', $limit = null)
    {
    	$listing = ProductVariant::orderByRaw($orderBy);

    	if(!empty($select))
    	{
    		$listing->select($select);
    	}
    	else
    	{
    		$listing->select([
    			'product_variants.*'
    		]);
    	}

	    if(!empty($where))
	    {
	    	foreach($where as $query => $values)
	    	{
	    		if(is_array($values))
	    			$listing->whereRaw($query, $values);
	    		elseif(!is_numeric($query))
                    $listing->where($query, $values);
                else
                    $listing->whereRaw($values);
	    	}
	    }

	    if($limit !== null && $limit !== "")
	    {
	    	$listing->limit($limit);
	    }

        $listing->orderByRaw($orderBy);

	    $listing = $listing->get();

	    return $listing;
    }

    /**
    * To get all records
    * @param $where
    * @param $orderBy
    * @param $limit
    */
    public static function getAllCategorySubCategory($ids = [])
    {
        $listing = ProductVariant::select([
                'id',
                'parent_id',
                'title',
                'slug'
            ])
            ->whereNotNull('parent_id');
        if($ids)
        {
            $listing->whereIn('id', $ids);
        }

        $subCategories = $listing->get();

        $finalSubCategories = [];
        foreach ($subCategories as $key => $value) {
            $finalSubCategories[$value->parent_id][] = $value;
        }

        $listing = ProductVariant::select([
                'id',
                'parent_id',
                'title'
            ])
            ->whereNull('parent_id');
        if($ids)
        {
            $listing->whereIn('id', $ids);
            if(!empty(array_keys($finalSubCategories)))
            {
                $listing->orWhereIn('id', array_keys($finalSubCategories));
            }
        }

        $categories = $listing->get();
        foreach($categories as $key => $value)
        {
            if(isset($finalSubCategories[$value->id]) && $finalSubCategories[$value->id])
            {
                $categories[$key]->sub_categories = $finalSubCategories[$value->id];
            }
        }
        return $categories;
    }

    /**
    * To get single record by id
    * @param $id
    */
    public static function get($id)
    {
    	$record = ProductVariant::where('id', $id)
            ->first();

	    return $record;
    }

    /**
    * To get single row by conditions
    * @param $where
    * @param $orderBy
    */
    public static function getRow($where = [], $orderBy = 'product_variants.id desc')
    {
    	$record = ProductVariant::orderByRaw($orderBy);

	    foreach($where as $query => $values)
	    {
	    	if(is_array($values))
                $listing->whereRaw($query, $values);
            elseif(!is_numeric($query))
                $listing->where($query, $values);
            else
                $listing->whereRaw($values);
	    }

	    $record = $record->limit(1)->first();

	    return $record;
    }

    /**
    * To insert
    * @param $where
    * @param $orderBy
    */
    public static function create($data)
    {
    	$category = new ProductVariant();

    	foreach($data as $k => $v)
    	{
    		$category->{$k} = $v;
    	}
        $category->created_by = AdminAuth::getLoginId();
    	$category->created = date('Y-m-d H:i:s');
    	$category->modified = date('Y-m-d H:i:s');
	    if($category->save())
	    {
            if(isset($data['title']) && $data['title'])
            {
                // Log::info('API Reques - ' . $data['title']);
                $category->slug = Str::slug($data['title']);
                $category->save();
            }
	    	return $category;
	    }
	    else
	    {
	    	return null;
	    }
    }

    /**
    * To update
    * @param $id
    * @param $where
    */
    public static function modify($id, $data)
    {
    	$category = ProductVariant::find($id);

    	foreach($data as $k => $v)
    	{
    		$category->{$k} = $v;
    	}

    	$category->modified = date('Y-m-d H:i:s');
	    if($category->save())
	    {
            if(isset($data['title']) && $data['title'])
            {
                $category->slug = Str::slug($category->title) . '-' . General::encode($category->id);
                $category->save();
            }
	    	return $category;
	    }
	    else
	    {
	    	return null;
	    }
    }


    /**
    * To update all
    * @param $id
    * @param $where
    */
    public static function modifyAll($ids, $data)
    {
    	if(!empty($ids))
    	{
    		return ProductVariant::whereIn('product_variants.id', $ids)
		    		->update($data);
	    }
	    else
	    {
	    	return null;
	    }

    }

    /**
    * To delete
    * @param $id
    */
    public static function remove($id)
    {
    	$category = ProductVariant::find($id);
    	return $category->delete();
    }

    /**
    * To delete all
    * @param $id
    * @param $where
    */
    public static function removeAll($ids)
    {
    	if(!empty($ids))
    	{
    		return ProductVariant::whereIn('product_variants.id', $ids)
		    		->delete();
	    }
	    else
	    {
	    	return null;
	    }

    }
}
