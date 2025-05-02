<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function updateStatus(Request $request)
    {
        $package = Package::find($request->id);

        if ($package) {
            $package->status = $request->status;
            $package->save();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
