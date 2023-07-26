<?php
namespace App\Http\Controllers;
use App\Models\Sections;
use App\Models\Invoices;
use App\Models\InvoicesDetails;
use App\Models\User;
use App\Models\invoice_attachments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoices::all();
        return view('invoices.invoices', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = Sections::all();
        return view('invoices.add_invoices',compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Invoices::create([
            'invoice_number'=>$request->invoice_number,
            'invoice_Date'=>$request->invoice_Date,
            'Due_date'=>$request->Due_date,
            'section_id'=>$request->Sections,
            'product'=>$request->product,
            'Amount_collection'=>$request->Amount_collection,
            'Amount_commission'=>$request->Amount_commission,
            'Discount'=>$request->Discount,
            'Rate_vat'=>$request->Rate_vat,
            'Value_vat'=>$request->Value_vat,
            'Total'=>$request->Total,
            'Status'=>'غير مدفوعة',
            'value_status'=>2,
            'note'=>$request->note,
        ]);
        $invoice_id = Invoices::latest()->first()->id;
        InvoicesDetails::create([
            'invoice_number'=>$request->invoice_number,
            'invoices_id'=>$invoice_id,
            'product'=>$request->product,
            'Section'=>$request->Sections,
            'Status'=>'غير مدفوعة',
            'Value_status'=>2,
            'note'=>$request->note,
            'user'=>(Auth::user()->name),

        ]);
        if($request->hasFile('pic')){
            $invoice_id = Invoices::latest()->first()->id;
            $image = $request->file('pic');
            $file_name=$image->getClientOriginalName();
            $invoice_number=$request->invoice_number;

            $attachments = new invoice_attachments();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->Created_by = Auth::user()->name;
            $attachments->invoices_id = $invoice_id;
            $attachments->save();

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);


        }
        $user = User::get();
        $invoices = Invoices::latest()->first();
        Notification::send($user,new \App\Notifications\Add_invoice_new($invoices));


        session()->flash('Add', 'تم اضافة الفاتورة بنجاح');
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $invoices = Invoices::where('id',$id)->first();
        return view('invoices.status_update',compact('invoices'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invoices = Invoices::where('id',$id)->first();
        $sections = Sections::all();
        return view('invoices.edit_invoices',compact('invoices','sections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $invoices = Invoices::findOrFail($request->invoices_id);
        $invoices->update([
            'invoice_number'=>$request->invoice_number,
            'invoice_Date'=>$request->invoice_Date,
            'Due_date'=>$request->Due_date,
            'section_id'=>$request->Sections,
            'product'=>$request->product,
            'Amount_collection'=>$request->Amount_collection,
            'Amount_commission'=>$request->Amount_commission,
            'Discount'=>$request->Discount,
            'Rate_vat'=>$request->Rate_vat,
            'Value_vat'=>$request->Value_vat,
            'Total'=>$request->Total,
            'note'=>$request->note,
        ]);
        session()->flash('edit','تم تعميل الفاتورة بنجاح ');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoices = Invoices::where('id',$id)->first();
        $details = invoice_attachments::where('invoices_id',$id)->first();
        $id_page = $request->id_page;
        if(!$id_page == 2 ){

            if(!empty($details->invoice_number)){
                Storage::disk('public_uploads')->deleteDirectory($details->invoice_number);
            }
            $invoices ->forceDelete();
            session()->flash('delete_invoice');
            return redirect('invoices');
        }else{
            $invoices ->delete();
            session()->flash('archive_invoices');
            return redirect('/Archive');
        }
    }
    public function getProducts($id){
        $products = DB::table('products')->where('section_id',$id)->pluck('products_name','id');
        return json_encode($products);
    }
    public function status_update($id, Request $request){
        $invoices = Invoices::findOrFail($id);
        if($request->Status === 'مدفوعة'){
            $invoices->update([
                "value_status" =>1,
                "Status"=>$request->Status,
                "Payment_Date"=>$request->Payment_Date,
            ]);
            InvoicesDetails::create([
                'invoice_number'=>$request->invoice_number,
                'invoices_id'=>$request->invoices_id,
                'product'=>$request->product,
                'Section'=>$request->Sections,
                'Status'=>$request->Status,
                'Value_status'=>1,
                'note'=>$request->note,
                "Payment_Date"=>$request->Payment_Date,
                'user'=>(Auth::user()->name),
            ]);
        }else{
            $invoices->update([
                "value_status" =>3,
                "Status"=>$request->Status,
                "Payment_Date"=>$request->Payment_Date,
            ]);
            InvoicesDetails::create([
                'invoice_number'=>$request->invoice_number,
                'invoices_id'=>$request->invoices_id,
                'product'=>$request->product,
                'Section'=>$request->Sections,
                'Status'=>$request->Status,
                'Value_status'=>3,
                'note'=>$request->note,
                "Payment_Date"=>$request->Payment_Date,
                'user'=>(Auth::user()->name),
            ]);
        }
        session()->flash('Status_Update');
        return redirect('/invoices');
    }
    public function Invoices_paid(){
        $invoices = Invoices::where('value_status',1)->get();
        return view('invoices.Invoices_paid',compact('invoices'));
    }
    public function Invoice_UnPaid(){
        $invoices = Invoices::where('value_status',2)->get();
        return view('invoices.Invoice_UnPaid',compact('invoices'));
    }
    public function Invoice_Partial(){
        $invoices = Invoices::where('value_status',3)->get();
        return view('invoices.Invoice_Partial',compact('invoices'));
    }
    public function print_invoice($id){
        $invoices = Invoices::where('id',$id)->first();
        return view('invoices.Print_invoice',compact('invoices'));
    }
}
