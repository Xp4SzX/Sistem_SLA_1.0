<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\Service;
use App\Models\DinasAdmin;
use App\Models\TicketAttachment;
use Carbon\Carbon;

class TicketController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $tickets = Ticket::with(['service', 'service.sla', 'admin'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $stats = [
            'total'       => $tickets->count(),
            'in_progress' => $tickets->whereIn('status', ['submitted', 'verified', 'assigned', 'in_progress'])->count(),
            'resolved'    => $tickets->where('status', 'resolved')->count(),
            'need_rating' => $tickets->where('status', 'resolved')->whereNull('rating')->count(),
        ];

        return view('pelapor.dashboard', compact('tickets', 'stats'));
    }

    public function ticketingView()
    {
        $userId = Auth::id();

        $tickets = Ticket::with(['service', 'service.sla', 'admin', 'logs', 'attachments'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $services = Service::all();

        return view('pelapor.ticketing', compact('tickets', 'services'));
    }

    /**
     * Form Buat Tiket dengan pemisahan layanan Controller-side
     */
    public function create()
    {
        $kendalaServices = Service::where('type', 'kendala')->get();
        $skServices = Service::where('type', '!=', 'kendala')->get();
        
        // Gabungkan seluruhnya untuk referensi objek layanan di frontend
        $services = Service::all();
        
        // Mengambil data Perangkat Daerah / OPD langsung dari tabel `dinas_admins`
        $departments = DinasAdmin::orderBy('nama_dinas')->get();

        return view('pelapor.create-ticket', compact('services', 'kendalaServices', 'skServices', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id'  => 'required|exists:services,id',
            'request_type'=> 'required|in:kendala_teknis,pengajuan_layanan',
            'title'       => 'required|string|max:150',
            'category'    => 'required|string',
            'priority'    => 'required|in:low,medium,high,urgent',
            'description' => 'required|string',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'request_type'=> 'nullable|string',
        ]);

        $todayCode    = Carbon::now()->format('Ymd');
        $latestTicket = Ticket::whereDate('created_at', Carbon::today())->latest('id')->first();
        $nextNumber   = 1;
        
        if ($latestTicket && preg_match('/-(\d{5})$/', $latestTicket->ticket_no, $matches)) {
            $nextNumber = ((int)$matches[1]) + 1;
        }

        $ticketNo = 'SLA-' . $todayCode . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        while (Ticket::where('ticket_no', $ticketNo)->exists()) {
            $nextNumber++;
            $ticketNo = 'SLA-' . $todayCode . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        }

        $ticket = Ticket::create([
            'ticket_no'    => $ticketNo,
            'user_id'      => Auth::id(),
            'service_id'   => $request->service_id,
            'request_type' => $request->request_type,
            'title'        => $request->title,
            'category'     => $request->category,
            'priority'     => $request->priority,
            'description'  => $request->description,
            'status'       => 'submitted',
        ]);

        if ($request->hasFile('attachment')) {
            $file     = $request->file('attachment');
            $filePath = $file->store('ticket-attachments', 'public');

            TicketAttachment::create([
                'ticket_id' => $ticket->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $file->getClientMimeType(),
            ]);
        }

        return redirect()->route('user.ticketing')->with('success', "Tiket aduan {$ticketNo} berhasil dibuat dan dikirim ke tim Helpdesk!");
    }

    public function show($ticket_no)
    {
        $ticket = Ticket::with(['service', 'service.sla', 'admin', 'logs.user', 'attachments'])
            ->where('ticket_no', $ticket_no)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pelapor.tickets-show', compact('ticket'));
    }

    public function rate(Request $request, $ticket_no)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $ticket = Ticket::where('ticket_no', $ticket_no)
            ->where('user_id', Auth::id())
            ->where('status', 'resolved')
            ->firstOrFail();

        $ticket->update([
            'rating'     => $request->rating,
            'feedback'   => $request->comment,
            'status'     => 'closed',
            'closed_at'  => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Terima kasih atas ulasan dan penilaian layanan yang diberikan!');
    }

    public function trackPublic(Request $request)
    {
        $ticketNo = $request->query('ticket_no');
        $ticket   = null;

        if ($ticketNo) {
            $ticket = Ticket::with(['service', 'service.sla', 'attachments'])
                ->where('ticket_no', trim($ticketNo))
                ->first();
        }

        return view('pelapor.track-public', compact('ticket', 'ticketNo'));
    }

    public function updateByTechnician(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $request->validate([
            'technician_notes' => 'nullable|string',
            'technician_file'  => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'status'           => 'required|string',
        ]);

        $ticket->technician_notes = $request->technician_notes;
        $ticket->status           = $request->status;

        // Handle Upload Berkas/Lampiran dari Teknisi
        if ($request->hasFile('technician_file')) {
            $ticket->technician_file = $request->file('technician_file')->store('tickets/technician', 'public');
        }

        $ticket->save();

        return redirect()->back()->with('success', 'Penanganan tiket berhasil diperbarui!');
    }
}