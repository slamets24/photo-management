<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEventRequest;
use App\Models\Event;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::with(['user', 'photos'])->get();
        return view('pages.dashboard.event.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.dashboard.event.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateEventRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create event
            $event = $this->createEvent($request);

            // Store Images
            $this->storeImages($request, $event);

            DB::commit();

            Alert::toast('Sukses Menambahkan Event', 'success');
            return redirect()->route('events.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal Menambahkan Event: ' . $e->getMessage()]);
        }
    }

    public function createEvent($request)
    {
        $thumbnail = $request->file("thumbnail");
        $path = $thumbnail->storePublicly("thumbnail", "public");

        return Event::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $path,
            'pass_event' => $request->pass_event,
            'slug' => Str::slug($request->name . '-' . Str::ulid()),
            'user_id' => Auth::user()->id,
        ]);
    }

    public function storeImages($request, $event)
    {
        $images = $request->file("images");
        foreach ($images as $image) {
            $path = $image->storePublicly("photos", "public");
            Photo::create([
                'event_id' => $event->id,
                'file_path' => $path
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        //
    }
}
