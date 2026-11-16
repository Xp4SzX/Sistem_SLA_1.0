// resources/js/admin/layanandigital.js

document.addEventListener("DOMContentLoaded", function() {
    // Jika ada data services yang dikirim dari blade untuk inisialisasi awal detail
    if (window.firstServiceData) {
        showDetail(window.firstServiceData);
    }
});

window.showDetail = function(item) {
    document.getElementById('detailTitle').innerText = item.name;
    document.getElementById('detailDesc').innerText = item.description || 'Sistem Layanan Digital';
    document.getElementById('detailDept').innerText = item.department ? item.department.name : (item.department_name || '-');
    
    // Format tampilan jenis layanan pada panel detail samping
    let typeMap = {
        'pengajuan_layanan': 'Layanan / Pengajuan',
        'kendala': 'Kendala / Masalah',
        'pelayanan_publik': 'Pelayanan Publik',
        'administrasi_pemerintahan': 'Administrasi Pemerintahan'
    };
    document.getElementById('detailType').innerText = typeMap[item.type] || 'Layanan / Pengajuan';
    document.getElementById('detailUptime').innerText = (item.kepatuhan_sla ?? 100) + '%';

    const imgTag = document.getElementById('detailImageTag');
    const imgIcon = document.getElementById('detailImageIcon');

    if (item.image) {
        imgTag.src = `/storage/${item.image}`;
        imgTag.classList.remove('hidden');
        imgIcon.style.display = 'none';
    } else {
        imgTag.src = '';
        imgTag.classList.add('hidden');
        imgIcon.style.display = 'block';
    }
};

window.openModalTambah = function() {
    const form = document.getElementById('formLayanan');

    form.method = 'POST';
    form.enctype = 'multipart/form-data';
    form.action = window.storeRoute || '';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalTitle').innerText = "Tambah Layanan Digital";

    form.reset();
    document.getElementById('inputType').value = 'pengajuan_layanan'; // Default pilihan baru

    document.getElementById('modalFormLayanan').classList.remove('hidden');
    document.getElementById('modalFormLayanan').classList.add('flex');
};

window.openModalEdit = function(item) {
    const form = document.getElementById('formLayanan');

    form.method = 'POST';
    form.enctype = 'multipart/form-data';
    
    // Gunakan rute update dinamis atau fallback ke URL standar
    const updateRouteTemplate = window.updateRouteTemplate || `/admin/layanan-digital/__ID__`;
    form.action = updateRouteTemplate.replace('__ID__', item.id);
    
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').innerText = "Edit Layanan Digital";

    // Isi field input dengan data layanan yang dipilih
    document.getElementById('inputNama').value = item.name || '';
    document.getElementById('inputKode').value = item.service_code || '';
    document.getElementById('inputDepartment').value = item.department ? item.department.name : (item.department_name || '');
    document.getElementById('inputType').value = item.type || 'pengajuan_layanan';
    
    // 14 Komponen Standar Pelayanan (SLA)
    document.getElementById('inputDasarHukum').value = item.dasar_hukum || '';
    document.getElementById('inputPersyaratan').value = item.persyaratan || '';
    document.getElementById('inputSistemProsedur').value = item.sistem_prosedur || '';
    document.getElementById('inputJangkaWaktu').value = item.jangka_waktu || '';
    document.getElementById('inputBiaya').value = item.biaya || 'Gratis';
    document.getElementById('inputProdukPelayanan').value = item.produk_pelayanan || '';
    document.getElementById('inputSaranaPrasarana').value = item.sarana_prasarana || '';
    document.getElementById('inputKompetensiPelaksana').value = item.kompetensi_pelaksana || '';
    document.getElementById('inputPengawasanInternal').value = item.pengawasan_internal || '';
    document.getElementById('inputPenangananPengaduan').value = item.penanganan_pengaduan || '';
    document.getElementById('inputJumlahPelaksana').value = item.jumlah_pelaksana || '';
    document.getElementById('inputJaminanKeamanan').value = item.jaminan_keamanan || '';
    document.getElementById('inputJaminanPelayanan').value = item.jaminan_pelayanan || '';
    document.getElementById('inputEvaluasiKinerja').value = item.evaluasi_kinerja || '';

    document.getElementById('modalFormLayanan').classList.remove('hidden');
    document.getElementById('modalFormLayanan').classList.add('flex');
};

window.closeModal = function() {
    document.getElementById('modalFormLayanan').classList.add('hidden');
    document.getElementById('modalFormLayanan').classList.remove('flex');
};

window.closeModalForm = function() {
    closeModal();
};