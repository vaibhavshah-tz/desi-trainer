<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerQuoteRequest;
use App\Http\Requests\InstallmentRequest;
use App\Models\Customer;
use App\Models\CustomerQuote;
use App\Models\CustomerQuoteInstallment;
use App\Models\Ticket;
use Carbon\Carbon;
use CommonHelper;
use Illuminate\Http\Request;

class TrainerInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $page_title = __('Customer Pricing');
            $ticketDetail = Ticket::select(['id','course_id', 'customer_id'])
                ->with(['course:id,course_price,course_special_price,currency',
                    'customerQuote',
                    'customerQuote.installments'])
                ->findOrFail($request->id);

            return view('customer-pricing.index', compact('page_title', 'ticketDetail'));
        } catch (\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!.'));

            return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
        }
    }

}
