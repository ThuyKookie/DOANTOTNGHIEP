<?php

namespace App\Http\Controllers\Admin;

use App\HelpersClass\Date;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Exports\statisticalExportExcel;
class AdminStatisticalController extends Controller
{
    private $product;
    private $transaction;
    private $order;
    const ITEM_LIMITED = 10;
    public function __construct(
        Product $product,
        Transaction $transaction,
        Order $order 
    ){
        $this->product = $product;
        $this->transaction = $transaction;
        $this->order = $order;
    }
    public function exportDoanhThu(Request $request)
    {
        $day1 = Carbon::create($request['datatime1a']);
        $day2 = Carbon::create($request['datatime2a'])->addDays(1);
        
        $transactions = Transaction::orderByDesc('id')->whereBetween('created_at',[$day1,$day2])
        ->get();

        return  (new statisticalExportExcel($transactions))->download('DoanhThu'.$day1->format("Y-m-d")."_".$day2->format("Y-m-d").'.xlsx');
    }

    public function selectdateStatis(Request $data)
    {
        if (!check_admin()) return redirect()->route('get.admin.index');
        $day1 = Carbon::create($data['datatime1']);
        $day2 = Carbon::create($data['datatime2'])->addDays(1);
        //Tổng hđơn hàng
        $totalTransactions = \DB::table('transactions')->select('id')->whereBetween('created_at',[$day1,$day2])->count();

        //Tổng thành viên
        $totalUsers = \DB::table('users')->select('id')->whereBetween('created_at',[$day1,$day2])->count();

        // Tông sản phẩm
        $totalProducts = \DB::table('products')->select('id')->whereBetween('created_at',[$day1,$day2])->count();


        // Tông đánh giá
        $totalRatings = \DB::table('ratings')->select('id')->whereBetween('created_at',[$day1,$day2])->count();

        // Danh sách đơn hàng mới
        $transactions = Transaction::orderByDesc('id')->whereBetween('created_at',[$day1,$day2])
                        ->limit(10)
                        ->get();

        // Doanh thu tinh tu ngay den ngay
		$totalMoney2Day = Transaction::whereBetween('created_at',[$day1,$day2])
			->where('tst_status',Transaction::STATUS_SUCCESS)
			->sum('tst_total_money');


            $revenuebyDay = Transaction::orderByDesc('id')
            ->where('tst_status',Transaction::STATUS_SUCCESS)
            ->whereBetween('created_at',[$day1,$day2])
            ->select(\DB::raw('sum(tst_total_money) as totalMoney'), \DB::raw('DATE(created_at) day'))
            ->groupBy('day')
            ->limit(10)
            ->get();
        
        
        $dt = Carbon::now();
        // Doanh thu ngày
		$totalMoneyDay =  Transaction::whereBetween('created_at',[$day1,$day2])
        ->where('tst_status',Transaction::STATUS_SUCCESS)
        ->sum('tst_total_money');

        // Doanh thu tuan
		$mondayLast = Carbon::now()->startOfWeek();
		$sundayFirst = Carbon::now()->endOfWeek()->addDays(1);;
        $totalMoneyWeed =  Transaction::whereBetween('created_at',[$day1,$day2])
        ->where('tst_status',Transaction::STATUS_SUCCESS)
        ->sum('tst_total_money');
		// $totalMoneyWeed = Transaction::whereBetween('created_at',[$mondayLast,$sundayFirst])
		// 	->where('tst_status',Transaction::STATUS_SUCCESS)
		// 	->sum('tst_total_money');
        
        $month1 =  Carbon::create($day1->year."-".$day1->month.'-01');
        $month2 = Carbon::create($day2->year."-".$day2->month.'-28');
		// doanh thu thag
		$totalMoneyMonth = Transaction::whereBetween('created_at',[$month1,$month2])
			->where('tst_status',Transaction::STATUS_SUCCESS)
			->sum('tst_total_money');

		// doanh thu nam
        $year1 =  Carbon::create($day1->year."-"."01".'-01');
        $year2 = Carbon::create($day2->year."-"."12".'-28');
		$totalMoneyYear = Transaction::whereBetween('created_at',[$year1,$year2])
			->where('tst_status',Transaction::STATUS_SUCCESS)
			->sum('tst_total_money');
    

        // Top sản phẩm xem nhiều
        $topViewProducts = Product::orderByDesc('pro_view')
            ->limit(10)
            ->get();

        // Top sản phẩm mua nhiều
        $topPayProducts = Product::orderByDesc('pro_pay')
            ->limit(10)
            ->get();

        // Top mua nhiều trong tháng
		$topProductBuyMonth = Order::with('product:id,pro_name,pro_avatar')->whereMonth('created_at',date('m'))
			->select(\DB::raw('sum(od_qty) as quantity'))
			->addSelect('od_product_id','od_price')
			->groupBy('od_product_id')
			->limit(20)
			->orderByDesc('quantity')
			->get();

        // Tiep nhan
        $transactionDefault = Transaction::where('tst_status',1)->select('id')->count();
        // dang van chuyen
        $transactionProcess = Transaction::where('tst_status',2)->select('id')->count();
        // Thành công
        $transactionSuccess = Transaction::where('tst_status',3)->select('id')->count();
        //Cancel
        $transactionCancel = Transaction::where('tst_status',-1)->select('id')->count();

        $statusTransaction = [
            [
                'Hoàn tất' , $transactionSuccess, false
            ],
            [
                'Đang vận chuyển' , $transactionProcess, false
            ],
            [
                'Tiếp nhận' , $transactionDefault, false
            ],
            [
                'Huỷ bỏ' , $transactionCancel, false
            ]
        ];


        $listDay = Date::getListDayInTowDay($day1, $day2);

        //Doanh thu theo tháng ứng với trạng thái đã xử lý
        $revenueTransactionMonth = Transaction::where('tst_status',3)
            ->whereBetween('created_at',[$day1,$day2])
            ->select(\DB::raw('sum(tst_total_money) as totalMoney'), \DB::raw('DATE(created_at) day'))
            ->groupBy('day')
            ->get()->toArray();

        //Doanh thu theo tháng ứng với trạng thái tiếp nhận
        $revenueTransactionMonthDefault = Transaction::where('tst_status',1)
            ->whereBetween('created_at',[$day1,$day2])
            ->select(\DB::raw('sum(tst_total_money) as totalMoney'), \DB::raw('DATE(created_at) day'))
            ->groupBy('day')
            ->get()->toArray();

        $arrRevenueTransactionMonth = [];
        $arrRevenueTransactionMonthDefault = [];
        foreach($listDay as $day) {
            $total = 0;
            foreach ($revenueTransactionMonth as $key => $revenue) {
                if ($revenue['day'] ==  $day) {
                    $total = $revenue['totalMoney'];
                    break;
                }
            }

            $arrRevenueTransactionMonth[] = (int)$total;

            $total = 0;
            foreach ($revenueTransactionMonthDefault as $key => $revenue) {
                if ($revenue['day'] ==  $day) {
                    $total = $revenue['totalMoney'];
                    break;
                }
            }
            $arrRevenueTransactionMonthDefault[] = (int)$total;
        }

        $day1 = Carbon::create($data['datatime1']);
        $day2 = Carbon::create($data['datatime2']);

        $viewData = [
            'dt'                         => "Từ ngày: ".$day1->day."/".$day1->month."/".$day1->year."/ Đến ngày: ".$day2->day."/".$day2->month."/".$day2->year,
            'totalTransactions'          => $totalTransactions,
            'totalUsers'                 => $totalUsers,
			'totalMoneyDay'				 => $totalMoneyDay,
            'totalMoney2Day'			 => $totalMoney2Day,
			'totalMoneyWeed'		     => $totalMoneyWeed,
			'totalMoneyMonth'		     => $totalMoneyMonth,
			'totalMoneyYear'		     => $totalMoneyYear,
            'totalProducts'              => $totalProducts,
            'totalRatings'               => $totalRatings,
            'transactions'               => $transactions,
            'topViewProducts'            => $topViewProducts,
            'topPayProducts'             => $topPayProducts,
            'revenuebyDay'               => $revenuebyDay,
			'topProductBuyMonth'		 => $topProductBuyMonth,
            'statusTransaction'          => json_encode($statusTransaction),
            'listDay'                    => json_encode($listDay),
            'arrRevenueTransactionMonth' => json_encode($arrRevenueTransactionMonth),
            'arrRevenueTransactionMonthDefault' => json_encode($arrRevenueTransactionMonthDefault)
        ];

    return view('admin.statistical.index', $viewData);
    }
	public function index()
    {
    	if (!check_admin()) return redirect()->route('get.admin.index');
        

        //Tổng hđơn hàng
        $totalTransactions = \DB::table('transactions')->select('id')->count();

        //Tổng thành viên
        $totalUsers = \DB::table('users')->select('id')->count();

        // Tông sản phẩm
        $totalProducts = \DB::table('products')->select('id')->count();


        // Tông đánh giá
        $totalRatings = \DB::table('ratings')->select('id')->count();

        // Danh sách đơn hàng mới
        $transactions = Transaction::orderByDesc('id')
                        ->limit(10)
                        ->get();

        $revenuebyDay = Transaction::orderByDesc('id')
        ->where('tst_status',Transaction::STATUS_SUCCESS)
        ->select(\DB::raw('sum(tst_total_money) as totalMoney'), \DB::raw('DATE(created_at) day'))
        ->groupBy('day')
        ->limit(10)
        ->get();
                    

        // Doanh thu ngày
		$totalMoneyDay = Transaction::whereDay('created_at',date('d'))
			->where('tst_status',Transaction::STATUS_SUCCESS)
			->sum('tst_total_money');

		$mondayLast = Carbon::now()->startOfWeek();
		$sundayFirst = Carbon::now()->endOfWeek();
		$totalMoneyWeed = Transaction::whereBetween('created_at',[$mondayLast,$sundayFirst])
			->where('tst_status',Transaction::STATUS_SUCCESS)
			->sum('tst_total_money');

		// doanh thu thag
		$totalMoneyMonth = Transaction::whereMonth('created_at',date('m'))
			->where('tst_status',Transaction::STATUS_SUCCESS)
			->sum('tst_total_money');

		// doanh thu nam
		$totalMoneyYear = Transaction::whereYear('created_at',date('Y'))
			->where('tst_status',Transaction::STATUS_SUCCESS)
			->sum('tst_total_money');


        // Top sản phẩm xem nhiều
        $topViewProducts = Product::orderByDesc('pro_view')
            ->limit(10)
            ->get();

        // Top sản phẩm mua nhiều
        $topPayProducts = Product::orderByDesc('pro_pay')
            ->limit(10)
            ->get();

        // Top mua nhiều trong tháng
		$topProductBuyMonth = Order::with('product:id,pro_name,pro_avatar')->whereMonth('created_at',date('m'))
			->select(\DB::raw('sum(od_qty) as quantity'))
			->addSelect('od_product_id','od_price')
			->groupBy('od_product_id')
			->limit(20)
			->orderByDesc('quantity')
			->get();

        // Tiep nhan
        $transactionDefault = Transaction::where('tst_status',1)->select('id')->count();
        // dang van chuyen
        $transactionProcess = Transaction::where('tst_status',2)->select('id')->count();
        // Thành công
        $transactionSuccess = Transaction::where('tst_status',3)->select('id')->count();
        //Cancel
        $transactionCancel = Transaction::where('tst_status',-1)->select('id')->count();

        $statusTransaction = [
            [
                'Hoàn tất' , $transactionSuccess, false
            ],
            [
                'Đang vận chuyển' , $transactionProcess, false
            ],
            [
                'Tiếp nhận' , $transactionDefault, false
            ],
            [
                'Huỷ bỏ' , $transactionCancel, false
            ]
        ];

        $listDay = Date::getListDayInMonth();

        //Doanh thu theo tháng ứng với trạng thái đã xử lý
        $revenueTransactionMonth = Transaction::where('tst_status',3)
            ->whereMonth('created_at',date('m'))
            ->select(\DB::raw('sum(tst_total_money) as totalMoney'), \DB::raw('DATE(created_at) day'))
            ->groupBy('day')
            ->get()->toArray();

        //Doanh thu theo tháng ứng với trạng thái tiếp nhận
        $revenueTransactionMonthDefault = Transaction::where('tst_status',1)
            ->whereMonth('created_at',date('m'))
            ->select(\DB::raw('sum(tst_total_money) as totalMoney'), \DB::raw('DATE(created_at) day'))
            ->groupBy('day')
            ->get()->toArray();

        $arrRevenueTransactionMonth = [];
        $arrRevenueTransactionMonthDefault = [];
        foreach($listDay as $day) {
            $total = 0;
            foreach ($revenueTransactionMonth as $key => $revenue) {
                if ($revenue['day'] ==  $day) {
                    $total = $revenue['totalMoney'];
                    break;
                }
            }

            $arrRevenueTransactionMonth[] = (int)$total;

            $total = 0;
            foreach ($revenueTransactionMonthDefault as $key => $revenue) {
                if ($revenue['day'] ==  $day) {
                    $total = $revenue['totalMoney'];
                    break;
                }
            }
            $arrRevenueTransactionMonthDefault[] = (int)$total;
        }

        $dt = Carbon::now('Asia/Ho_Chi_Minh');

        $viewData = [
            'dt'                         => "Hôm Nay: ".$dt->day."-".$dt->month."-".$dt->year,
            'totalTransactions'          => $totalTransactions,
            'totalUsers'                 => $totalUsers,
			'totalMoneyDay'				 => $totalMoneyDay,
			'totalMoneyWeed'		     => $totalMoneyWeed,
			'totalMoneyMonth'		     => $totalMoneyMonth,
			'totalMoneyYear'		     => $totalMoneyYear,
            'totalProducts'              => $totalProducts,
            'totalRatings'               => $totalRatings,
            'revenuebyDay'               => $revenuebyDay,
            'transactions'               => $transactions,
            'topViewProducts'            => $topViewProducts,
            'topPayProducts'             => $topPayProducts,
			'topProductBuyMonth'		 => $topProductBuyMonth,
            'statusTransaction'          => json_encode($statusTransaction),
            'listDay'                    => json_encode($listDay),
            'arrRevenueTransactionMonth' => json_encode($arrRevenueTransactionMonth),
            'arrRevenueTransactionMonthDefault' => json_encode($arrRevenueTransactionMonthDefault)
        ];

        return view('admin.statistical.index', $viewData);
    }

}
