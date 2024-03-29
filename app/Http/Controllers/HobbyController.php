<?php

namespace App\Http\Controllers;

use App\Models\Hobby;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class HobbyController extends Controller
{
    public function __construct(){
        $this->middleware('auth')->except(['index','show']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$hobbies = Hobby::all();
        //$hobbies = Hobby::paginate(10);


        $hobbies = Hobby::orderBy('created_at','DESC')->paginate(10);

        return view('hobby.index')->with([
            'hobbies' => $hobbies
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('hobby.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'description' => 'required|min:5',
        ]);

        $hobby = new Hobby([
            'name' => $request['name'],
            'description' => $request['description'],
            'user_id' => auth()->id()
        ]);
        $hobby->save();
        /*
   return $this->index()->with(	        return $this->index()->with(
       [	            [
           'message_success' => "The hobby <b>" . $hobby->name . "</b> was created."	                'message_success' => "The hobby <b>" . $hobby->name . "</b> was created."
       ]	            ]
   );	        );
   */
        return redirect('/hobby/' . $hobby->id)->with(
            [
                'message_warning' => "Please assign some tags now."
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Hobby  $hobby
     * @return \Illuminate\Http\Response
     */
    public function show(Hobby $hobby)
    {
        $allTags = Tag::all();
        $usedTags = $hobby->tags;
        $availableTags = $allTags ->diff($usedTags);

        return view('hobby.show')->with([
            'hobby' => $hobby,
            'availableTags' =>  $availableTags,
            'message_success' => Session::get('message_success'),
            'message_warning' => Session::get('message_warning')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Hobby  $hobby
     * @return \Illuminate\Http\Response
     */
    public function edit(Hobby $hobby)
    {
        return view('hobby.edit')->with([
            'hobby' => $hobby
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Hobby  $hobby
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Hobby $hobby)
    {
        $request->validate([
            'name' => 'required|min:3',
            'description' => 'required|min:5',
        ]);

        $hobby->update([
            'name' => $request['name'],
            'description' => $request['description'],
        ]);

        return $this->index()->with([
            'message_success' => 'The hobby  <b>' .  $hobby->name . "</b> was updated."
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Hobby  $hobby
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hobby $hobby)
    {
        $oldName = $hobby->name;
        $hobby->delete();

        return $this->index()->with([
            'message_success' => 'The hobby  <b>' .  $oldName . "</b> was deleted."
        ]);
    }
}
