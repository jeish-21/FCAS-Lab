<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SupportTicketController extends Controller
{
    /**
     * ==========================================
     * FACULTY SUPPORT TICKET METHODS
     * ==========================================
     */

    // 1. Ipakita ang Support page sa Faculty
    public function facultyIndex()
    {
        $tickets = collect();

        if (Schema::hasTable('support_tickets')) {
            $tickets = DB::table('support_tickets')
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('faculty.support', compact('tickets'));
    }

    // 2. I-submit ang Support Ticket gikan sa Faculty
    public function facultySubmit(Request $request)
    {
        $request->validate([
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (Schema::hasTable('support_tickets')) {
            DB::table('support_tickets')->insert([
                'user_id'    => $user->id,
                'user_name'  => $user->name,
                'email'      => $user->email,
                'subject'    => $request->input('subject'),
                'category'   => $request->input('category', 'Technical Support'),
                'message'    => $request->input('message'),
                'status'     => 'open',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Support ticket submitted successfully to Admin!');
    }


    /**
     * ==========================================
     * STUDENT SUPPORT TICKET METHODS
     * ==========================================
     */

    // 3. Ipakita ang Support page sa Student
    public function studentIndex()
    {
        return view('student.support');
    }

    // 4. I-submit ang Support Ticket gikan sa Student
    public function studentSubmit(Request $request)
    {
        $request->validate([
            'subject'     => 'required|string',
            'description' => 'required|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (Schema::hasTable('support_tickets')) {
            DB::table('support_tickets')->insert([
                'user_id'    => $user->id,
                'user_name'  => $user->name,
                'email'      => $user->email,
                'subject'    => $request->input('subject'),
                'category'   => $request->input('subject'),
                'message'    => $request->input('description'),
                'status'     => 'open',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Your support ticket has been submitted successfully to Admin!');
    }


    /**
     * ==========================================
     * ADMIN SUPPORT TICKET METHODS
     * ==========================================
     */

    // 5. Admin View All Support Tickets naay Search ug Status filter
    public function adminIndex(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $tickets = collect();

        if (Schema::hasTable('support_tickets')) {
            $query = DB::table('support_tickets');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('subject', 'LIKE', "%{$search}%")
                      ->orWhere('user_name', 'LIKE', "%{$search}%")
                      ->orWhere('message', 'LIKE', "%{$search}%");
                });
            }

            if (!empty($status)) {
                $query->where('status', $status);
            }

            $tickets = $query->orderBy('created_at', 'desc')->get();
        }

        return view('admin.support-tickets', compact('tickets', 'search', 'status'));
    }

    // 6. Admin Update Support Ticket Status (gikan sa 'open' ngadto sa 'closed', etc.)
    public function adminUpdateStatus(Request $request, $id)
    {
        $status = $request->input('status');

        if (Schema::hasTable('support_tickets')) {
            DB::table('support_tickets')->where('id', $id)->update([
                'status'     => $status,
                'updated_at' => now(),
            ]);
            return back()->with('success', "Support ticket status updated to " . strtoupper($status) . "!");
        }

        return back()->with('success', "Action simulated for Ticket #{$id}. Status set to " . strtoupper($status) . "!");
    }
}