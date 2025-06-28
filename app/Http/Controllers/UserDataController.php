<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserData;

class UserDataController extends Controller
{
    public function index()
    {
        $data = UserData::all();
        return view('index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
                $photos[] = $filename;
            }
        }

        UserData::create([
            'name'   => $request->name,
            'phone'  => $request->phone,
            'email'  => $request->email,
            'photos' => json_encode($photos),
        ]);

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $user = UserData::findOrFail($id);
        $user->photos = json_decode($user->photos);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $user = UserData::findOrFail($id);
        $photos = json_decode($user->photos) ?? [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
                $photos[] = $filename;
            }
        }

        $user->update([
            'name'   => $request->name,
            'phone'  => $request->phone,
            'email'  => $request->email,
            'photos' => json_encode($photos),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $user = UserData::findOrFail($id);

        // Optionally, remove photos from uploads folder
        if ($user->photos) {
            foreach (json_decode($user->photos) as $photo) {
                $filePath = public_path('uploads/' . $photo);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        $user->delete();
        return response()->json(['success' => true]);
    }

    public function viewJson()
    {
        $data = UserData::all();
        return response()->json($data);
    }
}

