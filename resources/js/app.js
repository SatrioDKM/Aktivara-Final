import "./bootstrap";
import Alpine from "alpinejs";

/**
 * Logika untuk komponen notifikasi real-time.
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

        // Ambil data notifikasi dari API
        fetchNotifications() {
            // URL diubah menjadi string manual
            fetch("/api/notifications", {
                headers: {
                    Accept: "application/json",
                },
            })
                .then((res) => res.json())
                .then((data) => {
                    this.unread = data.unread;
                    this.read = data.read;
                    this.unreadCount = data.unread.length;
                });
        },

        // Tandai semua notifikasi sebagai sudah dibaca
        async markAllAsRead() {
            // Kita butuh CSRF token untuk request POST
            await fetch("/sanctum/csrf-cookie");

            // URL diubah menjadi string manual
            fetch("/api/notifications/mark-as-read", {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "X-XSRF-TOKEN": this.getCsrfToken(),
                },
            }).then(() => {
                this.fetchNotifications();
            });
        },

        // Helper untuk mendapatkan CSRF token dari cookie
        getCsrfToken() {
            const c = document.cookie
                .split("; ")
                .find((r) => r.startsWith("XSRF-TOKEN="));
            return c ? decodeURIComponent(c.split("=")[1]) : "";
        },

        // Inisialisasi: ambil notifikasi saat halaman dimuat dan refresh setiap 1 menit
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
