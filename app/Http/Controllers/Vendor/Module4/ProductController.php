<?php

namespace App\Http\Controllers\Vendor\Module4;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use DateTime;
use App\Models\Vendor\Product;
use App\Models\Vendor\Category;
use App\Models\Vendor\Option;
use App\Models\Vendor\OptionValue;
use App\Models\Vendor\ProductOptionValue;
use App\Models\Vendor\ProductDiscount;
use App\Models\Vendor\ProductSpecial;
use App\Models\Vendor\ProductCategory;
use App\Models\Vendor\ProductImage;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Helpers\VendorDetail;
use Image;

class ProductController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;
    protected $guard = 'vendor';
    protected $configName;

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:products');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M4');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('products-create');

//Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('products-delete');

//Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('products-edit');

        $Product = Product::
                select('products.id', 'products.image', 'products.name_en', 'products.model', 'products.price', 'products.quantity', 'products.status')
                ->get();



        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Product)
                            ->editColumn('image', function ($Product) {
                                return $Product->image != '' ? '<img src="' . url('public/products_images/' . $Product->image) . '" width="50" />' : '';
                            })
                            ->editColumn('status', function ($Product) {
                                return $Product->status == 1 ? '<div class="label label-success status" sid="' . $Product->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $Product->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($Product) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Product->id . '">';
                            })
                            ->editColumn('action', function ($Product) {
                                if ($this->EditAccess)
                                    return '<a href="' . url($this->configName . '/products') . '/' . $Product->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>'
                                            . ' <a href="' . url($this->configName . '/products') . '/' . $Product->id . '/uploadImages" class="btn btn-red tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Upload Images" data-original-title="Upload Images"><i class="entypo-picture"></i></a>';
                            })
                            ->make();
        }

        return view('fitflowVendor.module4.products.index')
                        ->with('CreateAccess', $this->CreateAccess)
                        ->with('DeleteAccess', $this->DeleteAccess)
                        ->with('EditAccess', $this->EditAccess);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('products-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        $subcate = new Category;
        try {
            $allSubCategories = $subcate->getCategories();
        } catch (Exception $e) {
            //no parent category found
        }

        //options
        $options = Option::where('vendor_id', VendorDetail::getID())->get();

        return view('fitflowVendor.module4.products.create', compact('allSubCategories'))
                        ->with('options', $options);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        // validate
        $validator = Validator::make($request->all(), [
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'price' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
                    'quantity' => 'required|numeric',
                    'model' => 'required',
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'category_id.*' => 'required',
                    'options_id.*' => 'sometimes|required',
                    'option_value_id.*' => 'sometimes|required',
                    'option_value_quantity.*' => 'sometimes|required',
                    'option_value_price_prefix.*' => 'sometimes|required',
                    'option_value_price.*' => 'sometimes|required',
                    'discount_quantity.*' => 'sometimes|required',
                    'discount_price.*' => 'sometimes|required',
                    'discount_start_date.*' => 'sometimes|required',
                    'discount_end_date.*' => 'sometimes|required',
                    'special_price.*' => 'sometimes|required',
                    'special_start_date.*' => 'sometimes|required',
                    'special_end_date.*' => 'sometimes|required',
        ]);

        // validation failed
        if ($validator->fails()) {
            return redirect($this->configName . '/products/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->only('name_en', 'name_ar', 'price', 'description_en', 'description_ar', 'price', 'quantity', 'status', 'sort_order', 'model', 'location');
            // Image 
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $thumbnailImage = Image::make($image);
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('products_images/');
                $thumbnailImage->resize(config('global.productImageW'), config('global.productImageH'), function ($constraint) {
                    $constraint->aspectRatio();
                });
                // Canvas image
                $canvas = Image::canvas(config('global.productImageW'), config('global.productImageH'));
                $canvas->insert($thumbnailImage, 'center');
                $canvas->save($destinationPath . $filename);
                $input['image'] = $filename;
            }
            $input['vendor_id'] = VendorDetail::getID();

            //Sort order default 0
            if (!$request->sort_order || $request->sort_order == '')
                $input['sort_order'] = 0;

            $id = Product::create($input)->id;

            //add options value in product_option_value table
            $count = count($request->options_id);
            for ($i = 0; $i < $count; $i++) {
                $option_value_array['product_id'] = $id;
                $option_value_array['option_id'] = $request->options_id[$i];
                $option_value_array['option_value_id'] = $request->option_value_id[$i];
                $option_value_array['quantity'] = $request->option_value_quantity[$i];
                $option_value_array['price_prefix'] = $request->option_value_price_prefix[$i];
                $option_value_array['price'] = $request->option_value_price[$i];
                ProductOptionValue::create($option_value_array);
            }

            //add category  in product_to_category  table
            $count_category = count($request->category_id);
            for ($i = 0; $i < $count_category; $i++) {
                $category_value_array['product_id'] = $id;
                $category_value_array['category_id'] = $request->category_id[$i];
                ProductCategory::create($category_value_array);
            }

            //add Special price  in product_special  table
            $count_specail = count($request->special_price);
            $datetime = new DateTime();
            for ($i = 0; $i < $count_specail; $i++) {
                $special_value_array['product_id'] = $id;
                $special_value_array['price'] = $request->special_price[$i];
                $special_value_array['start_date'] = $request->special_start_date[$i];
                $special_value_array['end_date'] = $request->special_end_date[$i];

                ProductSpecial::create($special_value_array);
            }

            //add Discount price  in product_discount  table
            $count_discount = count($request->discount_quantity);
            $datetime = new DateTime();
            for ($i = 0; $i < $count_discount; $i++) {
                $discount_value_array['product_id'] = $id;
                $discount_value_array['quantity'] = $request->discount_quantity[$i];
                $discount_value_array['price'] = $request->discount_price[$i];
                $discount_value_array['start_date'] = $request->discount_start_date[$i];
                $discount_value_array['end_date'] = $request->discount_end_date[$i];

                ProductDiscount::create($discount_value_array);
            }

            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Product - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect($this->configName . '/products');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $id = $request->id;
        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('products-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $Product = Product::find($id);
        $productOptionValue = ProductOptionValue::where('product_id', $id)->get();
        $productOptionValueCount = ProductOptionValue::where('product_id', $id)->count();
        $productCategory = ProductCategory::select('category_id')->where('product_id', $id)->get();
        $productSpecial = ProductSpecial::where('product_id', $id)->get();
        $productDiscount = ProductDiscount::where('product_id', $id)->get();
        $productDiscountCount = ProductDiscount::where('product_id', $id)->count();

        //options
        $options = Option::where('vendor_id', VendorDetail::getID())->get();


        $subcate = new Category;
        try {
            $allSubCategories = $subcate->getCategories();
        } catch (Exception $e) {
            //no parent category found
        }

        // show the edit form and pass the nerd
        return View::make('fitflowVendor.module4.products.edit', compact('allSubCategories'))
                        ->with('Product', $Product)
                        ->with('productOptionValue', $productOptionValue)
                        ->with('productOptionValueCount', $productOptionValueCount)
                        ->with('productCategory', $productCategory)
                        ->with('productSpecial', $productSpecial)
                        ->with('productDiscount', $productDiscount)
                        ->with('productDiscountCount', $productDiscountCount)
                        ->with('options', $options);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $id = $request->id;

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('products-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $Product = Product::findOrFail($id);
            $Product->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $Product = Product::findOrFail($id);

        // validate    
        $validator = Validator::make($request->all(), [
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'price' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
                    'quantity' => 'required|numeric',
                    'model' => 'required',
                    'category_id.*' => 'required',
                    'options_id.*' => 'sometimes|required',
                    'option_value_id.*' => 'sometimes|required',
                    'option_value_quantity.*' => 'sometimes|required',
                    'option_value_price_prefix.*' => 'sometimes|required',
                    'option_value_price.*' => 'sometimes|required',
                    'discount_quantity.*' => 'sometimes|required',
                    'discount_price.*' => 'sometimes|required',
                    'discount_start_date.*' => 'sometimes|required',
                    'discount_end_date.*' => 'sometimes|required',
                    'special_price.*' => 'sometimes|required',
                    'special_start_date.*' => 'sometimes|required',
                    'special_end_date.*' => 'sometimes|required',
        ]);

        // Image Validate
        //If Uploaded Image removed
        if ($request->uploaded_image_removed != 0) {
            $validator = Validator::make($request->only(['image']), [
                        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
        }

        // validation failed
        if ($validator->fails()) {
            return redirect($this->configName . '/products/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->only('name_en', 'name_ar', 'price', 'description_en', 'description_ar', 'price', 'quantity', 'status', 'sort_order', 'model', 'location');
            $input['vendor_id'] = VendorDetail::getID();

            //If Uploaded Image removed           
            if ($request->uploaded_image_removed != 0 && !$request->hasFile('image')) {
                //Remove previous images
                $destinationPath = public_path('products_images/');
                if (file_exists($destinationPath . $Product->image) && $Product->image != '') {
                    unlink($destinationPath . $Product->image);
                }
                $input['image'] = '';
            } else {
                //Icon Image 
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $thumbnailImage = Image::make($image);
                    $filename = time() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('products_images/');
                    $thumbnailImage->resize(config('global.productImageW'), config('global.productImageH'), function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    // Canvas image
                    $canvas = Image::canvas(config('global.productImageW'), config('global.productImageH'));
                    $canvas->insert($thumbnailImage, 'center');
                    $canvas->save($destinationPath . $filename);

                    //Remove previous images
                    if (file_exists($destinationPath . $Product->image) && $Product->image != '') {
                        unlink($destinationPath . $Product->image);
                    }
                    $input['image'] = $filename;
                }
            }

            //Sort order default 0
            if (!$request->sort_order || $request->sort_order == '')
                $input['sort_order'] = 0;

            $Product->fill($input)->save();

            //add options value in product_option_value table
            $count = count($request->options_id);
            for ($i = 0; $i < $count; $i++) {
                $option_value_array['product_id'] = $id;
                $option_value_array['option_id'] = $request->options_id[$i];
                $option_value_array['option_value_id'] = $request->option_value_id[$i];
                $option_value_array['quantity'] = $request->option_value_quantity[$i];
                $option_value_array['price_prefix'] = $request->option_value_price_prefix[$i];
                $option_value_array['price'] = $request->option_value_price[$i];

                if (!$request->product_option_value_id[$i] || $request->product_option_value_id[$i] == '') {
                    ProductOptionValue::create($option_value_array);
                } else {
                    ProductOptionValue::updateOrCreate(['product_option_value_id' => $request->product_option_value_id[$i]], $option_value_array);
                }
            }
            
            //add category  in product_to_category  table
            $count_category = count($request->category_id);
            //delete all category then add new one      
             $delete = DB::table('product_to_category')->where('product_id', $id)->delete();
            for ($i = 0; $i < $count_category; $i++) {               
                $category_value_array['product_id'] = $id;
                $category_value_array['category_id'] = $request->category_id[$i];
                
                    ProductCategory::create($category_value_array);
            }
            
            //add Special price  in product_special  table
            $count_specail = count($request->special_price);
            $datetime = new DateTime();
            for ($i = 0; $i < $count_specail; $i++) {
                $special_value_array['product_id'] = $id;
                $special_value_array['price'] = $request->special_price[$i];
                $special_value_array['start_date'] = $request->special_start_date[$i];
                $special_value_array['end_date'] = $request->special_end_date[$i];

                if (!$request->special_id[$i] || $request->special_id[$i] == '') {
                    ProductSpecial::create($special_value_array);
                } else {
                    ProductSpecial::updateOrCreate(['product_special_id' => $request->special_id[$i]], $special_value_array);
                }
            }
           

            //add Discount price  in product_discount  table
            $count_discount = count($request->discount_quantity);
            $datetime = new DateTime();
            for ($i = 0; $i < $count_discount; $i++) {
                $discount_value_array['product_id'] = $id;
                $discount_value_array['quantity'] = $request->discount_quantity[$i];
                $discount_value_array['price'] = $request->discount_price[$i];
                $discount_value_array['start_date'] = $request->discount_start_date[$i];
                $discount_value_array['end_date'] = $request->discount_end_date[$i];

               if (!$request->discount_id[$i] || $request->discount_id[$i] == '') {
                    ProductDiscount::create($discount_value_array);
                } else {
                    ProductDiscount::updateOrCreate(['id' => $request->discount_id[$i]], $discount_value_array);
                }
            }
            

            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Product - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect($this->configName . '/products');
        }
    }

    /**
     * Remove the Multiple resource from storage.
     *
     * @param  int  $ids
     * @return \Illuminate\Http\Response
     */
    public function destroyMany(Request $request) {

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('products-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //logActivity
        //fetch title
        $Product = Product::
                select('name_en')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $Product->pluck('name_en');
        $groupname = $name->toJson();

        LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Product - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            //Delete Icon image 
            $Product = Product::
                    select('image')->where('id', $id)->first();

            $destinationPath = public_path('products_images/');

            if (!empty($Product)) {
                if (file_exists($destinationPath . $Product->image) && $Product->image != '') {
                    @unlink($destinationPath . $Product->image);
                }
            }
            Product::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect($this->configName . '/products');
    }

    public function getOptionValue(Request $request) {
        $id = $request->id;
        $optionValue = OptionValue::where('option_id', $id)->orderby('sort_order', 'DESC')->get();

        $returnHTML = view('fitflowVendor.module4.products.ajaxOptions')->with('optionValue', $optionValue)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    //Multiple Images
    public function uploadImages(Request $request) {

        $productID = $request->product_id;

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('products-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');


        //Get All Images
        $productImages = ProductImage::where('product_id', $productID)->get();

        $product = Product::select('name_en')->where('id', $productID)->first();

        return view('fitflowVendor.module4.products.uploadImages')
                        ->with('productImages', $productImages)
                        ->with('productID', $productID)
                        ->with('productName', $product->name_en);
    }

    //Multiple Images
    public function images(Request $request) {

        $productID = $request->product_id;

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('products-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');


        if ($request->hasFile('file')) {
            $validator = Validator::make($request->only(['file']), [
                        'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:800'
            ]);
            // validation failed
            if ($validator->fails()) {
                return response()->json(array('error' => config('global.errorImage')));
            } else {

                $image = $request->file('file');
                $thumbnailImage = Image::make($image);
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('products_images/');

                // Resized image
                $thumbnailImage->resize(config('global.productMultiImagesW'), config('global.productMultiImagesH'), function ($constraint) {
                    $constraint->aspectRatio();
                });
                // Canvas image
                $canvas = Image::canvas(config('global.productMultiImagesW'), config('global.productMultiImagesH'));
                $canvas->insert($thumbnailImage, 'center');
                $canvas->save($destinationPath . $filename);
                $input['image'] = $filename;
                $input['product_id'] = $productID;

                $id = ProductImage::create($input)->id;

                LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Product Images', 'uploaded');
            }
        }

        return response()->json(array('id' => $id));
    }

    //Delete image
    public function deleteImage(Request $request) {
        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('products-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $id = $request->id;

        //Ajax request
        if (request()->ajax()) {

            //Delete  image 
            $ProductImage = ProductImage::
                    select('image')->where('id', $id)->first();
            // dd($BannerImage);

            $destinationPath = public_path('products_images/');
            if (!empty($ProductImage)) {
                if (file_exists($destinationPath . $ProductImage->image) && $ProductImage->image != '') {
                    @unlink($destinationPath . $ProductImage->image);
                }
            }

            ProductImage::destroy($id);


            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Product Images', 'deleted');

            $images = ProductImage::get();

            return response()->json(array('response' => config('global.deletedRecords'), 'id' => $id));
        }
    }
    
     public function destroyOptionValue(Request $request) {
        $id = $request->id;
        $type = $request->type;
        if($type==1) //1: for Product Option Value
        ProductOptionValue::destroy($id);
        if($type==2) //2: for Product Discount Value
        ProductDiscount::destroy($id);
         if($type==3) //2: for Product Special Value
        ProductSpecial::destroy($id);
    }

}
