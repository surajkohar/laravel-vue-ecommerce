<?php

namespace App\Models\Admin;

use App\Models\AppModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Libraries\FileSystem;
use Illuminate\Support\Str;
use App\Libraries\General;

class Products extends AppModel
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $guarded = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'tags' => 'array',
    ];

    /**** ONLY USE FOR MAIN TALBLES NO NEED TO USE FOR RELATION TABLES OR DROPDOWNS OR SMALL SECTIONS ***/
    use SoftDeletes;

    public function category()
    {
        return $this->belongsTo(ProductCategories::class, 'category_id');
    }

    public function subcategories()
    {
        return $this->belongsToMany(
            ProductSubCategories::class, // your actual subcategory model
            'product_subcategories',     // pivot  table name
            'product_id',                // foreign key on pivot pointing to products
            'subcategory_id'            // foreign key on pivot pointing to subcategories
        );
    }



    public function brands()
    {
        return $this->belongsTo(Brands::class, 'brands_id');
    }

    public function colors()
    {
        return $this->belongsToMany(Colours::class, 'product_color');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    public function sizes()
    {
        return $this->belongsToMany(Sizes::class, 'product_sizes')->withPivot('quantity');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }


    /**
    * Products -> Users belongsTO relation
    *
    * @return Users
    */
    public function users()
    {
        return $this->belongsTo(users::class, 'user_id', 'id');
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
    * To search and get pagination listing
    * @param Request $request
    * @param $limit
    */

    public static function getListing(Request $request, array $where = [])
    {
        $orderBy = $request->get('sort_field', 'products.id');
        $direction = in_array(strtolower($request->get('sort_direction')), ['asc', 'desc'])
            ? $request->get('sort_direction')
            : 'desc';

        $page = max(1, (int)$request->get('page', 1));
        $limit = max(1, (int)$request->get('per_page', self::$paginationLimit));
        $offset = ($page - 1) * $limit;

        $query = Products::select([
            'products.*',
            'users.name as owner_first_name',
            'pc.title as category_title',
            'b.title as brand_title'
            // 'brands.title as brand',
        ])
            ->leftJoin('product_categories as pc','pc.id','=','products.category_id')
            ->leftJoin('brands as b','b.id','=','products.brands_id')
            ->leftJoin('users', 'users.id', '=', 'products.created_by')
            ->orderBy($orderBy, $direction);

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
    public static function getAll($select = [], $where = [], $orderBy = 'products.id desc', $limit = null)
    {
    	$listing = Products::orderByRaw($orderBy);

    	if(!empty($select))
    	{
    		$listing->select($select);
    	}
    	else
    	{
    		$listing->select([
    			'products.*'
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

	    $listing = $listing->get();

	    return $listing;
    }

    /**
    * To get single record by id
    * @param $id
    */
    public static function get($id)
    {
    	$record = Products::where('id', $id)
            ->with([
                'subCategories' => function($query) {
                    $query->select(['sub_category_title', 'sub_category_id']);
                },
                'brands' => function($query) {
                    $query->select(['brands.id', 'brands.title']);
                },
                'users' => function($query) {
                    $query->select(['id', 'first_name', 'last_name', 'status']);
                },
                'owner' => function($query) {
                    $query->select(['id', 'first_name', 'last_name', 'status']);
                },
                'sizes' => function($query) {
                    $query->select(['sizes.id', 'sizes.size_title', 'sizes.from_cm',  'sizes.to_cm', 'price','sale_price','color_id', 'product_sizes.status']);
                },
                'colors' => function($query) {
                    $query->select(['colours.id', 'colours.title', 'colours.color_code']);
                },
            ])
            ->first();

	    return $record;
    }

    /**
    * To get single row by conditions
    * @param $where
    * @param $orderBy
    */
    public static function getRow($where = [], $orderBy = 'products.id desc')
    {
    	$record = Products::orderByRaw($orderBy);

	    foreach($where as $query => $values)
	    {
	    	if(is_array($values))
                $record->whereRaw($query, $values);
            elseif(!is_numeric($query))
                $record->where($query, $values);
            else
                $record->whereRaw($values);
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
    	$product = new Products();

    	foreach($data as $k => $v)
    	{
    		$product->{$k} = $v;
    	}

        $product->created_by = AdminAuth::getLoginId();
    	$product->created = date('Y-m-d H:i:s');
    	$product->modified = date('Y-m-d H:i:s');
	    if($product->save())
	    {
            if(isset($data['title']) && $data['title'])
            {
                $product->slug = Str::slug($product->title) . '-' . General::encode($product->id);
                $product->save();
            }

	    	return $product;
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
    	$product = Products::find($id);
    	foreach($data as $k => $v)
    	{
    		$product->{$k} = $v;
    	}

    	$product->modified = date('Y-m-d H:i:s');
	    if($product->save())
	    {
            if(isset($data['title']) && $data['title'])
            {
                $product->slug = Str::slug($product->title) . '-' . General::encode($product->id);
                $product->save();
            }

	    	return $product;
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
    		return Products::whereIn('products.id', $ids)
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
    	$product = Products::find($id);
        $images = $product->getResizeImagesAttribute();
    	if($product->delete())
        {
            if($images)
            {
                foreach($images as $img)
                {
                    foreach($img as $i)
                    {
                        if($i && is_dir(public_path($i)) && file_exists(public_path($i)))
                        {
                            unlink(public_path($i));
                        }
                    }
                }
            }
            return true;
        }
        else
        {
            return false;
        }
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
    		foreach($ids as $id)
            {
                Products::remove($id);
            }

            return true;
	    }
	    else
	    {
	    	return null;
	    }

    }

    public static function handleBrands($id, $brands)
    {
        //Delete all first
        BrandProducts::where('product_id', $id)->delete();
        // Then Save
        foreach($brands as $c)
        {
            $relation = new BrandProducts();
            $relation->product_id = $id;
            $relation->brand_id = $c;
            $relation->created = date('Y-m-d H:i:s');
    	    $relation->modified = date('Y-m-d H:i:s');
            $relation->save();
        }
    }

    public static function handleColors($id, $colors)
    {
        ProductColors::where('product_id', $id)->delete();
        foreach($colors as $c)
        {
            $color = Colours::find($c);
            $relation = new ProductColors();
            $relation->product_id = $id;
            $relation->color_id = $c;
            $relation->color_title = $color->title;
            $relation->color_code = $color->code;
            $relation->created = date('Y-m-d H:i:s');
    	    $relation->modified = date('Y-m-d H:i:s');
            $relation->created_by = AdminAuth::getLoginId();
            $relation->save();
        }
    }


    public static function handleSubCategory($id, $subCategories)
    {
        //Delete all first
        ProductSubCategoryRelation::where('product_id', $id)->delete();
        // Then Save
        foreach($subCategories as $c)
        {
            $subCategory = ProductSubCategories::find($c);
            $relation = new ProductSubCategoryRelation();
            $relation->product_id = $id;
            $relation->sub_category_id = (int)$c;
            $relation->sub_category_title = $subCategory->title;
            $relation->save();
        }
        $product = Products::find($id);
    }

    public static function handleSizes($id, $sizesData)
    {
        ProductSizeRelation::where('product_id', $id)->delete();
        foreach ($sizesData as $colorId => $sizes) {
            if(!$colorId) continue;
            foreach ($sizes as $sizeData) {
                $size = Sizes::find($sizeData['id']);
                if ($size) {
                    if(!isset($sizeData['price']) || !$sizeData['price']) continue;
                    $relation = new ProductSizeRelation();
                    $relation->product_id = $id;
                    $relation->size_id = $size->id;
                    $relation->size_title = $size->size_title;
                    $relation->from_cm = $size->from_cm;
                    $relation->to_cm = $size->to_cm;
                    $relation->chest = $size->chest;
                    $relation->waist = $size->waist;
                    $relation->hip = $size->hip;
                    $relation->length = $size->length;
                    $relation->price = isset($sizeData['price']) ? $sizeData['price'] : null;
                    $relation->sale_price = isset($sizeData['sale_price']) ? $sizeData['sale_price'] : null;
                    $relation->color_id = $colorId;
                    $relation->status = isset($sizeData['status']) ? $sizeData['status'] : 0;
                    $relation->save();
                }
            }
        }
    }

}
