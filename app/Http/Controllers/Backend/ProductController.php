<?php

namespace App\Http\Controllers\Backend;

use App\Components\Recursive;
use App\Http\Controllers\Controller;
use App\Models\Backend\CategoryModel;

use App\Models\Backend\GalleryModel;
use App\Models\Backend\ProductModel;
use App\Models\User;
use App\Traits\StorageImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\False_;
use function Composer\Autoload\includeFile;

class ProductController extends Controller
{
    protected $categoryModel;
    protected $productModel;
    protected $galleryModel;
    protected $user;
    use StorageImage;


    public function __construct(
        CategoryModel $categoryModel,
        ProductModel $productModel,
        GalleryModel $galleryModel,
        User $user
    )
    {
        $this->categoryModel = $categoryModel;
        $this->productModel = $productModel;
        $this->galleryModel = $galleryModel;
        $this->user = $user;
    }

    public function index(Request $request)
    {

        $pageSize = 10;
        $categories = $this->categoryModel->all();

        $instance = $this->productModel;

        if ($request->all() == 0) {
            $products = $instance->paginate($pageSize);
        } else {
            $keyword = $request->query("product_name", '');
            $status = $request->query("product_status", 0);
            $category_id = $request->query('category_id', 0);
            $order_by = $request->query("order_by", 0);
            $queryORM = $instance->where("product_name", 'LIKE', "%" . $keyword . "%");
            $statusAll = [1, 2];
            if (in_array($status, $statusAll)) {
                $queryORM->where('product_status', $status);
            }
            if (!empty($category_id) ){
                $queryORM->where("category_id" , $category_id);
            }

            if ($request->order_by == 1) {
             $abc = $queryORM->orderBy("product_name");

            }
            if ($request->order_by == 2) {
                $queryORM->orderByDesc("product_name");
            }
            if ($request->order_by == 3) {
                $queryORM->orderBy("product_price");
            }
            if ($request->order_by == 4){
                $queryORM->orderByDesc("product_price");
            }

        }
        $products = $queryORM->paginate($pageSize)->appends($request->except('page'));
        $products->load('category');
        $data = [];
        $data['products'] = $products;
        $data['categories'] = $categories;
        $data['pageSize'] = $pageSize;
        $data['status'] = $status;
        $data['category_id'] = $category_id;
        $data['order_by'] = $order_by;
        $data['keyword'] = $keyword;

       
            return view("backend.product.index", $data);
       

    }

    public function create()
    {
        $categories = $this->categoryModel->all();
       
            return view("backend.product.create", compact('categories'));

    }

    public function getCategory($parentId)
    {
        $categories = $this->categoryModel->all();
        $recursive = new Recursive($categories);
        $html = $recursive->recursive($parentId);
        return $html;
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $ruler = [
                "product_name" => "required|unique:product,product_name",
                "product_price" => "required|numeric|min:1",
                "product_quantity" => "required|numeric|min:1",
                "category_id" => "required",
                "product_status" => "required",
                "product_image" => "required",
                "product_gallery" => "required",
                "product_desc" => "required",
                "short_desc" => "required",
            ];
            $message = [
                "product_name.required" => "T??n s???n ph???m kh??ng ???????c ????? tr???ng",
                "product_name.unique" => "S???n ph???m n??y ???? t???n t???i",
                "product_price.required" => "Gi?? s???n ph???m kh??ng ???????c ????? tr???ng",
                "product_price.min" => "Gi?? s???n ph???m l???n h??n ho???c b???ng 1",
                "product_quantity.required" => "S??? l?????ng s???n ph???m kh??ng ????? tr???ng",
                "product_quantity.min" => "S??? l?????ng s???n ph???m ph???i l???n h??n ho???c b???ng 1",
                "category_id.required" => "Ch??a ch???n danh m???c",
                "product_status.required" => "C???n ch???n tr???ng th??i c???a s???n ph???m",
                "product_image.required" => "Ch??a c?? ???nh s???n ph???m",
                "product_gallery.required" => "C???n 1 v??i ???nh chi ti???t",
                "product_desc.required" => "Kh??ng ????? tr???ng m?? t??? s???n ph???m",
                "short_desc.required" => "C???n c?? m?? t??? ng???n c???a s???n ph???m",
            ];
            $validator = Validator::make($request->all(), $ruler, $message);
            $product_name = $request->input('product_name', '');
            $product_price = $request->input('product_price', 0);
            $product_publish = $request->input('product_publish', '');
            $product_quantity = $request->input('product_quantity', 0);
            $category_id = $request->input('category_id', '');
            $product_desc = $request->input('product_desc', '');
            $short_desc = $request->input('short_desc', '');

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $product = $this->productModel;
                $product->product_name = $product_name;
                $product->product_price = $product_price;
                $product->product_quantity = $product_quantity;
                $product->category_id = $category_id;
                $product->product_desc = $product_desc;
                $product->short_desc = $short_desc;
                if ($request->product_status == 1) {
                    $product->product_status = 1;
                } else {
                    $product->product_status = 2;
                }
                if (empty($product_publish)) {
                    $product_publish = date("Y-m-d H:i:s");
                }
                $product->product_publish = $product_publish;

                $data_image = $this->StorageImage($request, 'product_image', "image_of_product");
                if (!empty($data_image)) {
                    $product_image = $data_image['file_path'];
                    $image_name = $data_image['file_name'];
                }
                $product->product_image = $product_image;
                $product->image_name = $image_name;
                $product->save();
                if ($request->hasFile('product_gallery')) {
                    foreach ($request->product_gallery as $productItem) {
                        $product_gallery = $this->StoreImageMultiple($productItem, 'product_gallery/' . "$product->id");
                        $image_path = $product_gallery['file_path'];
                        $image_name = $product_gallery['file_name'];
                        $product->gallery()->create([
                            'image_path' => $image_path,
                            'image_name' => $image_name
                        ]);
                    }
                }
                DB::commit();
                return redirect()->route('product.index')
                    ->with("success", "Th??m m???i s???n ph???m th??nh c??ng");
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->route('404');
        }
    }

    public function edit($id)
    {
        $product = $this->productModel->find($id);
        $categories = $this->getCategory($product->category_id);
        
            return view("backend.product.edit", compact('product', 'categories'));
       
    }

    public function update(Request $request, $id)
    {

        try {
            DB::beginTransaction();
            $ruler = [
                'product_name' => "required|unique:product,product_name,$id,id",
                "product_price" => "required|numeric|min:1",
                "product_quantity" => "required|numeric|min:1",
                'category_id' => "required",
                'product_desc' => "required",
                "short_desc" => "required",
            ];
            $message = [
                "product_name.required" => "T??n s???n ph???m kh??ng ???????c ????? tr???ng",
                "product_name.unique" => "S???n ph???m ???? t???n t???i",
                "product_price.required" => "Gi?? s???n ph???m kh??ng ???????c ????? tr???ng",
                "product_price.min" => "Gi?? s???n ph???m l???n h??n ho???c b???ng 1",
                "product_quantity.required" => "S??? l?????ng s???n ph???m kh??ng ????? tr???ng",
                "product_quantity.min" => "S??? l?????ng s???n ph???m ph???i l???n h??n ho???c b???ng 1",
                "category_id.required" => "Ch??a ch???n danh m???c",
                "product_desc.required" => "Ch??a c?? m?? t??? s???n ph???m",
                "short_desc.required" => "Ch??a c?? m?? t??? ng???n s???n ph???m",
            ];

            $validator = Validator::make($request->all(), $ruler, $message);
            $product_name = $request->input("product_name", '');
            $product_price = $request->input('product_price', 0);
            $product_publish = $request->input('product_publish', '');
            $product_quantity = $request->input('product_quantity', 0);
            $category_id = $request->input('category_id', '');
            $product_status = $request->input('product_status', '');
            $product_desc = $request->input("product_desc", '');
            $short_desc = $request->input('short_desc', '');
            if (!$product_publish == "") {
                $product_publish = date("Y-m-d");
            }
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            }
            $data = $this->StorageImage($request, 'product_image', 'image_of_product');
            if (!empty($data)) {
                $product_image = $data['file_path'];
                $image_name = $data['file_name'];
            }
            $product = $this->productModel->find($id);
            $product->product_name = $product_name;
            $product->product_price = $product_price;
            $product->product_publish = $product_publish;
            $product->product_quantity = $product_quantity;
            $product->category_id = $category_id;
            $product->product_status = $product_status;
            $product->product_desc = $product_desc;
            $product->short_desc = $short_desc;
            if ($request->hasFile('product_image')) {
                if ($product->product_image) {
                    $product_images = str_replace('/storage', '/public', $product->product_image);
                    Storage::delete($product_images);
                }
                $product->product_image = $product_image;
                $product->image_name = $image_name;
            }
            if ($request->input("removeGalleryIds") != "") {
                $strIds = rtrim($request->removeGalleryIds, '|');
                $listId = explode("|", $strIds);
                $removeList = $this->galleryModel->whereIn('id', $listId)->get();
                foreach ($removeList as $remove) {
                    $itemRemove = str_replace("/storage", '/public', $remove->image_path);
                    Storage::delete($itemRemove);
                }
                $this->galleryModel->destroy($listId);

            }
            if ($request->hasFile('product_gallery')) {
                foreach ($request->product_gallery as $gallery) {
                    $galleries = $this->StoreImageMultiple($gallery, "product_gallery/$product->id");
                    $image_path = $galleries['file_path'];
                    $image_name = $galleries['file_name'];
                    $product->gallery()->create([
                        'image_path' => $image_path,
                        'image_name' => $image_name,
                    ]);
                }
            }
            $product->save();
            DB::commit();
            return redirect()->route("product.index")->with("success", "S???a s???n ph???m xong");
        } catch (\Exception $exception) {
            echo $exception;
            return false;
        }
    }

    public function remove($id)
    {
        $product = $this->productModel->find($id);
        $product_gallery = $this->galleryModel->where('product_id', $id)->get();
        $product_image = $this->galleryModel->where('product_id', $id);
        if ($product->product_image) {
            $delete_item = str_replace("/storage", '/public', $product->product_image);
            Storage::delete($delete_item);
        }
        Storage::deleteDirectory("public/product_gallery/$product->id");
        $product_image->delete();
        $product->delete();
        return response()->json([
            'code' => '200',
            'message' => 'success'
        ], 200);
    }


}
