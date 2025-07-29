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
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'conclusion' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('services', 'public');
            $validated['image'] = $path;
        }

        $service = Service::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Service created successfully.',
            'data' => $service
        ], 201);
    }

    // Get All Services with Subservices
    public function index()
    {
        $services = Service::with('subservices')->latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Services fetched successfully.',
            'services' => $services
        ], 200);
    }

    // Update Service
    public function update(Request $request, $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['status' => false, 'message' => 'Service not found.'], 404);
        }

        $data = $request->only('name', 'description', 'conclusion');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Service updated successfully.',
            'data' => $service
        ], 200);
    }

    // Delete Service
    public function destroy($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['status' => false, 'message' => 'Service not found.'], 404);
        }

        $service->delete();

        return response()->json([
            'status' => true,
            'message' => 'Service deleted successfully.'
        ], 200);
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

        return response()->json([
            'status' => true,
            'message' => 'Subservice added successfully.',
            'data' => $subservice
        ], 201);
    }

    // Update Subservice
    public function updateSubservice(Request $request, $id)
    {
        $sub = Subservice::find($id);

        if (!$sub) {
            return response()->json(['status' => false, 'message' => 'Subservice not found.'], 404);
        }

        $data = $request->only('description');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('subservices', 'public');
        }

        $sub->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Subservice updated successfully.',
            'data' => $sub
        ], 200);
    }

    // Delete Subservice
    public function deleteSubservice($id)
    {
        $sub = Subservice::find($id);

        if (!$sub) {
            return response()->json(['status' => false, 'message' => 'Subservice not found.'], 404);
        }

        $sub->delete();

        return response()->json([
            'status' => true,
            'message' => 'Subservice deleted successfully.'
        ], 200);
    }

    // Get all services with their subservices
    public function getServicesWithSubservices()
    {
        $services = Service::with('subservices')->get();

        return response()->json([
            'status' => true,
            'message' => 'All services with subservices fetched.',
            'services' => $services
        ], 200);
    }

    // Get all subservices of a specific service
    public function getSubservicesByService($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['status' => false, 'message' => 'Service not found.'], 404);
        }

        $subservices = $service->subservices;

        return response()->json([
            'status' => true,
            'message' => 'Subservices fetched successfully.',
            'subservices' => $subservices
        ], 200);
    }
}
