<?php

namespace App\Http\Controllers\Api\OffersApi;

use App\Http\Controllers\Controller;
use App\Models\Lender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Image;
use Symfony\Component\HttpFoundation\Response;

class LenderController extends Controller
{
    /**
     * Update the specified Lender in database.
     *
     * @param Request $request
     * @param $name
     * @return JsonResponse
     */
    public function update(Request $request, $name): JsonResponse
    {
        $lender = Lender::query()
            ->where('name', 'LIKE', '%' . $name . '%')
            ->first();

        if ($lender) {
            if ($request->first_loan) {
                $lender->update([
                    'first_loan' => $request->first_loan
                ]);
            }

            if ($request->max_amount) {
                $lender->update([
                    'max_amount' => $request->max_amount
                ]);
            }

            if ($request->min_term) {
                $lender->update([
                    'min_term' => $request->min_term
                ]);
            }

            if ($request->max_term) {
                $lender->update([
                    'max_term' => $request->max_term
                ]);
            }

            if ($request->receiving_time) {
                $lender->update([
                    'receiving_time' => $request->receiving_time
                ]);
            }

            if ($request->min_years) {
                $lender->update([
                    'min_years' => $request->min_years
                ]);
            }

            if ($request->max_years) {
                $lender->update([
                    'max_years' => $request->max_years
                ]);
            }

            if ($request->daily_epc) {
                $lender->update([
                    'daily_epc' => $request->daily_epc
                ]);
            }

            if ($lender->is_provider == 0) {
                $lender->update([
                    'active' => ($request->active ?: 0)
                ]);
            }

            $lender->update([
                'zero_percent' => ($request->zero_percent ?: 0),
                'guaranteed_epc' => ($request->guaranteed_epc ?: null),
                'frequency' => ($request->frequency ?: null),
            ]);

            if ($request->has('zero_percent_value')) {
                $lender->update([
                    'zero_percent_value' => $request->zero_percent_value ?: null,
                ]);
            }

            if ($request->image) {
                $image = Image::make($request->image)->encode('png');

                $hash = md5($image->__toString());
                $path = "storage/logos/{$hash}.png";
                $image->save(public_path($path));

                $lender->update([
                    'image' => '/' . $path,
                ]);
            }

            if ($request->slogan) {
                if ($lender->dataAPI) {
                    $lender->dataAPI->where('lang', $request->slogan_lng)->where('lender_id', $lender->id)->update([
                        'slogan' => $request->slogan
                    ]);
                }
            }

            if ($request->slogan2) {
                if ($lender->dataAPI) {
                    $lender->dataAPI->where('lang', $request->slogan2_lng)->where('lender_id', $lender->id)->update([
                        'slogan' => $request->slogan2
                    ]);
                }
            }

            if ($request->company_name) {
                if ($lender->dataAPI) {
                    $lender->dataAPI->where('lang', $request->slogan_lng)->where('lender_id', $lender->id)->update([
                        'company_name' => $request->company_name
                    ]);
                }
            }

            if ($request->company_name2) {
                if ($lender->dataAPI) {
                    $lender->dataAPI->where('lang', $request->slogan2_lng)->where('lender_id', $lender->id)->update([
                        'company_name' => $request->company_name2
                    ]);
                }
            }

            if ($request->address) {
                if ($lender->dataAPI) {
                    $lender->dataAPI->where('lang', $request->slogan_lng)->where('lender_id', $lender->id)->update([
                        'address' => $request->address
                    ]);
                }
            }

            if ($request->address2) {
                if ($lender->dataAPI) {
                    $lender->dataAPI->where('lang', $request->slogan2_lng)->where('lender_id', $lender->id)->update([
                        'address' => $request->address2
                    ]);
                }
            }

            if ($request->phone) {
                if ($lender->dataAPI) {
                    $lender->dataAPI->where('lang', $request->slogan_lng)->where('lender_id', $lender->id)->update([
                        'phone' => $request->phone
                    ]);

                    $lender->dataAPI->where('lang', $request->slogan2_lng)->where('lender_id', $lender->id)->update([
                        'phone' => $request->phone
                    ]);
                }
            }

            if ($request->email) {
                if ($lender->dataAPI) {
                    $lender->dataAPI->where('lang', $request->slogan_lng)->where('lender_id', $lender->id)->update([
                        'email' => $request->email
                    ]);

                    $lender->dataAPI->where('lang', $request->slogan2_lng)->where('lender_id', $lender->id)->update([
                        'email' => $request->email
                    ]);
                }
            }

            if ($request->max_apr) {
                if ($lender->dataAPI) {
                    $max_apr = $request->max_apr;
                    $max_apr = str_replace('%', '', $max_apr);

                    $lender->dataAPI->where('lang', $request->slogan_lng)->where('lender_id', $lender->id)->update([
                        'max_apr' => $max_apr
                    ]);

                    $lender->dataAPI->where('lang', $request->slogan2_lng)->where('lender_id', $lender->id)->update([
                        'max_apr' => $max_apr
                    ]);
                }
            }

            if ($request->apr_example) {
                if ($lender->dataAPI) {
                    $lender->dataAPI->where('lang', $request->slogan_lng)->where('lender_id', $lender->id)->update([
                        'apr_example' => $request->apr_example
                    ]);
                }
            }

            if ($request->apr_example2) {
                if ($lender->dataAPI) {
                    $lender->dataAPI->where('lang', $request->slogan2_lng)->where('lender_id', $lender->id)->update([
                        'apr_example' => $request->apr_example2
                    ]);
                }
            }

            return response()->json([
                'message' => 'Lender updated!'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'message' => 'Lender not found!'
        ], Response::HTTP_NOT_FOUND);
    }
}
