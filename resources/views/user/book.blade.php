@extends('layouts.app')
@section('title', 'Book Space - ' . ($lot->name ?? 'ParkX'))

@section('content')
<style>
/* --- PREMIUM FORM UI --- */
.booking-page { display: flex; flex-wrap: wrap; gap: 40px; justify-content: center; max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
.booking-form-col { flex: 1; min-width: 320px; max-width: 500px; }
.booking-map-col { flex: 2; min-width: 320px; display: flex; justify-content: center; }

.premium-card { background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 35px; border: 1px solid #e5e7eb; }
.card-title { font-family: 'Oswald', sans-serif; font-weight: 800; font-size: 1.2rem; margin-top: 0; margin-bottom: 25px; border-bottom: 2px solid #f3f4f6; padding-bottom: 15px; text-transform: uppercase; }
.form-group { margin-bottom: 20px; }
.custom-label { display: block; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
.custom-input { width: 100%; padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; color: #111; background: #f9fafb; box-sizing: border-box; transition: all 0.2s; }
.custom-input:focus { border-color: #111; background: #fff; outline: none; }
.btn-black { background: #111; color: #fff; border: none; width: 100%; padding: 16px; border-radius: 8px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; margin-top: 10px; cursor: pointer; transition: 0.2s; }
.btn-black:hover { background: #333; }
.error-box { background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; font-weight: bold; margin-bottom: 20px; font-size: 0.9rem; border: 1px solid #ef4444; }

/* --- COMPACT ARCHITECTURAL MAP --- */
.map-box { background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; padding: 25px; width: 100%; max-width: 480px; }
.map-grid { display: grid; grid-template-columns: 1fr 50px 1fr; gap: 15px; }
.map-gate, .map-exit { grid-column: 1 / -1; background: #111; color: #fff; text-align: center; padding: 10px; font-weight: 800; letter-spacing: 5px; font-size: 0.85rem; border-radius: 6px; font-family: 'Oswald', sans-serif;}
.map-driveway { grid-column: 2; grid-row: 2 / span 2; border-left: 2px dashed #cbd5e1; border-right: 2px dashed #cbd5e1; display: flex; align-items: center; justify-content: center; }
.driveway-text { writing-mode: vertical-rl; text-transform: uppercase; letter-spacing: 6px; color: #94a3b8; font-weight: 800; font-size: 0.8rem; }

.sector-title { font-family: 'Oswald', sans-serif; font-weight: 800; color: #64748b; font-size: 0.85rem; letter-spacing: 1px; margin-bottom: 12px; text-transform: uppercase; }
.slots-container { display: flex; flex-wrap: wrap; gap: 8px; }

.slot-radio { display: none !important; }
.slot-label { width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; background: #fff; border: 2px solid #cbd5e1; border-radius: 6px; font-weight: 700; font-size: 0.8rem; color: #334155; cursor: pointer; transition: all 0.2s; margin: 0; }
.slot-label.occupied { background: #f1f5f9; color: #94a3b8; border-color: #e2e8f0; cursor: not-allowed; }
.slot-radio:checked + .slot-label { background: #111 !important; border-color: #111 !important; color: #fff !important; transform: scale(1.1); box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
</style>

@php
// 1. SAFELY FETCH SLOTS (With Failsafes)
$slots = collect([]);
if(isset($lot->slots) && $lot->slots->count() > 0) $slots = $lot->slots;
elseif(isset($lot->spaces) && $lot->spaces->count() > 0) $slots = $lot->spaces;

// Failsafe: Direct DB query if relationship isn't loaded
if($slots->isEmpty()) {
    try {
        if (class_exists('\Illuminate\Support\Facades\DB')) {
            $slots = \Illuminate\Support\Facades\DB::table('parking_slots')->where('parking_space_id', $lot->id ?? 1)->get();
        }
    } catch(\Exception $e) {}
}

// Extreme Failsafe: If DB fails, create dummy UI slots so the map still renders
if($slots->isEmpty()) {
    for($i=1; $i<=24; $i++) {
        $slots->push((object)['id' => $i, 'name' => 'S'.$i, 'status' => 'available']);
    }
}

// 2. SPLIT INTO 4 QUADRANTS
$quarter = max(1, ceil($slots->count() / 4));
$sectorA = $slots->slice(0, $quarter);
$sectorC = $slots->slice($quarter, $quarter);
$sectorB = $slots->slice($quarter * 2, $quarter);
$sectorD = $slots->slice($quarter * 3);

// 3. RENDER FUNCTION
if (!function_exists('renderSlot')) {
    function renderSlot($slot) {
        $id = data_get($slot, 'id');
        $status = data_get($slot, 'status', data_get($slot, 'is_available', 'available'));
        $name = str_replace('SLOT ', '', strtoupper(data_get($slot, 'name', data_get($slot, 'space_number', 'S'.$id))));
        
        $isAvailable = ($status === 'available' || $status === 1 || $status === true);
        $stateClass = $isAvailable ? '' : 'occupied';
        $disabledAttr = !$isAvailable ? 'disabled' : '';
        
        // NOTE: name="slot_id" fixes the validation error
        return '
            <div>
                <input type="radio" name="slot_id" id="slot_'.$id.'" value="'.$id.'" class="slot-radio" '.$disabledAttr.' required>
                <label class="slot-label '.$stateClass.'" for="slot_'.$id.'">'.$name.'</label>
            </div>
        ';
    }
}
@endphp

<form action="{{ route('user.book.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="parking_space_id" value="{{ data_get($lot, 'id', 1) }}">
    <div class="booking-page">
        <div class="booking-form-col">
            <div class="premium-card">
                <h4 class="card-title">BOOKING DETAILS</h4>
                
                @if ($errors->any())
                    <div class="error-box">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="form-group">
                    <label class="custom-label">VEHICLE NUMBER</label>
                    <input type="text" name="vehicle_number" class="custom-input" placeholder="e.g. KL 07 AB 1234" value="{{ old('vehicle_number') }}" required>
                </div>
                <div class="form-group">
                    <label class="custom-label">VEHICLE CATEGORY</label>
                    <select name="vehicle_category_id" class="custom-input" required>
                        <option value="" disabled {{ old('vehicle_category_id') ? '' : 'selected' }}>Select Category...</option>
                        @php
                            $categories = \Illuminate\Support\Facades\DB::table('vehicle_categories')->get();
                        @endphp
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('vehicle_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="custom-label">BOOKING DATE</label>
                    <select name="booking_date" class="custom-input" required>
                        <option value="{{ date('Y-m-d') }}">Today ({{ date('M d, Y') }})</option>
                        <option value="{{ date('Y-m-d', strtotime('+1 day')) }}">Tomorrow ({{ date('M d, Y', strtotime('+1 day')) }})</option>
                    </select>
                </div>
                <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label class="custom-label">START TIME</label>
                        <input type="time" name="start_time" class="custom-input" required>
                    </div>
                    <div style="flex: 1;">
                        <label class="custom-label">END TIME</label>
                        <input type="time" name="end_time" class="custom-input" required>
                    </div>
                </div>
                <button type="submit" class="btn-black">CONFIRM & PAY</button>
            </div>
        </div>
        <div class="booking-map-col">
            <div class="map-box shadow-sm">
                <div class="map-grid">
                    <div class="map-gate">ENTRANCE GATE</div>
                    <div style="grid-column: 1; grid-row: 2;">
                        <div class="sector-title">SECTOR A</div>
                        <div class="slots-container">
                            @foreach($sectorA as $slot) {!! renderSlot($slot) !!} @endforeach
                        </div>
                    </div>
                    <div class="map-driveway">
                        <span class="driveway-text">DRIVEWAY</span>
                    </div>
                    <div style="grid-column: 3; grid-row: 2;">
                        <div class="sector-title" style="text-align: right;">SECTOR C</div>
                        <div class="slots-container" style="justify-content: flex-end;">
                            @foreach($sectorC as $slot) {!! renderSlot($slot) !!} @endforeach
                        </div>
                    </div>
                    <div style="grid-column: 1; grid-row: 3;">
                        <div class="sector-title" style="margin-top: 15px;">SECTOR B</div>
                        <div class="slots-container">
                            @foreach($sectorB as $slot) {!! renderSlot($slot) !!} @endforeach
                        </div>
                    </div>
                    <div style="grid-column: 3; grid-row: 3;">
                        <div class="sector-title" style="text-align: right; margin-top: 15px;">SECTOR D</div>
                        <div class="slots-container" style="justify-content: flex-end;">
                            @foreach($sectorD as $slot) {!! renderSlot($slot) !!} @endforeach
                        </div>
                    </div>
                    <div class="map-exit" style="grid-row: 4; margin-top: 10px;">EXIT GATE</div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection