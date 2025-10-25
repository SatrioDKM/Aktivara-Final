import "./bootstrap";

// Import CSS dari Font Awesome yang sudah diinstall via NPM
import "@fortawesome/fontawesome-free/css/all.min.css";

// Import Chart.js dan jadikan global agar bisa diakses di script lain jika perlu
import Chart from "chart.js/auto";
window.Chart = Chart;

// Import jQuery
import jQuery from "jquery";
window.$ = window.jQuery = jQuery;

// Import Select2
import select2 from "select2";
import "select2/dist/css/select2.min.css";
select2(); // Inisialisasi fungsi select2() secara global

// Import iziToast
import iziToast from "izitoast";
import "izitoast/dist/css/iziToast.min.css";
window.iziToast = iziToast;

// === TAMBAHKAN IMPOR DATATABLES DI SINI ===
import DataTable from "datatables.net-dt";
import "datatables.net-dt/css/dataTables.dataTables.css"; // Sesuaikan path jika berbeda
window.DataTable = DataTable; // Jadikan global agar bisa diakses script Blade

import Alpine from "alpinejs";

/**
 * Logika untuk komponen notifikasi real-time.
 * Direvisi untuk menggunakan Axios agar lebih bersih dan terintegrasi.
 */
function notifications() {
    return {
        isOpen: false,
        unread: [],
        read: [],
        unreadCount: 0,

        toggle() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.fetchNotifications();
            }
        },

        fetchNotifications() {
            // Pastikan URL bersih dan benar
            axios
                .get("/api/notifications")
                .then((response) => {
                    this.unread = response.data.unread;
                    this.read = response.data.read;
                    this.unreadCount = response.data.unread.length;
                })
                .catch((error) => {
                    // Beri pesan error yang lebih jelas jika fetch gagal
                    console.error("Gagal mengambil notifikasi:", error);
                    // Anda bisa tambahkan iziToast error di sini jika diperlukan
                    // window.iziToast.error({ title: 'Error Notifikasi', message: 'Gagal memuat notifikasi dari server.', position: 'topRight' });
                });
        },

        async markAsRead(notificationId) {
            try {
                // Cari notifikasi SEBELUM request API
                const notification = [...this.unread, ...this.read].find(
                    (n) => n.id === notificationId
                );
                const targetUrl = notification?.data?.url; // Simpan URL target

                // Kirim POST request ke endpoint baru
                await axios.post("/api/notifications/mark-one-read", {
                    id: notificationId,
                });

                // Refresh daftar notifikasi (opsional, tergantung UX)
                // this.fetchNotifications(); // Anda bisa comment ini jika tidak ingin refresh langsung

                // --- PERBAIKAN DI SINI ---
                // Navigasi ke URL notifikasi JIKA URL ada, SETELAH request berhasil
                if (targetUrl) {
                    window.location.href = targetUrl; // Arahkan ke URL notifikasi
                } else {
                    // Jika tidak ada URL, cukup refresh notifikasi
                    this.fetchNotifications();
                }
                // --- AKHIR PERBAIKAN ---
            } catch (error) {
                console.error(
                    `Gagal menandai notifikasi ${notificationId} sebagai terbaca:`,
                    error
                );
                window.iziToast.error({
                    title: "Error",
                    message: "Gagal menandai notifikasi.",
                    position: "topRight",
                });
            }
        },

        async markAllAsRead() {
            try {
                // Gunakan endpoint yang sudah ada untuk mark all
                await axios.post("/api/notifications/mark-as-read");
                this.fetchNotifications();
            } catch (error) {
                console.error("Gagal menandai semua notifikasi:", error);
                window.iziToast.error({
                    title: "Error",
                    message: "Gagal menandai semua notifikasi.",
                    position: "topRight",
                });
            }
        },

        init() {
            this.fetchNotifications();
            setInterval(() => this.fetchNotifications(), 60000);
        },
    };
}

// Daftarkan komponen 'notifications' ke window agar bisa diakses oleh Alpine
window.notifications = notifications;

// Inisialisasi Alpine.js
window.Alpine = Alpine;
Alpine.start();
