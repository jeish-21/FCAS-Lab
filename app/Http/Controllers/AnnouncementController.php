<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AnnouncementController extends Controller
{
    /**
     * ==========================================
     * FACULTY ANNOUNCEMENT METHODS
     * ==========================================
     */

    // 1. Ipakita ang Announcements page sa Faculty
    public function facultyIndex()
    {
        $facultyId = Auth::id();
        $announcements = collect();

        if (Schema::hasTable('announcements')) {
            $announcements = DB::table('announcements')
                ->where('user_id', $facultyId)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('faculty.announcements', compact('announcements'));
    }

    // 2. I-store o I-save ang bag-ong Announcement gikan sa Faculty
    public function facultyStore(Request $request)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'target_audience' => 'required|string',
            'content'         => 'required|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (Schema::hasTable('announcements')) {
            DB::table('announcements')->insert([
                'user_id'         => $user->id,
                'title'           => $request->input('title'),
                'target_audience' => $request->input('target_audience'),
                'content'         => $request->input('content'),
                'author'          => $user->name ?? 'Faculty Member',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        return back()->with('success', 'Announcement published successfully!');
    }

    // 3. Quick Post Announcement gikan sa Faculty
    public function facultyQuickPost(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        if (Schema::hasTable('announcements')) {
            DB::table('announcements')->insert([
                'user_id'    => Auth::id(),
                'title'      => 'Faculty Announcement',
                'content'    => $request->input('message'),
                'author'     => Auth::user()->name ?? 'Faculty Member',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Announcement posted successfully! Students can now see this in their Announcements module.');
    }

    // 4. Pagtangtang o Delete sa Announcement
    public function facultyDelete($id)
    {
        if (Schema::hasTable('announcements')) {
            DB::table('announcements')
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->delete();
        }

        return back()->with('success', 'Announcement deleted successfully!');
    }


    /**
     * ==========================================
     * STUDENT ANNOUNCEMENT METHODS
     * ==========================================
     */

    // 5. Ipakita ang tanang Announcements sa Student side
    public function studentIndex()
    {
        $announcements = collect();

        if (Schema::hasTable('announcements')) {
            $announcements = DB::table('announcements')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('student.announcements', compact('announcements'));
    }
}