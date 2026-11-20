<?php

namespace App\Http\Controllers\Admincp\Lenders;

use App\Http\Controllers\Admincp\AdminController;
use App\Models\Lender\LenderCategory;
use App\Models\Lender\LenderData;
use App\Models\Lender;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Storage;

class EditController extends AdminController
{
    public function index($id): Renderable
    {
        return view('admincp.lenders.edit')
            ->with([
                'lender' => Lender::findOrFail($id),
                'lender_data' => $this->getCurrentLenderData($id),
                'categories' => $this->getCategories(),
                'languages' => $this->getLanguages(),
                'meta' => $this->metaTitle('Edit a lender'),
            ]);
    }

    public function update($id, Request $request)
    {
        $lender = Lender::findOrFail($id);

        if ($request->input('action') === 'delete') {
            $lender->delete();

            $this->logAdminAction('Deleted lender "' . $request->name . '"');

            return redirect(route('admincp.lenders.index'))
                ->with('success', 'Lender deleted!');
        }

        if ($request->input('action') === 'save') {
            LenderCategory::query()
                ->where('lender_id', $id)
                ->delete();

            if (!$request->category) {
                return redirect()->back()->with('error', 'Atleast 1 category must be selected!');
            }

            $data = $request->validate([
                'name' => 'required',
                'affLink' => 'required',
                'firstLoan' => 'required',
                'maxAmount' => 'required',
                'minTerm' => 'required',
                'maxTerm' => 'required',
                'minYears' => 'required',
                'maxYears' => 'required',
                'receivingTime' => 'required',
            ]);

            if ($request->image) {
                $path = Storage::putFile('public/logos', $request->file('image'));

                $path = explode('/', $path);
                $path = '/storage/' . $path[1] . '/' . $path[2];

                $lender->update([
                    'image' => $path,
                ]);
            }

            $lender->update([
                'name' => $data['name'],
                'affiliate_link' => $data['affLink'],
                'first_loan' => $data['firstLoan'],
                'max_amount' => $data['maxAmount'],
                'min_term' => $data['minTerm'],
                'max_term' => $data['maxTerm'],
                'min_years' => $data['minYears'],
                'max_years' => $data['maxYears'],
                'receiving_time' => $data['receivingTime'],
                'active' => $request->active ?? 0,
                'zero_percent' => $request->zero_percent ?? 0,
                'zero_percent_value' => $request->zero_percent_value ?? null,
            ]);

            LenderData::updateOrCreate([
                'lender_id' => $id,
                'lang' => app()->getLocale(),
            ], [
                'slogan' => $request->input('slogan'),
                'company_name' => $request->input('companysName'),
                'address' => $request->input('address'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'max_apr' => $request->input('maxAPR'),
                'apr_example' => $request->input('aprExample'),
                'title' => $request->input('lendersTitle'),
                'description' => $request->input('lendersDescription'),
            ]);

            foreach ($request->category as $key => $value) {
                if ($value == 1) {
                    LenderCategory::create([
                        'lender_id' => (int) $lender->id,
                        'loan_type_id' => $key,
                    ]);
                }
            }

            $this->logAdminAction('Updated lender "' . $data['name'] . '"');

            return redirect()
                ->back()
                ->with('success', 'Lender updated!');
        }

        return abort(404);
    }

    public function getCurrentLenderData(int $id)
    {
        return LenderData::query()
            ->where('lender_id', $id)
            ->where('lang', app()->getLocale())
            ->first();
    }
}
