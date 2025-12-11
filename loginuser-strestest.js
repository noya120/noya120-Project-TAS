import http from "k6/http";
import { check } from "k6";

export let options = {
    stages: [
        { duration: "20s", target: 10 },   // durasi awal
        { duration: "20s", target: 50 },   // naik jadi 50 user
        { duration: "20s", target: 100 },  // tekanan tinggi
        { duration: "20s", target: 0 },    // turunkan kembali
    ],
};

export default function () {

    const loginUrl = "http://localhost/TRPWIFix/userlogin.php";

    const payload = {
        username: "Kresna",
        password: "Kresna1234"
    };

    const params = {
        headers: { "Content-Type": "application/x-www-form-urlencoded" }
    };

    let res = http.post(loginUrl, payload, params);

    check(res, {
        "status ok": r => r.status === 200,
        "login masih berhasil": r => r.cookies.PHPSESSID !== undefined,
        "tidak error server": r =>
            !r.body.includes("Warning") &&
            !r.body.includes("Fatal")
    });
}
