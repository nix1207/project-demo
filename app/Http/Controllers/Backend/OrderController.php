<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Backend\OrderDetailModel;
use App\Models\Backend\OrderModel;
use App\Models\Backend\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class OrderController extends Controller
{
    protected $productModel;
    protected $orderModel;
    protected $orderDetailModel;

    public function __construct(ProductModel $productModel, OrderModel $orderModel, OrderDetailModel $orderDetailModel)
    {
        $this->productModel = $productModel;
        $this->orderModel = $orderModel;

    }

    public function index()
    {
        $orders = $this->orderModel->all();
            return view("backend.orders.index", compact('orders'));
        

    }

    public function create()
    {
       
            return view("backend.orders.create");
      
    }

    public function singleProduct(Request $request)
    {
        $keyWord = $request->input('search', '');
        $result = $this->productModel->where("product_name", "LIKE", "%" . "$keyWord" . "%");
        $products = $result->get();
        $msg['results'] = [];
        if ($products) {
            foreach ($products as $product) {
                $msg['results'][] = [
                    "id" => $product->id,
                    "text" => $product->id . "-" . $product->product_name,
                ];
            }
        }
        return response()->json($msg, 200);
    }

    public function ajaxAddProduct(Request $request)
    {
        $id = (int)$request->input("id", '');
        $product = $this->productModel->find($id);
        $productItem = [
            "id" => $product->id,
            "product_name" => $product->product_name,
            "product_image" => $product->product_image,
            "product_price" => $product->product_price,
            "product_quantity" => $product->product_quantity,
        ];
        $productItem['product_image'] = asset("$product->product_image");
        return response()->json($productItem, 200);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $ruler = [
                'customer_name' => "required",
                'customer_email' => "required",
                'customer_phone' => "required",
                'customer_address' => "required",
                "order_status" => "required",
            ];
            $message = [
                'customer_name.required' => "T??n kh??ch h??ng c??n thi???u",
                'customer_email.required' => "Email kh??ch h??ng c??n thi???u",
                'customer_phone.required' => "S??? ??i???n tho???i kh??ch h??ng ch??a c??",
                'customer_address.required' => "Ch??a ??i???n ?????a ch??? giao h??ng",
                'order_status.required' => "Ch???n tr???ng th??i giao h??ng",
            ];
            $validator = Validator::make($request->all(), $ruler, $message);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            }
            if (empty($request->input("product_ids"))) {
                return redirect()->back()->withInput()->with("toast_warning", "Ch??a ch???n s???n ph???m th??m v??o ????n h??ng");
            }

            $customer_name = $request->input("customer_name", '');
            $customer_email = $request->input("customer_email", '');
            $customer_phone = $request->input("customer_phone", '');
            $customer_address = $request->input('customer_address', '');
            $status = $request->input('order_status', '');
            $order_note = $request->input('order_note', '');
            $products = $request->input("product_ids");
            $quantities = $request->input("product_quantity");


            $order = $this->orderModel;

            $order->customer_name = $customer_name;
            $order->customer_email = $customer_email;
            $order->customer_phone = $customer_phone;
            $order->customer_address = $customer_address;
            $order->order_status = $status;
            $order->order_note = $order_note;

            foreach ($products as $key_id => $value) {
                $quantity = $quantities[$key_id];
                $product = $this->productModel->find($value);
                $totalPrice = $quantity * $product->product_price;
                $order->total_product += $quantity;
                $order->total_price += $totalPrice;
                $order->save();
            }

            foreach ($products as $key_id => $val) {
                $quantity = $quantities[$key_id];
                $arr_product = $this->productModel->find($val);
                $totalPrice = $quantity * $product->product_price;
                $detail = new OrderDetailModel();
                $detail->product_id = $val;
                $detail->product_price = $arr_product->product_price;
                $detail->order_id = $order->id;
                $detail->product_quantity = $quantity;
                $detail->save();
            }
            DB::commit();
            return redirect()->route("order.index");
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->route("404");
        }

    }

    public function edit($id)
    {

        $order = $this->orderModel->find($id);
      
            return view("backend.orders.edit", compact('order'));
       

    }

    public function update(Request $request , $id)
    {
        $ruler = [
            "customer_name" => "required",
            "customer_email" => "required",
            "customer_phone" => "required",
            "customer_address" => "required",
        ];
        $message = [
            "customer_name.required" => "Kh??ng ????? tr???ng t??n kh??ch h??mg",
            "customer_email.required" => "Kh??ng ????? tr???ng email kh??ch h??ng",
            "customer_phone" => "Kh??ng ????? tr???ng s??? ??i???n tho???i",
            "customer_address" => "Kh??ng ????? tr???ng ?????a ch??? giao h??ng",
        ];
        $validator = Validator::make($request->all(), $ruler, $message);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        } else {
            $order = OrderModel::find($id);
            $input = $request->all();
            $order->update($input);
            return redirect()->route("order.index")->with("success" , "S???a ????n h??ng th??nh c??ng");

        }
    }

    public function delete($id)
    {
        $order = OrderModel::find($id);
        $order->delete();
        return response()->json([
            'code' => 200,
            'message' => 'success'
        ], 200);
    }
}


