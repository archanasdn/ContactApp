<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::all();
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required|unique:contacts,phone_number',
        ]);

        $data = $request->except('_token');
        Contact::create($data);
        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
    }

    public function edit(Contact $contact)
    {
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required|unique:contacts,phone_number,' . $contact->id,
        ]);

        $data = $request->except('_token');
        $contact->update($data);
    
        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }

    public function importXML(Request $request)
    {
       // dd($request->file('xml_file')->getMimeType());

        $request->validate([
            //'xml_file' => 'required|file|mimes:xml',
            'xml_file' => 'required|file|mimetypes:text/plain,application/xml,text/xml',
        ]);

        $xmlContent = file_get_contents($request->file('xml_file'));
        $contacts = simplexml_load_string($xmlContent);

        foreach ($contacts as $contact) {
            Contact::create([
                'first_name' => (string)$contact->first_name,
                'last_name' => (string)$contact->last_name,
                'phone_number' => (string)$contact->phone_number,
            ]);
        }

        return redirect()->route('contacts.index')->with('success', 'Contacts imported successfully.');
    }
}
