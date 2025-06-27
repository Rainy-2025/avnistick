<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Service;
use App\Models\Subservice;

class ServiceController extends Controller
{
    // Add Service
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('services', 'public');
            $validated['image'] = $path;
        }

        $service = Service::create($validated);
        return response()->json(['status' => 'success', 'data' => $service]);
    }

    // Get All Services with Subservices
    public function index()
    {
        $services = Service::with('subservices')->latest()->get();
        return response()->json($services);
    }

    // Update Service
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $data = $request->only('description');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('services', 'public');
            $data['image'] = $path;
        }

        $service->update($data);

        return response()->json(['status' => 'success', 'message' => 'Service updated']);
    }

    // Delete Service
    public function destroy($id)
    {
        Service::findOrFail($id)->delete();
        return response()->json(['status' => 'success', 'message' => 'Service deleted']);
    }

    // Add Subservice to a Service
    public function addSubservice(Request $request, $serviceId)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('subservices', 'public');
        }

        $validated['service_id'] = $serviceId;

        $subservice = Subservice::create($validated);

        return response()->json(['status' => 'success', 'data' => $subservice]);
    }

    // Update Subservice
    public function updateSubservice(Request $request, $id)
    {
        $sub = Subservice::findOrFail($id);

        $data = $request->only('description');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('subservices', 'public');
        }

        $sub->update($data);

        return response()->json(['status' => 'success', 'message' => 'Subservice updated']);
    }

    // Delete Subservice
    public function deleteSubservice($id)
    {
        Subservice::findOrFail($id)->delete();
        return response()->json(['status' => 'success', 'message' => 'Subservice deleted']);
    }


  

public function getServicesWithSubservices()
{
    $services = Service::with('subservices')->get();

    return response()->json([
        'status' => 'success',
        'services' => $services
    ]);
}

public function getSubservicesByService($id)
{
    $service = Service::find($id);

    if (!$service) {
        return response()->json(['status' => 'error', 'message' => 'Service not found'], 404);
    }

    $subservices = $service->subservices;

    return response()->json([
        'status' => 'success',
        'subservices' => $subservices
    ]);
}

}

