<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\RegistrationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationCodeMail;
use App\Models\Audit;

class StaffController extends Controller
{
    // Generate a unique registration code
    public function generateCode(Request $request)
    {
        // Validate the email
        $request->validate([
            'email' => 'required|email',
        ]);

        // Generate a unique 10-character registration code
        $registrationCode = strtoupper(Str::random(10)); // Use uppercase letters
        RegistrationCode::create([
            'email' => $request->email,
            'code' => $registrationCode,
            'expires_at' => now()->addMinutes(30), // Optional: set an expiration time
        ]);
        Audit::create([
            'action' => 'Registration code generated for new user: ' . $request->email,            // Set to null as it's not applicable here
        ]);
        // Send email with the registration code
        Mail::to($request->email)->send(new RegistrationCodeMail($registrationCode));

        return response()->json(['message' => 'Registration code sent to your email.']);
    }

    // Register a new staff member
    // public function register(Request $request)
    // {
    //     // Validate the incoming request
    //     $validatedData = $request->validate([
    //         'surname' => 'required|string|max:255',
    //         'other_names' => 'required|string|max:255',
    //         'date_of_birth' => 'required|date_format:Y-m-d',
    //         'unique_code' => 'required|string|size:10',
    //         'id_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Optional image
    //     ]);
    
    //     // Check if the unique code exists in the database
    //     $registrationCode = RegistrationCode::where('code', $validatedData['unique_code'])->first();
    
    //     // Validate the unique code
    //     if (!$registrationCode) {
    //         return response()->json(['error' => 'Invalid code'], 400);
    //     }
    
    //     // Generate Employee Number
    //     $employeeNumber = 'EMP' . Str::random(5); // Generate a unique employee number
    
    //     // Handle the ID photo upload if provided
    //     $photoPath = null;
    //     if ($request->hasFile('id_photo')) {
    //         // Define the folder structure
    //         $folderPath = 'uploads/staff/' . date('Y/m/d'); // e.g., uploads/staff/2024/10/06
    
    //         // Define the full path for storage
    //         $fullPath = public_path($folderPath);
            
    //         // Check if the directory exists, if not create it
    //         if (!is_dir($fullPath)) {
    //             mkdir($fullPath, 0755, true); // Create the directory recursively
    //         }
    
    //         $fileName = $employeeNumber . '.' . $request->file('id_photo')->getClientOriginalExtension();
    
    //         // Store the file in the public directory
    //         $request->file('id_photo')->move($fullPath, $fileName);
            
    //         // Get the relative path to store in the database
    //         $photoPath = $folderPath . '/' . $fileName; // Store the relative path
    //     }
    
    //     // Create the staff member in the database
    //     $staff = Member::create([
    //         'surname' => $validatedData['surname'],
    //         'othernames' => $validatedData['other_names'],
    //         'date_of_birth' => $validatedData['date_of_birth'],
    //         'employee_number' => $employeeNumber,
    //         'photo' => $photoPath, // Store the relative path
    //     ]);
    
    //     return response()->json(['message' => 'Staff registered successfully', 'employee_number' => $employeeNumber], 201);
    // }
    
public function register(Request $request)
{
    // Validate the incoming request
    $validatedData = $request->validate([
        'surname' => 'required|string|max:255',
        'other_names' => 'required|string|max:255',
        'date_of_birth' => 'required|date_format:Y-m-d',
        'unique_code' => 'required|string|size:10',
        'id_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Optional image
    ]);

    // Check if the unique code exists in the database
    $registrationCode = RegistrationCode::where('code', $validatedData['unique_code'])->first();

    // Validate the unique code
    if (!$registrationCode) {
        return response()->json(['error' => 'Invalid code'], 400);
    }

    // Generate Employee Number
    $employeeNumber = 'EMP' . Str::random(5); // Generate a unique employee number

    // Handle the ID photo upload if provided
    $photoPath = null;
    if ($request->hasFile('id_photo')) {
        // Define the folder structure
        $folderPath = 'staff/' . date('Y/m/d'); // e.g., staff/2024/10/06

        // Define the full path for storage in the public directory
        $fullPath = public_path($folderPath);

        // Check if the directory exists, if not create it
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true); // Create the directory recursively
        }

        // Define the file name with the employee number and original extension
        $fileName = $employeeNumber . '.' . $request->file('id_photo')->getClientOriginalExtension();

        // Store the file in the public directory
        $request->file('id_photo')->move($fullPath, $fileName);

        // Get the relative path to store in the database (accessible via URL)
        $photoPath = $folderPath . '/' . $fileName; // Store the relative path
    }

    // Create the staff member in the database
    $staff = Member::create([
        'surname' => $validatedData['surname'],
        'othernames' => $validatedData['other_names'],
        'date_of_birth' => $validatedData['date_of_birth'],
        'employee_number' => $employeeNumber,
        'photo' => $photoPath, // Store the relative path to the image
    ]);
    Audit::create([
        'action' => 'New staff registration with employee number: ' . $employeeNumber . ', surname: ' . $validatedData['surname'] . ', other name: ' . $validatedData['other_names'],         // Link the registration code if applicable
    ]);
    return response()->json(['message' => 'Staff registered successfully', 'employee_number' => $employeeNumber], 201);
}


    // Retrieve staff information
    public function retrieve(Request $request)
    {
        $employeeNumber = $request->route('employeeNumber'); // Retrieves from the URL parameter
        
        if ($employeeNumber) {
            $staff = Member::where('employee_number', $employeeNumber)->first();
            
            if (!$staff) {
                return response()->json(['error' => 'Employee not found'], 404);
            }
    
            // If the photo path is set, prepend it with the base URL; otherwise, set it to the default image
            if ($staff->photo) {
                $staff->photo = asset($staff->photo); // This converts the storage path to a full URL
            } else {
                $staff->photo = asset('staff/default.png'); // Path to default image in the staff folder
            }
            
            return response()->json($staff, 200);
        }
        
        $staff = Member::all();
    
        // Loop through all staff to prepend the base URL for photos
        foreach ($staff as $member) {
            if ($member->photo) {
                $member->photo = asset($member->photo);
            } else {
                $member->photo = asset('staff/default.png'); // Path to default image in the staff folder
            }
        }
    
        return response()->json($staff, 200);
    }
    
    
    
    
    

    // Get specific staff details by employee number
    public function getStaff($employeeNumber)
    {
        $staff = Member::where('employee_number', $employeeNumber)->first();
    
        if (!$staff) {
            return response()->json(['error' => 'Employee not found'], 404);
        }
    
        // Check if the photo path is set, if not set it to default.png
        if ($staff->photo) {
            $staff->photo_url = asset($staff->photo); // Use the provided photo path
        } else {
            $staff->photo_url = asset('staff/default.png'); // Path to default image in the staff folder
        }
    
        return response()->json($staff);
    }
    
    
    

    

    // Update the staff details
    // public function updateStaff(Request $request, $employeeNumber)
    // {
    //     // Find the staff member by employee number
    //     $staff = Member::where('employee_number', $employeeNumber)->first();
    
    //     if (!$staff) {
    //         return response()->json(['error' => 'Employee not found'], 404);
    //     }
    
    //     // Validate the incoming request
    //     $validatedData = $request->validate([
    //         'date_of_birth' => 'nullable|date_format:Y-m-d',
    //         'id_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Optional image
    //     ]);
    
    //     // Update date of birth if provided
    //     if ($request->has('date_of_birth')) {
    //         $staff->date_of_birth = $validatedData['date_of_birth'];
    //     }
    
    //     // Update the ID photo if provided
    //     if ($request->hasFile('id_photo')) {
    //         // If there is an old photo, delete it
    //         if ($staff->photo && file_exists(public_path($staff->photo))) {
    //             unlink(public_path($staff->photo)); // Remove the old image file
    //         }
    
    //         // Define the folder structure
    //         $folderPath = 'staff/' . date('Y/m/d'); // e.g., staff/2024/10/06
    
    //         // Define the full path for storage in the public directory
    //         $fullPath = public_path($folderPath);
    
    //         // Check if the directory exists, if not create it
    //         if (!is_dir($fullPath)) {
    //             mkdir($fullPath, 0755, true); // Create the directory recursively
    //         }
    
    //         // Define the file name with the employee number and original extension
    //         $fileName = $employeeNumber . '.' . $request->file('id_photo')->getClientOriginalExtension();
    
    //         // Store the file in the public directory
    //         $request->file('id_photo')->move($fullPath, $fileName);
    
    //         // Get the relative path to store in the database (accessible via URL)
    //         $photoPath = $folderPath . '/' . $fileName; // Store the relative path
    
    //         // Update the photo path in the database
    //         $staff->photo = $photoPath;
    //     }
    
    //     // Save the updated staff member
    //     $staff->save();
        
    
    //     return response()->json(['message' => 'Staff details updated successfully'], 200);
    // }
    public function updateStaff(Request $request, $employeeNumber)
{
    // Find the staff member by employee number
    $staff = Member::where('employee_number', $employeeNumber)->first();

    if (!$staff) {
        return response()->json(['error' => 'Employee not found'], 404);
    }

    // Validate the incoming request
    $validatedData = $request->validate([
        'date_of_birth' => 'nullable|date_format:Y-m-d',
        'id_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Optional image
    ]);

    // Initialize an array to hold audit messages
    $auditMessages = [];

    // Update date of birth if provided
    if ($request->has('date_of_birth')) {
        $staff->date_of_birth = $validatedData['date_of_birth'];
        $auditMessages[] = "updated their date of birth";
    }

    // Update the ID photo if provided
    if ($request->hasFile('id_photo')) {
        // If there is an old photo, delete it
        if ($staff->photo && file_exists(public_path($staff->photo))) {
            unlink(public_path($staff->photo)); // Remove the old image file
        }

        // Define the folder structure
        $folderPath = 'staff/' . date('Y/m/d'); // e.g., staff/2024/10/06

        // Define the full path for storage in the public directory
        $fullPath = public_path($folderPath);

        // Check if the directory exists, if not create it
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true); // Create the directory recursively
        }

        // Define the file name with the employee number and original extension
        $fileName = $employeeNumber . '.' . $request->file('id_photo')->getClientOriginalExtension();

        // Store the file in the public directory
        $request->file('id_photo')->move($fullPath, $fileName);

        // Get the relative path to store in the database (accessible via URL)
        $photoPath = $folderPath . '/' . $fileName; // Store the relative path

        // Update the photo path in the database
        $staff->photo = $photoPath;

        $auditMessages[] = "updated their photo";
    }

    // Save the updated staff member
    $staff->save();

    // Create the audit log entry with the specified syntax
    Audit::create([
        'action' => 'Staff with employee number: ' . $employeeNumber . 
                    ', ' . implode(' and ', $auditMessages),
    ]);

    return response()->json(['message' => 'Staff details updated successfully'], 200);
}

    
}
