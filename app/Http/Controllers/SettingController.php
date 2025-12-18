<?php
namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        if (!PermissionHelper::hasPermission('setting', 'read')) {
            abort(403, 'Unauthorized action.');
        }
        $setting = Setting::first();
        return view('settings.index', compact('setting'));
    }

    public function store(Request $request)
    {
        // Validate the input data
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_gstin' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pin_code' => 'required|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'authorized_signatory' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Get or create the setting record
        $setting = Setting::firstOrNew([]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->move(public_path('logos'), $request->file('logo')->getClientOriginalName());
            $setting->logo = 'logos/' . $request->file('logo')->getClientOriginalName();
        }

        // Handle authorized signatory upload
        if ($request->hasFile('authorized_signatory')) {
            $signatoryPath = $request->file('authorized_signatory')->move(public_path('authorized_signatories'), $request->file('authorized_signatory')->getClientOriginalName());
            $setting->authorized_signatory = 'authorized_signatories/' . $request->file('authorized_signatory')->getClientOriginalName();
        }

        if ($request->hasFile('purchase_order_logo')) {
            $signatoryPath = $request->file('purchase_order_logo')->move(public_path('authorized_signatories'), $request->file('purchase_order_logo')->getClientOriginalName());
            $setting->purchase_order_logo = 'authorized_signatories/' . $request->file('purchase_order_logo')->getClientOriginalName();
        }

        if ($request->hasFile('prepared_by')) {
            $signatoryPath = $request->file('prepared_by')->move(public_path('authorized_signatories'), $request->file('prepared_by')->getClientOriginalName());
            $setting->prepared_by = 'authorized_signatories/' . $request->file('prepared_by')->getClientOriginalName();
        }

        if ($request->hasFile('approved_by')) {
            $signatoryPath = $request->file('approved_by')->move(public_path('authorized_signatories'), $request->file('approved_by')->getClientOriginalName());
            $setting->approved_by = 'authorized_signatories/' . $request->file('approved_by')->getClientOriginalName();
        }

        // Save other settings
        $setting->company_name = $request->company_name;
        $setting->company_gstin = $request->company_gstin;
        $setting->mobile_number = $request->mobile_number;
        $setting->email = $request->email;
        $setting->address = $request->address;
        $setting->city = $request->city;
        $setting->state = $request->state;
        $setting->pin_code = $request->pin_code;
        $setting->purchase_order_gstin = $request->purchase_order_gstin;
        $setting->purchase_order_mobile_number = $request->purchase_order_mobile_number;
        $setting->purchase_order_email = $request->purchase_order_email;
        $setting->purchase_order_address = $request->purchase_order_address;

        $setting->save();

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully');
    }
}
