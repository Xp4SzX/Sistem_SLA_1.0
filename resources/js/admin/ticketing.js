document.addEventListener('DOMContentLoaded', function () {
    // Jalankan timer real-time SLA pada tabel jika ada
    initSlaTimers();
});

// Fungsi untuk membuka modal tindak lanjut dan mengisi data tiket termasuk gambar bukti
window.handleKelolaBtn = function(button) {
    try {
        const ticket = JSON.parse(button.getAttribute('data-ticket'));
        const pelapor = button.getAttribute('data-pelapor');

        // Set teks informasi ke dalam modal
        document.getElementById('modalTicketNo').innerText = ticket.ticket_no || 'SLA-' + ticket.id;
        document.getElementById('modalLayanan').innerText = ticket.service && ticket.service.name ? ticket.service.name : 'Layanan Digital';
        document.getElementById('modalPelapor').innerText = pelapor || 'Pelapor';
        document.getElementById('modalPrioritas').innerText = (ticket.priority || 'MEDIUM').toUpperCase();
        document.getElementById('modalTechnician').value = ticket.technician || '';
        document.getElementById('modalStatus').value = (ticket.status || 'in_progress').toLowerCase();
        
        // Atur action form update status dinamis berdasarkan ID tiket
        document.getElementById('formTindakLanjut').action = `/admin/tickets/${ticket.id}/update-status`;

        // Cek berbagai kemungkinan nama kolom database untuk lampiran gambar/bukti
        const imagePath = ticket.image || ticket.attachment || ticket.file || ticket.bukti || ticket.photo;

        const imageContainer = document.getElementById('modalImageContainer');
        const ticketImage = document.getElementById('modalTicketImage');
        
        if (imagePath) {
            // Tangani path gambar dari storage atau URL lengkap
            ticketImage.src = imagePath.startsWith('http') || imagePath.startsWith('/') ? imagePath : `/storage/${imagePath}`;
            imageContainer.classList.remove('hidden');
        } else {
            imageContainer.classList.add('hidden');
            ticketImage.src = '';
        }

        // Tampilkan modal (Ubah dari hidden ke flex)
        const modal = document.getElementById('modalTindakLanjut');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    } catch (error) {
        console.error('Gagal memuat data tiket ke modal:', error);
    }
};

// Fungsi untuk menutup modal
window.closeModal = function() {
    const modal = document.getElementById('modalTindakLanjut');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
};

// Fungsi real-time countdown / timer SLA pada baris tabel admin
function initSlaTimers() {
    const timerCells = document.querySelectorAll('.sla-timer');

    timerCells.forEach(cell => {
        const createdAtStr = cell.getAttribute('data-created');
        const status = cell.getAttribute('data-status');
        const maxHours = parseInt(cell.getAttribute('data-max-hours')) || 8;

        if (['resolved', 'closed', 'completed'].includes(status.toLowerCase())) {
            return; // Lewati jika sudah selesai
        }

        const createdAt = new Date(createdAtStr);
        const deadline = new Date(createdAt.getTime() + maxHours * 60 * 60 * 1000);

        function updateTimer() {
            const now = new Date();
            const diffMs = deadline - now;

            if (diffMs <= 0) {
                const overMs = Math.abs(diffMs);
                const overSec = Math.floor(overMs / 1000);
                const overMin = Math.floor(overSec / 60);
                const overHour = Math.floor(overMin / 60);
                const remMin = overMin % 60;
                
                cell.innerHTML = `<span class="text-rose-600 font-bold">Lewat ${overHour}j ${remMin}m (Melewati SLA)</span>`;
            } else {
                const totalSec = Math.floor(diffMs / 1000);
                const hours = Math.floor(totalSec / 3600);
                const minutes = Math.floor((totalSec % 3600) / 60);
                const seconds = totalSec % 60;

                cell.innerText = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }
        }

        updateTimer();
        setInterval(updateTimer, 1000);
    });
}

// Tutup modal otomatis ketika user menekan tombol ESC di keyboard
window.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});