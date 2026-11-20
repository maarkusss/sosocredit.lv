<?php

namespace App\Http\Controllers\Admincp\Lenders;

use App\Http\Controllers\Admincp\AdminController;
use App\Models\Lender\LenderCategory;
use App\Models\Lender;
use Illuminate\Http\Request;
use Storage;

class AddController extends AdminController
{
    /**
     * Show the lenders edit page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admincp.lenders.add')->with([
            'categories' => $this->getCategories(),
            'languages' => $this->getLanguages(),
            'meta' => $this->metaTitle('Add a lender'),
        ]);
    }

    public function create(Request $request)
    {
        if (!$request->category) {
            return redirect()
                ->back()
                ->with('error', 'Atleast 1 category must be selected!');
        }

        $data = $request->validate([
            'name' => 'required',
            'affLink' => 'required',
//            'slogan' => 'required',
            'firstLoan' => 'required',
            'maxAmount' => 'required',
            'minTerm' => 'required',
            'maxTerm' => 'required',
            'minYears' => 'required',
            'maxYears' => 'required',
            'receivingTime' => 'required',
//            'companysName' => 'required',
//            'address' => 'required',
//            'phone' => 'required',
//            'email' => 'required',
//            'maxAPR' => 'required',
//            'aprExample' => 'required',
        ]);

        $path = Storage::putFile('public/logos', $request->file('image'));

        $path = explode('/', $path);
        $path = '/storage/' . $path[1] . '/' . $path[2];

        $lender = new Lender();

        $lender->name = $data['name'];
        $lender->image = $path;
        $lender->affiliate_link = $data['affLink'];
        $lender->first_loan = $data['firstLoan'];
        $lender->max_amount = $data['maxAmount'];
        $lender->min_term = $data['minTerm'];
        $lender->max_term = $data['maxTerm'];
        $lender->min_years = $data['minYears'];
        $lender->max_years = $data['maxYears'];
        $lender->receiving_time = $data['receivingTime'];
        $lender->active = $request->active ?? 0;
        $lender->zero_percent = $request->zero_percent ?? 0;
        $lender->zero_percent_value = $request->zero_percent_value ?? null;

        $lender->save();

        foreach ($request->category as $key => $value) {
            if ($value == 1) {
                LenderCategory::create([
                    'lender_id' => $lender->id,
                    'loan_type_id' => $key,
                ]);
            }
        }

        $this->logAdminAction('Created lender "' . $data['name'] . '"');

        return redirect(route('admincp.lenders.index'))
            ->with(['success', 'Lender has been added!']);
    }
}
