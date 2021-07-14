<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\Order;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Http\Requests\AdminRequestProduct;
use App\Models\Category;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\Keyword;
use App\Exports\InvenExportExcel;
use Maatwebsite\Excel\Facades\Excel;
class AdminInventoryController extends Controller
{
    /**
	 * Tồn kho
	 */
    public function selectDataTimeInline(Request $request)
    {
           // dd(\Hash::make('12345678'));
           $day1 = Carbon::create($request['datatime1']);
           $day2 = Carbon::create($request['datatime2'])->addDays(1);

           $products = Product::with('category:id,c_name');
           if ($id = $request->id) $products->where('id', $id);
           if ($name = $request->name) $products->where('pro_name','like', '%'.$name.'%');
           if ($category = $request->category) $products->where('pro_category_id',$category);
           $products = $products->orderByDesc('id')->whereBetween('created_at',[$day1,$day2])->paginate(10);
           $categories = Category::all();
           $viewData = [
               'products'   => $products,
               'categories' => $categories,
               'query'      => $request->query()
           ];
   
           return view('admin.inventory.inven', $viewData);
    }



    public function getInven(Request $request)
    {
           // dd(\Hash::make('12345678'));
           $products = Product::with('category:id,c_name');
           if ($id = $request->id) $products->where('id', $id);
           if ($name = $request->name) $products->where('pro_name','like', '%'.$name.'%');
           if ($category = $request->category) $products->where('pro_category_id',$category);
   
           $products = $products->orderByDesc('id')->paginate(10);
           $categories = Category::all();
           $viewData = [
               'products'   => $products,
               'categories' => $categories,
               'query'      => $request->query()
           ];
   
           return view('admin.inventory.inven', $viewData);
    }




	/**
	 * Nhập kho
	 */
      public function selectDataTimeImport(Request $request)
    {
        
        $dt = Carbon::create($request['day']);
        $day1 = Carbon::create($request['datatime1']);
        $day2 = Carbon::create($request['datatime2'])->addDays(1);
        $warehouses =  Warehouse::orderByDesc('id')->whereBetween('created_at',[$day1,$day2])
        ->paginate(10);
        echo $warehouses;

		$viewData = [
			'warehouses' => $warehouses,
		];

		return view('admin.inventory.import', $viewData);
	}




    public function importYear(Request $request)
    {
        $dt = Carbon::now('Asia/Ho_Chi_Minh');
        $warehouses =  Warehouse::orderByDesc('id')->whereYear('created_at',$request['year'])
        ->paginate(10);
        

		$viewData = [
			'warehouses' => $warehouses,
		];

		return view('admin.inventory.import', $viewData);
	}
    public function importMonth(Request $request)
    {
        $dt = Carbon::now('Asia/Ho_Chi_Minh');
        $warehouses =  Warehouse::orderByDesc('id')->whereMonth('created_at',$request['month'])->whereYear('created_at',$dt->year)
        ->paginate(10);
        

		$viewData = [
			'warehouses' => $warehouses,
		];

		return view('admin.inventory.import', $viewData);
	}
    public function importDay(Request $request)
    {
        
        $dt = Carbon::create($request['day']);
        $warehouses =  Warehouse::orderByDesc('id')->whereDay('created_at',$dt->day)
        ->whereMonth('created_at',$dt->month)
        ->whereYear('created_at',$dt->year)
        ->paginate(10);
        echo $warehouses;

		$viewData = [
			'warehouses' => $warehouses,
		];

		return view('admin.inventory.import', $viewData);
	}



    public function getWarehousing(Request $request)
	{
        $warehouses = Warehouse::whereRaw(1);
        if ($request->day){ 
            $dt = Carbon::create($request['day']);
            $warehouses = $warehouses->whereDay('created_at',$dt->day)
            ->whereMonth('created_at',$dt->month)
            ->whereYear('created_at',$dt->year);
        }
        if ($request->month){
            $warehouses =  $warehouses->whereMonth('created_at',$request->month);
        }
        if ($request->year){
            $warehouses =  $warehouses->whereYear('created_at',$request->year);
        }
        $warehouses = $warehouses->orderByDesc('id')
        ->paginate(10);


		$viewData = [
			'warehouses' => $warehouses,
		];

		return view('admin.inventory.import', $viewData);
	}

	public function add()
    {
        $products = Product::all();
        return view('admin.inventory.import_add', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        Warehouse::create($data);
        return redirect()->route('admin.inventory.warehousing');
    }

    public function edit($id)
    {
        $warehouse = Warehouse::find($id);
        $products = Product::all();
        return view('admin.inventory.import_update', compact('products','warehouse'));
    }

    public function update(Request $request,$id)
    {
        $data = $request->except('_token');
        $warehouse = Warehouse::find($id);
        $warehouse->fill($data)->save();
        return redirect()->route('admin.inventory.warehousing');
    }

    public function delete(Request $request,$id)
    {
        Warehouse::find($id)->delete();
        return redirect()->route('admin.inventory.warehousing');
    }

	/**
	 * Xuất kho
	 */
    public function selectDataTimeExport(Request $request)
    {
        
        $dt = Carbon::now('Asia/Ho_Chi_Minh');
        $day1 = Carbon::create($request['datatime1']);
        $day2 = Carbon::create($request['datatime2'])->addDays(1);
        $inventoryExport = Order::with('product');

        if ($request->time) {
            $time = $this->getStartEndTime($request->time,[]);
            $inventoryExport->whereBetween('created_at', $time);
        }
        $dt = Carbon::create($request['day']);
        $inventoryExport = $inventoryExport->orderByDesc('id')->whereBetween('created_at',[$day1,$day2])        
        ->paginate(20);

        $viewData = [
            'day1' => $day1 ?  $day1 : Carbon::now()->toDateTimeString(),
            'day2' => $day2 ? $day2->subDays(1) :  Carbon::now(),
            'inventoryExport' => $inventoryExport,
            'query' => $request->query()
        ];

        return view('admin.inventory.export', $viewData);
	}


    public function exportYear(Request $request)
    {
        
        $dt = Carbon::now('Asia/Ho_Chi_Minh');
        
        $inventoryExport = Order::with('product');

        if ($request->time) {
            $time = $this->getStartEndTime($request->time,[]);
            $inventoryExport->whereBetween('created_at', $time);
        }
        $dt = Carbon::create($request['day']);
        $inventoryExport = $inventoryExport->orderByDesc('id')->whereYear('created_at',$request['year'])
        
        ->paginate(20);

        $viewData = [
            'inventoryExport' => $inventoryExport,
            'query' => $request->query()
        ];

        return view('admin.inventory.export', $viewData);
	}
    public function exportMonth(Request $request)
    {
        $dt = Carbon::now('Asia/Ho_Chi_Minh');
        
        $inventoryExport = Order::with('product');

        if ($request->time) {
            $time = $this->getStartEndTime($request->time,[]);
            $inventoryExport->whereBetween('created_at', $time);
        }
        $dt = Carbon::create($request['day']);
        $inventoryExport = $inventoryExport->orderByDesc('id')->whereMonth('created_at',$request['month'])->whereYear('created_at',$dt->year)
        
        ->paginate(20);

        $viewData = [
            'inventoryExport' => $inventoryExport,
            'query' => $request->query()
        ];

        return view('admin.inventory.export', $viewData);
	}
    public function exportDay(Request $request)
    {

        $inventoryExport = Order::with('product');

        if ($request->time) {
            $time = $this->getStartEndTime($request->time,[]);
            $inventoryExport->whereBetween('created_at', $time);
        }
        $dt = Carbon::create($request['day']);
        $inventoryExport = $inventoryExport->orderByDesc('id')->whereDay('created_at',$dt->day)
        ->whereMonth('created_at',$dt->month)
        ->whereYear('created_at',$dt->year)
        ->paginate(20);

        $viewData = [
            'inventoryExport' => $inventoryExport,
            'query' => $request->query()
        ];

        return view('admin.inventory.export', $viewData);
	}





	public function getOutOfStock(Request $request)
	{
        $inventoryExport = Order::with('product');

        if ($request->time) {
            $time = $this->getStartEndTime($request->time,[]);
            $inventoryExport->whereBetween('created_at', $time);
        }

        if ($request->day){ 
            $dt = Carbon::create($request['day']);
            $inventoryExport = $inventoryExport->whereDay('created_at',$dt->day)
            ->whereMonth('created_at',$dt->month)
            ->whereYear('created_at',$dt->year);
        }
        if ($request->month){
            $inventoryExport =  $inventoryExport->whereMonth('created_at',$request->month);
        }
        if ($request->year){
            $inventoryExport =  $inventoryExport->whereYear('created_at',$request->year);
        }

        $inventoryExport = $inventoryExport->orderByDesc('id')
            ->paginate(20);

        $viewData = [
            'inventoryExport' => $inventoryExport,
            'query' => $request->query()
        ];

        return view('admin.inventory.export', $viewData);
//
//		$inventoryExport = Export::orderByDesc('id')
//            ->paginate(10);
//
//		$viewData = [
//			'inventoryExport' => $inventoryExport,
//		];
//
//		return view('admin.inventory.export', $viewData);
	}

    public function exportInven(Request $request)
    {
        $day1 = Carbon::create($request['datatime1a']);
        $day2 = Carbon::create($request['datatime2a'])->addDays(1);;
        return  (new InvenExportExcel($day1, $day2))->download('DanhSachTonKho'.$day1->format("Y-m-d")."_".$day2->format("Y-m-d").'.xlsx');
    }

	public function exportAdd()
    {
        $transactions = Transaction::all();
        return view('admin.inventory.export_add', compact('transactions'));
    }

    public function exportStore(Request $request)
    {
        $data = $request->except('_token');
        Export::create($data);
        return redirect()->route('admin.export.out_of_stock');
    }

    public function exportEdit($id)
    {
        $export = Export::find($id);
        $transactions = Transaction::all();
        return view('admin.inventory.export_update', compact('transactions','export'));
    }

    public function exportUpdate(Request $request,$id)
    {
        $data = $request->except('_token');
        $warehouse = Export::find($id);
        $warehouse->fill($data)->save();
        return redirect()->route('admin.export.out_of_stock');
    }

    public function exportDelete(Request $request,$id)
    {
        Export::find($id)->delete();
        return redirect()->route('admin.export.out_of_stock');
    }
}
