<?php
/**
 * Created by PhpStorm.
 * User: GitzJoey
 * Date: 9/7/2016
 * Time: 12:35 AM
 */

namespace App\Http\Controllers;

use Auth;
use Config;
use Validator;
use Illuminate\Http\Request;

use App\Services\TruckService;

use App\Repos\LookupRepo;

class TruckController extends Controller
{
    private $truckService;

    public function __construct(TruckService $truckService)
    {
        $this->middleware('auth');
        $this->truckService = $truckService;
    }

    public function index()
    {
        return view('truck.index');
    }

    public function read(Request $request)
    {
        return $this->truckService->read();
    }

    public function store(Request $data)
    {
        Validator::make($data->all(), [
            'plate_number' => 'required|string|max:255',
            'inspection_date' => 'required|string|max:255',
            'driver' => 'required|string|max:255',
            'status' => 'required',
        ])->validate();
        
        $this->truckService->create([
            'company_id' => Auth::user()->company->id,
            'type' => $data['truck_type'],
            'plate_number' => $data['plate_number'],
            'inspection_date' => date(Config::get('const.DATETIME_FORMAT.DATEBASE_DATETIME_FORMAT'), strtotime($data->input('inspection_date '))),
            'driver' => $data['driver'],
            'status' => $data['status'],
            'remarks' => $data['remarks']
        ]);
        
        return response()->json();
            
    }

    public function update($truck, Request $req)
    {
        Validator::make($req->all(), [
            'plate_number' => 'required|string|max:255',
            'inspection_date' => 'required|string|max:255',
            'driver' => 'required|string|max:255',
            'status' => 'required',
        ])->validate();
        
        $this->truckService->find($truck)->update($req->all());

        return response()->json();
    }

    public function delete($truck)
    {
        $this->truckService->find($truck)->delete();
        
        return response()->json();
    }
}