<?php
namespace App\Http\Controllers;
use App\Models\Sections;
use App\Models\Invoices;
use App\Models\InvoicesDetails;
use App\Models\invoice_attachments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
            'section'=>$request->Sections,
            'status'=>'غير مدفوعة',
            'value_status'=>2,
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

        session()->flash('Add', 'تم اضافة الفاتورة بنجاح');
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoices $invoices)
    {
        //
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
        $details = invoice_attachments::where('invoices_id',$id)->get();
        if(!empty($details->invoice_number)){
            Storage::disk('public_uploads')->deleteDirectory($details->invoice_number);
        }
        $invoices ->forceDelete();
        session()->flash('delete_invoice');
        return redirect('invoices');
    }
    public function getProducts($id){
        $products = DB::table('products')->where('section_id',$id)->pluck('products_name','id');
        return json_encode($products);
    }
}
