<?php

namespace App\Http\Controllers\Admincp;

use App\Models\Setting;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends AdminController
{
    public function index(): Renderable
    {
        return view('admincp.settings.index')
            ->with([
            'name' => Setting::retrieve('name'),
            'lang' => Setting::query()->whereName('lang')->get(),
            'prefix' => Setting::retrieve('prefix'),
            'api_key' => Setting::retrieve('api_key'),
            'consumer_daily_application_limit' => Setting::retrieve('consumer_daily_application_limit'),
            'individual_days_epc' => Setting::retrieve('individual_days_epc'),
            'sitemap' => Setting::retrieve('sitemap'),
            'head_code' => Setting::retrieve('head_code'),
            'head_code_go' => Setting::retrieve('head_code_go'),
            'country_code' => Setting::retrieve('country_code'),
            'hoi_fm_sender_name' => Setting::retrieve('hoi_fm_sender_name'),
            'manifest_json' => Setting::retrieve('manifest_json'),
            'robots_txt' => Setting::retrieve('robots_txt'),
            'api_default_zero_percent_value' => Setting::retrieve('api_default_zero_percent_value'),
            'page_title' => 'Settings',
            'meta' => $this->metaTitle('Settings'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|min:2',
            'lang' => 'required',
            'prefix' => 'required',
            'api_key' => 'required',
            'consumer_daily_application_limit' => 'required|min:1|max:1|regex:/^[0-9]+$/',
            'individual_days_epc' => 'required',
            'sitemap' => 'required',
            'country_code' => 'required|min:1|max:3|regex:/^[0-9]+$/',
            'hoi_fm_sender_name' => 'nullable',
            'manifest_json' => 'nullable',
            'robots_txt' => 'nullable',
            'api_default_zero_percent_value' => 'nullable',
        ]);

        $this->updateName($data['name']);
        $this->updatePrefix($data['prefix']);
        $this->updateApiKey($data['api_key']);
        $this->updateSitemap($data['sitemap']);
        $this->updateIndividualDaysEpc($data['individual_days_epc']);
        $this->updateConsumerDailyApplicationLimit($data['consumer_daily_application_limit']);
        $this->updateLang($data['lang']);
        $this->updateCountryCode($data['country_code']);
        $this->updateApiDefaultZeroPercentValue($data['api_default_zero_percent_value']);

        $this->updateOrRemoveField('qr_code', $request->qr_code);
        $this->updateOrRemoveField('qr_popup', $request->qr_popup);

        if ($request->has('head_code')) {
            $this->updateHeadCode($request->input('head_code') ?? '');
        }

        if ($request->has('head_code_go')) {
            $this->updateHeadCodeGo($request->input('head_code_go') ?? '');
        }

        if ($request->has('hoi_fm_sender_name')) {
            $this->updateHoiFmSenderName($data['hoi_fm_sender_name']);
        }

        if ($request->has('manifest_json')) {
            $this->updateManifestJson($data['manifest_json']);
        }

        if ($request->has('robots_txt')) {
            $this->updateRobotsTxt($data['robots_txt']);
        }

        if ($request->file('logo')) {
            $this->updateLogo($request->file('logo'));
        }

        $this->updateOrRemoveField('email_domains_whitelist_enabled', $request->email_domains_whitelist_enabled);

        $this->logAdminAction('Updated settings');

        flush_settings_cache();

        return redirect()
            ->back()
            ->with([
                'success' => 'Settings have been updated!',
            ]);
    }

    public function updateName($name_value): void
    {
        Setting::updateOrCreate([
            'name' => 'name',
        ], [
            'value' => $name_value,
        ]);
    }

    public function updateCountryCode($country_code_value): void
    {
        Setting::updateOrCreate([
            'name' => 'country_code',
        ], [
            'value' => $country_code_value,
        ]);
    }

    public function updateApiDefaultZeroPercentValue($api_default_zero_percent_value): void
    {
        Setting::updateOrCreate([
            'name' => 'api_default_zero_percent_value',
        ], [
            'value' => $api_default_zero_percent_value ?? '',
        ]);
    }

    public function updateHeadCode($head_code_value): void
    {
        Setting::updateOrCreate([
            'name' => 'head_code',
        ], [
            'value' => $head_code_value,
        ]);
    }

    public function updateHeadCodeGo($head_code_go_value): void
    {
        Setting::updateOrCreate([
            'name' => 'head_code_go',
        ], [
            'value' => $head_code_go_value,
        ]);
    }

    public function updatePrefix($prefix_value): void
    {
        Setting::updateOrCreate([
            'name' => 'prefix',
        ], [
            'value' => $prefix_value,
        ]);
    }

    public function updateSitemap($sitemap_value)
    {
        Setting::updateOrCreate([
            'name' => 'sitemap',
        ], [
            'value' => $sitemap_value,
        ]);
    }

    public function updateIndividualDaysEpc($invidivual_days_epc_value): void
    {
        Setting::updateOrCreate([
            'name' => 'individual_days_epc',
        ], [
            'value' => $invidivual_days_epc_value,
        ]);
    }

    public function updateApiKey($api_key_value): void
    {
        Setting::updateOrCreate([
            'name' => 'api_key',
        ], [
            'value' => $api_key_value,
        ]);
    }

    public function updateLogo($image): void
    {
        $path = Storage::putFile('public/images/', $image);
        $path = explode('public/', $path);

        $fullpath = '/storage/' . $path[1];
        $fullpath = str_replace('/storage/images//', '/storage/images/', $fullpath);

        Setting::updateOrCreate([
            'name' => 'logo',
        ], [
            'value' => $fullpath,
        ]);
    }

    public function updateLang(array $langs): void
    {
        Setting::query()
            ->where('name', 'lang')
            ->delete();

        if (!empty($langs)) {
            foreach ($langs as $lang) {
                Setting::create([
                    'name' => 'lang',
                    'value' => $lang,
                ]);
            }
        }
    }

    public function updateHoiFmSenderName($hoi_fm_sender_name): void
    {
        Setting::updateOrCreate([
            'name' => 'hoi_fm_sender_name',
        ], [
            'value' => $hoi_fm_sender_name ?? '',
        ]);
    }

    public function updateManifestJson($manifest_json): void
    {
        Setting::updateOrCreate([
            'name' => 'manifest_json',
        ], [
            'value' => $manifest_json ?? '',
        ]);
    }

    public function updateRobotsTxt($robots_txt): void
    {
        Setting::updateOrCreate([
            'name' => 'robots_txt',
        ], [
            'value' => $robots_txt ?? '',
        ]);
    }

    public function updateConsumerDailyApplicationLimit($consumer_daily_application_limit): void
    {
        Setting::updateOrCreate([
            'name' => 'consumer_daily_application_limit',
        ], [
            'value' => $consumer_daily_application_limit ?? '',
        ]);
    }

    public function updateOrRemoveField($name, $value): void
    {
        if (\is_null($value)) {
            Setting::where('name', $name)->delete();
        } else {
            $settings = Setting::where('name', $name)->first();
            if ($settings) {
                $settings->update([
                    'value' => $value,
                ]);
            } else {
                Setting::create([
                    'name' => $name,
                    'value' => $value,
                ]);
            }
        }
    }
}
