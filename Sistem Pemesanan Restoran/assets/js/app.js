/**
 * Sistem Pemesanan Restoran - JavaScript Library
 * Mengelola interaktivitas dashboard, dynamic row pesanan menu,
 * kalkulasi harga real-time, dan konfirmasi aksi.
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Toggle Sidebar untuk Mobile & Tablet
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        if (!sidebar) return;
        sidebar.classList.toggle('active');
        if (sidebarOverlay) {
            sidebarOverlay.classList.toggle('active');
        }
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('active');
        if (sidebarOverlay) sidebarOverlay.classList.remove('active');
    }

    if (sidebarCollapse) {
        sidebarCollapse.addEventListener('click', toggleSidebar);
    }
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    // 2. Format Angka ke Rupiah di JS
    function formatRupiahJS(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    // 3. Dynamic Menu Item Builder pada Pemesanan
    const menuContainer = document.getElementById('menuItemsContainer');
    const btnAddMenu = document.getElementById('btnAddMenuItem');
    const grandTotalDisplay = document.getElementById('displayGrandTotal');

    function calculateOrderTotals() {
        if (!menuContainer) return;

        let subtotalTotal = 0;
        const rows = menuContainer.querySelectorAll('.order-item-row');

        rows.forEach(row => {
            const selectMenu = row.querySelector('.select-menu-item');
            const inputQty = row.querySelector('.input-menu-qty');
            const displaySubtotal = row.querySelector('.display-subtotal');

            const selectedOption = selectMenu ? selectMenu.options[selectMenu.selectedIndex] : null;
            const price = selectedOption ? parseFloat(selectedOption.getAttribute('data-price') || 0) : 0;
            const qty = inputQty ? parseInt(inputQty.value || 0, 10) : 0;

            const subtotal = price * (qty > 0 ? qty : 0);
            subtotalTotal += subtotal;

            if (displaySubtotal) {
                displaySubtotal.textContent = formatRupiahJS(subtotal);
            }
        });

        // Rumus Keuangan:
        // - Subtotal = jumlah seluruh (harga menu x kuantitas)
        // - Biaya layanan = subtotal x 5%
        // - Pajak = subtotal x 10%
        // - Total pembayaran = subtotal + biaya layanan + pajak
        const serviceFee = Math.round(subtotalTotal * 0.05);
        const taxFee = Math.round(subtotalTotal * 0.10);
        const grandTotal = subtotalTotal + serviceFee + taxFee;

        const subtotalDisplay = document.getElementById('displaySubtotalOrder');
        const serviceDisplay = document.getElementById('displayServiceFee');
        const taxDisplay = document.getElementById('displayTaxFee');

        if (subtotalDisplay) subtotalDisplay.textContent = formatRupiahJS(subtotalTotal);
        if (serviceDisplay) serviceDisplay.textContent = formatRupiahJS(serviceFee);
        if (taxDisplay) taxDisplay.textContent = formatRupiahJS(taxFee);

        if (grandTotalDisplay) {
            grandTotalDisplay.textContent = formatRupiahJS(grandTotal);
        }
    }

    if (menuContainer) {
        // Event delegation untuk perubahan menu atau qty
        menuContainer.addEventListener('change', function (e) {
            if (e.target.classList.contains('select-menu-item') || e.target.classList.contains('input-menu-qty')) {
                calculateOrderTotals();
            }
        });

        menuContainer.addEventListener('input', function (e) {
            if (e.target.classList.contains('input-menu-qty')) {
                calculateOrderTotals();
            }
        });

        // Event delegation untuk hapus baris menu
        menuContainer.addEventListener('click', function (e) {
            const btnRemove = e.target.closest('.btn-remove-item');
            if (btnRemove) {
                const row = btnRemove.closest('.order-item-row');
                if (row) {
                    row.remove();
                    calculateOrderTotals();
                }
            }
        });

        // Tambah baris menu baru
        if (btnAddMenu) {
            btnAddMenu.addEventListener('click', function () {
                const template = document.getElementById('menuItemTemplate');
                if (template) {
                    const clone = template.content.cloneNode(true);
                    menuContainer.appendChild(clone);
                    calculateOrderTotals();
                }
            });
        }

        // Hitung total awal saat halaman dimuat
        calculateOrderTotals();
    }

    // 4. Validasi Kapasitas Meja Real-time
    const selectMeja = document.getElementById('id_meja');
    const inputJumlahOrang = document.getElementById('jumlah_orang');
    const warningKapasitas = document.getElementById('kapasitasWarning');

    function checkCapacity() {
        if (!selectMeja || !inputJumlahOrang || !warningKapasitas) return;

        const selectedOption = selectMeja.options[selectMeja.selectedIndex];
        const kapasitas = selectedOption ? parseInt(selectedOption.getAttribute('data-kapasitas') || 0, 10) : 0;
        const jumlahOrang = parseInt(inputJumlahOrang.value || 0, 10);

        if (kapasitas > 0 && jumlahOrang > kapasitas) {
            warningKapasitas.classList.remove('d-none');
            warningKapasitas.textContent = `Peringatan: Jumlah orang (${jumlahOrang}) melebihi kapasitas meja yang dipilih (${kapasitas} orang)!`;
        } else {
            warningKapasitas.classList.add('d-none');
        }
    }

    if (selectMeja && inputJumlahOrang) {
        selectMeja.addEventListener('change', checkCapacity);
        inputJumlahOrang.addEventListener('input', checkCapacity);
        checkCapacity();
    }

    // 5. Konfirmasi Hapus Data
    const deleteButtons = document.querySelectorAll('.btn-confirm-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const itemName = this.getAttribute('data-name') || 'data ini';
            if (!confirm(`Apakah Anda yakin ingin menghapus ${itemName}? Tindakan ini tidak dapat dibatalkan.`)) {
                e.preventDefault();
            }
        });
    });
});
