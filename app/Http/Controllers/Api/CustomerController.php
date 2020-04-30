<?php

namespace App\Http\Controllers\Api;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\CustomerCollection;
use App\Http\Resources\Customer\CustomerResource;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\New_;

class CustomerController extends Controller
{

    public function index()
    {
       return new CustomerCollection(Customer::latest()->paginate(10));
    }


    public function store(Request $request)
    {
       $this->validate($request, [
           'name' => 'required',
           'email' => 'required|email|unique:customers,email',
           'mobile' => 'required',
           'address' => 'required',
           'total' => 'required|numeric',
       ]);

       $customer = New Customer();
       $customer->name = $request->name;
       $customer->email = $request->email;
       $customer->phone = $request->mobile;
       $customer->address = $request->address;
       $customer->total = $request->total;
       $customer->save();

       return new CustomerResource($customer);

    }


    public function show($id)
    {
        return new CustomerResource(Customer::find($id));
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:customers,email,'.$id,
            'mobile' => 'required',
            'address' => 'required',
            'total' => 'required|numeric',
        ]);

        $customer = Customer::find($id);
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->mobile;
        $customer->address = $request->address;
        $customer->total = $request->total;
        $customer->save();

        return new CustomerResource($customer);
    }


    public function destroy($id)
    {
        $customer = Customer::find($id);
        $customer->delete();
        return new CustomerResource($customer);
    }

    public function customerSearch($field, $query)
    {
        return new CustomerCollection(Customer::where($field, 'like', '%'.$query.'%')->latest()->paginate(10));
    }

}
