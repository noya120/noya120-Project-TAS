import http from "k6/http";
import { check, sleep } from "k6";

export let options = {
    vus: 1,            // 1 virtual user saja
    duration: "20s",   // tes cepat
};

export default function () {

    const url = "http://localhost/TRPWIFix/koleksi.php";   // <-- ubah jika beda nama file

    // Jika halaman koleksi membutuhkan login:
    const params = {
        headers: {
            "Cookie": "PHPSESSID=ISI_SESSION_VALID"   // opsional, boleh dihapus jika tidak perlu login
        }
    };

    const res = http.get(url, params);

    check(res, {
        "Status 200 OK": r => r.status === 200,
        "Halaman koleksi tampil": r =>
            r.body.includes("Koleksi") ||
            r.body.includes("Buku") ||
            r.body.includes("Judul"),
        "Tidak ada error PHP": r =>
            !r.body.includes("Warning") &&
            !r.body.includes("Fatal") &&
            !r.body.includes("Notice"),
    });

    sleep(1);
}
