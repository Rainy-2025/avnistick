<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactNotification;



class ContactController extends Controller
{
    // Submit contact form (User)
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'nullable|string',
        ]);

        $contact = Contact::create($validated);

        // Send Email to Admin
        Mail::to('darwairavina2002@gmail.com')->send(new ContactNotification($contact));

        return response()->json([
            'status' => 'success',
            'message' => 'Contact form submitted and email sent to admin'
        ]);
    }

    // Admin: Get all contacts
    public function index()
    {
        $contacts = Contact::latest()->get();
        return response()->json($contacts);
    }

    // Admin: Update contact
    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        $contact->update($request->only(['name', 'email', 'phone', 'subject', 'message']));

        return response()->json([
            'status' => 'success',
            'message' => 'Contact updated successfully',
            'data' => $contact
        ]);
    }

    // Admin: Delete contact
    public function destroy($id)
    {
        Contact::findOrFail($id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Contact deleted successfully'
        ]);
    }
}
