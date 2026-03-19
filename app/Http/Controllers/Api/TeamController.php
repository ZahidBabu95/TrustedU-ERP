<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * GET /api/team — List all team members
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['profile', 'financial'])
            ->where('is_active', true);

        // Filter by department
        if ($request->has('department')) {
            $query->where('department', $request->department);
        }

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $members = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $members->through(fn(User $user) => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'phone'       => $user->phone,
                'role'        => $user->role,
                'department'  => $user->department,
                'designation' => $user->designation,
                'is_active'   => $user->is_active,
                'avatar_url'  => $user->getFilamentAvatarUrl(),
                'profile'     => $user->profile ? [
                    'date_of_birth'   => $user->profile->date_of_birth?->format('Y-m-d'),
                    'gender'          => $user->profile->gender,
                    'joining_date'    => $user->profile->joining_date?->format('Y-m-d'),
                    'employment_type' => $user->profile->employment_type,
                    'bio'             => $user->profile->bio,
                ] : null,
                'joined_at' => $user->created_at->format('Y-m-d'),
            ]),
            'meta' => [
                'current_page' => $members->currentPage(),
                'last_page'    => $members->lastPage(),
                'total'        => $members->total(),
            ],
        ]);
    }

    /**
     * GET /api/team/{id} — Get single team member
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['profile', 'documents', 'financial']);

        return response()->json([
            'success' => true,
            'data' => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'phone'       => $user->phone,
                'role'        => $user->role,
                'department'  => $user->department,
                'designation' => $user->designation,
                'is_active'   => $user->is_active,
                'avatar_url'  => $user->getFilamentAvatarUrl(),
                'profile'     => $user->profile,
                'documents'   => $user->documents->map(fn($doc) => [
                    'id'       => $doc->id,
                    'title'    => $doc->title,
                    'type'     => $doc->type,
                    'file_url' => $doc->file_url,
                    'file_size' => $doc->file_size_formatted,
                ]),
                'financial' => $user->financial ? [
                    'bank_name'             => $user->financial->bank_name,
                    'account_holder_name'   => $user->financial->account_holder_name,
                    'mobile_banking_type'   => $user->financial->mobile_banking_type,
                    'mobile_banking_number' => $user->financial->mobile_banking_number,
                ] : null,
            ],
        ]);
    }

    /**
     * PUT /api/profile — Update own profile (authenticated)
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'                    => 'sometimes|string|max:255',
            'phone'                   => 'sometimes|nullable|string|max:20',
            'date_of_birth'           => 'sometimes|nullable|date',
            'gender'                  => 'sometimes|nullable|in:male,female,other',
            'address'                 => 'sometimes|nullable|string',
            'bio'                     => 'sometimes|nullable|string',
            'emergency_contact_name'  => 'sometimes|nullable|string|max:255',
            'emergency_contact_phone' => 'sometimes|nullable|string|max:20',
            'bank_name'              => 'sometimes|nullable|string|max:255',
            'account_number'         => 'sometimes|nullable|string|max:255',
            'account_holder_name'    => 'sometimes|nullable|string|max:255',
            'branch_name'            => 'sometimes|nullable|string|max:255',
            'mobile_banking_type'    => 'sometimes|nullable|in:bkash,nagad,rocket,upay,other',
            'mobile_banking_number'  => 'sometimes|nullable|string|max:20',
        ]);

        // Update user fields
        $userFields = array_intersect_key($validated, array_flip(['name', 'phone']));
        if (!empty($userFields)) {
            $user->update($userFields);
        }

        // Update profile
        $profileFields = array_intersect_key($validated, array_flip([
            'date_of_birth', 'gender', 'address', 'bio',
            'emergency_contact_name', 'emergency_contact_phone',
        ]));
        if (!empty($profileFields)) {
            $user->profile()->updateOrCreate(['user_id' => $user->id], $profileFields);
        }

        // Update financial
        $financialFields = array_intersect_key($validated, array_flip([
            'bank_name', 'account_number', 'account_holder_name',
            'branch_name', 'mobile_banking_type', 'mobile_banking_number',
        ]));
        if (!empty($financialFields)) {
            $user->financial()->updateOrCreate(['user_id' => $user->id], $financialFields);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
        ]);
    }
}
