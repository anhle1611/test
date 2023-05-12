<?php

namespace App\Modules\Guest\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Guest;

use Validator;

class GuestController extends Controller
{

    public function index($id)
    {
        return response()->json([
            'message' => 'get Guest successfully',
            'guest' => Guest::find($id)
        ], 200);
    }

    public function show(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'offset' => 'required|integer|min:0',
            'limit' => 'required|integer|min:1',
            'search' => 'string|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        return response()->json([
            'message' => 'get Guests successfully',
            'count' => Guest::count(),
            'list' => Guest::where('name', 'like', '%' . $request->search . '%')->offset($request->offset)->limit($request->limit)->get()
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $guest = Guest::create($request->all());

        return response()->json([
            'message' => 'Guest successfully created',
            'guest' => $guest
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $guest = Guest::find($id)->updateOrCreate($request->all());

        return response()->json([
            'message' => 'Guest successfully updated',
            'guest' => $guest
        ], 200);
    }

    public function destroy($id)
    {
        Guest::destroy($id);

        return response()->json([
            'message' => 'Guest successfully deleted',
        ], 200);
    }
}
