<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AcademicCalendarScraper;
use App\Models\KalenderAkademik;

class AcademicCalendarController extends Controller
{
    private $scraper;

    public function __construct(AcademicCalendarScraper $scraper)
    {
        $this->scraper = $scraper;
    }

    public function index(Request $request)
    {
        $query = KalenderAkademik::query();
        
        // Filter by class type
        if ($request->has('class_filter') && $request->class_filter !== 'all') {
            $query->filterByClass($request->class_filter);
        }
        
        // Order by date
        if ($request->has('order_by') && $request->has('order_direction')) {
            $query->orderByDate($request->order_by, $request->order_direction);
        } else {
            $query->orderBy('tanggal_mulai', 'asc');
        }
        
        $kalender = $query->get();
        
        return view('academic-calendar.index', compact('kalender'));
    }

    public function showLoginForm()
    {
        return view('academic-calendar.login');
    }

    public function scrape(Request $request)
    {
        $request->validate([
            'nim' => 'required',
            'password' => 'required'
        ]);

        // Login first
        $loginSuccess = $this->scraper->login($request->nim, $request->password);
        
        if (!$loginSuccess) {
            return redirect()->back()->with('error', 'Login gagal. Periksa NIM dan password Anda.');
        }

        // Scrape calendar
        $scrapeSuccess = $this->scraper->scrapeCalendar();
        
        if ($scrapeSuccess) {
            return redirect()->route('academic-calendar.index')->with('success', 'Data kalender akademik berhasil diperbarui.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengambil data kalender akademik.');
        }
    }
}