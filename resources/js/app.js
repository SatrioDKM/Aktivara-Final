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

        // Buka/tutup dropdown notifikasi
        toggle() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.fetchNotifications();
            }
        },

        // Ambil data notifikasi dari API menggunakan Axios
        fetchNotifications() {
            // Axios sudah dikonfigurasi di bootstrap.js
            axios
                .get("/api/notifications")
                .then((response) => {
                    this.unread = response.data.unread;
                    this.read = response.data.read;
                    this.unreadCount = response.data.unread.length;
                })
                .catch((error) => {
                    console.error("Gagal mengambil notifikasi:", error);
                });
        },

        // Tandai semua notifikasi sebagai sudah dibaca menggunakan Axios
        async markAllAsRead() {
            // Axios akan secara otomatis menangani header X-XSRF-TOKEN.
            // Tidak perlu lagi mengambil token secara manual dari cookie.
            try {
                await axios.post("/api/notifications/mark-as-read");
                // Setelah berhasil, panggil fetchNotifications lagi untuk memperbarui UI
                this.fetchNotifications();
            } catch (error) {
                console.error("Gagal menandai notifikasi:", error);
            }
        },

        // Inisialisasi: ambil notifikasi saat halaman dimuat dan refresh setiap 1 menit
        init() {
            this.fetchNotifications();
            setInterval(() => this.fetchNotifications(), 60000); // 60 detik
        },
    };
}

// Daftarkan komponen 'notifications' ke window agar bisa diakses oleh Alpine
window.notifications = notifications;

// Inisialisasi Alpine.js
window.Alpine = Alpine;
Alpine.start();
