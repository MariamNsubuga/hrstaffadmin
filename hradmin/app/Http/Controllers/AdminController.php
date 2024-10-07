<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Audit;
use App\Models\Member;
use App\Models\RegistrationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    //
    public function index(Request $request)
    {
        $audits = Audit::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.audits.index', compact('audits')); // Return the view with audits
    }

    // Show the admin login form
    public function showLoginForm()
    {
        return view('admin.login'); // Create a login view specifically for admins
    }

    // Handle admin login
    public function login(Request $request)
    {
        // Validate login credentials
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to log in as admin
        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard'); // Redirect to admin dashboard if login successful
        }

        // Return back with an error message if login fails
        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ]);
    }

    // Admin dashboard view
    // public function dashboard()
    // {
    //     return view('admin.dashboard'); 
    // }
    public function dashboard()
    {
        $staffCount = Member::count(); // Get the total number of staff members
        $codeCount = RegistrationCode::count(); // Get the total number of generated codes

        return view('admin.dashboard', compact('staffCount', 'codeCount')); // Pass counts to the view
    }
     // Show the form to create a new admin
     public function create()
     {
         return view('admin.create');
     }
 
     // Store the new admin in the database
     public function store(Request $request)
     {
         // Validate the incoming request
         $request->validate([
             'name' => 'required|string|max:255',
             'email' => 'required|string|email|max:255|unique:admins', // Adjust according to your table
             'password' => 'required|string|confirmed|min:8', // Confirmed rule checks for password confirmation
         ]);
 
         // Create the new admin
         $admin = new Admin();
         $admin->name = $request->name;
         $admin->email = $request->email;
         $admin->password = Hash::make($request->password);
         $admin->save();
 
         return redirect()->route('admin.dashboard')->with('success', 'Admin added successfully.');
     }
}
