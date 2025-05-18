<?php

namespace App\Models\Admin;

use App\Models\AppModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Libraries\FileSystem;
use Illuminate\Support\Str;
use App\Libraries\General;

class Offers extends AppModel
{
    protected $table = 'offers';
    protected $primaryKey = 'id';
    public $timestamps = false;


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
    * Products -> Admins belongsTO relation
    * 
    * @return Admins
    */
    public function owner()
    {
        return $this->belongsTo(Admins::class, 'created_by', 'id');
    }

    public function getSizesAttribute($value)
    {
        return $value ? explode(',',$value) : null;
    }

    public function getColorsAttribute($value)
    {
        return $value ? explode(',',$value) : null;
    }

    /**
    * To search and get pagination listing
    * @param Request $request
    * @param $limit
    */

    public static function getListing(Request $request, $where = [])
    {
        $orderBy = $request->get('sort') ? $request->get('sort') : 'offers.id';
        $direction = $request->get('direction') ? $request->get('direction') : 'desc';
        $page = $request->get('page') ? $request->get('page') : 1;
        $limit = self::$paginationLimit;
        $offset = ($page - 1) * $limit;
        
        $listing = Offers::select([
            'offers.*',
            'owner.first_name as owner_first_name',
            'owner.last_name as owner_last_name'
        ])
        ->leftJoin('admins as owner', 'owner.id', '=', 'offers.created_by')
        ->orderBy($orderBy, $direction);

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

        // Put offset and limit in case of pagination
        if($page !== null && $page !== "" && $limit !== null && $limit !== "")
        {
            $listing->offset($offset);
            $listing->limit($limit);
        }
        
        $listing = $listing->paginate($limit);

        return $listing;
    }

    /**
    * To get all records
    * @param $where
    * @param $orderBy
    * @param $limit
    */
    public static function getAll($select = [], $where = [], $orderBy = 'offers.id desc', $limit = null)
    {
        $listing = Offers::orderByRaw($orderBy);

        if(!empty($select))
        {
            $listing->select($select);
        }
        else
        {
            $listing->select([
                'offers.*'
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
        $record = Offers::where('id', $id)
            ->with([
                'owner' => function($query) {
                    $query->select(['id', 'first_name', 'last_name', 'status']);
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
    public static function getRow($where = [], $orderBy = 'offers.id desc')
    {
        $record = Offers::orderByRaw($orderBy);

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
        $product = new Offers();

        foreach($data as $k => $v)
        {
            $product->{$k} = $v;
        }

        $product->created_by = AdminAuth::getLoginId();
        $product->created = date('Y-m-d H:i:s');
        $product->modified = date('Y-m-d H:i:s');
        if($product->save())
        {
            
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
        $product = Offers::find($id);
        foreach($data as $k => $v)
        {
            $product->{$k} = $v;
        }

        $product->modified = date('Y-m-d H:i:s');
        if($product->save())
        {
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
            return Offers::whereIn('offers.id', $ids)
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
        $product = Offers::find($id);
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
                Offers::remove($id);
            }

            return true;
        }
        else
        {
            return null;
        }

    }
}