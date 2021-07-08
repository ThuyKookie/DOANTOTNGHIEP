<?php

namespace App\Http\Controllers\Admin;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\Product;
use App\Exports\TransactionExport;

class AdminTransactionController extends Controller
{
    public function dateTransaction(Request $request)
    {
        $dt = Carbon::now('Asia/Ho_Chi_Minh');
        $day1 = Carbon::create($request['datatime1']);
        $day2 = Carbon::create($request['datatime2'])->addDays(1);
        $transactions = Transaction::whereRaw(1)->whereBetween('created_at',[$day1,$day2]);
        

        if ($request->id) $transactions->where('id',$request->id);
        if ($email = $request->email) {
            $transactions->where('tst_email','like','%'.$email.'%');
        }

        if ($type = $request->type) {
            if ($type == 1)
            {
                $transactions->where('tst_user_id','<>',0);
            }else {
                $transactions->where('tst_user_id',0);
            }
        }

        if ($status = $request->status) {
            $transactions->where('tst_status',$status);
        }

        $transactions = $transactions->orderByDesc('id')
                            ->paginate(10);
        if ($request->export) {
            // Gọi thới export excel 
            return \Excel::download(new TransactionExport($transactions), 'don-hang.xlsx');
        }

        $viewData = [
            'day1' => $day1 ?  $day1 : Carbon::now()->toDateTimeString(),
            'day2' => $day2 ? $day2->subDays(1) :  Carbon::now(),
            'transactions' => $transactions,
            'query'        => $request->query()
        ];
        return view('admin.transaction.index', $viewData);
    }

    public function exportTransaction(Request $request)
    {
        $day1 = Carbon::create($request['datatime1a']);
        $day2 = Carbon::create($request['datatime2a'])->addDays(1);
        $transactions = Transaction::whereRaw(1)->whereBetween('created_at',[$day1,$day2])->get();

        return \Excel::download(new TransactionExport($transactions), 'don-hang.xlsx');
        // return  (new TransactionExportExcel($day1, $day2))->download('DanhSachDonHang'.$day1->format("Y-m-d")."_".$day2->format("Y-m-d").'.xlsx');
    }

    public function selectdateTransactionMonth(Request $request)
    {
        $dt = Carbon::now('Asia/Ho_Chi_Minh');
        $transactions = Transaction::whereRaw(1)->whereMonth('created_at',$request['month']);

        if ($request->id) $transactions->where('id',$request->id);
        if ($email = $request->email) {
            $transactions->where('tst_email','like','%'.$email.'%');
        }

        if ($type = $request->type) {
            if ($type == 1)
            {
                $transactions->where('tst_user_id','<>',0);
            }else {
                $transactions->where('tst_user_id',0);
            }
        }

        if ($status = $request->status) {
            $transactions->where('tst_status',$status);
        }

        $transactions = $transactions->orderByDesc('id')
                            ->paginate(10);
        if ($request->export) {
            // Gọi thới export excel 
            return \Excel::download(new TransactionExport($transactions), 'don-hang.xlsx');
        }

        $viewData = [
            'transactions' => $transactions,
            'query'        => $request->query()
        ];

        return view('admin.transaction.index', $viewData);
    }

    public function selectdateTransactionDay($request)
    {
        $dt = Carbon::create($request['day']);
        $transactions = Transaction::whereRaw(1)->whereDay('created_at',$dt->day)
        ->whereMonth('created_at',$dt->month)
        ->whereYear('created_at',$dt->year);
        if ($request->id) $transactions->where('id',$request->id);
        if ($email = $request->email) {
            $transactions->where('tst_email','like','%'.$email.'%');
        }

        if ($type = $request->type) {
            if ($type == 1)
            {
                $transactions->where('tst_user_id','<>',0);
            }else {
                $transactions->where('tst_user_id',0);
            }
        }

        if ($status = $request->status) {
            $transactions->where('tst_status',$status);
        }

        $transactions = $transactions->orderByDesc('id')
                            ->paginate(10);
        if ($request->export) {
            // Gọi thới export excel 
            return \Excel::download(new TransactionExport($transactions), 'don-hang.xlsx');
        }

        $viewData = [
            'transactions' => $transactions,
            'query'        => $request->query()
        ];

       // return view('admin.transaction.index', $viewData);
    }


    public function index(Request $request)
    {
        $transactions = Transaction::whereRaw(1);
        if ($request->day){ 
        $dt = Carbon::create($request['day']);
        $transactions = $transactions->whereDay('created_at',$dt->day)
        ->whereMonth('created_at',$dt->month)
        ->whereYear('created_at',$dt->year);
        }
        if ($request->month){
        $transactions =  $transactions->whereMonth('created_at',$request->month);
        }
        if ($request->year){
            $transactions =  $transactions->whereYear('created_at',$request->year);
            }
        if ($request->id) $transactions->where('id',$request->id);
        if ($email = $request->email) {
            $transactions->where('tst_email','like','%'.$email.'%');
        }

        if ($type = $request->type) {
            if ($type == 1)
            {
                $transactions->where('tst_user_id','<>',0);
            }else {
                $transactions->where('tst_user_id',0);
            }
        }

        if ($status = $request->status) {
            $transactions->where('tst_status',$status);
        }

        $transactions = $transactions->orderByDesc('id')
                            ->paginate(10);
        if ($request->export) {
            // Gọi thới export excel 
            return \Excel::download(new TransactionExport($transactions), 'don-hang.xlsx');
        }

        $viewData = [
            'transactions' => $transactions,
            'query'        => $request->query()
        ];

        return view('admin.transaction.index', $viewData);
    }

    public function getTransactionDetail(Request $request, $id)
    {

        if ($request->ajax()) {
            $orders = Order::with('product:id,pro_name,pro_slug,pro_avatar')->where('od_transaction_id', $id)
                ->get();

            $html = view("components.orders", compact('orders'))->render();
            
            return response([
                'html' => $html
            ]);    
        }    
    }

    public function deleteOrderItem(Request $request, $id)
    {
        if ($request->ajax()) {
            $order = Order::find($id);
            if ($order) {
                $money = $order->od_qty * $order->od_price;
                //
                \DB::table('transactions')
                    ->where('id', $order->od_transaction_id)
                    ->decrement('tst_total_money', $money);
                $order->delete();
            }

            return response(['code' => 200]);
        }
    }



    public function delete($id)
    {
        $transaction = Transaction::find($id);
        if ($transaction) {
            $transaction->delete();
            \DB::table('orders')->where('od_transaction_id', $id)
                ->delete();
        }

        return redirect()->back();
    }

    public function getAction(Request $request, $action, $id)
    {
        $transaction = Transaction::find($id);
        if ($transaction) {
            switch ($action) {
                case 'process':
                    $transaction->tst_status = 2;
                    break;

                case 'success':
                    $transaction->tst_status = 3;
                    $this->syncDecrementProduct($id);
                    break;

                case 'cancel':
                    $transaction->tst_status = -1;
//                    $this->syncIncrementProduct($id);
                    break;
            }
			$transaction->tst_admin_id = get_data_user('admins');
            $transaction->save();
        }

        if ($request->ajax())
		{
			return response()->json(['code' => 200]);
		}

        return redirect()->back();
    }

    protected function syncIncrementProduct($transactionID)
	{
		$orders = Order::where('od_transaction_id', $transactionID)
			->get();
		if ($orders)
		{
			foreach ($orders as $order)
			{
				\DB::table('products')
					->where('id', $order->od_product_id)
					->increment("pro_number",$order->od_qty);
			}
		}
	}

	protected function syncDecrementProduct($transactionID)
	{
		$orders = Order::where('od_transaction_id', $transactionID)
			->get();
		if ($orders)
		{
			foreach ($orders as $order)
			{
				\DB::table('products')
					->where('id', $order->od_product_id)
					->decrement("pro_number",$order->od_qty);
			}
		}
	}
}
