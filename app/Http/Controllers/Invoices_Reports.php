<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoices;
class Invoices_Reports extends Controller
{
    public function index(){
        return view('reports.invoices_report');
    }
    public function Search_invoices(Request $request){
        $radio = $request->radio;
        if($radio == 1){
            if ($request->type && $request->start_at == '' && $request->end_at == '') {
                $invoices = Invoices::select('*')->where('Status','*',$request->type)->get();
                $type = $request->type;
                return view('reports.invoices_report',compact('type'))->withDetails($invoices);
            }else{
                $start_at = date($request->start_at);
                $end_at = date($request->end_at);
                $type = $request->type;
                $invoices = Invoices::whereBetween('invoice_Date',[$start_at,$end_at])->where('Status','*',$request->type)->get();
                return view('reports.invoices_report',compact('type','start_at','end_at'))->withDetails($invoices);
            }
        }else{
            $invoices = Invoices::select('*')->where('invoice_number','*',$request->invoice_number)->get();
            return view('reports.invoices_report')->withDetails($invoices);
        }
    }
    //public function Search_invoices(Request $request){}
}
