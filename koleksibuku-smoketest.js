import http from "k6/http";
import { check, sleep } from "k6";

export let options = {
    vus: 1,            
    duration: "20s",   
};

export default function () {

    const url = "http://localhost/TRPWIFix/koleksi.php";   

    // Jika halaman koleksi membutuhkan login:
    const params = {
        headers: {
            "Cookie": "PHPSESSID=ISI_SESSION_VALID"   
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
