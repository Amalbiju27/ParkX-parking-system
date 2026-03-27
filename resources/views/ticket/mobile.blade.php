<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ParkX Ticket - #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .ticket-card { border: none; border-radius: 1rem; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; }
        .ticket-header { background: #000; color: #fff; padding: 2rem 1.5rem; text-align: center; }
        .ticket-body { padding: 2rem 1.5rem; background: #fff; position: relative; }
        .ticket-divider { border-top: 2px dashed #e9ecef; margin: 1.5rem 0; position: relative; }
        .ticket-divider::before, .ticket-divider::after { content: ''; position: absolute; top: -15px; width: 30px; height: 30px; background: #f8f9fa; border-radius: 50%; }
        .ticket-divider::before { left: -30px; }
        .ticket-divider::after { right: -30px; }
        .status-badge { font-weight: 700; letter-spacing: 1px; padding: 0.5rem 1rem; border-radius: 50rem; text-transform: uppercase; font-size: 0.75rem; }
        .status-paid { background: #d1e7dd; color: #0f5132; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-cancelled { background: #f8d7da; color: #842029; }
        .data-label { font-size: 0.75rem; color: #6c757d; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.25rem; font-weight: 600; }
        .data-value { font-size: 1.1rem; font-weight: 700; color: #212529; margin-bottom: 0; }
        .slot-display { background: #f8f9fa; border: 2px solid #e9ecef; border-radius: 0.75rem; padding: 1.5rem; text-align: center; margin: 1.5rem 0; }
        .slot-label { font-size: 0.875rem; color: #6c757d; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; }
        .slot-number { font-size: 4rem; font-weight: 900; line-height: 1; color: #000; letter-spacing: -2px; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                
                <div class="ticket-card">
                    <!-- Header -->
                    <div class="ticket-header">
                        <div class="mb-3 d-flex justify-content-center align-items-center gap-2">
                             <div class="bg-white rounded-circle d-flex align-items-center justify-content-center p-2" style="width: 50px; height: 50px;">
                                <img src="{{ asset('parkx-logo.svg') }}" alt="ParkX" style="width: 30px;">
                             </div>
                             <h2 class="mb-0 fw-black text-uppercase tracking-tighter" style="font-weight: 900; letter-spacing: -1px;">PARKX</h2>
                        </div>
                        <h5 class="text-white-50 mb-0 text-uppercase" style="font-size: 0.85rem; letter-spacing: 2px;">Mobile Ticket</h5>
                    </div>

                    <!-- Body -->
                    <div class="ticket-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <p class="data-label">Booking ID</p>
                                <p class="data-value text-muted">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <div>
                                @php
                                    $statusClass = 'status-pending';
                                    if ($booking->payment_status === 'paid' && $booking->status !== 'cancelled') $statusClass = 'status-paid';
                                    if ($booking->status === 'cancelled') $statusClass = 'status-cancelled';
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    @if($booking->status === 'cancelled')
                                        CANCELLED
                                    @elseif($booking->payment_status === 'paid')
                                        <i class="fas fa-check-circle me-1"></i> PAID
                                    @else
                                        <i class="fas fa-clock me-1"></i> PENDING
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="data-label">Location</p>
                            <p class="data-value fs-4"><i class="fas fa-map-marker-alt text-danger me-2"></i>{{ $booking->space_name }}</p>
                            <p class="text-muted small mt-1 mb-0">{{ $booking->location ?? 'Downtown District' }}</p>
                        </div>

                        <!-- Slot Box -->
                        <div class="slot-display">
                            <div class="slot-label mb-2">Assigned Slot</div>
                            <div class="slot-number">{{ $booking->slot_id }}</div>
                        </div>

                        <!-- Manual PIN Fallback -->
                        <div class="bg-light border rounded-3 p-3 text-center mb-4">
                            <span class="text-muted small fw-bold tracking-widest uppercase d-block mb-1">TICKET PIN</span>
                            <span class="h2 fw-bolder tracking-widest text-dark">{{ $booking->ticket_number ?? '--- ---' }}</span>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <p class="data-label">Vehicle</p>
                                <p class="data-value font-monospace">{{ strtoupper($booking->vehicle_number) }}</p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="data-label">Date</p>
                                <p class="data-value">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <div class="ticket-divider"></div>

                        <div class="row mb-0">
                            <div class="col-6">
                                <p class="data-label">Check-In</p>
                                <p class="data-value text-success"><i class="fas fa-arrow-right-to-bracket me-2"></i>{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }}</p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="data-label">Check-Out</p>
                                <p class="data-value text-danger"><i class="fas fa-arrow-right-from-bracket me-2"></i>{{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</p>
                            </div>
                        </div>
                        
                        @if($booking->scanned_at)
                            <div class="mt-4 p-3 bg-light rounded text-center border">
                                <p class="data-label text-success mb-1"><i class="fas fa-check-double me-1"></i> SCANNED AT GATE</p>
                                <p class="fw-bold mb-0 small">{{ \Carbon\Carbon::parse($booking->scanned_at)->format('M d, Y h:i A') }}</p>
                            </div>
                        @else
                            <div class="mt-4 p-3 bg-light rounded text-center border">
                                <p class="data-label text-warning mb-1"><i class="fas fa-hourglass-half me-1"></i> AWAITING ARRIVAL</p>
                                <p class="text-muted mb-0 small">Please present this ticket at the entry gate.</p>
                            </div>
                        @endif

                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-muted small">Powered by ParkX Platform &copy; {{ date('Y') }}</p>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
