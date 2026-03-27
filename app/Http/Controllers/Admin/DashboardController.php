<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Services\AnalyticsService;

class DashboardController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Parking spaces
        |--------------------------------------------------------------------------
        */
        $parkingSpaces = DB::table('parking_spaces')->get()->map(function ($space) {
            $space->available_slots_count = DB::table('parking_slots')
                ->where('parking_space_id', $space->id)
                ->where('status', 'available')
                ->count();

            $space->occupied_slots = DB::table('parking_slots')
                ->where('parking_space_id', $space->id)
                ->where('status', 'occupied')
                ->count();

            return $space;
        });

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Live slot statistics (CORRECT WAY)
        |--------------------------------------------------------------------------
        */
        $totalSpaces = $parkingSpaces->count();

        $totalCapacity = DB::table('parking_slots')->count();

        $totalAvailable = DB::table('parking_slots')
            ->where('status', 'available')
            ->count();

        $totalOccupied = DB::table('parking_slots')
            ->where('status', 'occupied')
            ->count();

        // 2. Multi-Line Chart Aggregation & Data Fixing
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $chartData = [
            'labels' => $months,
            'datasets' => []
        ];
        $reportData = [];

        // Colors for the lines (Blue and Orange like the user's reference)
        $colors = ['#3b82f6', '#f97316', '#10b981', '#8b5cf6', '#ef4444']; 
        $colorIndex = 0;

        $lots = DB::table('parking_spaces')
            ->leftJoin('users', 'parking_spaces.owner_id', '=', 'users.id')
            ->select('parking_spaces.*', 'users.name as owner_name')
            ->get();

        foreach($lots as $lot) {
            // --- A. Process Online User Bookings & B. Manual Gate Entries ---
            $bookings = DB::table('bookings')
                ->where('parking_space_id', $lot->id)
                ->whereIn('status', ['completed', 'booked', 'occupied', 'reserved'])
                ->whereYear('created_at', date('Y'))
                ->get();

            $manuals = DB::table('vehicles')
                ->where('parking_space_id', $lot->id)
                ->where('status', 'exited')
                ->whereYear('created_at', date('Y'))
                ->get();

            $monthlyRevenue = $this->analyticsService->aggregateMonthlyRevenue($bookings, $manuals, (int)date('Y'));
            
            $totalRev = array_sum($monthlyRevenue);
            $totalBookings = $bookings->count() + $manuals->count();

            // --- 3. Add to Chart Dataset (Configured for a Line Graph) ---
            $chartData['datasets'][] = [
                'label' => $lot->name,
                'data' => $monthlyRevenue,
                'borderColor' => $colors[$colorIndex % count($colors)],
                'backgroundColor' => $colors[$colorIndex % count($colors)],
                'borderWidth' => 3,
                'tension' => 0.1, // Slight curve, mostly straight lines
                'fill' => false,
                'pointBackgroundColor' => '#fff',
                'pointBorderWidth' => 2,
                'pointRadius' => 4
            ];
            
            $colorIndex++;
            
            // --- 4. Assign Unified Stats for the Table ---
            $lot->unified_total_revenue = $totalRev;
            $lot->unified_bookings_count = $totalBookings;
            $lot->owner = (object)['name' => $lot->owner_name ?? 'System']; // Fallback mapped for Blade
            $reportData[] = $lot;
        }

        return view('admin.dashboard', compact(
            'parkingSpaces',
            'totalSpaces',
            'totalCapacity',
            'totalAvailable',
            'totalOccupied',
            'chartData',
            'reportData'
        ));
    }

    public function downloadReport() 
    {
        $lots = DB::table('parking_spaces')
            ->leftJoin('users', 'parking_spaces.owner_id', '=', 'users.id')
            ->select('parking_spaces.*', 'users.name as owner_name', 'users.email as owner_email')
            ->get();
        
        // --- 1. GATHER AND CALCULATE DATA FIRST ---
        $processedData = [];
        $overallBookings = 0;
        $overallRevenue = 0;
            
            foreach ($lots as $lot) {
                $totalRev = 0;
                $totalBookings = 0;
                
                // Online Bookings
                $bookingsData = DB::table('bookings')
                    ->where('parking_space_id', $lot->id)
                    ->whereIn('status', ['completed', 'booked', 'occupied', 'reserved'])
                    ->select(DB::raw('COUNT(id) as count, SUM(amount) as total'))
                    ->first();
                    
                $totalRev += (float)($bookingsData->total ?? 0);
                $totalBookings += (int)($bookingsData->count ?? 0);
                
                // Manual Entries
                $manualsData = DB::table('vehicles')
                    ->where('parking_space_id', $lot->id)
                    ->where('status', 'exited')
                    ->select(DB::raw('COUNT(id) as count, SUM(charge) as total'))
                    ->first();
                    
                $totalRev += (float)($manualsData->total ?? 0);
                $totalBookings += (int)($manualsData->count ?? 0);
                
                $overallBookings += $totalBookings;
                $overallRevenue += $totalRev;
                
                $processedData[] = [
                    'name' => $lot->name,
                    'owner_name' => $lot->owner_name ?? 'System / Unassigned',
                    'owner_email' => $lot->owner_email ?? 'N/A',
                    'capacity' => $lot->capacity . ' Slots', // Accessing capacity directly from parking_spaces
                    'bookings' => $totalBookings,
                    'revenue' => $totalRev
                ];
            }
            
            // --- 2. SORT DATA BY REVENUE (Highest to Lowest) using AnalyticsService ---
            $processedData = $this->analyticsService->rankParkingSpaces($processedData);
            
            $reportData = [
                'lots' => $processedData,
                'overallBookings' => $overallBookings,
                'overallRevenue' => $overallRevenue,
                'topPerformer' => count($processedData) > 0 ? $processedData[0] : null,
                'runnerUp' => count($processedData) > 1 ? $processedData[1] : null,
            ];
            
            // Force the HTML view to download as an Excel file
            $filename = "ParkX_Financial_Report_" . date('Y_m_d_Hi') . ".xls";
            $html = view('admin.exports.financial_report', ['data' => $reportData])->render();
            
            return response($html)
                ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
    }
}
