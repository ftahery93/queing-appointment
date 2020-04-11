<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\VendorDetail;

class Category extends Model {

    protected $guarded = [];
    
    public function getCategories(){

            $categories=Category::where('parent_id',0)->where('vendor_id',VendorDetail::getID())->get();//united
           
            $categories=$this->addRelation($categories);

            return $categories;

        }

        protected function selectChild($id)
        {
            $categories=Category::where('parent_id',$id)->get(); 
            
            $categories=$this->addRelation($categories);
               
            return $categories;

        }

        protected function addRelation($categories){

            $categories->map(function ($item, $key) {
                
                $sub=$this->selectChild($item->id); 
              
                return $item=array_add($item,'subCategory',$sub);

            });
           
            return $categories;
        }

}
